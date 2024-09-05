<?php
// delete_account.php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require_once '../db_connect.php';

$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // ユーザーのレコードに delete_flag を設定
    $sql_update_user = "UPDATE users SET delete_flag = 1 WHERE user_id = :user_id";
    $stmt_update_user = $pdo->prepare($sql_update_user);
    $stmt_update_user->execute(['user_id' => $user_id]);

    $pdo->commit();
    session_destroy(); // セッションを破棄
    header("Location: ../goodbye.html"); // 退会完了後のページへリダイレクト
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "退会処理に失敗しました。<br>";
    echo "エラーコード: " . $e->getCode() . "<br>";
    echo "エラーメッセージ: " . $e->getMessage(); // エラーメッセージを表示
}
?>