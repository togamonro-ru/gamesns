<?php
// profile.php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require_once '../db_connect.php';

$user_id = $_SESSION['user_id'];

// プロフィール情報の取得
$sql = "SELECT * FROM profiles WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// プロフィールが存在しない場合は空の配列を設定
if (!$profile) {
    $profile = [
        'display_name' => '',
        'bio' => '',
        'profile_image' => ''
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // プロフィールの更新
    $display_name = $_POST['display_name'];
    $bio = $_POST['bio'];
    
    // 画像アップロード処理
    $profile_image = $profile['profile_image']; // 現在の画像パス
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // アップロードディレクトリ
        
        // ディレクトリが存在しない場合は作成
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $tmp_name = $_FILES['profile_image']['tmp_name'];
        $file_name = basename($_FILES['profile_image']['name']);
        $profile_image_path = $upload_dir . $file_name;

        // デバッグ用
        echo "tmp_name: " . $tmp_name . "<br>";
        echo "file_name: " . $file_name . "<br>";
        echo "profile_image_path: " . $profile_image_path . "<br>";

        if (move_uploaded_file($tmp_name, $profile_image_path)) {
            // 画像がアップロードされた場合は、パスを更新
            $profile_image = $profile_image_path;
            echo "画像のアップロードに成功しました。<br>";
        } else {
            echo "画像のアップロードに失敗しました。<br>";
        }
    } else {
        echo "ファイルアップロードエラー: " . $_FILES['profile_image']['error'] . "<br>";
    }
    
    if ($profile['display_name'] !== '') {
        // プロフィールが存在する場合は更新
        $sql = "UPDATE profiles SET display_name = :display_name, bio = :bio, profile_image = :profile_image, updated_at = current_timestamp() WHERE user_id = :user_id";
    } else {
        // プロフィールが存在しない場合は新規作成
        $sql = "INSERT INTO profiles (user_id, display_name, bio, profile_image, created_at, updated_at, delete_flag) VALUES (:user_id, :display_name, :bio, :profile_image, current_timestamp(), current_timestamp(), 0)";
    }
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        'user_id' => $user_id, 
        'display_name' => $display_name, 
        'bio' => $bio, 
        'profile_image' => $profile_image
    ]);

    if ($success) {
        echo "データベースの更新に成功しました。<br>";
    } else {
        echo "データベースの更新に失敗しました。<br>";
        print_r($stmt->errorInfo()); // SQLエラーの詳細を表示
    }
    
    // リダイレクトはデバッグが完了してから有効化
    // header("Location: profile.php");
    // exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
</head>
<body>
    <h1>プロフィール編集</h1>
    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <label for="display_name">表示名:</label>
        <input type="text" id="display_name" name="display_name" value="<?php echo htmlspecialchars($profile['display_name'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
        
        <label for="bio">自己紹介:</label>
        <textarea id="bio" name="bio" rows="4" cols="50" required><?php echo htmlspecialchars($profile['bio'], ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>
        
        <label for="profile_image">プロフィール画像:</label>
        <input type="file" id="profile_image" name="profile_image"><br><br>
        <?php if (!empty($profile['profile_image'])): ?>
            <img src="<?php echo htmlspecialchars($profile['profile_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="プロフィール画像" style="max-width: 200px;"><br><br>
        <?php endif; ?>
        
        <input type="submit" value="保存">
    </form>
    <br><a href="welcome.php">戻る</a>
</body>
</html>
