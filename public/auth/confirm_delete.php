<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
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

                <p style="margin-bottom:28px; color:#e00; text-align:center;">
                    本当に退会しますか？
                </p>

                <div class="confirm-actions">
                    <a href="/top" class="btn-cancel">キャンセル</a>
                    <form action="/auth/delete_account" method="post">
                        <button type="submit" class="btn-danger-sm">退会する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
