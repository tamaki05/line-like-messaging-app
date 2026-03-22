<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>退会確認</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                    <a href="../chat_list" class="btn-cancel">キャンセル</a>
                    <form action="delete_account" method="post">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn-danger-sm">退会する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
