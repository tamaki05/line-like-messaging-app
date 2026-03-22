<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login');
    exit;
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../src/Model/Room.php';
require_once __DIR__ . '/../src/Model/Message.php';

// テキスト内のURLをリンクに変換する関数
function linkify(string $text): string {
    $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $linked  = preg_replace(
        '/(https?:\/\/[^\s<>"\']+)/u',
        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
        $escaped
    );
    return nl2br($linked);
}

$roomId        = (int)($_GET['id'] ?? 0);
$currentUserId = (int)$_SESSION['user_id'];

if (!$roomId) {
    header('Location: ../chat_list');
    exit;
}

$roomModel = new Room();
$room = $roomModel->findById($roomId);

// ルームが存在しない、または自分が参加者でない場合は弾く
if (!$room || !in_array($currentUserId, [(int)$room['created_user_id'], (int)$room['invited_user_id']])) {
    header('Location: ../chat_list');
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
    <link rel="stylesheet" href="../assets/css/style.css">
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
                                <form action="../messages/delete" method="post" class="delete-form"
                                      onsubmit="return confirm('このメッセージを削除しますか？')">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                    <input type="hidden" name="room_id" value="<?= $roomId ?>">
                                    <button type="submit" class="btn-delete-message">削除</button>
                                </form>
                            <?php endif; ?>
                            <div class="message-bubble">
                                <?php if ($message['content']): ?>
                                    <p><?= linkify($message['content']) ?></p>
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
                <form action="../messages/send" method="post" enctype="multipart/form-data" id="message-form">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="room_id" value="<?= $roomId ?>">
                    <div class="chat-input-row">
                        <input type="text" name="content" id="content-input" placeholder="メッセージを入力" autocomplete="off">
                        <label class="btn-attach" for="image-input">📎</label>
                        <input type="file" name="image" id="image-input" accept="image/jpeg,image/png" style="display:none;">
                        <button type="submit" class="btn-send">送信</button>
                    </div>
                    <div id="image-preview-area"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chat-messages');
        const roomId        = <?= $roomId ?>;
        const currentUserId = <?= $currentUserId ?>;
        const csrfToken     = '<?= csrf_token() ?>';

        // 最下部にスクロール（画像の読み込み完了後に実行）
        window.addEventListener('load', () => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });

        // 最後のメッセージ日時
        let lastCreatedAt = <?= !empty($messages) ? '"' . end($messages)['created_at'] . '"' : '"' . date('Y-m-d H:i:s') . '"' ?>;

        // URLをリンクに変換
        function linkify(text) {
            const escaped = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            return escaped.replace(/(https?:\/\/[^\s<>"']+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>')
                          .replace(/\n/g, '<br>');
        }

        // メッセージをDOMに追加
        function appendMessage(msg) {
            const isMine = msg.user_id === currentUserId;
            const row = document.createElement('div');
            row.className = 'message-row ' + (isMine ? 'mine' : 'theirs');

            let inner = '<div class="message-with-actions">';

            if (isMine) {
                inner += `<form action="../messages/delete" method="post" class="delete-form"
                               onsubmit="return confirm('このメッセージを削除しますか？')">
                            <input type="hidden" name="csrf_token" value="${csrfToken}">
                            <input type="hidden" name="message_id" value="${msg.id}">
                            <input type="hidden" name="room_id" value="${roomId}">
                            <button type="submit" class="btn-delete-message">削除</button>
                          </form>`;
            } else {
                row.innerHTML = `<span class="message-sender">${msg.sender_username}</span>`;
            }

            inner += '<div class="message-bubble">';
            if (msg.content) inner += `<p>${linkify(msg.content)}</p>`;
            if (msg.image_path) inner += `<img src="${msg.image_path}" class="message-image" alt="画像">`;
            inner += '</div></div>';
            inner += `<span class="message-time">${msg.time}</span>`;

            row.innerHTML += inner;
            chatMessages.appendChild(row);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // ポーリング
        const poll = async () => {
            try {
                const res = await fetch(`<?= $base ?>api/messages.php?room_id=${roomId}&last_created_at=${encodeURIComponent(lastCreatedAt)}`);
                if (!res.ok) throw new Error(`HTTP error: ${res.status}`);
                const messages = await res.json();
                if (Array.isArray(messages) && messages.length > 0) {
                    messages.forEach(appendMessage);
                    lastCreatedAt = messages[messages.length - 1].created_at;
                }
            } catch (e) {
                console.error('ポーリングエラー:', e);
            } finally {
                setTimeout(poll, document.hidden ? 10000 : 2000);
            }
        };
        poll();

        // 画像プレビュー
        document.getElementById('image-input').addEventListener('change', function () {
            const preview = document.getElementById('image-preview-area');
            preview.innerHTML = '';
            if (this.files && this.files[0]) {
                const wrapper = document.createElement('div');
                wrapper.className = 'image-preview-wrapper';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(this.files[0]);
                img.className = 'image-preview';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'image-preview-remove';
                removeBtn.textContent = '×';
                removeBtn.addEventListener('click', function () {
                    document.getElementById('image-input').value = '';
                    preview.innerHTML = '';
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                preview.appendChild(wrapper);
            }
        });
    </script>
</body>
</html>
