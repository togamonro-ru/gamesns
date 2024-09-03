<?php
require_once '../db_connect.php'; // $pdoを使用

$user_id = $_GET['user_id'];

// プロフィール画像を取得
$sql = "SELECT profile_image FROM profiles WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql); // SQLインジェクションを避けるためにプリペアドステートメントを使用
$stmt->execute(['user_id' => $user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// プロフィール画像をJSONに変換して返す
echo json_encode($profile);
?>
