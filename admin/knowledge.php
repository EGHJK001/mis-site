<?php
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
require_login();

// 上傳目錄（知識分享附件）
$uploadDir = __DIR__ . "/../uploads/knowledge/";
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

$alert = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    if ($action === "create") {
        $title   = trim($_POST["title"] ?? "");
        $cat     = trim($_POST["category"] ?? "");
        $author  = trim($_POST["author_name"] ?? "");
        $content = trim($_POST["content"] ?? "");
        $filePath = "";

        // 處理附件上傳（選填）
        if (!empty($_FILES["attachment"]["name"])) {
            $file = $_FILES["attachment"];
            $maxSize = 10 * 1024 * 1024; // 10MB
            $allowedExt = ["pdf","doc","docx","ppt","pptx","xls","xlsx","jpg","jpeg","png","zip"];

            if ($file["error"] === UPLOAD_ERR_OK) {
                if ($file["size"] <= $maxSize) {
                    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                    if (in_array($ext, $allowedExt)) {
                        $safeName = preg_replace("/[^A-Za-z0-9_\\.\\-]+/", "_", basename($file["name"]));
                        $newName = date("Ymd_His") . "_" . uniqid() . "." . $ext;
                        $target  = $uploadDir . $newName;

                        if (move_uploaded_file($file["tmp_name"], $target)) {
                            $filePath = "/uploads/knowledge/" . $newName;
                        } else {
                            $alert = "附件搬移失敗，請確認 uploads/knowledge 目錄權限。";
                        }
                    } else {
                        $alert = "不允許的附件格式，只接受：" . implode(", ", $allowedExt);
                    }
                } else {
                    $alert = "附件大小超過限制（最大 10MB）。";
                }
            } else {
                $alert = "附件上傳發生錯誤，錯誤代碼：" . $file["error"];
            }
        }

        if ($title && $content && !$alert) {
            // 寫入含附件欄位
            $stmt = $mysqli->prepare("INSERT INTO knowledge_articles (title, category, author_name, content, attachment_path, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param("sssss", $title, $cat, $author, $content, $filePath);
                if ($stmt->execute()) {
                    $alert = "已新增文章。";
                } else {
                    $alert = "寫入資料庫失敗：" . $stmt->error;
                }
            } else {
                $alert = "資料表結構可能尚未加入 attachment_path 欄位，請先在資料庫執行 ALTER TABLE 指令。";
            }
        } elseif (!$alert) {
            $alert = "請至少輸入標題與內容。";
        }

    } elseif ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id) {
            // 刪除前先刪附件
            $stmt = $mysqli->prepare("SELECT attachment_path FROM knowledge_articles WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                if ($row && !empty($row["attachment_path"])) {
                    if (strpos($row["attachment_path"], "/uploads/knowledge/") === 0) {
                        $fullPath = __DIR__ . "/../" . ltrim($row["attachment_path"], "/");
                        if (is_file($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                }
            }

            $stmt = $mysqli->prepare("DELETE FROM knowledge_articles WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $alert = "已刪除文章。";
                } else {
                    $alert = "刪除失敗：" . $stmt->error;
                }
            } else {
                $alert = "刪除失敗，請確認資料表結構。";
            }
        }
    }
}

$result = $mysqli->query("SELECT * FROM knowledge_articles ORDER BY created_at DESC");

require __DIR__ . "/../includes/header.php";
?>
<div class="container py-4">
  <h1 class="h4 mb-3">知識庫管理模組</h1>
  <p class="text-muted">建立實習、課業、活動心得等文章，可選擇附上檔案提供下載。</p>

  <?php if ($alert): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($alert); ?></div>
  <?php endif; ?>

  <form method="post" class="card mb-4" enctype="multipart/form-data">
    <div class="card-header">新增文章</div>
    <div class="card-body row g-3">
      <input type="hidden" name="action" value="create">
      <div class="col-12">
        <label class="form-label">標題</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">類別</label>
        <input type="text" name="category" class="form-control" placeholder="如：實習、課業、活動、公告等">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">作者</label>
        <input type="text" name="author_name" class="form-control">
      </div>
      <div class="col-12">
        <label class="form-label">內容</label>
        <textarea name="content" class="form-control" rows="5" required></textarea>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">附件檔案（選填）</label>
        <input type="file" name="attachment" class="form-control">
        <div class="form-text">最大 10MB，支援 pdf, docx, pptx, xlsx, 圖片、zip 等。</div>
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
          <th>標題</th>
          <th>類別</th>
          <th>作者</th>
          <th>建立時間</th>
          <th>附件</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['author_name']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td>
              <?php if (!empty($row['attachment_path'])): ?>
                <a href="<?php echo htmlspecialchars($row['attachment_path']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                  檢視附件
                </a>
              <?php else: ?>
                <span class="text-muted small">無</span>
              <?php endif; ?>
            </td>
            <td>
              <form method="post" class="d-inline" onsubmit="return confirm('確定刪除此文章？');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button class="btn btn-sm btn-outline-danger">刪除</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted">尚無文章。</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
