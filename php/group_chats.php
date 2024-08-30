<?php
// group_chats.php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

require_once '../db_connect.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch user's group chats
$sql_chats = "SELECT gc.* FROM group_chats gc
              JOIN chat_invitations ci ON gc.chat_id = ci.chat_id
              WHERE ci.invited_user_id = :user_id AND ci.status = 'accepted'";
$stmt_chats = $pdo->prepare($sql_chats);
$stmt_chats->execute(['user_id' => $user_id]);
$chats = $stmt_chats->fetchAll(PDO::FETCH_ASSOC);

// Create new group chat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chat_name'])) {
    $chat_name = $_POST['chat_name'];
    $chat_details = $_POST['chat_details'];
    
    $sql_create_chat = "INSERT INTO group_chats (chat_name, chat_details, user_id, created_at, updated_at) 
                        VALUES (:chat_name, :chat_details, :user_id, NOW(), NOW())";
    $stmt_create_chat = $pdo->prepare($sql_create_chat);
    $stmt_create_chat->execute([
        'chat_name' => $chat_name,
        'chat_details' => $chat_details,
        'user_id' => $user_id
    ]);
    $chat_id = $pdo->lastInsertId();
    
    // Add the creator to the chat
    $sql_invite_creator = "INSERT INTO chat_invitations (chat_id, invited_user_id, inviter_user_id, status, created_at, updated_at) 
                           VALUES (:chat_id, :invited_user_id, :inviter_user_id, 'accepted', NOW(), NOW())";
    $stmt_invite_creator = $pdo->prepare($sql_invite_creator);
    $stmt_invite_creator->execute([
        'chat_id' => $chat_id,
        'invited_user_id' => $user_id,
        'inviter_user_id' => $user_id
    ]);

    header("Location: group_chats.php");
    exit();
}

// Invite user to chat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_user_id'])) {
    $chat_id = $_POST['chat_id'];
    $invite_user_id = $_POST['invite_user_id'];
    
    $sql_invite_user = "INSERT INTO chat_invitations (chat_id, invited_user_id, inviter_user_id, status, created_at, updated_at) 
                        VALUES (:chat_id, :invited_user_id, :inviter_user_id, 'pending', NOW(), NOW())";
    $stmt_invite_user = $pdo->prepare($sql_invite_user);
    $stmt_invite_user->execute([
        'chat_id' => $chat_id,
        'invited_user_id' => $invite_user_id,
        'inviter_user_id' => $user_id
    ]);

    header("Location: group_chats.php?chat_id={$chat_id}");
    exit();
}

// Accept chat invitation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_invitation'])) {
    $invitation_id = $_POST['invitation_id'];
    
    $sql_accept_invitation = "UPDATE chat_invitations SET status = 'accepted' WHERE invitation_id = :invitation_id";
    $stmt_accept_invitation = $pdo->prepare($sql_accept_invitation);
    $stmt_accept_invitation->execute(['invitation_id' => $invitation_id]);

    header("Location: group_chats.php");
    exit();
}

// Leave chat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_chat'])) {
    $chat_id = $_POST['chat_id'];

    // 退会メッセージをデータベースに保存
    $leave_message = "{$user_name}が退会しました。";
    $sql_leave_message = "INSERT INTO chat_messages (chat_id, user_id, message, created_at) 
                          VALUES (:chat_id, :user_id, :message, NOW())";
    $stmt_leave_message = $pdo->prepare($sql_leave_message);
    $stmt_leave_message->execute([
        'chat_id' => $chat_id,
        'user_id' => $user_id,
        'message' => $leave_message
    ]);

    // 退会処理を実行
    $sql_leave_chat = "DELETE FROM chat_invitations WHERE chat_id = :chat_id AND invited_user_id = :user_id";
    $stmt_leave_chat = $pdo->prepare($sql_leave_chat);
    $stmt_leave_chat->execute([
        'chat_id' => $chat_id,
        'user_id' => $user_id
    ]);

    header("Location: group_chats.php");
    exit();
}

