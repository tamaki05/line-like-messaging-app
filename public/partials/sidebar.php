<div class="sidebar">
    <h2 class="sidebar-title">MSG</h2>
    <p class="sidebar-username">
        <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>
    </p>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="/user_list">ユーザー一覧</a></li>
            <li><a href="/room_list">チャット一覧</a></li>
            <li><a href="/auth/logout">ログアウト</a></li>
            <li><a href="/auth/confirm_delete">退会</a></li>
        </ul>
    </nav>
</div>
