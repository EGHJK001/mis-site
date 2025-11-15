<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="zh-Hant">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>資管系學會資訊平台</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="/assets/css/style.css" rel="stylesheet">
  </head>
  <body class="d-flex flex-column min-vh-100">

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="/index.php"><img src="../img/aeust_logo.png" alt="">資管系學會</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="切換導覽">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="/index.php">首頁</a></li>
          <li class="nav-item"><a class="nav-link" href="/activities.php">活動資訊</a></li>
          <li class="nav-item"><a class="nav-link" href="/members.php">學會介紹</a></li>
          <li class="nav-item"><a class="nav-link" href="/downloads.php">文件下載</a></li>
          <li class="nav-item"><a class="nav-link" href="/knowledge.php">知識管理系統</a></li>
          <li class="nav-item"><a class="nav-link" href="/contact.php">聯絡我們</a></li>
        </ul>
        <ul class="navbar-nav">
          <?php if (!empty($_SESSION['user_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="/admin/index.php">後台</a></li>
            <li class="nav-item"><a class="nav-link" href="/admin/logout.php">登出</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/admin/login.php">幹部登入</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  