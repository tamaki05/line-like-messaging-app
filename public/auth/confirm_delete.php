<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>退会確認</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="main">
            <div class="content-card">
                <h1 class="page-title">退会確認</h1>

                <p style="margin-bottom:20px; color:#e00;">
                    本当に退会しますか？
                </p>

                <form action="/auth/delete_account.php" method="post">
                    <button type="submit" class="btn-danger">退会する</button>
                </form>

                <p style="margin-top:16px;">
                    <a href="/top.php">キャンセル</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
