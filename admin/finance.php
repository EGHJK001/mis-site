<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
require_login();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    if ($action === "create") {
        $name   = trim($_POST["applicant_name"] ?? "");
        $item   = trim($_POST["item"] ?? "");
        $amount = floatval($_POST["amount"] ?? 0);
        $date   = trim($_POST["receipt_date"] ?? "");
        $status = trim($_POST["status"] ?? "審核中");
        $remark = trim($_POST["remark"] ?? "");
        if ($name && $item) {
            $stmt = $mysqli->prepare("INSERT INTO finance_claims (applicant_name, item, amount, receipt_date, status, remark, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssdsss", $name, $item, $amount, $date, $status, $remark);
            $stmt->execute();
        }
    } elseif ($action === "update_status") {
        $id     = (int)($_POST["id"] ?? 0);
        $status = trim($_POST["status"] ?? "");
        if ($id && $status) {
            $stmt = $mysqli->prepare("UPDATE finance_claims SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            $stmt->execute();
        }
    }
}

$result = $mysqli->query("SELECT * FROM finance_claims ORDER BY created_at DESC");

require __DIR__ . "/../includes/header.php";
?>
<div class="container py-4">
  <h1 class="h4 mb-3">經費核銷管理模組</h1>
  <p class="text-muted">登錄各活動支出與報帳狀態，作為對帳與查詢依據。</p>

  <form method="post" class="card mb-4">
    <div class="card-header">新增核銷紀錄</div>
    <div class="card-body row g-3">
      <input type="hidden" name="action" value="create">
      <div class="col-12 col-md-6">
        <label class="form-label">申請人 / 活動名稱</label>
        <input type="text" name="applicant_name" class="form-control" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">支出項目</label>
        <input type="text" name="item" class="form-control" required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">金額</label>
        <input type="number" step="0.01" name="amount" class="form-control" required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">發票日期</label>
        <input type="date" name="receipt_date" class="form-control">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">狀態</label>
        <select name="status" class="form-select">
          <option value="審核中">審核中</option>
          <option value="已核銷">已核銷</option>
          <option value="退回補件">退回補件</option>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">備註</label>
        <textarea name="remark" class="form-control" rows="2"></textarea>
      </div>
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary">儲存</button>
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr><th>申請人 / 活動</th><th>項目</th><th>金額</th><th>發票日期</th><th>狀態</th><th>備註</th><th>操作</th></tr></thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
            <td><?php echo htmlspecialchars($row['item']); ?></td>
            <td><?php echo htmlspecialchars($row['amount']); ?></td>
            <td><?php echo htmlspecialchars($row['receipt_date']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td class="small"><?php echo nl2br(htmlspecialchars($row['remark'])); ?></td>
            <td>
              <form method="post" class="d-flex gap-1">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <select name="status" class="form-select form-select-sm">
                  <option value="審核中" <?php if($row['status']==='審核中') echo 'selected'; ?>>審核中</option>
                  <option value="已核銷" <?php if($row['status']==='已核銷') echo 'selected'; ?>>已核銷</option>
                  <option value="退回補件" <?php if($row['status']==='退回補件') echo 'selected'; ?>>退回補件</option>
                </select>
                <button class="btn btn-sm btn-outline-primary">更新</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-center text-muted">尚無核銷紀錄。</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
