<?php
$host = 'localhost';
$user = 'webuser';
$pass = '(任意のパスワード)';
$dbname = 'lamp_app';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
