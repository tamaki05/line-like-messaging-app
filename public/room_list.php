<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

require_once __DIR__ . '/../src/Model/Room.php';
require_once __DIR__ . '/../src/Model/Message.php';

$currentUserId = (int)$_SESSION['user_id'];
$roomModel     = new Room();
$messageModel  = new Message();

$rooms = $roomModel->findByUserId($currentUserId);

// 各ルームに最新メッセージを付加
foreach ($rooms as &$room) {
    $latest = $messageModel->findLatestByRoomId($room['id']);
    $room['latest_message'] = $latest ?: null;

    // トーク相手の名前を設定
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
                                <a href="/rooms/chat?id=<?= $room['id'] ?>" class="room-link">
                                    <div class="room-info">
                                        <span class="room-partner-name">
                                            <?= htmlspecialchars($room['partner_username'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="room-latest-message">
                                            <?php if ($room['latest_message']): ?>
                                                <?php if ($room['latest_message']['image_path'] && !$room['latest_message']['content']): ?>
                                                    📷 画像
                                                <?php else: ?>
                                                    <?= htmlspecialchars(mb_strimwidth($room['latest_message']['content'], 0, 30, '…'), ENT_QUOTES, 'UTF-8') ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="color:#bbb;">メッセージなし</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <span class="room-updated-at">
                                        <?php if ($room['latest_message']): ?>
                                            <?= date('m/d H:i', strtotime($room['latest_message']['created_at'])) ?>
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
