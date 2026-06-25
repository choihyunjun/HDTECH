<?php
require_once __DIR__ . '/auth.php';
// $pageId: 현재 페이지 식별자 ('list' | 'card')
// $pageTitle: 페이지 타이틀
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? '해득테크') ?> — 해득테크</title>
<link rel="stylesheet" href="/mold/assets/css/style.css">
</head>
<body>

<header class="app-header">
    <a class="logo-wrap" href="/mold/index.php">
        <img src="/mold/assets/img/logo.png" alt="해득테크" class="header-logo">
    </a>
    <div class="header-divider"></div>
    <span class="header-app-name">금형이력카드 관리시스템</span>
    <div style="flex:1"></div>
    <?php if (!empty($headerActions)): ?>
    <div class="header-actions"><?= $headerActions ?></div>
    <?php endif; ?>
</header>

<div class="app-layout">

    <aside class="sidebar">
        <nav class="sidebar-nav">
            <a href="/mold/index.php" class="nav-item <?= ($pageId??'') === 'list' ? 'active' : '' ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 8a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm6-6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>금형 목록</span>
            </a>
        </nav>
        <div class="sidebar-user">
            <span class="sidebar-username"><?= htmlspecialchars($_SESSION['display_name'] ?? '') ?></span>
            <a href="/mold/logout.php" class="logout-btn">로그아웃</a>
        </div>
        <div class="sidebar-footer">HAEDEUK TECH</div>
    </aside>

    <main class="main-wrap">
