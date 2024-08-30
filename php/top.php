<?php
require_once '../db_connect.php'; // $pdoを使用

// 投稿を取得
$sql = "SELECT * FROM posts WHERE delete_flag = 0 ORDER BY created_at DESC";
$stmt = $pdo->query($sql); // $connではなく$pdoを使用
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSONに変換して返す
echo json_encode($posts);
?>
