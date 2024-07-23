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

        if (move_uploaded_file($tmp_name, $profile_image_path)) {
            // 画像がアップロードされた場合は、パスを更新
            $profile_image = $profile_image_path;
        }
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
        header("Location: welcome.php");
        exit();
    } else {
        echo "データベースの更新に失敗しました。<br>";
        print_r($stmt->errorInfo()); // SQLエラーの詳細を表示
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
    <link rel="stylesheet" href="../css/style_2.css"> <!-- CSSのリンク -->
</head>
<body>
    <a href="welcome.php" class="back-button">戻る</a> <!-- 戻るボタン -->
    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <h1>プロフィール編集</h1>
        <div class="form-group">
            <label for="display_name">表示名:</label>
            <input type="text" id="display_name" name="display_name" value="<?php echo htmlspecialchars($profile['display_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="form-group">
            <label for="bio">自己紹介:</label>
            <textarea id="bio" name="bio" rows="6" cols="50" required><?php echo htmlspecialchars($profile['bio'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="form-group">
            <label for="profile_image">プロフィール画像:</label>
            <input type="file" id="profile_image" name="profile_image">
            <?php if (!empty($profile['profile_image'])): ?>
                <img src="<?php echo htmlspecialchars($profile['profile_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="プロフィール画像" style="max-width: 200px;">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <input type="submit" value="保存" class="submit-button">
        </div>
    </form>
</body>
</html>
