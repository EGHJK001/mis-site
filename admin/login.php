<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $stmt = $mysqli->prepare("SELECT id, username, password_hash, role, name FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["name"] = $user["name"];
        header("Location: /admin/index.php");
        exit;
    } else {
        $error = "帳號或密碼錯誤。";
    }
}
?>
<!doctype html>
<html lang="zh-Hant">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>幹部登入 - 資管系學會</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="bg-light d-flex align-items-center" style="min-height:100vh;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h1 class="h4 text-center mb-3">幹部登入</h1>
              <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
              <?php endif; ?>
              <form method="post" class="row g-3">
                <div class="col-12">
                  <label class="form-label" for="username">帳號</label>
                  <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="col-12">
                  <label class="form-label" for="password">密碼</label>
                  <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="col-12 d-grid">
                  <button type="submit" class="btn btn-primary">登入</button>
                </div>
              </form>
            </div>
          </div>
          <p class="text-center small text-muted mt-3 mb-0">
            回到 <a href="/index.php">前台首頁</a>
          </p>
        </div>
      </div>
    </div>
  </body>
</html>
