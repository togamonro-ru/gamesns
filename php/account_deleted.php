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
    <link rel="stylesheet" href="../css/picture.css">
</head>
<body>
    <div class="container">
        <div class="text-with-images">
            <img src="../img/images.jpg" alt="左の画像" class="side-image left-image">
            <div class="text-content">
                <h1>退会済みです</h1>
                <p>申し訳ありませんが、このアカウントは退会済みです。</p>
            </div>
            <img src="../img/images.jpg" alt="右の画像" class="side-image right-image">
        </div>
        <a href="../index.html" class="back-button">ホームに戻る</a>
    </div>
</body>
</html>
