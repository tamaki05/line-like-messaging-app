<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

require_once __DIR__ . '/../../src/Model/Room.php';
require_once __DIR__ . '/../../src/Model/Message.php';

$roomId        = (int)($_GET['id'] ?? 0);
$currentUserId = (int)$_SESSION['user_id'];

if (!$roomId) {
    header('Location: /top');
    exit;
}

$roomModel = new Room();
$room = $roomModel->findById($roomId);

// ルームが存在しない、または自分が参加者でない場合は弾く
if (!$room || !in_array($currentUserId, [(int)$room['created_user_id'], (int)$room['invited_user_id']])) {
    header('Location: /top');
    exit;
}

// トーク相手のユーザー名を取得
$partnerUsername = $currentUserId === (int)$room['created_user_id']
    ? $room['invited_username']
    : $room['created_username'];

$messageModel = new Message();
$messages = $messageModel->findByRoomId($roomId);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($partnerUsername, ENT_QUOTES, 'UTF-8') ?> | MSG</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="chat-container">
            <!-- チャットヘッダー -->
            <div class="chat-header">
                <span class="chat-partner-name">
                    <?= htmlspecialchars($partnerUsername, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <!-- メッセージ一覧 -->
            <div class="chat-messages" id="chat-messages">
                <?php foreach ($messages as $message): ?>
                    <?php $isMine = (int)$message['user_id'] === $currentUserId; ?>
                    <div class="message-row <?= $isMine ? 'mine' : 'theirs' ?>">
                        <?php if (!$isMine): ?>
                            <span class="message-sender">
                                <?= htmlspecialchars($message['sender_username'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                        <div class="message-with-actions">
                            <?php if ($isMine): ?>
                                <form action="/messages/delete" method="post" class="delete-form"
                                      onsubmit="return confirm('このメッセージを削除しますか？')">
                                    <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                    <input type="hidden" name="room_id" value="<?= $roomId ?>">
                                    <button type="submit" class="btn-delete-message">削除</button>
                                </form>
                            <?php endif; ?>
                            <div class="message-bubble">
                                <?php if ($message['content']): ?>
                                    <p><?= nl2br(htmlspecialchars($message['content'], ENT_QUOTES, 'UTF-8')) ?></p>
                                <?php endif; ?>
                                <?php if ($message['image_path']): ?>
                                    <img src="<?= htmlspecialchars($message['image_path'], ENT_QUOTES, 'UTF-8') ?>" class="message-image" alt="画像">
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="message-time">
                            <?= date('H:i', strtotime($message['created_at'])) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- メッセージ入力 -->
            <div class="chat-input-area">
                <form action="/messages/send" method="post" enctype="multipart/form-data" id="message-form">
                    <input type="hidden" name="room_id" value="<?= $roomId ?>">
                    <div class="chat-input-row">
                        <input type="text" name="content" id="content-input" placeholder="メッセージを入力" autocomplete="off">
                        <label class="btn-attach" for="image-input">📎</label>
                        <input type="file" name="image" id="image-input" accept="image/*" style="display:none;">
                        <button type="submit" class="btn-send">送信</button>
                    </div>
                    <div id="image-preview-area"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 最下部にスクロール
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // 画像プレビュー
        document.getElementById('image-input').addEventListener('change', function () {
            const preview = document.getElementById('image-preview-area');
            preview.innerHTML = '';
            if (this.files && this.files[0]) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(this.files[0]);
                img.className = 'image-preview';
                preview.appendChild(img);
            }
        });
    </script>
</body>
</html>
