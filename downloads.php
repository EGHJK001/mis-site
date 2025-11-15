<?php
require __DIR__ . "/includes/db.php";
require __DIR__ . "/includes/header.php";

$result = $mysqli->query("SELECT id, title, description, file_path, category FROM downloads ORDER BY created_at DESC");
?>
<div class="container py-4">
  <h1 class="h3 mb-3">文件下載</h1>
  <p class="text-muted">提供常用表單、社團章程及經費核銷相關文件下載。</p>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>名稱</th>
          <th>類別</th>
          <th>說明</th>
          <th>下載</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">下載</a></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" class="text-center text-muted">目前尚無可下載文件。</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>
