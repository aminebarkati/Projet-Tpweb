<?php

declare(strict_types=1);

require_once __DIR__ . '/../autoloader.php';

const JUDGE_IMAGE = 'projet-web-dev:latest';
const WORK_LIMIT = 20;
const MAX_MEMORY_MB = 256;
const POLL_INTERVAL_SECONDS = 2;

function logLine(string $message): void
{
    echo '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
}

function projectRoot(): string
{
    return dirname(__DIR__, 2);
}

function ensureJudgeImageExists(): void
{
    $inspectCommand = 'docker image inspect ' . escapeshellarg(JUDGE_IMAGE) . ' >/dev/null 2>&1';
    exec($inspectCommand, $inspectOutput, $inspectExitCode);

    if ($inspectExitCode === 0) {
        return;
    }

    $root = projectRoot();
    $buildCommand = sprintf(
        'docker build -t %s -f %s %s',
        escapeshellarg(JUDGE_IMAGE),
        escapeshellarg($root . '/Dockerfile'),
        escapeshellarg($root)
    );

    logLine('Building judge image...');
    passthru($buildCommand, $buildExitCode);

    if ($buildExitCode !== 0) {
        throw new RuntimeException('Failed to build the judge image.');
    }
}

function normalizeOutput(string $output): string
{
    $output = str_replace(["\r\n", "\r"], "\n", $output);
    $lines = explode("\n", $output);
    $normalizedLines = [];

    foreach ($lines as $line) {
        $normalizedLines[] = rtrim($line, " \t");
    }

    while ($normalizedLines !== [] && end($normalizedLines) === '') {
        array_pop($normalizedLines);
    }

    return implode("\n", $normalizedLines);
}

function getVerdictId(string $verdictCode): int
{
    static $cache = [];

    if (isset($cache[$verdictCode])) {
        return $cache[$verdictCode];
    }

    $db = ConnexionDB::getInstance();
    $response = $db->prepare('SELECT id FROM verdict_status WHERE verdict = ?');
    $response->execute([$verdictCode]);
    $verdictId = (int) ($response->fetchColumn() ?: 0);

    if ($verdictId <= 0) {
        throw new RuntimeException('Unknown verdict code: ' . $verdictCode);
    }

    $cache[$verdictCode] = $verdictId;
    return $verdictId;
}

function languageProfile(object $language): array
{
    $name = strtolower(trim((string) $language->language_name));
    $extension = strtolower(trim((string) ($language->file_extension ?? '')));

    return match ($name) {
        'c++' => [
            'source_file' => 'main.cpp',
            'compile' => 'g++ -O2 -std=c++17 -pipe -o main main.cpp',
            'run' => './main',
            'needs_compile' => true,
        ],
        'c' => [
            'source_file' => 'main.c',
            'compile' => 'gcc -O2 -pipe -o main main.c',
            'run' => './main',
            'needs_compile' => true,
        ],
        'java' => [
            'source_file' => 'Main.java',
            'compile' => 'javac -encoding UTF-8 Main.java',
            'run' => 'java Main',
            'needs_compile' => true,
        ],
        'javascript' => [
            'source_file' => 'main.js',
            'compile' => null,
            'run' => 'node main.js',
            'needs_compile' => false,
        ],
        'python' => [
            'source_file' => 'main.py',
            'compile' => null,
            'run' => 'python3 main.py',
            'needs_compile' => false,
        ],
        default => match ($extension) {
            '.cpp' => [
                'source_file' => 'main.cpp',
                'compile' => 'g++ -O2 -std=c++17 -pipe -o main main.cpp',
                'run' => './main',
                'needs_compile' => true,
            ],
            '.c' => [
                'source_file' => 'main.c',
                'compile' => 'gcc -O2 -pipe -o main main.c',
                'run' => './main',
                'needs_compile' => true,
            ],
            '.java' => [
                'source_file' => 'Main.java',
                'compile' => 'javac -encoding UTF-8 Main.java',
                'run' => 'java Main',
                'needs_compile' => true,
            ],
            '.js' => [
                'source_file' => 'main.js',
                'compile' => null,
                'run' => 'node main.js',
                'needs_compile' => false,
            ],
            '.py' => [
                'source_file' => 'main.py',
                'compile' => null,
                'run' => 'python3 main.py',
                'needs_compile' => false,
            ],
            default => throw new RuntimeException('Unsupported language: ' . (string) $language->language_name),
        },
    };
}

