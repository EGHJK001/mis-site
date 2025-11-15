<?php
require __DIR__ . "/includes/db.php";
require __DIR__ . "/includes/header.php";

// 取得搜尋條件
$q        = trim($_GET['q'] ?? "");
$category = trim($_GET['category'] ?? "");
$hasFile  = isset($_GET['has_file']) ? 1 : 0;

// 取得所有類別供下拉選單使用
$catSql = "SELECT DISTINCT category FROM knowledge_articles WHERE category IS NOT NULL AND category <> '' ORDER BY category ASC";
$catResult = $mysqli->query($catSql);

// 組 SQL 條件（為了簡單，用 real_escape_string + 字串組合；實作在校內系統可接受）
$where = [];
if ($q !== "") {
    $qEsc = $mysqli->real_escape_string($q);
    $where[] = "(title LIKE '%{$qEsc}%' OR content LIKE '%{$qEsc}%' OR category LIKE '%{$qEsc}%')";
}
if ($category !== "") {
    $catEsc = $mysqli->real_escape_string($category);
    $where[] = "category = '{$catEsc}'";
}
if ($hasFile) {
    $where[] = "(attachment_path IS NOT NULL AND attachment_path <> '')";
}

$sql = "SELECT id, title, category, author_name, created_at, attachment_path 
        FROM knowledge_articles 
        WHERE 1";
if (!empty($where)) {
    $sql .= " AND " . implode(" AND ", $where);
}
$sql .= " ORDER BY created_at DESC";

$result = $mysqli->query($sql);
?>
<div class="container py-4">
  <h1 class="h3 mb-3">知識管理系統</h1>
  <p class="text-muted">
    提供實習、課業、活動、競賽等相關的文件與經驗分享，可依關鍵字、類別與是否有附檔進行查詢。
  </p>

  <!-- 篩選區塊（搜尋 + 類別 + 是否有附檔） -->
  <form method="get" class="card mb-3">
    <div class="card-body row g-3 align-items-end">
      <div class="col-12 col-md-5">
        <label class="form-label">關鍵字搜尋</label>
        <input type="text" name="q" class="form-control" 
               placeholder="輸入標題、內容或類別關鍵字"
               value="<?php echo htmlspecialchars($q); ?>">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">類別篩選</label>
        <select name="category" class="form-select">
          <option value="">全部類別</option>
          <?php if ($catResult && $catResult->num_rows > 0): ?>
            <?php while ($catRow = $catResult->fetch_assoc()): ?>
              <?php $c = $catRow['category']; ?>
              <option value="<?php echo htmlspecialchars($c); ?>"
                <?php if ($c === $category) echo 'selected'; ?>>
                <?php echo htmlspecialchars($c); ?>
              </option>
            <?php endwhile; ?>
          <?php endif; ?>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <div class="form-check mt-4 pt-1">
          <input class="form-check-input" type="checkbox" name="has_file" id="has_file"
                 <?php if ($hasFile) echo 'checked'; ?>>
          <label class="form-check-label" for="has_file">
            只顯示有附檔
          </label>
        </div>
      </div>
      <div class="col-6 col-md-2 text-end">
        <button type="submit" class="btn btn-primary w-100 mb-2">查詢</button>
        <a href="knowledge.php" class="btn btn-outline-secondary w-100">清除條件</a>
      </div>
    </div>
  </form>

  <!-- 查詢結果列表 -->
  <div class="list-group">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <a href="knowledge_detail.php?id=<?php echo $row['id']; ?>" 
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold">
              <?php echo htmlspecialchars($row['title']); ?>
              <?php if (!empty($row['attachment_path'])): ?>
                <span class="badge bg-secondary ms-1">附檔</span>
              <?php endif; ?>
            </div>
            <div class="small text-muted">
              類別：<?php echo htmlspecialchars($row['category']); ?>
              <?php if (!empty($row['author_name'])): ?>
                ｜作者：<?php echo htmlspecialchars($row['author_name']); ?>
              <?php endif; ?>
              ｜建立日期：<?php echo substr($row['created_at'], 0, 10); ?>
            </div>
          </div>
          <span class="badge bg-secondary rounded-pill">
            <?php echo substr($row['created_at'], 0, 10); ?>
          </span>
        </a>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="alert alert-info">
        查無符合條件的資料，可以放寬關鍵字或取消「只顯示有附檔」再試一次。
      </div>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>
