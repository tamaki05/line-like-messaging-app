<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

require_once __DIR__ . '/../../src/Model/Message.php';

$messageId     = (int)($_POST['message_id'] ?? 0);
$roomId        = (int)($_POST['room_id'] ?? 0);
$currentUserId = (int)$_SESSION['user_id'];

if (!$messageId || !$roomId) {
    header('Location: /top');
    exit;
}

$messageModel = new Message();
$message = $messageModel->findById($messageId);

// メッセージが存在しない、または自分のメッセージでない場合は弾く
if (!$message || (int)$message['user_id'] !== $currentUserId) {
    header('Location: /rooms/chat?id=' . $roomId);
    exit;
}

// 画像ファイルも削除
if ($message['image_path']) {
    $filePath = __DIR__ . '/../' . ltrim($message['image_path'], '/');
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

$messageModel->delete($messageId);

header('Location: /rooms/chat?id=' . $roomId);
exit;
