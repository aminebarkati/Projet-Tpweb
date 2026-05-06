<?php

/**
 * AlgoSpark Judge Worker
 * 
 * Run via CLI: php worker.php
 * Or as a daemon: nohup php worker.php >> /var/log/algospark_judge.log 2>&1 &
 * 
 * Picks PENDING submissions, judges them against all test cases,
 * and updates the DB with the verdict.
 */

declare(strict_types=1);

require_once __DIR__ . '/../autoloader.php';

// ─────────────────────────────────────────────
// CONFIG
// ─────────────────────────────────────────────


define('SUBMISSIONS_BASE_DIR', __DIR__ . '/../../storage/submission_files');

// Verdict IDs — must match your verdict_status table
define('VERDICT_AC',          1);
define('VERDICT_WA',          2);
define('VERDICT_TLE',         3);
define('VERDICT_MLE',         4);
define('VERDICT_RE',          5);
define('VERDICT_CE',          6);
define('VERDICT_PENDING',     8);
define('VERDICT_INPROGRESS',  9);

// Worker loop config
define('POLL_INTERVAL_SECONDS', 3);   // How long to sleep when no jobs
define('MAX_ITERATIONS', 0);          // 0 = run forever, N = stop after N submissions (useful for testing)


// ─────────────────────────────────────────────
// BOOTSTRAP
// ─────────────────────────────────────────────

// Ensure we're running from CLI only
if (php_sapi_name() !== 'cli') {
    die("worker.php must be run from the command line.\n");
}

// Initialize repositories
$submissionsRepository = new SubmissionsRepository();
$problemsRepository = new ProblemsRepository();
$testCasesRepository = new TestCasesRepository();
$languagesRepository = new LanguagesRepository();

log_msg("AlgoSpark Judge Worker started. PID: " . getmypid());

if (!is_dir(SUBMISSIONS_BASE_DIR)) {
    mkdir(SUBMISSIONS_BASE_DIR, 0755, true);
}


// ─────────────────────────────────────────────
// MAIN LOOP
// ─────────────────────────────────────────────

$iterations = 0;

while (true) {
    $submissions = $submissionsRepository->findPending(1);

    if (empty($submissions)) {
        sleep(POLL_INTERVAL_SECONDS);
        continue;
    }

    $submission = (array) $submissions[0];

    log_msg("Picked up submission #{$submission['id']} | User:{$submission['user_id']} | Problem:{$submission['problem_id']} | Lang:{$submission['language_name']}");

    $result = judgeSubmission($submission, $submissionsRepository, $testCasesRepository);
    $submissionsRepository->updateJudgingResult(
        (int) $submission['id'],
        $result['verdict_id'],
        $result['execution_time_ms'],
        null,
        $result['passed_tests'],
        $result['total_tests'],
        $result['error_message']
    );

    $problemsRepository->updateJudgingStats((int) $submission['problem_id'], $result['verdict_id'] === VERDICT_AC);

    log_msg(
        "Submission #{$submission['id']} done | " .
            "Verdict: {$result['verdict_code']} | " .
            "Passed: {$result['passed_tests']}/{$result['total_tests']} | " .
            "Time: {$result['execution_time_ms']}ms"
    );

    $iterations++;
    if (MAX_ITERATIONS > 0 && $iterations >= MAX_ITERATIONS) {
        log_msg("Reached MAX_ITERATIONS limit. Exiting.");
        break;
    }
}


// ─────────────────────────────────────────────
// JUDGE
// ─────────────────────────────────────────────

