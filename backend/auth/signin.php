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
    $UserRepository->create(["username" => $username, "email" => $email, "password" => $hashToStoreInDb]);
    echo "<script>
        alert('Signed In Sucessfully !');
        window.location.href = '{$referer}';
        </script>";
} else {
    echo "<script>
        alert('Sorry, this username is unavailable !');
        window.location.href = '{$referer}';
        </script>";
    // header("Location: $referer");
}
