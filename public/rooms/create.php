<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

require_once __DIR__ . '/../../src/Model/Room.php';

$invitedUserId = (int)($_GET['invited_user_id'] ?? 0);
$currentUserId = (int)$_SESSION['user_id'];

// 不正なリクエストは弾く
if (!$invitedUserId || $invitedUserId === $currentUserId) {
    header('Location: /user_list');
    exit;
}

$roomModel = new Room();

// 既存ルームがあればそちらへ、なければ作成
$room = $roomModel->findByUsers($currentUserId, $invitedUserId);

if ($room) {
    header('Location: /rooms/chat?id=' . $room['id']);
} else {
    $roomId = $roomModel->create($currentUserId, $invitedUserId);
    header('Location: /rooms/chat?id=' . $roomId);
}
exit;
