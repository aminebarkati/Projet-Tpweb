<?php
session_start();
require_once __DIR__ . '/../../backend/autoloader.php';
$UserRepository = new UserRepository();
$isLoggedIn = !empty($_SESSION['loggedIn']) && !empty($_SESSION['user']);
$currentUser = null;
if (!isset($_SESSION["target"]) || $_SESSION["target"] == "") {
    $_SESSION["target"] = $_SESSION["user"];
}

if ($isLoggedIn) {
    $currentUser = $UserRepository->findByUsername($_SESSION['target']);
    if (!$currentUser) {
        $_SESSION['auto_logout_missing_user'] = true;
        unset($_SESSION['loggedIn'], $_SESSION['user'], $_SESSION['role']);
        $isLoggedIn = false;
    }
}

$showAutoLogoutAlert = !empty($_SESSION['auto_logout_missing_user']);
if ($showAutoLogoutAlert) {
    unset($_SESSION['auto_logout_missing_user']);
}
unset($_SESSION["target"]);
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AlgoSpark</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link
        rel="icon"
        type="image/svg+xml"
        href="/public/assets/media/svg-components/code-slash.svg" />
    <link
        rel="stylesheet"
        href="/public/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/public/assets/css/style.css" />
    <?php if ($showAutoLogoutAlert): ?>
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                window.alert('You have been logged out because your account was not found. Please log in again or report to support team.');
            });
        </script>
    <?php endif; ?>

</head>