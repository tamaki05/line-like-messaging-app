<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

require_once __DIR__ . '/../../src/Model/Room.php';
require_once __DIR__ . '/../../src/Model/Message.php';

$roomId        = (int)($_POST['room_id'] ?? 0);
$content       = trim($_POST['content'] ?? '');
$currentUserId = (int)$_SESSION['user_id'];

if (!$roomId) {
    header('Location: /chat_list');
    exit;
}

// 自分がそのルームの参加者か確認
$roomModel = new Room();
$room = $roomModel->findById($roomId);

if (!$room || !in_array($currentUserId, [(int)$room['created_user_id'], (int)$room['invited_user_id']])) {
    header('Location: /chat_list');
    exit;
}

// 画像アップロード処理
$imagePath = null;
if (!empty($_FILES['image']['tmp_name'])) {
    $file     = $_FILES['image'];
    $mimeType = mime_content_type($file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mimeType, $allowedTypes)) {
        $_SESSION['error'] = '画像はJPEG・PNG・GIF・WebPのみ対応しています';
        header('Location: /chats/show?id=' . $roomId);
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = '画像サイズは5MB以内にしてください';
        header('Location: /chats/show?id=' . $roomId);
        exit;
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $ext;
    $destPath = __DIR__ . '/../uploads/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        $imagePath = '/uploads/' . $filename;
    }
}

// テキストも画像もない場合は何もしない
if ($content === '' && $imagePath === null) {
    header('Location: /chats/show?id=' . $roomId);
    exit;
}

$messageModel = new Message();

if ($imagePath) {
    $messageModel->createWithImage($roomId, $currentUserId, $content, $imagePath);
} else {
    $messageModel->create($roomId, $currentUserId, $content);
}

// ルームのupdated_atを更新
$roomModel->touch($roomId);

header('Location: /chats/show?id=' . $roomId);
exit;
