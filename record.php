<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$now = date('H:i:s');
$action = $_POST['action'];

if ($action === 'clock_in') {
    // 出勤記録を追加（存在しなければ）
    $sql = "INSERT INTO attendance (user_id, date, clock_in) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $today, $now]);
} elseif ($action === 'clock_out') {
    // 退勤時刻を更新
    $sql = "UPDATE attendance SET clock_out = ? WHERE user_id = ? AND date = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$now, $user_id, $today]);
}

header('Location: home.php');
exit;
