<?php
require __DIR__ . "/includes/db.php";
require __DIR__ . "/includes/header.php";

$name = $email = $subject = $message = "";
$sent = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST["name"] ?? "");
    $email   = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name && $email && $message) {
        $stmt = $mysqli->prepare("INSERT INTO messages (name, email, subject, message, created_at, is_approved) VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $sent = true;
            $name = $email = $subject = $message = "";
        }
    }
}
?>
<div class="container py-4">
  <h1 class="h3 mb-3">聯絡我們</h1>
  <div class="row g-4">
    <div class="col-12 col-lg-6">
      <p class="text-muted">
        若對系學會活動、經費或其他事項有任何問題，歡迎透過下列表單留言，我們會儘快回覆。
      </p>
      <?php if ($sent): ?>
        <div class="alert alert-success">已成功送出留言，待幹部審核後將進行回覆。</div>
      <?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-12">
          <label class="form-label" for="name">姓名</label>
          <input type="text" class="form-control" name="name" id="name" required value="<?php echo htmlspecialchars($name); ?>">
        </div>
        <div class="col-12">
          <label class="form-label" for="email">Email</label>
          <input type="email" class="form-control" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>">
        </div>
        <div class="col-12">
          <label class="form-label" for="subject">主旨</label>
          <input type="text" class="form-control" name="subject" id="subject" value="<?php echo htmlspecialchars($subject); ?>">
        </div>
        <div class="col-12">
          <label class="form-label" for="message">留言內容</label>
          <textarea class="form-control" name="message" id="message" rows="4" required><?php echo htmlspecialchars($message); ?></textarea>
        </div>
        <div class="col-12 text-end">
          <button type="submit" class="btn btn-primary">送出</button>
        </div>
      </form>
    </div>
    <div class="col-12 col-lg-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">學會辦公室資訊</h5>
          <p class="card-text mb-1">地點：亞東科技大學 有庠大樓13樓11310辦公室</p>
          <p class="card-text mb-1">服務時間：平日中午 12:00 - 13:00（依實際公告為主）</p>
          <p class="card-text mb-0">Email：mis.sa@example.edu.tw</p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>