function runDockerCommand(string $workDir, string $innerCommand, int $memoryLimitMb): array
{
    $dockerCommand = sprintf(
        'docker run --rm --network none --cap-drop=ALL --security-opt no-new-privileges --pids-limit=64 --read-only --tmpfs /tmp:rw,noexec,nosuid,size=64m -e HOME=/tmp --memory %dm --memory-swap %dm -v %s:/workspace -w /workspace %s bash -lc %s',
        $memoryLimitMb,
        $memoryLimitMb,
        escapeshellarg($workDir),
        escapeshellarg(JUDGE_IMAGE),
        escapeshellarg('cd /workspace && ' . $innerCommand)
    );

    $output = [];
    $exitCode = 0;
    exec($dockerCommand . ' 2>&1', $output, $exitCode);

    return [
        'exit_code' => $exitCode,
        'output' => implode("\n", $output),
    ];
}

function recursiveRemove(string $directory): void
{
    if (!is_dir($directory)) {
        return;
    }

    $items = array_diff(scandir($directory) ?: [], ['.', '..']);

    foreach ($items as $item) {
        $path = $directory . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            recursiveRemove($path);
            continue;
        }

        @unlink($path);
    }

    @rmdir($directory);
}

function updateProblemStats(ProblemsRepository $problemsRepository, int $problemId, bool $accepted): void
{
    $problemsRepository->updateJudgingStats($problemId, $accepted);
}

