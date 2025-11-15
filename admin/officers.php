<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
require_login();

// 確保上傳目錄存在（用來存放幹部照片）
$photoDir = __DIR__ . "/../uploads/officers/";
if (!is_dir($photoDir)) {
    @mkdir($photoDir, 0777, true);
}

$alert = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    if ($action === "create") {
        $name       = trim($_POST["name"] ?? "");
        $pos        = trim($_POST["position"] ?? "");
        $generation = trim($_POST["generation"] ?? ""); // 第幾屆 / 學年度
        $email      = trim($_POST["email"] ?? "");
        $phone      = trim($_POST["phone"] ?? "");
        $intro      = trim($_POST["intro"] ?? "");
        $order      = (int)($_POST["display_order"] ?? 0);
        $photoPath  = "";

        // 若有上傳照片就處理檔案
        if (!empty($_FILES["photo"]["name"])) {
            $file = $_FILES["photo"];
            $maxSize = 5 * 1024 * 1024; // 5MB
            $allowedExt = ["jpg","jpeg","png","gif"];

            if ($file["error"] === UPLOAD_ERR_OK) {
                if ($file["size"] <= $maxSize) {
                    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                    if (in_array($ext, $allowedExt)) {
                        $safeName = preg_replace("/[^A-Za-z0-9_\.\-]+/", "_", basename($file["name"]));
                        $newName  = date("Ymd_His") . "_" . uniqid() . "." . $ext;
                        $target   = $photoDir . $newName;

                        if (move_uploaded_file($file["tmp_name"], $target)) {
                            // 存相對路徑, 讓前台可以直接存取
                            $photoPath = "/uploads/officers/" . $newName;
                        } else {
                            $alert = "照片搬移失敗，請確認 uploads/officers 目錄權限。";
                        }
                    } else {
                        $alert = "不允許的照片格式，只接受：" . implode(", ", $allowedExt);
                    }
                } else {
                    $alert = "照片大小超過限制（最大 5MB）。";
                }
            } else {
                $alert = "照片上傳發生錯誤，錯誤代碼：" . $file["error"];
            }
        }

        if ($name && $pos && !$alert) {
            $stmt = $mysqli->prepare("INSERT INTO officers (name, position, generation, email, phone, intro, photo_path, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssi", $name, $pos, $generation, $email, $phone, $intro, $photoPath, $order);
            if ($stmt->execute()) {
                $alert = "已新增幹部資料。";
            } else {
                $alert = "寫入資料庫失敗：" . $mysqli->error;
            }
        } elseif (!$alert) {
            $alert = "請至少填寫姓名與職稱。";
        }
    } elseif ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id) {
            // 刪除前試著把照片檔也刪掉
            $stmt = $mysqli->prepare("SELECT photo_path FROM officers WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if ($row && !empty($row["photo_path"])) {
                if (strpos($row["photo_path"], "/uploads/officers/") === 0) {
                    $full = __DIR__ . "/../" . ltrim($row["photo_path"], "/");
                    if (is_file($full)) {
                        @unlink($full);
                    }
                }
            }

            $stmt = $mysqli->prepare("DELETE FROM officers WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $alert = "已刪除幹部資料。";
            } else {
                $alert = "刪除失敗：" . $mysqli->error;
            }
        }
    }
}

$result = $mysqli->query("SELECT * FROM officers ORDER BY display_order ASC");

require __DIR__ . "/../includes/header.php";
?>
<div class="container py-4">
  <h1 class="h4 mb-3">系學會成員管理模組</h1>

  <?php if ($alert): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($alert); ?></div>
  <?php endif; ?>

  <form method="post" class="card mb-4" enctype="multipart/form-data">
    <div class="card-header">新增幹部</div>
    <div class="card-body row g-3">
      <input type="hidden" name="action" value="create">
      <div class="col-12 col-md-6">
        <label class="form-label">姓名</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">職稱</label>
        <input type="text" name="position" class="form-control" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">第幾屆 / 學年度</label>
        <input type="text" name="generation" class="form-control" placeholder="例如：第 25 屆、112 學年度">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">電話</label>
        <input type="text" name="phone" class="form-control">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">照片（頭像）</label>
        <input type="file" name="photo" class="form-control">
        <div class="form-text">建議上傳正方形 JPG/PNG，大小 5MB 以內。</div>
      </div>
      <div class="col-12">
        <label class="form-label">簡短介紹 / 自我介紹</label>
        <textarea name="intro" class="form-control" rows="3" placeholder="例如：負責活動企劃與對外聯絡，喜歡程式設計與籃球。"></textarea>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">排序</label>
        <input type="number" name="display_order" class="form-control" value="0">
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
          <th>照片</th>
          <th>姓名</th>
          <th>職稱</th>
          <th>屆別</th>
          <th>Email</th>
          <th>電話</th>
          <th>排序</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td style="width:80px;">
              <?php if (!empty($row['photo_path'])): ?>
                <img src="<?php echo htmlspecialchars($row['photo_path']); ?>" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;" alt="photo">
              <?php else: ?>
                <span class="text-muted small">無</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['position']); ?></td>
            <td><?php echo htmlspecialchars($row['generation']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['display_order']); ?></td>
            <td>
              <form method="post" class="d-inline" onsubmit="return confirm('確定刪除此幹部？');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button class="btn btn-sm btn-outline-danger">刪除</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8" class="text-center text-muted">尚無幹部資料。</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
