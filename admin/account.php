<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
require_login();

$userId = $_SESSION["user_id"];
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $pwd1  = trim($_POST["password"] ?? "");
    $pwd2  = trim($_POST["password_confirm"] ?? "");

    if ($name && $email) {
        $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $userId);
        $stmt->execute();
        $success = "資料已更新。";
    }

    if ($pwd1 || $pwd2) {
        if ($pwd1 === $pwd2 && strlen($pwd1) >= 6) {
            $hash = password_hash($pwd1, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $userId);
            $stmt->execute();
            $success .= " 密碼已更新。";
        } else {
            $error = "密碼不一致或長度不足（至少 6 碼）。";
        }
    }
}

$stmt = $mysqli->prepare("SELECT username, name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

require __DIR__ . "/../includes/header.php";
?>
<div class="container py-4">
  <h1 class="h4 mb-3">帳號與安全設定模組</h1>

  <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="post" class="row g-3">
    <div class="col-12 col-md-6">
      <label class="form-label">登入帳號（不可修改）</label>
      <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">姓名</label>
      <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($user['name']); ?>">
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($user['email']); ?>">
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">新密碼（選填）</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">確認新密碼</label>
      <input type="password" name="password_confirm" class="form-control">
    </div>
    <div class="col-12 text-end">
      <button class="btn btn-primary">儲存變更</button>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
