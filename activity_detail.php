<?php
require __DIR__ . "/includes/db.php";
require __DIR__ . "/includes/header.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $mysqli->prepare("SELECT * FROM activities WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();
?>
<div class="container py-4">
<?php if ($activity): ?>
  <h1 class="h3 mb-3"><?php echo htmlspecialchars($activity['title']); ?></h1>
  <p class="text-muted">
    日期：<?php echo htmlspecialchars($activity['event_date']); ?><br>
    地點：<?php echo htmlspecialchars($activity['location']); ?><br>
    報名截止：<?php echo htmlspecialchars($activity['signup_deadline']); ?>
  </p>
  <hr>
  <p><?php echo nl2br(htmlspecialchars($activity['description'])); ?></p>
  <div class="mt-4">
    <a href="activities.php" class="btn btn-secondary">回活動列表</a>
  </div>
<?php else: ?>
  <div class="alert alert-warning">找不到此活動。</div>
  <a href="activities.php" class="btn btn-secondary">回活動列表</a>
<?php endif; ?>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>
