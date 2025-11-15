<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dept_association";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    die("資料庫連線失敗: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
?>
