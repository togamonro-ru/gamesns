<?php
require '../db_connect.php';  // db_connect.php のパスを修正しました

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tournament_id'])) {
    $tournament_id = $_POST['tournament_id'];
    
    // 削除フラグを立てる
    $stmt = $pdo->prepare("UPDATE localtournaments SET deleted_flag = 1 WHERE tournament_id = ?");
    $stmt->execute([$tournament_id]);
    
    // 作成が成功した後は一覧ページにリダイレクトします
    header('Location: tournament.php');
    exit;
}
?>
