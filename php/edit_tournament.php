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

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $tournament_starts = $_POST['tournament_starts'];
    $people = intval($_POST['people']);

    // バリデーション
    if (empty($name) || empty($start_date) || empty($end_date) || empty($tournament_starts)) {
        $error = 'すべての必須項目を入力してください。';
    } else {
        // データベースの大会情報を更新
        $stmt = $pdo->prepare("
            UPDATE localtournaments 
            SET name = ?, description = ?, start_date = ?, end_date = ?, Tournament_starts = ?, people = ?, updated_at = NOW()
            WHERE tournament_id = ? AND deleted_flag = 0
        ");
        $result = $stmt->execute([$name, $description, $start_date, $end_date, $tournament_starts, $people, $tournament_id]);

        if ($result) {
            // 成功した場合は大会一覧ページにリダイレクト
            header('Location: view_tournament.php?tournament_id=' . $tournament_id);
            exit;
        } else {
            $error = '大会の更新に失敗しました。';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>大会編集</title>
    <style>
        .container {
            max-width: 600px;
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
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>大会情報の編集</h1>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form action="edit_tournament.php?tournament_id=<?php echo htmlspecialchars($tournament_id, ENT_QUOTES, 'UTF-8'); ?>" method="post">
            <p><label for="name">大会名:</label><br>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($localtournament['name'], ENT_QUOTES, 'UTF-8'); ?>" required></p>
            
            <p><label for="description">詳細説明:</label><br>
            <textarea id="description" name="description"><?php echo htmlspecialchars($localtournament['description'], ENT_QUOTES, 'UTF-8'); ?></textarea></p>
            
            <p><label for="start_date">募集開始日時:</label><br>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($localtournament['start_date'], ENT_QUOTES, 'UTF-8'); ?>" required></p>
            
            <p><label for="end_date">募集終了日時:</label><br>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($localtournament['end_date'], ENT_QUOTES, 'UTF-8'); ?>" required></p>
            
            <p><label for="tournament_starts">大会開始日時:</label><br>
            <input type="datetime-local" id="tournament_starts" name="tournament_starts" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($localtournament['Tournament_starts'])), ENT_QUOTES, 'UTF-8'); ?>" required></p>
            
            <p><label for="people">最大参加人数:</label><br>
            <input type="number" id="people" name="people" value="<?php echo htmlspecialchars($localtournament['people'], ENT_QUOTES, 'UTF-8'); ?>" required></p>
            
            <p><button type="submit" class="button">更新</button></p>
        </form>

        <p><a href="view_tournament.php?tournament_id=<?php echo htmlspecialchars($tournament_id, ENT_QUOTES, 'UTF-8'); ?>" class="button">詳細ページに戻る</a></p>
    </div>
</body>
</html>
