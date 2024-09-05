<?php
// send_message.php

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../db_connect.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chat_id']) && isset($_POST['message'])) {
    $chat_id = $_POST['chat_id'];
    $message = $_POST['message'];
    
    $sql_send_message = "INSERT INTO chat_messages (chat_id, user_id, message, created_at) 
                         VALUES (:chat_id, :user_id, :message, NOW())";
    $stmt_send_message = $pdo->prepare($sql_send_message);
    $stmt_send_message->execute([
        'chat_id' => $chat_id,
        'user_id' => $user_id,
        'message' => $message
    ]);

    $message_id = $pdo->lastInsertId();
    
    // Fetch the created_at time of the message
    $sql_fetch_time = "SELECT created_at FROM chat_messages WHERE message_id = :message_id";
    $stmt_fetch_time = $pdo->prepare($sql_fetch_time);
    $stmt_fetch_time->execute(['message_id' => $message_id]);
    $created_at = $stmt_fetch_time->fetchColumn();
    
    echo json_encode([
        'success' => true, 
        'user_name' => $user_name, 
        'message' => $message,
        'created_at' => $created_at
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
