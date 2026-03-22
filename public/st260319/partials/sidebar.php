<?php require_once __DIR__ . '/../config/app.php'; ?>

<div class="sidebar">
    <h2 class="sidebar-title">MSG</h2>
    <p class="sidebar-username">
        <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?> でログイン中
    </p>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="<?= $base ?>user_list">ユーザー一覧</a></li>
            <li><a href="<?= $base ?>chat_list">チャット一覧</a></li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= $base ?>auth/logout">ログアウト</a>
        <a href="<?= $base ?>auth/confirm_delete">退会</a>
    </div>
</div>
