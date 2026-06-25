<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

$mold_id = intval($_POST['mold_id'] ?? 0);
if (!$mold_id)              jsonResponse(['error' => '금형 ID 필요'], 400);
if (empty($_FILES['file'])) jsonResponse(['error' => '파일 없음'], 400);

$file    = $_FILES['file'];
$maxSize = 10 * 1024 * 1024;
$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed)) jsonResponse(['error' => '이미지 파일만 가능합니다'], 400);
if ($file['size'] > $maxSize)   jsonResponse(['error' => '파일 크기 초과 (최대 10MB)'], 400);

$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = uniqid('mold_', true) . '.' . $ext;

if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
    jsonResponse(['error' => '파일 저장 실패'], 500);
}

$db   = getDB();
$stmt = $db->prepare("INSERT INTO mold_images (mold_id, file_path, original_name) VALUES (?, ?, ?)");
$stmt->execute([$mold_id, $filename, $file['name']]);

jsonResponse([
    'success'       => true,
    'id'            => $db->lastInsertId(),
    'file_path'     => $filename,
    'url'           => UPLOAD_URL . $filename,
    'original_name' => $file['name']
]);
