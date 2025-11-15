<?php
require __DIR__ . "/../includes/db.php";

// 想要設定的新密碼（你可以改成自己想要的）
$newPassword = "admin123";

$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
$stmt->bind_param("s", $hash);

if ($stmt->execute()) {
    echo "已將 admin 密碼重設為：{$newPassword}";
} else {
    echo "更新失敗：" . $mysqli->error;
}
?>
