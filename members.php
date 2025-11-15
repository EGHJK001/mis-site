<?php
require __DIR__ . "/includes/db.php";
require __DIR__ . "/includes/header.php";

// 取出所有幹部，準備在「成員介紹」分區塊顯示
$result = $mysqli->query("
    SELECT name, position, generation, email, phone, intro, photo_path, display_order
    FROM officers
    ORDER BY 
        generation DESC,      -- 屆別由新到舊
        display_order ASC,
        name ASC
");

$currentGen = null;
$firstBlock = true;
?>
<div class="container py-4">
  <h1 class="h3 mb-3">學會介紹</h1>
  <p class="text-muted">
    本頁整合系學會的基本資訊、成員介紹與組織章程，方便同學快速了解系學會的運作方式與服務內容。
  </p>

  <!-- 上方可以放一小段學會簡介，文字你可以自己改 -->
  <div class="card mb-4">
    <div class="card-body">
      <h2 class="h5 mb-2">學會簡介</h2>
      <p class="mb-1">
        增進同學交流：舉辦迎新活動、系聚、聯誼等，讓同學之間更熟悉、互助合作。
        專業技能培養：規劃資訊相關講座、程式設計工作坊與競賽，提升專業能力。
        活動規劃與服務：籌辦多元活動如運動比賽、志工服務、學術研討，豐富同學的大學生活。
        傳承與經驗分享：保存並傳遞學長姐的寶貴經驗，讓學弟妹在學習與未來發展上更有方向。
      </p>
    </div>
  </div>

  <!-- Tab 導覽：成員介紹 / 組織章程 -->
  <ul class="nav nav-tabs mb-3" id="assocTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="members-tab" data-bs-toggle="tab"
              data-bs-target="#members-pane" type="button" role="tab"
              aria-controls="members-pane" aria-selected="true">
        成員介紹
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="rules-tab" data-bs-toggle="tab"
              data-bs-target="#rules-pane" type="button" role="tab"
              aria-controls="rules-pane" aria-selected="false">
        組織章程
      </button>
    </li>
  </ul>

  <div class="tab-content" id="assocTabsContent">
    <!-- 成員介紹 Tab -->
    <div class="tab-pane fade show active" id="members-pane" role="tabpanel" aria-labelledby="members-tab">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
            // 若沒填 generation，就歸類到「未分類屆別」
            $gen = trim($row["generation"] ?? "");
            if ($gen === "") {
                $gen = "未分類屆別";
            }

            // 遇到新的屆別就開一個新的區塊
            if ($gen !== $currentGen) {
                if (!$firstBlock) {
                    echo "</div>"; // 關閉上一個 .row
                }
                $firstBlock = false;
                $currentGen = $gen;
          ?>
            <h2 class="h5 mt-4 mb-2 border-start border-3 border-primary ps-2">
              <?php echo htmlspecialchars($gen); ?>
            </h2>
            <div class="row g-3">
          <?php } ?>

          <div class="col-12 col-md-6 col-lg-4">
            <div class="card card-hover h-100 text-center p-3">
              <div class="d-flex flex-column align-items-center">
                <?php if (!empty($row['photo_path'])): ?>
                  <img src="<?php echo htmlspecialchars($row['photo_path']); ?>"
                       class="rounded-circle mb-3"
                       style="width:96px;height:96px;object-fit:cover;"
                       alt="<?php echo htmlspecialchars($row['name']); ?>">
                <?php else: ?>
                  <div class="rounded-circle bg-secondary mb-3 d-flex align-items-center justify-content-center"
                       style="width:96px;height:96px;">
                    <span class="text-white fw-bold fs-4">
                      <?php echo htmlspecialchars(mb_substr($row['name'], 0, 1, 'UTF-8')); ?>
                    </span>
                  </div>
                <?php endif; ?>

                <h5 class="card-title mb-1"><?php echo htmlspecialchars($row['name']); ?></h5>
                <p class="card-subtitle text-muted mb-1"><?php echo htmlspecialchars($row['position']); ?></p>

                <?php if (!empty($row['email'])): ?>
                  <div class="small">Email：<?php echo htmlspecialchars($row['email']); ?></div>
                <?php endif; ?>
                <?php if (!empty($row['phone'])): ?>
                  <div class="small">聯絡電話：<?php echo htmlspecialchars($row['phone']); ?></div>
                <?php endif; ?>
              </div>

              <?php if (!empty($row['intro'])): ?>
                <div class="mt-3 text-start small text-muted">
                  <?php echo nl2br(htmlspecialchars($row['intro'])); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>

        <?php if (!$firstBlock): ?>
          </div><!-- 關掉最後一個 .row -->
        <?php endif; ?>

      <?php else: ?>
        <div class="alert alert-info mt-3">尚未建立幹部資料。</div>
      <?php endif; ?>
    </div>

        <!-- 組織章程 Tab -->
    <div class="tab-pane fade" id="rules-pane" role="tabpanel" aria-labelledby="rules-tab">
      <div class="card">
        <div class="card-body">
          <h2 class="h5 mb-2">系學會組織章程（113 年版）</h2>
          <p class="small text-muted">
            本章程最初訂於民國 83 年，並於 88、90、95、104、108、111 年陸續修正，以符合本校與系上最新規定。
          </p>

          <p class="mt-3 mb-2">章程主要內容包含下列幾個部分：</p>
          <ul>
            <li>
              <strong>第一章　總則：</strong>
              說明本系學會的名稱、性質（學生自治性社團），以及以服務全體資管系學生、
              促進各班情誼與系務發展為主要宗旨。
            </li>
            <li>
              <strong>第二章　組織：</strong>
              說明本會的組成單位，例如社員大會、系學會幹部、指導老師等，
              並界定各單位在學會運作中的角色。
            </li>
            <li>
              <strong>第三章　會員：</strong>
              規範會員資格、入會與退會方式、會員的權利（選舉、被選舉、參與活動等）
              與義務（遵守章程、配合會務與活動等）。
            </li>
            <li>
              <strong>第四章　幹部之產生與職權：</strong>
              說明會長、副會長及各部門幹部的產生方式與任期，
              並條列各職務的具體工作內容，例如：對外代表、會務推動、財務管理、
              活動規劃、文書與紀錄、學術與資訊等職責。
            </li>
            <li>
              <strong>第五章　會議：</strong>
              包含社員大會、幹部會議、監察委員會議等開會規定，
              包括召開條件、出席人數標準、決議方式與會議紀錄等要求。
            </li>
            <li>
              <strong>其他條文：</strong>
              說明經費來源與運用原則、監察委員會對會務與財務的監督權限，
              以及章程修正的程序等，確保學會運作公開透明。
            </li>
          </ul>

          <hr>

          <p class="mb-2">
            若需查閱完整條文（包含各條細項），可下載 PDF 版本：
          </p>
          <a href="/uploads/files/113資管系學會組織章程修正後.pdf"
            target="_blank"
            class="btn btn-sm btn-outline-primary">
            下載完整《資訊管理系學會組織章程》（PDF）
          </a>

          <p class="small text-muted mt-3 mb-0">
            ※ 若你實際存放章程 PDF 的路徑不同，請將上方連結中的路徑改成正確位置即可。
          </p>
        </div>
      </div>
    </div>

  </div>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?>
