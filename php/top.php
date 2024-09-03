<?php
require_once '../db_connect.php'; // $pdoを使用

$lastPostAt = $_GET['lastPostAt'];

// 投稿を取得
$sql = "SELECT * FROM posts WHERE delete_flag = 0 AND created_at > :lastPostAt ORDER BY created_at ASC";
$stmt = $pdo->prepare($sql); // $connではなく$pdoを使用
$stmt->bindValue(':lastPostAt', $lastPostAt, PDO::PARAM_STR);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSONに変換して返す
echo json_encode($posts);
?>
