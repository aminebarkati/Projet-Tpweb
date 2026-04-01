<?php
session_start();
require_once '../autoloader.php';
$username = $_POST['username'];
$UserRepository = new UserRepository();
$User = $UserRepository->findByUsername($username);
if ($User) {
    $existingHashFromDb = $User->password;
    $isPasswordCorrect = password_verify($_POST['password'], $existingHashFromDb);
} else {
    $isPasswordCorrect = false;
}

if ($isPasswordCorrect) {
    $_SESSION['user'] = $username;
    $_SESSION['role'] = !empty($User->is_admin) ? 'Admin' : 'User';
    $_SESSION['loggedIn'] = true;
    echo json_encode(['success' => true, 'redirect' => $_SESSION['HTTP_REFERER']]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password !']);
    exit;
}
