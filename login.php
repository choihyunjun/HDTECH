<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /mold/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $pdo = new PDO(
            "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['display_name'] = $user['display_name'];
            header('Location: /mold/index.php');
            exit;
        } else {
            $error = '아이디 또는 비밀번호가 올바르지 않습니다.';
        }
    } catch(Exception $e) {
        $error = '로그인 중 오류가 발생했습니다.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>로그인 — 해득테크</title>
<link rel="stylesheet" href="/mold/assets/css/style.css">
</head>
<body class="login-page">

<div class="login-wrap">
    <div class="login-card">

        <div class="login-logo">
            <img src="/mold/assets/img/logo_dark.png" alt="해득테크">
        </div>

        <div class="login-welcome">
            해득테크 금형 관리 시스템에<br>오신것을 환영합니다.
        </div>

        <?php if ($error): ?>
        <div class="login-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="login-field">
                <label>아이디</label>
                <input type="text" name="username" placeholder="아이디를 입력하세요"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autofocus>
            </div>
            <div class="login-field">
                <label>비밀번호</label>
                <input type="password" name="password" placeholder="비밀번호를 입력하세요">
            </div>
            <button type="submit" class="login-btn">로그인</button>
        </form>

        <div class="login-footer">© 2026 HAEDEUK TECH. All rights reserved.</div>
    </div>
</div>

</body>
</html>
