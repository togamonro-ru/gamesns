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
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        
        header("Location: welcome.php");
        exit();
    } else {
        echo "ログインに失敗しました。ユーザー名またはパスワードが正しくありません。";
    }
}
?>
