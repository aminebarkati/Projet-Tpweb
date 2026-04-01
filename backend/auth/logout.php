<?php
session_start();
require_once '../autoloader.php';
session_destroy();
$referer = $_SERVER['HTTP_REFERER'];
header("location:{$referer}");
