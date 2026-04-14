<?php
session_start();
require_once '../autoloader.php';
require_once __DIR__ . '/common.php';
header('Content-Type: application/json');
$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$FavoritrRepository = new FavoriteRepository();
try {
    $FavoritrRepository->deleteByUserId(
        $payload['user_id'],
        $payload['favorite_user_id']
    );
    echo json_encode(['success' => true]);
    exit;
} catch (\Throwable $th) {
    echo json_encode(['success' => false]);
}
