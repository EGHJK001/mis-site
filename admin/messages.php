<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    if ($action === "approve") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id) {
            $stmt = $mysqli->prepare("UPDATE messages SET is_approved = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    } elseif ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id) {
            $stmt = $mysqli->prepare("DELETE FROM messages WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
}

$result = $mysqli->query("SELECT * FROM messages ORDER BY created_at DESC");

require __DIR__ . "/../includes/header.php";
?>
<div class="container py-4">
  <h1 class="h4 mb-3">留言與回覆管理模組</h1>
  <p class="text-muted">集中處理前台「聯絡我們」表單送出的留言，可進行審核與刪除。</p>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr><th>時間</th><th>姓名</th><th>Email</th><th>主旨</th><th>內容</th><th>審核狀態</th><th>操作</th></tr></thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="small"><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td class="small"><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td class="small"><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
            <td><?php echo $row['is_approved'] ? '已審核' : '未審核'; ?></td>
            <td>
              <form method="post" class="d-inline">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <?php if (!$row['is_approved']): ?>
                  <input type="hidden" name="action" value="approve">
                  <button class="btn btn-sm btn-outline-success mb-1">通過</button>
                <?php endif; ?>
              </form>
              <form method="post" class="d-inline" onsubmit="return confirm('確定刪除此留言？');">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="action" value="delete">
                <button class="btn btn-sm btn-outline-danger">刪除</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-center text-muted">尚無留言。</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
