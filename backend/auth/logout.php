<?php
session_start();
require_once '../autoloader.php';
$_SESSION['username'] = "";
$referer = $_SERVER['HTTP_REFERER'];
setcookie("logedIn", true, time() - 3600 * 48, "/");
header("location:{$referer}");
