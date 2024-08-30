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
        // Check if the user has been deactivated
        if ($user['delete_flag'] == 1) {
            // Redirect to the account deleted page
            header("Location: account_deleted.php");
            exit();
        } else {
            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];

            header("Location: welcome.php");
            exit();
        }
    } else {
        // Display a styled error message on failed login
        $error_message = "ログインに失敗しました。ユーザー名またはパスワードが正しくありません。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Login Error</title>
    <style>
        /* css/style.css */
body {
    background-color: black; /* 全体を黒に */
    color: white; /* 文字を白に */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
    position: relative; /* 位置を相対的に設定 */
}

form {
    border: 2px solid transparent; /* ボーダーの透明にする */
    border-radius: 5px;
    padding: 20px;
    width: 100%;
    max-width: 500px;
    background-color: #333; /* フォームの背景をダークグレーに */
    margin: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    animation: rainbow 5s linear infinite; /* 虹色のエフェクトを追加 */
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
    width: 100%;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input[type="text"],
.form-group input[type="password"],
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #555;
    border-radius: 4px;
    background-color: #222;
    color: white;
}

.form-group input[type="submit"],
.back-button {
    cursor: pointer;
    background-color: #007bff; /* ボタンの背景色 */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    text-decoration: none; /* リンクの下線を消す */
    display: inline-block;
}

.form-group input[type="submit"]:hover,
.back-button:hover {
    background-color: #0056b3; /* ホバー時の背景色 */
}

.submit-button {
    margin-top: 20px;
}

/* 「戻る」ボタンのスタイル */
.back-button {
    position: absolute;
    top: 10px;
    left: 10px; /* 左上に配置 */
}

/* 虹色の光るエフェクト */
@keyframes rainbow {
    0% { box-shadow: 0 0 10px 4px red; }
    14.29% { box-shadow: 0 0 10px 4px orange; }
    28.58% { box-shadow: 0 0 10px 4px yellow; }
    42.87% { box-shadow: 0 0 10px 4px green; }
    57.16% { box-shadow: 0 0 10px 4px blue; }
    71.45% { box-shadow: 0 0 10px 4px indigo; }
    85.74% { box-shadow: 0 0 10px 4px violet; }
    100% { box-shadow: 0 0 10px 4px red; }
}

    </style>
</head>
<body>
    <?php if (isset($error_message)): ?>
        <div class="error-message">
            <?php echo $error_message; ?>
        </div>
        <a href="login.html" class="back-link">戻る</a>
    <?php endif; ?>
</body>
</html>
