<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
require_login();

// 確保上傳目錄存在
$uploadDir = __DIR__ . "/../uploads/files/";
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

$alert = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    if ($action === "create") {
        $title = trim($_POST["title"] ?? "");
        $desc  = trim($_POST["description"] ?? "");
        $cat   = trim($_POST["category"] ?? "");
        $path  = trim($_POST["file_path"] ?? "");

        // 若有選擇檔案上傳，優先使用上傳檔案
        if (!empty($_FILES["upload_file"]["name"])) {
            $file     = $_FILES["upload_file"];
            $maxSize  = 10 * 1024 * 1024; // 10MB
            $allowedExt = ["pdf","doc","docx","xls","xlsx","ppt","pptx","jpg","jpeg","png","zip"];

            if ($file["error"] === UPLOAD_ERR_OK) {
                if ($file["size"] <= $maxSize) {
                    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                    if (in_array($ext, $allowedExt)) {
                        $safeName = preg_replace("/[^A-Za-z0-9_\.\-]+/", "_", basename($file["name"]));
                        $newName  = date("Ymd_His") . "_" . uniqid() . "." . $ext;
                        $target   = $uploadDir . $newName;

                        if (move_uploaded_file($file["tmp_name"], $target)) {
                            // 存相對路徑，前台可直接下載
                            $path = "/uploads/files/" . $newName;
                        } else {
                            $alert = "檔案搬移失敗，請確認 uploads 目錄權限。";
                        }
                    } else {
                        $alert = "不允許的檔案格式，只接受：" . implode(", ", $allowedExt);
                    }
                } else {
                    $alert = "檔案大小超過限制（最大 10MB）。";
                }
            } else {
                $alert = "檔案上傳發生錯誤，錯誤代碼：" . $file["error"];
            }
        }

        if ($title && $path && !$alert) {
            $stmt = $mysqli->prepare("INSERT INTO downloads (title, description, category, file_path, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $title, $desc, $cat, $path);
            if ($stmt->execute()) {
                $alert = "已新增下載項目。";
            } else {
                $alert = "寫入資料庫失敗：" . $mysqli->error;
            }
        } elseif (!$alert) {
            $alert = "請至少輸入名稱並上傳檔案或填寫檔案路徑。";
        }

    } elseif ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id) {
            // 先取出路徑，若是本機 uploads 內的檔案可一併刪除
            $stmt = $mysqli->prepare("SELECT file_path FROM downloads WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if ($row) {
                $filePath = $row["file_path"];
                if (strpos($filePath, "/uploads/files/") === 0) {
                    $fullPath = __DIR__ . "/../" . ltrim($filePath, "/");
                    if (is_file($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }

            $stmt = $mysqli->prepare("DELETE FROM downloads WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $alert = "已刪除下載項目。";
            } else {
                $alert = "刪除失敗：" . $mysqli->error;
            }
        }
    }
}

$result = $mysqli->query("SELECT * FROM downloads ORDER BY created_at DESC");

require __DIR__ . "/../includes/header.php";
?>
<div class="container py-4">
  <h1 class="h4 mb-3">文件下載管理模組</h1>
  <p class="text-muted">管理表單、社團章程、經費核銷範本等檔案，可直接上傳檔案或填寫外部連結。</p>

  <?php if ($alert): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($alert); ?></div>
  <?php endif; ?>

  <form method="post" class="card mb-4" enctype="multipart/form-data">
    <div class="card-header">新增下載項目</div>
    <div class="card-body row g-3">
      <input type="hidden" name="action" value="create">
      <div class="col-12 col-md-6">
        <label class="form-label">名稱</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">類別</label>
        <input type="text" name="category" class="form-control" placeholder="如：申請表、章程、經費核銷表">
      </div>
      <div class="col-12">
        <label class="form-label">說明</label>
        <input type="text" name="description" class="form-control">
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">上傳檔案</label>
        <input type="file" name="upload_file" class="form-control">
        <div class="form-text">最大 10MB，建議格式：pdf, docx, xlsx, pptx, jpg, png, zip。</div>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">或填寫檔案路徑 / 外部連結</label>
        <input type="text" name="file_path" class="form-control" placeholder="/files/form01.pdf 或 https://...">
        <div class="form-text">若同時上傳檔案與填寫連結，系統會以「上傳檔案」為主。</div>
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary">儲存</button>
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr><th>名稱</th><th>類別</th><th>說明</th><th>路徑 / 連結</th><th>操作</th></tr></thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td class="small">
              <?php if ($row['file_path']): ?>
                <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">
                  <?php echo htmlspecialchars($row['file_path']); ?>
                </a>
              <?php endif; ?>
            </td>
            <td>
              <form method="post" class="d-inline" onsubmit="return confirm('確定刪除此項目？');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button class="btn btn-sm btn-outline-danger">刪除</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center text-muted">尚無下載項目。</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
