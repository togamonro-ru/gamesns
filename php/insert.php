<?php
require_once '../db_connect.php';

$passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (user_name, email, password, created_at, delete_flag) VALUES (:user_name, :email, :password, current_timestamp(), 0)";
$stm = $pdo->prepare($sql);
$stm->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR);
$stm->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
$stm->bindValue(':password', $passwordHash, PDO::PARAM_STR);
$stm->execute();

echo json_encode(['status' => 'success']);
?>