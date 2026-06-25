<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$id   = intval($data['id'] ?? 0);
if (!$id) jsonResponse(['error' => 'ID 없음'], 400);

$db   = getDB();
$stmt = $db->prepare("SELECT file_path FROM mold_images WHERE id = ?");
$stmt->execute([$id]);
$img  = $stmt->fetch();

if ($img) {
    $path = UPLOAD_DIR . basename($img['file_path']);
    if (file_exists($path)) unlink($path);
    $db->prepare("DELETE FROM mold_images WHERE id = ?")->execute([$id]);
}

jsonResponse(['success' => true]);
