<?php
session_start();

// ログインしていない場合はログイン画面へ
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ホーム</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="layout">
        <!-- サイドバー -->
        <div class="sidebar">
            <h2>MSG</h2>

            <p>ログイン中：<?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></p>

            <ul>
                <li><a href="#">ユーザー一覧</a></li>
            </ul>

            <a href="/auth/logout.php" class="logout-btn">ログアウト</a>
        </div>

        <!-- メイン -->
        <div class="main">
            <div class="content-card">
                <h1 class="page-title">ユーザー一覧</h1>

                <p>ここにユーザーリストを表示していく</p>
            </div>
        </div>
    </div>
</body>
</html>
