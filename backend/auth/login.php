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
    setcookie("loggedIn", true, time() + 3600 * 48, "/");
    echo json_encode(['success' => true, 'redirect' => $_SERVER['HTTP_REFERER']]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password !']);
    exit;
}