// Fetch chat messages if chat_id is provided
$chat_id = null;
$messages = [];
if (isset($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
    
    // Fetch chat details
    $sql_chat = "SELECT * FROM group_chats WHERE chat_id = :chat_id";
    $stmt_chat = $pdo->prepare($sql_chat);
    $stmt_chat->execute(['chat_id' => $chat_id]);
    $chat = $stmt_chat->fetch(PDO::FETCH_ASSOC);
    
    if ($chat) {
        // Fetch chat messages
        $sql_messages = "SELECT cm.*, u.user_name FROM chat_messages cm
                         JOIN users u ON cm.user_id = u.user_id
                         WHERE cm.chat_id = :chat_id ORDER BY cm.created_at";
        $stmt_messages = $pdo->prepare($sql_messages);
        $stmt_messages->execute(['chat_id' => $chat_id]);
        $messages = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Handle invalid chat_id
        echo "Invalid Chat ID.";
        exit();
    }
}


// Accept chat invitation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_invitation'])) {
    $invitation_id = $_POST['invitation_id'];
    
    $sql_accept_invitation = "UPDATE chat_invitations SET status = 'accepted' WHERE invitation_id = :invitation_id";
    $stmt_accept_invitation = $pdo->prepare($sql_accept_invitation);
    $stmt_accept_invitation->execute(['invitation_id' => $invitation_id]);

    header("Location: group_chats.php");
    exit();
}

// Reject chat invitation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_invitation'])) {
    $invitation_id = $_POST['invitation_id'];
    
    $sql_reject_invitation = "DELETE FROM chat_invitations WHERE invitation_id = :invitation_id";
    $stmt_reject_invitation = $pdo->prepare($sql_reject_invitation);
    $stmt_reject_invitation->execute(['invitation_id' => $invitation_id]);

    header("Location: group_chats.php");
    exit();
}
// Fetch pending invitations
$sql_pending_invitations = "SELECT ci.*, gc.chat_name, u.user_name AS inviter_name 
                            FROM chat_invitations ci
                            JOIN group_chats gc ON ci.chat_id = gc.chat_id
                            JOIN users u ON ci.inviter_user_id = u.user_id
                            WHERE ci.invited_user_id = :user_id AND ci.status = 'pending'";
$stmt_pending_invitations = $pdo->prepare($sql_pending_invitations);
$stmt_pending_invitations->execute(['user_id' => $user_id]);
$pending_invitations = $stmt_pending_invitations->fetchAll(PDO::FETCH_ASSOC);

// Fetch users for invitation excluding already invited or participating users
$sql_exclude_users = "SELECT u.user_id, u.user_name FROM users u
                      LEFT JOIN chat_invitations ci ON u.user_id = ci.invited_user_id AND ci.chat_id = :chat_id
                      LEFT JOIN chat_invitations ci2 ON u.user_id = ci2.invited_user_id AND ci2.chat_id = :chat_id2 AND ci2.status = 'accepted'
                      WHERE u.user_id != :user_id AND ci.invited_user_id IS NULL AND ci2.invited_user_id IS NULL";
$stmt_exclude_users = $pdo->prepare($sql_exclude_users);
$stmt_exclude_users->execute(['chat_id' => $chat_id, 'chat_id2' => $chat_id, 'user_id' => $user_id]);
$available_users = $stmt_exclude_users->fetchAll(PDO::FETCH_ASSOC);

