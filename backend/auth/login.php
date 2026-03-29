<?php
session_start();
require_once '../autoloader.php';
$username = $_POST['username'];
$UserRepository = new UserRepository();
$User = $UserRepository->findByUsername($username);
$existingHashFromDb = $User->password;
$referer = $_SERVER['HTTP_REFERER'];
$isPasswordCorrect = password_verify($_POST['password'], $existingHashFromDb);
if ($isPasswordCorrect) {
    $_SESSION['user'] = $username;
    setcookie("logedIn", true, time() + 3600 * 48, "/");
    echo "<script>
        alert('Loged In Sucessfully !');
        window.location.href = '{$referer}';
        </script>";
} else {
    echo "<script>
        alert('Incorrect password !');
        window.location.href = '{$referer}';
        </script>";
    // header("Location: $referer");
}
