<?php
// account_deleted.php

session_start();

// セッションがある場合はログアウトさせる
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>退会済み</title>
    <link rel="stylesheet" href="../css/style_2.css"> <!-- CSSファイルへのリンク -->
</head>
<body>
    <div class="container">
        <h1>退会済みです</h1>
        <p>申し訳ありませんが、このアカウントは退会済みです。</p>
        <a href="../index.html" class="back-button">ホームに戻る</a>
    </div>
</body>
</html>
