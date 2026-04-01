<?php
session_start();
require_once '../autoloader.php';
$hashToStoreInDb = password_hash($_POST['password'], PASSWORD_DEFAULT);
$email = $_POST['email'];
$username = $_POST['username'];
$valid = true;
$UserRepository = new UserRepository();
$Users = $UserRepository->findAllUsers();
foreach ($Users as $User) {
    if ($User->username == $username) {
        $valid = false;
    }
}
$referer = $_SERVER['HTTP_REFERER'];

if ($valid) {
    $_SESSION['user'] = $username;
    $_SESSION['role'] = 'User';
    $_SESSION['loggedIn'] = true;
    $UserRepository->create(["username" => $username, "email" => $email, "password" => $hashToStoreInDb]);
    echo json_encode(['success' => true, 'redirect' => $_SERVER['HTTP_REFERER']]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'This username is unavailable !']);
    exit;
}
