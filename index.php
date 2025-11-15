<?php
require __DIR__ . "/includes/db.php";
require __DIR__ . "/includes/header.php";

// 最新公告 (用 knowledge_articles 的 category = '公告')
$annStmt = $mysqli->prepare("SELECT id, title, created_at FROM knowledge_articles WHERE category = '公告' ORDER BY created_at DESC LIMIT 5");
$annStmt->execute();
$announcements = $annStmt->get_result();

// 活動快訊 (最近 3 筆活動)
$actStmt = $mysqli->prepare("SELECT id, title, event_date, location FROM activities ORDER BY event_date ASC LIMIT 3");
$actStmt->execute();
$activities = $actStmt->get_result();
?>
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-12 col-lg-7">
        <h1 class="mb-3">歡迎來到資管系學會資訊平台</h1>
        <p class="lead mb-4">
          提供最新公告、活動資訊、系學會幹部介紹與文件下載，讓同學快速掌握系學會動態。
        </p>
        <div class="d-flex flex-wrap gap-2">
          <a href="activities.php" class="btn btn-light btn-lg">查看活動資訊</a>
          <a href="knowledge.php" class="btn btn-outline-light btn-lg">閱讀經驗分享</a>
        </div>
      </div>
      <div class="col-12 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-header bg-white border-0">
            <h5 class="mb-0"><i class="bi bi-megaphone"></i> 最新公告</h5>
          </div>
          <div class="card-body">
            <?php if ($announcements && $announcements->num_rows > 0): ?>
              <ul class="list-group list-group-flush">
                <?php while ($row = $announcements->fetch_assoc()): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="knowledge_detail.php?id=<?php echo $row['id']; ?>">
                      <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                    <span class="badge bg-secondary rounded-pill">
                      <?php echo substr($row['created_at'], 0, 10); ?>
                    </span>
                  </li>
                <?php endwhile; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted mb-0">目前尚無公告。</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <div class="row g-4">
      <div class="col-12 col-lg-8">
        <h2 class="h4 mb-3">活動快訊</h2>
        <div class="row g-3">
          <?php if ($activities && $activities->num_rows > 0): ?>
            <?php while ($row = $activities->fetch_assoc()): ?>
              <div class="col-12 col-md-6">
                <div class="card card-hover h-100">
                  <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text mb-1">
                      日期：<?php echo htmlspecialchars($row['event_date']); ?><br>
                      地點：<?php echo htmlspecialchars($row['location']); ?>
                    </p>
                    <a href="activity_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-primary mt-auto">查看詳情</a>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="col-12">
              <div class="alert alert-info mb-0">目前尚無活動資訊。</div>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <h2 class="h4 mb-3">快速連結</h2>
        <div class="list-group">
          <a class="list-group-item list-group-item-action" href="downloads.php">表單與章程下載</a>
          <a class="list-group-item list-group-item-action" href="members.php">系學會幹部名單</a>
          <a class="list-group-item list-group-item-action" href="knowledge.php">實習與課業經驗分享</a>
          <a class="list-group-item list-group-item-action" href="contact.php">聯絡學會與留言</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
