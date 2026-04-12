<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/common.php';

$UserRepository = new UserRepository();
$actorUser = requireAuthenticatedUser();
$targetUser = resolveTargetUser($UserRepository, $actorUser);

$username = trim((string) ($_POST['username'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$bio = trim((string) ($_POST['bio'] ?? ''));
$errors = [];

if ($username === '' || strlen($username) > 30) {
    $errors[] = 'Username is required and must be at most 30 characters.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    $errors[] = 'Email must be valid and at most 150 characters.';
}

$existingByUsername = $UserRepository->findByUsername($username);
if ($existingByUsername && (int) $existingByUsername->id !== (int) $targetUser->id) {
    $errors[] = 'This username is already taken.';
}


$existingByEmail = $UserRepository->findByEmail($email);
if ($existingByEmail && (int) $existingByEmail->id !== (int) $targetUser->id) {
    $errors[] = 'This email is already used by another account.';
}

if (!empty($errors)) {
    jsonResponse(false, implode("\n", $errors));
}

$UserRepository->update((int) $targetUser->id, [
    'username' => $username,
    'email' => $email,
    'bio' => $bio !== '' ? $bio : null,
]);

$updatedUser = $UserRepository->findByUsername($username);

if ((int) $actorUser->id === (int) $targetUser->id) {
    $_SESSION['user'] = $updatedUser;
}


jsonResponse(true, 'Profile updated successfully.', [
    'user' => [
        'username' => (string) $updatedUser->username,
        'email' => (string) $updatedUser->email,
        'bio' => (string) ($updatedUser->bio ?? ''),
        'avatar_url' => (string) ($updatedUser->avatar_url ?? ''),
        'rating' => (int) $updatedUser->rating,
        'is_admin' => !empty($updatedUser->is_admin),
        'created_at' => (string) $updatedUser->created_at,
        'updated_at' => (string) $updatedUser->updated_at,
    ],
]);
