<?php
require_once __DIR__ . '/../autoloader.php';
session_start();
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';
$currentUser = $_SESSION['user'];
$SubmissionsRepository = new SubmissionsRepository();
$UserRepository = new UserRepository();
$submissions = null;

switch ($type) {
    case 'me':
        if ($currentUser) {
            $submissions = $SubmissionsRepository->findByUserId($currentUser->id);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Not logged in']);
            exit;
        }
        break;
    case 'favourites':
        if ($currentUser) {
            $Users = $UserRepository->findFavouritesById($currentUser->id);
            foreach ($Users as $User) {
                $submissions += $SubmissionsRepository->findByUserId($currentUser->id);
            }
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Not logged in']);
            exit;
        }
        break;
    case 'all':
    default:
        $submissions = $SubmissionsRepository->findAll();
        break;
}

echo json_encode(['submissions' => $submissions]);
