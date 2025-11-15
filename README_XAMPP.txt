=== 資管系學會資訊平台（學生版 + 幹部版） ===

此專案依照「網站前端架構圖（學生版）」與「後台管理模組」概念設計，
使用 RWD Bootstrap 5 + PHP + MySQL，適用於 XAMPP 環境。

[一、安裝步驟]

1. 將整個資料夾放到：
   C:\xampp\htdocs\dept_association

2. 啟動 XAMPP 的 Apache 與 MySQL 服務。

3. 透過瀏覽器開啟 phpMyAdmin：
   http://localhost/phpmyadmin

4. 匯入 sql/schema.sql：
   - 會自動建立資料庫：dept_association
   - 建立 users / activities / officers / downloads / knowledge_articles / messages / finance_claims 等資料表
   - 並建立預設管理員帳號：
     帳號：admin
     密碼：admin123

5. 前台（學生版）網址：
   http://localhost/dept_association/index.php

6. 後台（幹部管理）登入：
   http://localhost/dept_association/admin/login.php
   帳號：admin / 密碼：admin123

[二、前台主要功能對應]

1. 首頁：
   - 最新公告（知識庫類別為「公告」）
   - 活動快訊（最近幾筆活動）
   - 快速連結：活動資訊、成員介紹、文件下載、知識分享、聯絡我們

2. 活動資訊：
   - 活動列表（活動名稱 / 日期 / 地點 / 報名截止）
   - 活動詳細內容頁面

3. 系學會成員：
   - 列出各幹部姓名、職稱與聯絡方式。

4. 文件下載：
   - 提供表單 / 社團章程 / 經費核銷表等檔案連結。

5. 知識分享 / 經驗談：
   - 顯示各類主題文章，例如實習、課業、活動心得等。

6. 聯絡我們：
   - 留言表單（寫入 messages 資料表）
   - 學會辦公室聯絡資訊／地點

[三、後台管理模組對應]

1. 活動管理模組（admin/activities.php）
   - 新增 / 刪除活動基本資料。

2. 系學會成員管理模組（admin/officers.php）
   - 新增 / 刪除幹部姓名、職稱與聯絡方式。

3. 文件下載管理模組（admin/downloads.php）
   - 新增 / 刪除下載項目（名稱、類別、說明、檔案路徑）。

4. 經費核銷管理模組（admin/finance.php）
   - 建立核銷紀錄，管理金額、發票日期與審核狀態。

5. 知識庫管理模組（admin/knowledge.php）
   - 新增 / 刪除文章，包含類別與作者資訊。

6. 留言與回覆管理模組（admin/messages.php）
   - 檢視來自前台「聯絡我們」的留言。
   - 進行審核（通過 / 刪除）。

7. 帳號與安全設定模組（admin/account.php）
   - 修改登入者姓名 / Email。
   - 變更密碼。

[四、調整建議]

- 若網站實際欄位名稱與架構圖略有差異，可直接修改對應 PHP 檔案與資料表欄位。
- 若需要活動線上報名、會員登入等進階功能，可以再擴充 users / activities / registrations 資料表。
