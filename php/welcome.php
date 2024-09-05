<?php
// welcome.php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require_once '../db_connect.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// プロフィール情報の取得
$sql_profile = "SELECT profile_image FROM profiles WHERE user_id = :user_id";
$stmt_profile = $pdo->prepare($sql_profile);
$stmt_profile->execute(['user_id' => $user_id]);
$profile = $stmt_profile->fetch(PDO::FETCH_ASSOC);

// デフォルトのプロフィール画像パス
$default_image_path = '/gamesns/img/default_profile.png';

echo "ようこそ、{$user_name} さん！";
echo "<br><a href='./logout.php'>ログアウト</a>";

// プロフィール画像の表示
if (!empty($profile['profile_image'])) {
    $profile_image = htmlspecialchars($profile['profile_image'], ENT_QUOTES, 'UTF-8');
} else {
    $profile_image = $default_image_path;
}

echo "<br><img src='{$profile_image}' alt='プロフィール画像' style='width:50px; height:50px; border-radius:50%;'>";
echo "<br><a href='./profile.php'>プロフィールを表示/編集</a>";
echo "<br><a href='./group_chats.php'>グループチャット</a>";
echo "<br><a href='./tornament.php'>大会一覧</a>";
// 退会リンク
echo "<br><a href='./delete_account.php' onclick=\"return confirm('本当に退会しますか？');\">退会</a>";
?>