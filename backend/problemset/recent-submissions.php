<?php

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../autoloader.php';

function recentResponse(bool $success, string $message, array $extra = []): void
{
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    recentResponse(false, 'Method not allowed.');
}

if (empty($_SESSION['user'])) {
    recentResponse(false, 'Unauthorized. Please log in.');
}

$problemId = isset($_GET['problem_id']) ? (int) $_GET['problem_id'] : 0;
if ($problemId <= 0) {
    recentResponse(false, 'Invalid problem id.');
}

$userRepository = new UserRepository();
$submissionsRepository = new SubmissionsRepository();
$problemRepository = new ProblemsRepository();

$currentUser = $_SESSION['user'];
if (!$currentUser) {
    recentResponse(false, 'User not found.');
}

$problem = $problemRepository->findById($problemId);
if (!$problem) {
    recentResponse(false, 'Problem not found.');
}

$rows = $submissionsRepository->findRecentByUserAndProblem((int) $currentUser->id, $problemId, 5);
recentResponse(true, 'Recent submissions loaded.', ['items' => $rows]);
