<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/common.php';

$userRepository = new UserRepository();
$actorUser = requireAuthenticatedUser();
requireAdminUser($actorUser);
$targetUser = resolveTargetUser($userRepository, $actorUser);

if ((int) $actorUser->id === (int) $targetUser->id) {
    jsonResponse(false, 'You cannot delete your own account.');
}

$avatarFileName = trim((string) ($targetUser->avatar_url ?? ''));
$userRepository->delete((int) $targetUser->id);

if ($avatarFileName !== '') {
    $storageDir = getProfileStorageDir();
    $safeName = basename($avatarFileName);
    $avatarPath = $storageDir . '/' . $safeName;
    if (is_file($avatarPath)) {
        @unlink($avatarPath);
    }
}

jsonResponse(true, 'Account deleted successfully.');
