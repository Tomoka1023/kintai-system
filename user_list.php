<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザーリスト</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>


<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "アクセス権限がありません。";
    echo '<p><a href="home.php">ホーム画面に戻る</a></p>';
    exit;
}

// ユーザー一覧を取得
$stmt = $pdo->query("SELECT id, username FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();
?>

<h2>ユーザー一覧</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>ユーザー名</th>
        <th>操作</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td>
                <form method="POST" action="delete_user.php" onsubmit="return confirm('本当に削除しますか？');">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <button type="submit">削除</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<p><a href="home.php">ホームに戻る</a></p>

    </body>
    </html>