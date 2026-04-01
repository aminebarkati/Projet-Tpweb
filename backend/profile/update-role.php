<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/common.php';

$userRepository = new UserRepository();
$actorUser = requireAuthenticatedUser();
requireAdminUser($actorUser);
$targetUser = resolveTargetUser($userRepository, $actorUser);

$isAdminInput = $_POST['is_admin'] ?? null;
if ($isAdminInput === null) {
    jsonResponse(false, 'Role value is required.');
}

$nextIsAdmin = in_array((string) $isAdminInput, ['1', 'true', 'on'], true) ? 1 : 0;

if ((int) $actorUser->id === (int) $targetUser->id && $nextIsAdmin === 0) {
    jsonResponse(false, 'You cannot remove your own admin role.');
}

$userRepository->update((int) $targetUser->id, [
    'is_admin' => $nextIsAdmin,
]);

$updatedUser = $userRepository->findById((int) $targetUser->id);
jsonResponse(true, 'Role updated successfully.', [
    'user' => [
        'id' => (int) $updatedUser->id,
        'is_admin' => !empty($updatedUser->is_admin),
    ],
]);
