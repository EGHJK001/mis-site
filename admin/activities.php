<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    if ($action === "create") {
        $title   = trim($_POST["title"] ?? "");
        $date    = trim($_POST["event_date"] ?? "");
        $loc     = trim($_POST["location"] ?? "");
        $deadline= trim($_POST["signup_deadline"] ?? "");
        $desc    = trim($_POST["description"] ?? "");
        if ($title) {
            $stmt = $mysqli->prepare("INSERT INTO activities (title, event_date, location, signup_deadline, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $title, $date, $loc, $deadline, $desc);
            $stmt->execute();
        }
    } elseif ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id) {
            $stmt = $mysqli->prepare("DELETE FROM activities WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
}

$result = $mysqli->query("SELECT * FROM activities ORDER BY event_date DESC");

require __DIR__ . "/../includes/header.php";
?>
<div class="container py-4">
  <h1 class="h4 mb-3">活動管理模組</h1>

  <form method="post" class="card mb-4">
    <div class="card-header">新增活動</div>
    <div class="card-body row g-3">
      <input type="hidden" name="action" value="create">
      <div class="col-12">
        <label class="form-label">活動名稱</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">活動日期</label>
        <input type="date" name="event_date" class="form-control">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">地點</label>
        <input type="text" name="location" class="form-control">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">報名截止</label>
        <input type="date" name="signup_deadline" class="form-control">
      </div>
      <div class="col-12">
        <label class="form-label">活動說明</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
      </div>
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary">儲存</button>
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>名稱</th><th>日期</th><th>地點</th><th>報名截止</th><th>操作</th>
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
              <form method="post" class="d-inline" onsubmit="return confirm('確定刪除此活動？');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button class="btn btn-sm btn-outline-danger">刪除</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center text-muted">尚無活動。</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
