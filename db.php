<?php
date_default_timezone_set('Asia/Tokyo');
$dsn = 'mysql:host=localhost;port=8889;dbname=xs279861_kintaimasa;charset=utf8';
$user = 'xs279861_masabou';
$pass = 'masabouadmin'; 

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
}
?>