// Display chat list
echo "グループチャット一覧:";
echo "<ul>";
foreach ($chats as $chat_item) {
    echo "<li><a href='group_chats.php?chat_id=" . htmlspecialchars($chat_item['chat_id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($chat_item['chat_name'], ENT_QUOTES, 'UTF-8') . "</a></li>";
}
echo "</ul>";

// Display chat messages if chat_id is provided
if ($chat_id !== null) {
    echo "<h2>チャット名: " . htmlspecialchars($chat['chat_name'], ENT_QUOTES, 'UTF-8') . "</h2>";
    echo "<h3>チャット詳細: " . htmlspecialchars($chat['chat_details'], ENT_QUOTES, 'UTF-8') . "</h3>";
    
    echo "<h3>チャットメッセージ:</h3>";
    echo "<ul id='chat_messages'>";
    foreach ($messages as $message) {
        $formatted_time = date('Y-m-d H:i:s', strtotime($message['created_at']));
        echo "<li><strong>" . htmlspecialchars($message['user_name'], ENT_QUOTES, 'UTF-8') . ":</strong> " . htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8') . " <small>(" . htmlspecialchars($formatted_time, ENT_QUOTES, 'UTF-8') . ")</small></li>";
    }
    echo "</ul>";
    
    echo "
    <form id='message_form'>
        <textarea id='message' name='message' required></textarea>
        <input type='hidden' name='chat_id' value='" . htmlspecialchars($chat_id, ENT_QUOTES, 'UTF-8') . "'>
        <button type='button' id='submit_message'>送信</button>
    </form>
    ";
    
    // Invite user form
    echo "
    <h3>ユーザーを招待</h3>
    <form method='POST' action='group_chats.php'>
        <input type='hidden' name='chat_id' value='" . htmlspecialchars($chat_id, ENT_QUOTES, 'UTF-8') . "'>
        <label for='invite_user_id'>ユーザーを選択:</label>
        <select name='invite_user_id' required>
            <option value=''>選択してください</option>";
            foreach ($available_users as $user) {
                echo "<option value='" . htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($user['user_name'], ENT_QUOTES, 'UTF-8') . "</option>";
            }
            echo "</select>
        <button type='submit'>招待</button>
    </form>
    ";
    
    // Leave chat form
    echo "
    <form method='POST' action='group_chats.php'>
        <input type='hidden' name='chat_id' value='" . htmlspecialchars($chat_id, ENT_QUOTES, 'UTF-8') . "'>
        <button type='submit' name='leave_chat'>チャットを退会</button>
    </form>
    ";
}

// New group chat creation form
echo "
<h2>新しいグループチャットを作成</h2>
<form method='POST' action='group_chats.php'>
    <label for='chat_name'>チャット名:</label>
    <input type='text' id='chat_name' name='chat_name' required>
    <label for='chat_details'>チャット詳細:</label>
    <textarea id='chat_details' name='chat_details' required></textarea>
    <button type='submit'>作成</button>
</form>
";

// Pending invitations
if (!empty($pending_invitations)) {
    echo "<h3>保留中の招待</h3>";
    echo "<ul>";
    foreach ($pending_invitations as $invitation) {
        echo "<li>" . htmlspecialchars($invitation['chat_name'], ENT_QUOTES, 'UTF-8') . " (招待者: " . htmlspecialchars($invitation['inviter_name'], ENT_QUOTES, 'UTF-8') . ")";
        echo "
        <form method='POST' action='group_chats.php' style='display:inline;'>
            <input type='hidden' name='invitation_id' value='" . htmlspecialchars($invitation['invitation_id'], ENT_QUOTES, 'UTF-8') . "'>
            <button type='submit' name='accept_invitation'>参加</button>
        </form>
        <form method='POST' action='group_chats.php' style='display:inline;'>
            <input type='hidden' name='invitation_id' value='" . htmlspecialchars($invitation['invitation_id'], ENT_QUOTES, 'UTF-8') . "'>
            <button type='submit' name='reject_invitation'>拒否</button>
        </form>
        </li>";
    }
    echo "</ul>";
}

echo "
<a href='../top.html'>戻る</a>
";

?>

<!-- JavaScript for sending messages -->
<script>
document.getElementById('submit_message').addEventListener('click', function() {
    var messageForm = document.getElementById('message_form');
    var messageInput = document.getElementById('message');
    var chatMessages = document.getElementById('chat_messages');
    
    var formData = new FormData(messageForm);
    
    fetch('send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var newMessage = document.createElement('li');
            var formattedTime = new Date(data.created_at).toLocaleString(); // Format the timestamp
            
            newMessage.innerHTML = "<strong>" + data.user_name + ":</strong> " + data.message + " <small>(" + formattedTime + ")</small>";
            chatMessages.appendChild(newMessage);
            messageInput.value = '';
        } else {
            alert('メッセージの送信に失敗しました。');
        }
    })
    .catch(error => {
        console.error('エラー:', error);
    });
});
</script>
