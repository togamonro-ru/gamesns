<?php
require_once '../db_connect.php';

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$title_id = 1;
$images = "";
$maxContentLength = 200;

$content = $_POST['content'];
if (mb_strlen($content) > $maxContentLength) {
    echo json_encode(['status' => 'error', 'message' => '内容が最大許容文字数の ' . $maxContentLength . ' 文字を超えています。']);
    exit();
}

$sql = 'INSERT INTO posts (user_id, title_id, content, images) VALUES (:user_id, :title_id, :content, :images)';

try {
    $targetDir = 'uploads/';
    $imageName = basename($_FILES['img']['name']);
    $targetFilePath = $targetDir . $imageName;

    if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFilePath)) {
        $images = $targetFilePath; // 保存した画像のパスを取得
    }

    $stm = $pdo->prepare($sql);
    $stm->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stm->bindValue(':title_id', $title_id, PDO::PARAM_INT);
    $stm->bindValue(':content', $content, PDO::PARAM_STR);
    $stm->bindValue(':images', $images, PDO::PARAM_STR);
    $stm->execute();

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
