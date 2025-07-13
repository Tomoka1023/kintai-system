<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ホーム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<p id="clock">現在時刻</p>


<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// 今日の勤怠データを取得
$sql = "SELECT * FROM attendance WHERE user_id = ? AND date = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $today]);
$record = $stmt->fetch();
?>

<h2>こんにちは、<?= htmlspecialchars($_SESSION['username']) ?>さん</h2>

<form action="record.php" method="post">
    <?php if (!$record || !$record['clock_in']): ?>
        <button type="submit" name="action" value="clock_in">出勤</button>
    <?php else: ?>
        <p>出勤済み: <?= $record['clock_in'] ?></p>
    <?php endif; ?>

    <?php if ($record && !$record['clock_out']): ?>
        <button type="submit" name="action" value="clock_out">退勤</button>
    <?php elseif ($record && $record['clock_out']): ?>
        <p>退勤済み: <?= $record['clock_out'] ?></p>
    <?php endif; ?>
</form>

<?php if ($_SESSION['role'] === 'admin'): ?>
    <p><a href="admin_history.php">▶ 全ユーザーの勤怠履歴を見る（管理者専用）</a></p>
    <p><a href="calendar_view.php">▶ カレンダー表示（ユーザー選択）</a></p>
    <p><a href="user_list.php">▶︎ ユーザーリスト</a></p>
<?php endif; ?>

<p><a href="history.php">勤怠履歴</a></p>
<p><a href="logout.php">ログアウト</a></p>

<script>
function updateClock() {
    const now = new Date();
    const formatted = now.toLocaleString('ja-JP', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    document.getElementById('clock').textContent = "現在時刻: " + formatted;
}

// 初回表示
updateClock();

// 1秒ごとに更新
setInterval(updateClock, 1000);
</script>

</body>
</html>
