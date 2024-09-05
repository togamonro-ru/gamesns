<?php
//tornament.php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
require '../db_connect.php';

// データベースから大会情報を取得
$stmt = $pdo->query("SELECT * FROM localtournaments WHERE deleted_flag = 0");
$localtournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>大会一覧</title>
    <!-- 修正した style.css のパスを指定 -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>大会一覧</h1>
    <p><a href="create_tournament.php" class="button">新規大会を作成</a></p>  <!-- 新規作成ページへのリンク -->

    <div class="border">
    <table class="border">
        <tr>
            <th>大会名</th>
            <th>詳細説明</th>
            <th>募集開始日時</th>
            <th>募集終了日時</th>
            <th>大会開始日時</th>
            <th>最大参加人数</th>
            <th>作成日</th>
            <th>更新日</th>
            <th>アクション</th>
        </tr>
        <?php foreach ($localtournaments as $localtournament): ?>
        <tr>
            <td><?php echo htmlspecialchars($localtournament['name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($localtournament['description'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($localtournament['start_date'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($localtournament['end_date'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($localtournament['Tournament_starts'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($localtournament['people'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($localtournament['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($localtournament['updated_at'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>
                <a href="view_tournament.php?tournament_id=<?php echo htmlspecialchars($localtournament['tournament_id'], ENT_QUOTES, 'UTF-8'); ?>" class="button">詳細</a>
                <a href="edit_tournament.php?tournament_id=<?php echo htmlspecialchars($localtournament['tournament_id'], ENT_QUOTES, 'UTF-8'); ?>" class="button">編集</a>
                <form action="delete_tournament.php" method="post" style="display:inline;">
                    <input type="hidden" name="tournament_id" value="<?php echo htmlspecialchars($localtournament['tournament_id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" onclick="return confirm('本当に削除してもよろしいですか？');" class="button">削除</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
</body>
</html>