function judgeSingleSubmission(
    object $submission,
    ProblemsRepository $problemsRepository,
    TestCasesRepository $testCasesRepository,
    SubmissionsRepository $submissionsRepository
): void {
    $problem = $problemsRepository->findById((int) $submission->problem_id);
    if (!$problem) {
        $submissionsRepository->updateJudgingResult(
            (int) $submission->id,
            getVerdictId('RE'),
            null,
            null,
            0,
            0,
            'Problem not found.'
        );
        return;
    }

    $testCases = $testCasesRepository->findByProblemId((int) $submission->problem_id);
    if ($testCases === []) {
        $submissionsRepository->updateJudgingResult(
            (int) $submission->id,
            getVerdictId('RE'),
            null,
            null,
            0,
            0,
            'No test cases are configured for this problem.'
        );
        updateProblemStats($problemsRepository, (int) $submission->problem_id, false);
        return;
    }

    $languageProfile = languageProfile($submission);
    $workDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'judge-' . (int) $submission->id . '-' . bin2hex(random_bytes(4));
    mkdir($workDir, 0777, true);

    try {
        $sourcePath = $workDir . DIRECTORY_SEPARATOR . $languageProfile['source_file'];
        file_put_contents($sourcePath, (string) $submission->code);

        $submissionStart = microtime(true);

        if ($languageProfile['needs_compile']) {
            $compileCommand = 'timeout --kill-after=1s 30s ' . $languageProfile['compile'];
            $compileResult = runDockerCommand($workDir, $compileCommand, max(256, (int) ($problem->memory_limit_mb ?? MAX_MEMORY_MB)));

            if ($compileResult['exit_code'] !== 0) {
                $errorMessage = trim($compileResult['output']) !== '' ? trim($compileResult['output']) : 'Compilation failed.';
                $submissionsRepository->updateJudgingResult(
                    (int) $submission->id,
                    getVerdictId('CE'),
                    (int) round((microtime(true) - $submissionStart) * 1000),
                    null,
                    0,
                    count($testCases),
                    $errorMessage
                );
                updateProblemStats($problemsRepository, (int) $submission->problem_id, false);
                return;
            }
        }

        $timeLimitMs = max(1, (int) ($problem->time_limit_ms ?? 1000));
        $memoryLimitMb = max(64, (int) ($problem->memory_limit_mb ?? MAX_MEMORY_MB));
        $passedTests = 0;
        $verdictCode = 'AC';
        $errorMessage = null;

        foreach ($testCases as $index => $testCase) {
            $inputFile = $workDir . DIRECTORY_SEPARATOR . 'input.txt';
            $actualFile = $workDir . DIRECTORY_SEPARATOR . 'actual.txt';
            $expectedNormalized = normalizeOutput((string) $testCase->expected_output);
            file_put_contents($inputFile, (string) $testCase->input);

            $runtimeCommand = sprintf(
                'timeout --kill-after=1s %ss %s < input.txt > actual.txt 2> runtime.err',
                max(1, (int) ceil($timeLimitMs / 1000)),
                $languageProfile['run']
            );

            $runtimeResult = runDockerCommand($workDir, $runtimeCommand, $memoryLimitMb);
            $actualOutput = is_file($actualFile) ? (string) file_get_contents($actualFile) : '';
            $actualNormalized = normalizeOutput($actualOutput);
            $rawNormalized = str_replace(["\r\n", "\r"], "\n", $actualOutput);
            $expectedRawNormalized = str_replace(["\r\n", "\r"], "\n", (string) $testCase->expected_output);

            if ($runtimeResult['exit_code'] === 124) {
                $verdictCode = 'TLE';
                $errorMessage = 'Time limit exceeded on test case ' . ((int) $index + 1) . '.';
                break;
            }

            if ($runtimeResult['exit_code'] === 137) {
                $verdictCode = 'MLE';
                $errorMessage = 'Memory limit exceeded on test case ' . ((int) $index + 1) . '.';
                break;
            }

            if ($runtimeResult['exit_code'] !== 0) {
                $verdictCode = 'RE';
                $stderrPath = $workDir . DIRECTORY_SEPARATOR . 'runtime.err';
                $stderrOutput = is_file($stderrPath) ? trim((string) file_get_contents($stderrPath)) : '';
                $errorMessage = $stderrOutput !== '' ? $stderrOutput : 'Runtime error on test case ' . ((int) $index + 1) . '.';
                break;
            }

            if ($actualNormalized !== $expectedNormalized) {
                $verdictCode = 'WA';
                $errorMessage = 'Wrong answer on test case ' . ((int) $index + 1) . '.';
                break;
            }

            $passedTests++;

            if ($rawNormalized !== $expectedRawNormalized) {
                $verdictCode = 'PE';
                $errorMessage = 'Presentation difference detected on test case ' . ((int) $index + 1) . '.';
                break;
            }
        }

        $executionTimeMs = (int) round((microtime(true) - $submissionStart) * 1000);
        $submissionVerdictId = getVerdictId($verdictCode);

        $submissionsRepository->updateJudgingResult(
            (int) $submission->id,
            $submissionVerdictId,
            $executionTimeMs,
            null,
            $passedTests,
            count($testCases),
            $errorMessage
        );

        updateProblemStats($problemsRepository, (int) $submission->problem_id, $verdictCode === 'AC');
        logLine(sprintf('Submission #%d judged as %s (%d/%d tests).', (int) $submission->id, $verdictCode, $passedTests, count($testCases)));
    } finally {
        recursiveRemove($workDir);
    }
}

function processPendingSubmissions(): void
{
    $submissionsRepository = new SubmissionsRepository();
    $problemsRepository = new ProblemsRepository();
    $testCasesRepository = new TestCasesRepository();

    $pendingSubmissions = $submissionsRepository->findPending(WORK_LIMIT);
    if ($pendingSubmissions === []) {
        logLine('No pending submissions found.');
        return;
    }

    foreach ($pendingSubmissions as $submission) {
        judgeSingleSubmission($submission, $problemsRepository, $testCasesRepository, $submissionsRepository);
    }
}

if (PHP_SAPI !== 'cli') {
    throw new RuntimeException('The judge must be run from the command line.');
}

ensureJudgeImageExists();
logLine('Judge started.');

$runOnce = in_array('--once', $argv ?? [], true);

do {
    processPendingSubmissions();
    if (!$runOnce) {
        sleep(POLL_INTERVAL_SECONDS);
    }
} while (!$runOnce);
