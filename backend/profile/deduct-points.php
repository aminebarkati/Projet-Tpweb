<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/common.php';

$userRepository = new UserRepository();
$actorUser = requireAuthenticatedUser();
requireAdminUser($actorUser);
$targetUser = resolveTargetUser($userRepository, $actorUser);

$points = isset($_POST['points']) ? (int) $_POST['points'] : 0;
if ($points <= 0) {
    jsonResponse(false, 'Points to deduct must be greater than 0.');
}

$userRepository->deductPointsById((int) $targetUser->id, -$points);

$updatedUser = $userRepository->findById((int) $targetUser->id);
jsonResponse(true, 'Points deducted successfully.', [
    'user' => [
        'id' => (int) $updatedUser->id,
        'rating' => (int) $updatedUser->rating,
    ],
]);
