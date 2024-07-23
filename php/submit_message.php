<?php
// submit_message.php

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        // ユーザーがログインしていない場合はエラーを返す
        http_response_code(401); // Unauthorized
        echo json_encode(array('error' => 'ログインしていません。'));
        exit();
    }

    // メッセージを受け取る
    $user_id = $_SESSION['user_id'];
    $chat_id = $_POST['chat_id'];
    $message = $_POST['message'];

    // 入力チェック
    if (empty($chat_id) || empty($message)) {
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'chat_id とメッセージを入力してください。'));
        exit();
    }

    // データベースへの接続
    require_once '../db_connect.php';

    try {
        // メッセージをデータベースに保存
        $sql = "INSERT INTO chat_messages (chat_id, user_id, message, created_at) 
                VALUES (:chat_id, :user_id, :message, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'message' => $message
        ]);

        // メッセージ一覧を再取得して表示する
        $sql_messages = "SELECT cm.*, u.user_name FROM chat_messages cm
                         JOIN users u ON cm.user_id = u.user_id
                         WHERE cm.chat_id = :chat_id ORDER BY cm.created_at";
        $stmt_messages = $pdo->prepare($sql_messages);
        $stmt_messages->execute(['chat_id' => $chat_id]);
        $messages = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);

        // メッセージをHTMLで表示
        echo "<ul id='chat_messages'>";
        foreach ($messages as $message) {
            echo "<li><strong>" . htmlspecialchars($message['user_name'], ENT_QUOTES, 'UTF-8') . ":</strong> " . htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8') . "</li>";
        }
        echo "</ul>";

    } catch (PDOException $e) {
        // エラーハンドリング
        http_response_code(500); // Internal Server Error
        echo json_encode(array('error' => 'データベースエラー: ' . $e->getMessage()));
    }

    exit();
} else {
    // POSTリクエストでない場合はエラーを返す
    http_response_code(405); // Method Not Allowed
    echo json_encode(array('error' => 'POSTメソッドが必要です。'));
    exit();
}
?>
