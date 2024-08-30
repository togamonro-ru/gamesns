<?php
// check_login.php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    require_once '../db_connect.php';
    
    $sql = "SELECT * FROM users WHERE user_name = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // ユーザーが退会済みかどうかを確認
        if ($user['delete_flag'] == 1) {
            // 退会済みのページにリダイレクト
            header("Location: account_deleted.php");
            exit();
        } else {
            // セッションにユーザー情報を設定
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            
            header("Location: ../top.html");
            exit();
        }
    } else {
        echo "ログインに失敗しました。ユーザー名またはパスワードが正しくありません。";
    }
}
?>