function judgeSubmission(array $sub, SubmissionsRepository $submissionsRepository, TestCasesRepository $testCasesRepository): array
{
    $workDir = sys_get_temp_dir() . '/algospark_' . $sub['id'] . '_' . time();
    mkdir($workDir, 0755, true);

    $result = [
        'verdict_id'       => VERDICT_AC,
        'verdict_code'     => 'AC',
        'execution_time_ms' => 0,
        'passed_tests'     => 0,
        'total_tests'      => 0,
        'error_message'    => null,
    ];

    try {
        // ── 1. Copy source file to work dir ──────────────────────────────
        $fileExtension = strtolower(trim((string) ($sub['file_extension'] ?? '')));
        if ($fileExtension === '') {
            $fileExtension = '.txt';
        }

        if ($fileExtension[0] !== '.') {
            $fileExtension = '.' . $fileExtension;
        }

        $fileExtension = preg_replace('/[^a-z0-9.]/', '', $fileExtension) ?: '.txt';
        $srcPath = SUBMISSIONS_BASE_DIR . '/' . $sub['user_id'] . '/' . $sub['id'] . $fileExtension;

        if (!file_exists($srcPath)) {
            throw new RuntimeException("Source file not found: {$srcPath}");
        }

        $localSrc = $workDir . '/Main' . $sub['file_extension'];
        copy($srcPath, $localSrc);

        // ── 2. Compile (if needed) ────────────────────────────────────────
        $binaryPath = null;

        if (needsCompile($sub['language_name'])) {
            $compileResult = compile($sub['language_name'], $sub['compiler_command'], $localSrc, $workDir);

            if ($compileResult['error']) {
                $result['verdict_id']    = VERDICT_CE;
                $result['verdict_code']  = 'CE';
                $result['error_message'] = $compileResult['output'];
                return $result;
            }

            // For Java the "binary" is the class directory, for C/C++ it's the executable
            $binaryPath = ($sub['language_name'] === 'Java') ? $workDir : $workDir . '/Main';
        }

        // ── 3. Fetch test cases ───────────────────────────────────────────
        $testCaseObjects = $testCasesRepository->findByProblemId((int) $sub['problem_id']);
        $testCases = array_map(fn($tc) => (array) $tc, $testCaseObjects);
        $result['total_tests'] = count($testCases);

        if ($result['total_tests'] === 0) {
            // No test cases configured — treat as system error
            $result['verdict_id']   = VERDICT_RE;
            $result['verdict_code'] = 'RE';
            $result['error_message'] = 'No test cases found for this problem.';
            return $result;
        }

        // ── 4. Run against each test case ─────────────────────────────────
        $maxTimeMs = 0;

        foreach ($testCases as $tc) {
            $inputFile = $workDir . '/input.txt';
            file_put_contents($inputFile, $tc['input']);

            $runResult = runCode(
                $sub['language_name'],
                $localSrc,
                $binaryPath,
                $workDir,
                $inputFile,
                (int)$sub['time_limit_ms']
            );

            // Track max execution time
            if ($runResult['time_ms'] > $maxTimeMs) {
                $maxTimeMs = $runResult['time_ms'];
            }

            // Check for TLE
            if ($runResult['timed_out']) {
                $result['verdict_id']    = VERDICT_TLE;
                $result['verdict_code']  = 'TLE';
                $result['execution_time_ms'] = $maxTimeMs;
                return $result;
            }

            // Check for RE (non-zero exit code, not timeout)
            if ($runResult['exit_code'] !== 0) {
                $result['verdict_id']    = VERDICT_RE;
                $result['verdict_code']  = 'RE';
                $result['error_message'] = $runResult['stderr'];
                $result['execution_time_ms'] = $maxTimeMs;
                return $result;
            }

            // Compare output
            $actual   = normalizeOutput($runResult['stdout']);
            $expected = normalizeOutput($tc['expected_output']);

            if ($actual !== $expected) {
                $result['verdict_id']    = VERDICT_WA;
                $result['verdict_code']  = 'WA';
                $result['execution_time_ms'] = $maxTimeMs;
                // Optionally store diff info for debugging:
                $result['error_message'] = "Test #{$tc['order_index']}: expected [{$expected}], got [{$actual}]";
                return $result;
            }

            $result['passed_tests']++;
        }

        // All tests passed
        $result['execution_time_ms'] = $maxTimeMs;
    } catch (Throwable $e) {
        $result['verdict_id']    = VERDICT_RE;
        $result['verdict_code']  = 'RE';
        $result['error_message'] = $e->getMessage();
    } finally {
        cleanUp($workDir);
    }

    return $result;
}


