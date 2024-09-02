<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require_once '../db_connect.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT p.*, u.user_name FROM profiles p LEFT JOIN users u ON p.user_id = u.user_id WHERE p.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    $profile = [
        'user_name' => '',
        'bio' => '',
        'profile_image' => ''
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['user_name'];
    $bio = $_POST['bio'];
    
    $profile_image = $profile['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $tmp_name = $_FILES['profile_image']['tmp_name'];
        $file_name = basename($_FILES['profile_image']['name']);
        $profile_image_path = $upload_dir . $file_name;

        if (move_uploaded_file($tmp_name, $profile_image_path)) {
            $profile_image = $profile_image_path;
        }
    }

    $sql = "UPDATE users SET user_name = :user_name WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_name' => $user_name,
        'user_id' => $user_id
    ]);

    if (isset($profile['user_name']) && $profile['user_name'] !== '') {
        $sql = "UPDATE profiles SET bio = :bio, profile_image = :profile_image, updated_at = current_timestamp() WHERE user_id = :user_id";
    } else {
        $sql = "INSERT INTO profiles (user_id, bio, profile_image, created_at, updated_at, delete_flag) VALUES (:user_id, :bio, :profile_image, current_timestamp(), current_timestamp(), 0)";
    }
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        'user_id' => $user_id, 
        'bio' => $bio, 
        'profile_image' => $profile_image
    ]);
    
    if ($success) {
        header("Location: ../top.html");
        exit();
    } else {
        echo "データベースの更新に失敗しました。<br>";
        print_r($stmt->errorInfo());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
    <link rel="stylesheet" href="../css/style_2.css">
</head>
<body>
    <a href="../top.html" class="back-button">戻る</a>
    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <h1>プロフィール編集</h1>
        <div class="form-group">
            <label for="user_name">表示名:</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($profile['user_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
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
        <div class="form-group">
            <a href="./delete_account.php" class="delete-account-button" onclick="return confirm('本当に退会しますか？');">退会</a>
        </div>
    </form>
</body>
</html>
