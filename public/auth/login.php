<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">MSG</h1>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="error-message">
                    <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <p class="success-message">
                    登録が完了しました！
                </p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form action="/auth/login_process" method="post">
                <div class="form-group">
                    <input type="text" name="username" placeholder="ユーザー名" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="パスワード" required>
                </div>
                <button type="submit" class="btn-primary">ログイン</button>
            </form>

            <p class="auth-link">アカウントをお持ちでない方は<a href="/auth/register">新規登録</a></p>
        </div>
    </div>
</body>
</html>
