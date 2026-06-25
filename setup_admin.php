<?php
require_once 'config.php';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL UNIQUE,
        `password` varchar(255) NOT NULL,
        `display_name` varchar(100) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $hash = password_hash('1234', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password, display_name) VALUES (?, ?, ?)");
    $stmt->execute(['김호길', $hash, '김호길']);

    echo "<p style='font-family:sans-serif;padding:20px'>✅ 관리자 계정 생성 완료!<br><br>아이디: 김호길<br>비밀번호: 1234<br><br><b style='color:red'>보안을 위해 이 파일을 즉시 삭제하세요:</b><br><code>rm /var/www/html/mold/setup_admin.php</code></p>";
} catch(Exception $e) {
    echo "오류: " . $e->getMessage();
}
