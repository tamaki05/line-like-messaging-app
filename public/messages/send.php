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
    header('Location: /top');
    exit;
}

// 自分がそのルームの参加者か確認
$roomModel = new Room();
$room = $roomModel->findById($roomId);

if (!$room || !in_array($currentUserId, [(int)$room['created_user_id'], (int)$room['invited_user_id']])) {
    header('Location: /top');
    exit;
}

// テキストも画像もない場合は何もしない
if ($content === '') {
    header('Location: /rooms/chat?id=' . $roomId);
    exit;
}

$messageModel = new Message();
$messageModel->create($roomId, $currentUserId, $content);

// ルームのupdated_atを更新
$roomModel->touch($roomId);

header('Location: /rooms/chat?id=' . $roomId);
exit;
