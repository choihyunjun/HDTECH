<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':   getMoldList();  break;
    case 'get':    getMold();      break;
    case 'save':   saveMold();     break;
    case 'delete': deleteMold();   break;
    case 'nextno': getNextNo();    break;
    default: jsonResponse(['error' => '잘못된 요청'], 400);
}

function getMoldList() {
    $db      = getDB();
    $keyword = '%' . ($_GET['keyword'] ?? '') . '%';
    $grade   = $_GET['grade'] ?? '';

    $sql = "SELECT id, mold_no, customer, car_model, dm, product_name, mold_name,
                   made_date, mold_grade, check_count, exchange_count
            FROM molds
            WHERE (mold_no LIKE ? OR customer LIKE ? OR product_name LIKE ? OR mold_name LIKE ?)";
    $params = [$keyword, $keyword, $keyword, $keyword];

    if ($grade !== '') {
        $sql    .= " AND mold_grade = ?";
        $params[] = $grade;
    }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    jsonResponse($stmt->fetchAll());
}

function getMold() {
    $db = getDB();
    $id = intval($_GET['id'] ?? 0);

    $stmt = $db->prepare("SELECT * FROM molds WHERE id = ?");
    $stmt->execute([$id]);
    $mold = $stmt->fetch();
    if (!$mold) jsonResponse(['error' => '금형을 찾을 수 없습니다'], 404);

    $stmt = $db->prepare("SELECT * FROM mold_repairs WHERE mold_id = ? ORDER BY sort_order, repair_date");
    $stmt->execute([$id]);
    $mold['repairs'] = $stmt->fetchAll();

    $stmt = $db->prepare("SELECT * FROM mold_worklogs WHERE mold_id = ? ORDER BY sort_order, work_date");
    $stmt->execute([$id]);
    $mold['worklogs'] = $stmt->fetchAll();

    $stmt = $db->prepare("SELECT * FROM mold_images WHERE mold_id = ? ORDER BY sort_order");
    $stmt->execute([$id]);
    $mold['images'] = $stmt->fetchAll();

    jsonResponse($mold);
}

function saveMold() {
    $db   = getDB();
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) jsonResponse(['error' => '데이터 없음'], 400);
    if (empty($data['mold_no'])) jsonResponse(['error' => '금형번호는 필수입니다'], 400);

    $id = intval($data['id'] ?? 0);

    $fields = [
        'mold_no'         => $data['mold_no'],
        'customer'        => $data['customer']        ?? '',
        'car_model'       => $data['car_model']       ?? '',
        'dm'              => $data['dm']              ?? '',
        'part_no'         => $data['part_no']         ?? '',
        'product_name'    => $data['product_name']    ?? '',
        'mold_name'       => $data['mold_name']       ?? '',
        'mold_size'       => $data['mold_size']       ?? '',
        'main_equipment'  => $data['main_equipment']  ?? '',
        'material'        => $data['material']        ?? '',
        'mold_material'   => $data['mold_material']   ?? '',
        'basis'           => $data['basis']           ?? '',
        'maker'           => $data['maker']           ?? '',
        'made_date'       => $data['made_date']       ?: null,
        'made_cost'       => intval($data['made_cost']       ?? 0),
        'exchange_count'  => intval($data['exchange_count']  ?? 0),
        'last_photo_date' => $data['last_photo_date'] ?: null,
        'expire_date'     => $data['expire_date']     ?: null,
        'mold_grade'      => $data['mold_grade']      ?? '',
        'note'            => $data['note']            ?? '',
    ];

    if ($id === 0) {
        $cols = implode(', ', array_keys($fields));
        $vals = implode(', ', array_fill(0, count($fields), '?'));
        $stmt = $db->prepare("INSERT INTO molds ($cols) VALUES ($vals)");
        $stmt->execute(array_values($fields));
        $id = $db->lastInsertId();
    } else {
        $set  = implode(' = ?, ', array_keys($fields)) . ' = ?';
        $stmt = $db->prepare("UPDATE molds SET $set WHERE id = ?");
        $stmt->execute([...array_values($fields), $id]);
    }

    // 수리이력 전체 교체
    $db->prepare("DELETE FROM mold_repairs WHERE mold_id = ?")->execute([$id]);
    foreach (($data['repairs'] ?? []) as $i => $r) {
        $stmt = $db->prepare("INSERT INTO mold_repairs (mold_id, repair_date, content, manager, cost, sort_order)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $r['repair_date'] ?: null, $r['content'] ?? '', $r['manager'] ?? '', intval($r['cost'] ?? 0), $i]);
    }

    // 작업이력 전체 교체 (누적타수 자동 계산)
    $db->prepare("DELETE FROM mold_worklogs WHERE mold_id = ?")->execute([$id]);
    $total = 0;
    foreach (($data['worklogs'] ?? []) as $i => $w) {
        $cnt   = intval($w['work_count'] ?? 0);
        $total += $cnt;
        $stmt = $db->prepare("INSERT INTO mold_worklogs (mold_id, work_date, equipment, content, note, work_count, total_count, sort_order)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $w['work_date'] ?: null, $w['equipment'] ?? '', $w['content'] ?? '', $w['note'] ?? '', $cnt, $total, $i]);
    }

    // 점검타수(누적) 업데이트
    $db->prepare("UPDATE molds SET check_count = ? WHERE id = ?")->execute([$total, $id]);

    jsonResponse(['success' => true, 'id' => $id]);
}

function deleteMold() {
    $db   = getDB();
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = intval($data['id'] ?? 0);
    if (!$id) jsonResponse(['error' => 'ID 없음'], 400);

    $stmt = $db->prepare("SELECT file_path FROM mold_images WHERE mold_id = ?");
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll() as $img) {
        $path = UPLOAD_DIR . basename($img['file_path']);
        if (file_exists($path)) unlink($path);
    }

    $db->prepare("DELETE FROM molds WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}

function getNextNo() {
    $db     = getDB();
    $prefix = $_GET['prefix'] ?? 'PS-QC';

    $stmt = $db->prepare("SELECT last_no FROM mold_sequence WHERE prefix = ?");
    $stmt->execute([$prefix]);
    $row = $stmt->fetch();

    if (!$row) {
        $db->prepare("INSERT INTO mold_sequence (prefix, last_no) VALUES (?, 0)")->execute([$prefix]);
        $next = 1;
    } else {
        $next = $row['last_no'] + 1;
    }
    $db->prepare("UPDATE mold_sequence SET last_no = ? WHERE prefix = ?")->execute([$next, $prefix]);
    jsonResponse(['mold_no' => $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT)]);
}
