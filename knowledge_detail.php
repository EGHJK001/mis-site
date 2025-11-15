<?php
require __DIR__ . "/includes/db.php";
require __DIR__ . "/includes/header.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $mysqli->prepare("SELECT * FROM knowledge_articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();
?>
<div class="container py-4">
<?php if ($article): ?>
  <h1 class="h4 text-muted mb-1">知識管理系統 - 文章內容</h1>
  <h1 class="h3 mb-2"><?php echo htmlspecialchars($article['title']); ?></h1>

  <div class="text-muted small mb-3">
    類別：<?php echo htmlspecialchars($article['category']); ?>
    <?php if ($article['author_name']): ?>｜作者：<?php echo htmlspecialchars($article['author_name']); ?><?php endif; ?>
    ｜發佈：<?php echo htmlspecialchars($article['created_at']); ?>
  </div>

  <?php if (!empty($article['attachment_path'])): ?>
    <div class="alert alert-secondary d-flex justify-content-between align-items-center">
      <div class="small mb-0">
        此文章包含附件檔案，可供下載參考。
      </div>
      <a href="<?php echo htmlspecialchars($article['attachment_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
        檢視 / 下載附件
      </a>
    </div>
  <?php endif; ?>

  <hr>
  <div><?php echo nl2br(htmlspecialchars($article['content'])); ?></div>
  <div class="mt-4">
    <a href="knowledge.php" class="btn btn-secondary">回列表</a>
  </div>
<?php else: ?>
  <div class="alert alert-warning">找不到此文章。</div>
  <a href="knowledge.php" class="btn btn-secondary">回列表</a>
<?php endif; ?>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>
