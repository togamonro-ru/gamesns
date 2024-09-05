<?php
require_once '../db_connect.php';

try {
    // まず、user_nameが既に存在するかを確認
    $sql = "SELECT COUNT(*) FROM users WHERE user_name = :user_name";
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR);
    $stm->execute();
    $count = $stm->fetchColumn();

    if ($count > 0) {
        // user_nameが既に存在する場合
        echo json_encode(['status' => 'error', 'message' => 'ユーザー名は既に使用されています。']);
    } else {
        // user_nameが存在しない場合、パスワードをハッシュ化して新規ユーザーを挿入
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (user_name, email, password, created_at, delete_flag) VALUES (:user_name, :email, :password, current_timestamp(), 0)";
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR);
        $stm->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $stm->bindValue(':password', $passwordHash, PDO::PARAM_STR);
        $stm->execute();

        echo json_encode(['status' => 'success']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'データベースエラーが発生しました: ' . $e->getMessage()]);
}
?>