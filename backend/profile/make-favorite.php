<?php
session_start();
require_once '../autoloader.php';
require_once __DIR__ . '/common.php';
header('Content-Type: application/json');
$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$FavoritrRepository = new FavoriteRepository();
try {
    $FavoritrRepository->create([
        'user_id' => (int) $payload['user_id'],
        'favorite_user_id' => (int) $payload['favorite_user_id']
    ]);
    echo json_encode(['success' => true,]);
    exit;
} catch (\Throwable $th) {
    echo json_encode(['success' => false]);
}
