<?php
$host = 'localhost';
$user = 'webuser';
$pass = '(設定したパスワード)';
$dbname = 'lamp_app';

//DBへ接続
$conn = new mysqli('__________');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
