<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "不正なアクセスです。";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    if ($id === $_SESSION['user_id']) {
    echo "自分自身は削除できません。";
    exit;
    }

    // 勤怠データも削除（外部キー制約がある場合など）
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE user_id = ?");
    $stmt->execute([$id]);

    // ユーザー削除
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: user_list.php");
exit;
