<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠履歴カレンダー</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
session_start();
require 'db.php';

// 管理者チェック
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<p>アクセス権限がありません。</p>";
    echo '<p><a href="home.php">ホームに戻る</a></p>';
    exit;
}

// 年月とユーザーIDの取得
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

// ユーザー一覧取得
$users_stmt = $pdo->query("SELECT id, username FROM users ORDER BY username ASC");
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

// カレンダー処理を行うのは user_id が選ばれている場合のみ
$records = [];
if ($selected_user_id !== null) {
    $start_date = "$year-$month-01";
    $end_date = date("Y-m-t", strtotime($start_date));

    $stmt = $pdo->prepare("SELECT date, clock_in, clock_out FROM attendance WHERE user_id = ? AND date BETWEEN ? AND ?");
    $stmt->execute([$selected_user_id, $start_date, $end_date]);
    $records = $stmt->fetchAll(PDO::FETCH_UNIQUE);
}

// 曜日配列
$weekdays = ['日','月','火','水','木','金','土'];
?>

<h2>勤怠カレンダー（月別）</h2>

<form method="get" action="calendar_view.php">
    <label>対象ユーザー：
        <select name="user_id" required>
            <option value="">選択してください</option>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $u['id'] == $selected_user_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>年月：
        <input type="number" name="year" value="<?= $year ?>" style="width: 80px;">年
        <input type="number" name="month" value="<?= $month ?>" style="width: 50px;">月
    </label>
    <button type="submit">表示</button>
</form>

<?php if ($selected_user_id !== null): ?>
    <h3>
        <?= htmlspecialchars($users[array_search($selected_user_id, array_column($users, 'id'))]['username']) ?>
        さんの <?= $year ?>年<?= $month ?>月の勤務状況
    </h3>

    <table border="1" cellpadding="8">
        <tr>
            <th>日付</th>
            <th>曜日</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>勤務時間</th>
        </tr>
        <?php
        $totalSeconds = 0;

        $days_in_month = date('t', strtotime("$year-$month-01"));
        for ($d = 1; $d <= $days_in_month; $d++) {
            $day_str = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $w = $weekdays[date('w', strtotime($day_str))];

            $in = $records[$day_str]['clock_in'] ?? '-';
            $out = $records[$day_str]['clock_out'] ?? '-';
            $workTime = '-';

            if ($in !== '-' && $out !== '-') {
                $diff = strtotime($out) - strtotime($in);
                $h = floor($diff / 3600);
                $m = floor(($diff % 3600) / 60);
                $workTime = sprintf('%02d:%02d', $h, $m);

                $totalSeconds += $diff;
            }

            echo "<tr>";
            echo "<td>{$year}/{$month}/{$d}</td>";
            echo "<td>{$w}</td>";
            echo "<td>{$in}</td>";
            echo "<td>{$out}</td>";
            echo "<td>{$workTime}</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <?php
$totalHours = floor($totalSeconds / 3600);
$totalMinutes = floor(($totalSeconds % 3600) / 60);
?>

<tr>
    <td colspan="4" style="text-align: right; font-weight: bold;">合計勤務時間</td>
    <td><strong><?= sprintf('%02d:%02d', $totalHours, $totalMinutes) ?></strong></td>
</tr>

<?php endif; ?>

<p><a href="home.php">ホームに戻る</a></p>


</body>
</html>