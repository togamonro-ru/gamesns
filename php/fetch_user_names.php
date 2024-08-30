<?php
require_once '../db_connect.php'; // データベース接続ファイルをインクルード

// クエリパラメータからuser_idを取得
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id > 0) {
    // ユーザー名を取得するSQLクエリ
    $sql = "SELECT user_name FROM users WHERE user_id = :user_id AND delete_flag = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // JSONに変換して返す
    echo json_encode($user);
} else {
    echo json_encode(['user_name' => '不明']); // user_idが無効な場合のデフォルト値
}
?>