// ─────────────────────────────────────────────
// COMPILE
// ─────────────────────────────────────────────

function needsCompile(string $lang): bool
{
    return in_array(strtolower($lang), ['c', 'c++', 'java'], true);
}

function compile(string $lang, string $compilerCmd, string $srcPath, string $workDir): array
{
    $lang = strtolower($lang);

    if ($lang === 'java') {
        // javac Main.java — outputs .class into same dir
        $cmd = "{$compilerCmd} {$srcPath} 2>&1";
    } else {
        // gcc/g++ — output binary as $workDir/Main
        $binary = $workDir . '/Main';
        $cmd = "{$compilerCmd} {$srcPath} -o {$binary} 2>&1";
    }

    exec($cmd, $outputLines, $exitCode);

    return [
        'error'  => $exitCode !== 0,
        'output' => implode("\n", $outputLines),
    ];
}


// ─────────────────────────────────────────────
// RUN
// ─────────────────────────────────────────────

function runCode(
    string $lang,
    string $srcPath,
    ?string $binaryPath,
    string $workDir,
    string $inputFile,
    int $timeLimitMs
): array {
    $lang        = strtolower($lang);
    $timeLimitSec = max(1, (int)ceil($timeLimitMs / 1000));

    // Build the run command per language
    switch ($lang) {
        case 'c':
        case 'c++':
            $runCmd = $binaryPath;
            break;

        case 'java':
            // binaryPath is the class directory, Main is the class name
            $runCmd = "java -cp {$binaryPath} Main";
            break;

        case 'python':
            $runCmd = "python3 {$srcPath}";
            break;

        case 'javascript':
            $runCmd = "node {$srcPath}";
            break;

        default:
            throw new RuntimeException("Unsupported language: {$lang}");
    }

    // Capture stdout, stderr separately
    $stdoutFile = $workDir . '/stdout.txt';
    $stderrFile = $workDir . '/stderr.txt';

    $startTime = microtime(true);

    $fullCmd = "timeout {$timeLimitSec}s {$runCmd} < {$inputFile} > {$stdoutFile} 2> {$stderrFile}";
    exec($fullCmd, $ignored, $exitCode);

    $elapsed = (int)round((microtime(true) - $startTime) * 1000);

    $stdout = file_exists($stdoutFile) ? file_get_contents($stdoutFile) : '';
    $stderr = file_exists($stderrFile) ? file_get_contents($stderrFile) : '';

    // exit code 124 = timeout by `timeout` command
    $timedOut = ($exitCode === 124);

    return [
        'stdout'    => $stdout,
        'stderr'    => $stderr,
        'exit_code' => $exitCode,
        'timed_out' => $timedOut,
        'time_ms'   => $elapsed,
    ];
}


// ─────────────────────────────────────────────
// OUTPUT COMPARISON
// ─────────────────────────────────────────────

/**
 * Normalize output for comparison:
 * - Trim leading/trailing whitespace
 * - Normalize line endings to \n
 * - Trim trailing whitespace from each line
 * - Remove trailing empty lines
 */
function normalizeOutput(string $output): string
{
    $output = str_replace("\r\n", "\n", $output);
    $lines  = explode("\n", $output);
    $lines  = array_map('rtrim', $lines);

    // Remove trailing empty lines
    while (!empty($lines) && $lines[count($lines) - 1] === '') {
        array_pop($lines);
    }

    return implode("\n", $lines);
}





// ─────────────────────────────────────────────
// UTILITY
// ─────────────────────────────────────────────

function cleanUp(string $dir): void
{
    if (!is_dir($dir)) return;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($dir);
}

function log_msg(string $msg): void
{
    echo '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
}
