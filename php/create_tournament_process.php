<?php
require '../db_connect.php';
session_start();
$user_id = $_SESSION['user_id'];

// POST メソッドで送信されたデータを受け取ります
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $registration_start = $_POST['registration_start'];
    $registration_end = $_POST['registration_end'];
    $tournament_start = $_POST['tournament_start'];
    $max_participants = $_POST['max_participants'];

    // データベースに大会を追加します
    $stmt = $pdo->prepare("
        INSERT INTO localtournaments (
            user_id, name, description, start_date, end_date, Tournament_starts, people, created_at, updated_at, deleted_flag
        ) VALUES (
            :user_id, :name, :description, :registration_start, :registration_end, :tournament_start, :max_participants, NOW(), NOW(), 0
        )
    ");
    
    $stmt->execute([
        ':user_id' => $user_id,
        ':name' => $name,
        ':description' => $description,
        ':registration_start' => $registration_start,
        ':registration_end' => $registration_end,
        ':tournament_start' => $tournament_start,
        ':max_participants' => $max_participants
    ]);

    // 作成が成功した後は一覧ページにリダイレクトします
    header('Location: tournament.php');
    exit;
}
?>
