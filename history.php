<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>勤怠履歴</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<?php
session_start();
require 'db.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// 勤怠履歴を取得（新しい順）
$sql = "SELECT date, clock_in, clock_out FROM attendance WHERE user_id = ? ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$records = $stmt->fetchAll();

$totalSeconds = 0;
?>

<h2>勤怠履歴（<?= htmlspecialchars($_SESSION['username']) ?>さん）</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>日付</th>
        <th>出勤</th>
        <th>退勤</th>
        <th>勤務時間</th>
    </tr>
    <?php foreach ($records as $row): ?>
        <?php
        $workTimeStr = '-';
        if ($row['clock_in'] && $row['clock_out']) {
            $in = strtotime($row['clock_in']);
            $out = strtotime($row['clock_out']);
            $workSeconds = $out - $in;

            $hours = floor($workSeconds / 3600);
            $minutes = floor(($workSeconds % 3600) / 60);
            $workTimeStr = sprintf('%02d:%02d', $hours, $minutes);

            $totalSeconds += $workSeconds;
        }
        ?>
        <tr>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['clock_in'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['clock_out'] ?? '-') ?></td>
            <td><?= $workTimeStr ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$totalHours = floor($totalSeconds / 3600);
$totalMinutes = floor(($totalSeconds % 3600) / 60);
?>

<p><strong>総勤務時間：</strong> <?= sprintf('%02d時間%02d分', $totalHours, $totalMinutes) ?></p>

<p><a href="home.php">戻る</a></p>

    </body>
    </html>