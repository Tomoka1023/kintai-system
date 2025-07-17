<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠履歴一覧</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
session_start();
require 'db.php';

// 管理者以外はアクセス不可
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<p>アクセス権限がありません。</p>";
    echo '<p><a href="home.php">ホームに戻る</a></p>';
    exit;
}

// ユーザーと勤怠履歴を結合して取得
$sql = "SELECT u.username, a.date, a.clock_in, a.clock_out
        FROM attendance a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.date DESC, u.username ASC";
$stmt = $pdo->query($sql);
$records = $stmt->fetchAll();

$grouped = [];
foreach ($records as $row) {
    $grouped[$row['username']][] = $row;
}
?>

<h2>全ユーザーの勤怠履歴（まとめ表示）</h2>

<?php foreach ($grouped as $username => $logs): ?>
    <h3>ユーザー名：<?= htmlspecialchars($username) ?></h3>
    <ul>
        <?php foreach ($logs as $row): ?>
            <?php
            $workTime = '-';
            if ($row['clock_in'] && $row['clock_out']) {
                $in = strtotime($row['clock_in']);
                $out = strtotime($row['clock_out']);
                $diff = $out - $in;
                $h = floor($diff / 3600);
                $m = floor(($diff % 3600) / 60);
                $workTime = sprintf('%02d:%02d', $h, $m);
            }
            ?>
            <li>
                <?= htmlspecialchars($row['date']) ?> |
                出勤: <?= htmlspecialchars($row['clock_in'] ?? '-') ?> |
                退勤: <?= htmlspecialchars($row['clock_out'] ?? '-') ?> |
                勤務時間: <?= $workTime ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>

<p><a href="home.php">ホームに戻る</a></p>

</body>
</html>