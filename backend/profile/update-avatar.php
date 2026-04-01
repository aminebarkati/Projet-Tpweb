<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/common.php';

$UserRepository = new UserRepository();
$actorUser = requireAuthenticatedUser();
$targetUser = resolveTargetUser($UserRepository, $actorUser);

if (empty($_FILES['avatar_file']) || (int) $_FILES['avatar_file']['error'] === UPLOAD_ERR_NO_FILE) {
    jsonResponse(false, 'Please choose an image to upload.');
}

if ((int) $_FILES['avatar_file']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(false, 'Avatar upload failed. Please try again.');
}

$maxSize = 3 * 1024 * 1024;
$tmpPath = (string) $_FILES['avatar_file']['tmp_name'];
$originalName = (string) $_FILES['avatar_file']['name'];
$fileSize = (int) $_FILES['avatar_file']['size'];
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$mimeType = mime_content_type($tmpPath) ?: '';
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if ($fileSize <= 0 || $fileSize > $maxSize) {
    jsonResponse(false, 'Avatar size must be between 1 byte and 3 MB.');
}

if (!in_array($extension, $allowedExtensions, true)) {
    jsonResponse(false, 'Avatar extension must be jpg, jpeg, png, gif, or webp.');
}

if (!in_array($mimeType, $allowedMimeTypes, true)) {
    jsonResponse(false, 'Uploaded file is not a valid image.');
}

$storageDir = getProfileStorageDir();
if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true) && !is_dir($storageDir)) {
    jsonResponse(false, 'Could not create avatar storage directory.');
}

$avatarFileName = 'avatar_' . (int) $targetUser->id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
$destinationPath = $storageDir . '/' . $avatarFileName;
if (!move_uploaded_file($tmpPath, $destinationPath)) {
    jsonResponse(false, 'Failed to save uploaded avatar image.');
}

$oldAvatarFileName = trim((string) ($targetUser->avatar_url ?? ''));

$UserRepository->update((int) $targetUser->id, [
    'avatar_url' => $avatarFileName,
]);

if ($oldAvatarFileName !== '' && $oldAvatarFileName !== $avatarFileName) {
    $safeOldName = basename($oldAvatarFileName);
    $oldAvatarPath = $storageDir . '/' . $safeOldName;
    if (is_file($oldAvatarPath)) {
        @unlink($oldAvatarPath);
    }
}

jsonResponse(true, 'Avatar updated successfully.', [
    'avatar_url' => $avatarFileName,
    'avatar_src' => '/storage/imgs/' . rawurlencode($avatarFileName),
]);
