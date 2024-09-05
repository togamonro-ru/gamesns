<?php
require '../db_connect.php';

// `tournament_id`を取得
$tournament_id = isset($_GET['tournament_id']) ? intval($_GET['tournament_id']) : 0;

// `tournament_id`が無効な場合や不正な場合はエラーメッセージを表示
if ($tournament_id <= 0) {
    die('無効な大会IDです。');
}

// データベースから大会情報を取得
$stmt = $pdo->prepare("SELECT * FROM localtournaments WHERE tournament_id = ? AND deleted_flag = 0");
$stmt->execute([$tournament_id]);
$localtournament = $stmt->fetch(PDO::FETCH_ASSOC);

// 大会情報が見つからない場合はエラーメッセージを表示
if (!$localtournament) {
    die('大会が見つかりません。');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>大会詳細</title>
    <!-- 修正した style.css のパスを指定 -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .button {
            padding: 10px 15px;
            border: none;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($localtournament['name'], ENT_QUOTES, 'UTF-8'); ?> の詳細</h1>
        <p><strong>大会名:</strong> <?php echo htmlspecialchars($localtournament['name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>詳細説明:</strong> <?php echo htmlspecialchars($localtournament['description'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>募集開始日時:</strong> <?php echo htmlspecialchars($localtournament['start_date'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>募集終了日時:</strong> <?php echo htmlspecialchars($localtournament['end_date'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>大会開始日時:</strong> <?php echo htmlspecialchars($localtournament['Tournament_starts'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>最大参加人数:</strong> <?php echo htmlspecialchars($localtournament['people'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>作成日:</strong> <?php echo htmlspecialchars($localtournament['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>更新日:</strong> <?php echo htmlspecialchars($localtournament['updated_at'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><a href="tournament.php" class="button">大会一覧に戻る</a></p>
        <p><a href="edit_tournament.php?tournament_id=<?php echo htmlspecialchars($localtournament['tournament_id'], ENT_QUOTES, 'UTF-8'); ?>" class="button">この大会を編集</a></p>
        <form action="delete_tournament.php" method="post" style="display:inline;">
            <input type="hidden" name="tournament_id" value="<?php echo htmlspecialchars($localtournament['tournament_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" onclick="return confirm('本当に削除してもよろしいですか？');" class="button">大会を削除</button>
        </form>
    </div>
</body>
</html>