<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT); // パスワードのハッシュ化

    // 重複ユーザー名チェック
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        echo "そのユーザー名はすでに使われています。";
    } else {
        // 新規ユーザーの挿入
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $password_hash])) {
            echo "ユーザー登録が完了しました。";
            header('Location: login.php'); // ログインページにリダイレクト
            exit;
        } else {
            echo "ユーザー登録に失敗しました。";
        }
    }
}
?>

<h2>ユーザー登録</h2>
<form method="POST" action="register.php">
    ユーザー名: <input type="text" name="username" required><br>
    パスワード: <input type="password" name="password" required><br>
    <button type="submit">登録</button>
</form>

<p><a href="login.php">ログインページへ戻る</a></p>


</body>
</html>