<?php
require '../db_connect.php';  // db_connect.php のパスを修正しました
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>大会作成フォーム</title>
</head>
<body>
    <h1>大会作成フォーム</h1>
    <form action="create_tournament_process.php" method="post">
        <label for="name">大会名:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="description">詳細説明:</label>
        <textarea id="description" name="description"></textarea><br>

        <label for="registration_start">募集開始日時:</label>
        <input type="datetime-local" id="registration_start" name="registration_start" required><br>

        <label for="registration_end">募集終了日時:</label>
        <input type="datetime-local" id="registration_end" name="registration_end" required><br>

        <label for="tournament_start">大会開始日時:</label>
        <input type="datetime-local" id="tournament_start" name="tournament_start" required><br>

        <label for="max_participants">最大参加人数:</label>
        <input type="number" id="max_participants" name="max_participants" required><br>

        <input type="submit" value="作成">
    </form>
    <p><a href="tournament.php">大会一覧に戻る</a></p>  <!-- 一覧ページに戻るリンクを追加しました -->
</body>
</html>
