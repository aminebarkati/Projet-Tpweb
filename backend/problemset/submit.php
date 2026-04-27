<?php

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../autoloader.php';

function submitResponse(bool $success, string $message, array $extra = []): void
{
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    submitResponse(false, 'Method not allowed.');
}

if (empty($_SESSION['user'])) {
    submitResponse(false, 'Unauthorized. Please log in.');
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody ?: '{}', true);

if (!is_array($payload)) {
    submitResponse(false, 'Invalid request payload.');
}

$problemId = isset($payload['problem_id']) ? (int) $payload['problem_id'] : 0;
$languageId = isset($payload['language_id']) ? (int) $payload['language_id'] : 0;
$code = isset($payload['code']) ? trim((string) $payload['code']) : '';
$attachedFilename = isset($payload['attached_filename']) ? trim((string) $payload['attached_filename']) : '';

if ($problemId <= 0) {
    submitResponse(false, 'Invalid problem id.');
}

if ($languageId <= 0) {
    submitResponse(false, 'Please select a valid programming language.');
}

if ($code === '') {
    submitResponse(false, 'Code is required.');
}

if (strlen($code) > 262144) {
    submitResponse(false, 'Code exceeds maximum allowed size (256KB).');
}

$userRepository = new UserRepository();
$problemRepository = new ProblemsRepository();
$languagesRepository = new LanguagesRepository();
$submissionsRepository = new SubmissionsRepository();

$currentUser = $userRepository->findByUsername((string) $_SESSION['user']->username);
if (!$currentUser) {
    submitResponse(false, 'User not found.');
}

$problem = $problemRepository->findById($problemId);
if (!$problem) {
    submitResponse(false, 'Problem not found.');
}

$language = $languagesRepository->findById($languageId);
if (!$language || empty($language->is_enabled)) {
    submitResponse(false, 'Selected language is unavailable.');
}

if ($attachedFilename !== '') {
    $expectedExtension = strtolower((string) ($language->file_extension ?? ''));
    $attachedExtension = strtolower(pathinfo($attachedFilename, PATHINFO_EXTENSION));
    $attachedExtension = $attachedExtension !== '' ? '.' . $attachedExtension : '';

    if ($expectedExtension !== '' && $attachedExtension !== $expectedExtension) {
        submitResponse(false, 'Attached file extension does not match selected language.');
    }
}

$selectedExtension = strtolower(trim((string) ($language->file_extension ?? '')));
if ($selectedExtension === '' && $attachedFilename !== '') {
    $attachedExtension = strtolower(pathinfo($attachedFilename, PATHINFO_EXTENSION));
    $selectedExtension = $attachedExtension !== '' ? '.' . $attachedExtension : '';
}

if ($selectedExtension === '') {
    $selectedExtension = '.txt';
}

if ($selectedExtension[0] !== '.') {
    $selectedExtension = '.' . $selectedExtension;
}

$selectedExtension = preg_replace('/[^a-z0-9.]/', '', $selectedExtension) ?: '.txt';

$submissionId = null;

try {
    $submissionId = $submissionsRepository->createPending((int) $currentUser->id, $problemId, $languageId);

    $relativeFilePath = (int) $currentUser->id . '/' . $submissionId . $selectedExtension;
    $baseSubmissionsDir = dirname(__DIR__, 2) . '/storage/submission_files';
    $absoluteFilePath = $baseSubmissionsDir . '/' . $relativeFilePath;
    $targetDirectory = dirname($absoluteFilePath);

    if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true) && !is_dir($targetDirectory)) {
        throw new RuntimeException('Could not create submissions directory.');
    }

    if (file_put_contents($absoluteFilePath, $code) === false) {
        throw new RuntimeException('Could not store submitted code.');
    }
} catch (Throwable $exception) {
    if (!empty($submissionId ?? null)) {
        $submissionsRepository->delete((int) $submissionId);
    }
    submitResponse(false, 'Submission could not be saved. Please try again.');
}

submitResponse(true, 'Submission received. Your solution is queued for judging.');
