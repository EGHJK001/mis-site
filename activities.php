<?php
require __DIR__ . "/includes/db.php";
require __DIR__ . "/includes/header.php";

$result = $mysqli->query("SELECT id, title, event_date, location, signup_deadline FROM activities ORDER BY event_date DESC");
?>
<div class="container py-4">
  <h1 class="h3 mb-3">活動資訊</h1>
  <p class="text-muted">瀏覽系學會舉辦之各項活動，包含迎新、聯誼、講座等。</p>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>活動名稱</th>
          <th>日期</th>
          <th>地點</th>
          <th>報名截止</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['event_date']); ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td><?php echo htmlspecialchars($row['signup_deadline']); ?></td>
            <td>
              <a href="activity_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">查看</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center text-muted">目前尚無活動資料。</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>
