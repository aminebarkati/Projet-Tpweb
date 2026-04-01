<?php

require_once __DIR__ . '/../autoloader.php';

function jsonResponse(bool $success, string $message, array $extra = []): void
{
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $extra));
    exit;
}

function requireAuthenticatedUser(): object
{
    if (empty($_SESSION['loggedIn']) || empty($_SESSION['user'])) {
        jsonResponse(false, 'Unauthorized. Please log in.');
    }

    $userRepository = new UserRepository();
    $currentUser = $userRepository->findByUsername($_SESSION['user']);
    if (!$currentUser) {
        jsonResponse(false, 'User not found.');
    }

    return $currentUser;
}

function isAdminUser(object $user): bool
{
    return !empty($user->is_admin);
}

function requireAdminUser(object $user): void
{
    if (!isAdminUser($user)) {
        jsonResponse(false, 'Forbidden. Admin privileges are required.');
    }
}

function resolveTargetUser(UserRepository $userRepository, object $actorUser): object
{
    $targetUserId = isset($_POST['target_user_id']) ? (int) $_POST['target_user_id'] : (int) $actorUser->id;
    if ($targetUserId <= 0) {
        jsonResponse(false, 'Invalid target user.');
    }

    if (!isAdminUser($actorUser) && $targetUserId !== (int) $actorUser->id) {
        jsonResponse(false, 'Forbidden. You cannot update another user.');
    }

    $targetUser = $userRepository->findById($targetUserId);
    if (!$targetUser) {
        jsonResponse(false, 'Target user not found.');
    }

    return $targetUser;
}

function getProfileStorageDir(): string
{
    return __DIR__ . '/../../storage/imgs';
}
