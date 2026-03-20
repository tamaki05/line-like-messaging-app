<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

require_once __DIR__ . '/../src/Model/Room.php';

$currentUserId = (int)$_SESSION['user_id'];
$roomModel     = new Room();

$rooms = $roomModel->findByUserId($currentUserId);

// トーク相手の名前を設定
foreach ($rooms as &$room) {
    $room['partner_username'] = $currentUserId === (int)$room['created_user_id']
        ? $room['invited_username']
        : $room['created_username'];
}
unset($room);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>チャット一覧 | MSG</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="main">
            <div class="content-card">
                <h1 class="page-title">チャット一覧</h1>

                <?php if (empty($rooms)): ?>
                    <p class="empty-message">まだトークがありません。</p>
                <?php else: ?>
                    <ul class="room-list">
                        <?php foreach ($rooms as $room): ?>
                            <li class="room-list-item">
                                <a href="/chats/show?id=<?= $room['id'] ?>" class="room-link">
                                    <div class="room-info">
                                        <span class="room-partner-name">
                                            <?= htmlspecialchars($room['partner_username'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="room-latest-message">
                                            <?php if ($room['latest_created_at'] !== null): ?>
                                                <?php if ($room['latest_image_path'] && !$room['latest_content']): ?>
                                                    📷 画像
                                                <?php else: ?>
                                                    <?= htmlspecialchars(mb_strimwidth($room['latest_content'], 0, 30, '…'), ENT_QUOTES, 'UTF-8') ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="color:#bbb;">メッセージなし</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <span class="room-updated-at">
                                        <?php if ($room['latest_created_at'] !== null): ?>
                                            <?= date('m/d H:i', strtotime($room['latest_created_at'])) ?>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
