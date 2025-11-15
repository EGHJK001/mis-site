<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
require_login();

// 簡單統計
$activityCount = $mysqli->query("SELECT COUNT(*) AS c FROM activities")->fetch_assoc()["c"] ?? 0;
$officerCount  = $mysqli->query("SELECT COUNT(*) AS c FROM officers")->fetch_assoc()["c"] ?? 0;
$downloadCount = $mysqli->query("SELECT COUNT(*) AS c FROM downloads")->fetch_assoc()["c"] ?? 0;
$articleCount  = $mysqli->query("SELECT COUNT(*) AS c FROM knowledge_articles")->fetch_assoc()["c"] ?? 0;
$messageCount  = $mysqli->query("SELECT COUNT(*) AS c FROM messages")->fetch_assoc()["c"] ?? 0;
$claimCount    = $mysqli->query("SELECT COUNT(*) AS c FROM finance_claims")->fetch_assoc()["c"] ?? 0;

require __DIR__ . "/../includes/header.php";
?>
<div class="container py-4">
  <h1 class="h3 mb-3">系學會後台管理</h1>
  <p class="text-muted mb-4">提供活動、幹部、文件下載、經費核銷、知識庫與留言等管理功能。</p>
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-3">
      <a href="activities.php" class="text-decoration-none text-dark">
        <div class="card card-hover">
          <div class="card-body text-center">
            <div class="h4 mb-0"><?php echo $activityCount; ?></div>
            <div class="small text-muted">活動筆數</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="officers.php" class="text-decoration-none text-dark">
        <div class="card card-hover">
          <div class="card-body text-center">
            <div class="h4 mb-0"><?php echo $officerCount; ?></div>
            <div class="small text-muted">幹部人數</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="downloads.php" class="text-decoration-none text-dark">
        <div class="card card-hover">
          <div class="card-body text-center">
            <div class="h4 mb-0"><?php echo $downloadCount; ?></div>
            <div class="small text-muted">下載文件</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="knowledge.php" class="text-decoration-none text-dark">
        <div class="card card-hover">
          <div class="card-body text-center">
            <div class="h4 mb-0"><?php echo $articleCount; ?></div>
            <div class="small text-muted">知識文章</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="messages.php" class="text-decoration-none text-dark">
        <div class="card card-hover">
          <div class="card-body text-center">
            <div class="h4 mb-0"><?php echo $messageCount; ?></div>
            <div class="small text-muted">留言數</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="finance.php" class="text-decoration-none text-dark">
        <div class="card card-hover">
          <div class="card-body text-center">
            <div class="h4 mb-0"><?php echo $claimCount; ?></div>
            <div class="small text-muted">經費核銷</div>
          </div>
        </div>
      </a>
    </div>
  </div>

  <h2 class="h5 mb-3">管理選單</h2>
  <div class="list-group">
    <a href="activities.php" class="list-group-item list-group-item-action">活動管理模組</a>
    <a href="officers.php" class="list-group-item list-group-item-action">系學會成員管理模組</a>
    <a href="downloads.php" class="list-group-item list-group-item-action">文件下載管理模組</a>
    <a href="finance.php" class="list-group-item list-group-item-action">經費核銷管理模組</a>
    <a href="knowledge.php" class="list-group-item list-group-item-action">知識庫管理模組</a>
    <a href="messages.php" class="list-group-item list-group-item-action">留言與回覆管理模組</a>
    <a href="account.php" class="list-group-item list-group-item-action">帳號與安全設定模組</a>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
