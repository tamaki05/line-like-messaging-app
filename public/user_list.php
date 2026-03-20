<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

require_once __DIR__ . '/../src/Model/User.php';
$userModel = new User();
$users = array_filter(
    $userModel->findAll(),
    fn($u) => (int)$u['id'] !== (int)$_SESSION['user_id']
);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー一覧 | MSG</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="main">
            <div class="content-card">
                <h1 class="page-title">ユーザー一覧</h1>

                <?php if (empty($users)): ?>
                    <p class="empty-message">他のユーザーがいません。</p>
                <?php else: ?>
                    <ul class="user-list">
                        <?php foreach ($users as $user): ?>
                            <li class="user-list-item">
                                <span class="user-name">
                                    <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <form action="/rooms/create" method="post">
                                    <input type="hidden" name="invited_user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn-talk">トークする</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
