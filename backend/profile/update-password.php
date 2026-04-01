<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/common.php';

$UserRepository = new UserRepository();
$actorUser = requireAuthenticatedUser();
$targetUser = resolveTargetUser($UserRepository, $actorUser);
$isAdminEditingOther = isAdminUser($actorUser) && (int) $actorUser->id !== (int) $targetUser->id;

$currentPassword = (string) ($_POST['current_password'] ?? '');
$newPassword = (string) ($_POST['new_password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');
$errors = [];

if (!$isAdminEditingOther && !password_verify($currentPassword, (string) $targetUser->password)) {
    $errors[] = 'Current password is incorrect.';
}
if (strlen($newPassword) < 8) {
    $errors[] = 'New password must contain at least 8 characters.';
}
if (!preg_match('/[A-Z]/', $newPassword)) {
    $errors[] = 'New password must include at least one uppercase letter.';
}
if (!preg_match('/[a-z]/', $newPassword)) {
    $errors[] = 'New password must include at least one lowercase letter.';
}
if (!preg_match('/[0-9]/', $newPassword)) {
    $errors[] = 'New password must include at least one number.';
}
if (!preg_match('/[!@#$%^&*]/', $newPassword)) {
    $errors[] = 'New password must include at least one special character (!@#$%^&*).';
}
if ($newPassword !== $confirmPassword) {
    $errors[] = 'New password and confirmation do not match.';
}

if (!empty($errors)) {
    jsonResponse(false, implode("\n", $errors));
}

$UserRepository->update((int) $targetUser->id, [
    'password' => password_hash($newPassword, PASSWORD_DEFAULT),
]);

jsonResponse(true, 'Password updated successfully.');
