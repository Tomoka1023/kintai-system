<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<p id="clock">現在時刻</p>

<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ユーザー名とパスワードで検索
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: home.php');
        exit;
    } else {
        echo "ユーザー名またはパスワードが間違っています。";
    }
}
?>

<h1>勤怠管理システムまさ坊(仮)</h1>
<h2>ログイン</h2>
<form method="POST" action="login.php">
    ユーザー名: <input type="text" name="username" required><br>
    パスワード: <input type="password" name="password" required><br>
    <button type="submit">ログイン</button>
</form>

<p><a href="register.php">ユーザー登録はこちら</a></p>

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