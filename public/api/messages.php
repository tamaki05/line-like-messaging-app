<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

require_once __DIR__ . '/../../src/Model/Room.php';
require_once __DIR__ . '/../../src/Model/Message.php';

$roomId        = (int)($_GET['room_id'] ?? 0);
$lastCreatedAt = $_GET['last_created_at'] ?? '';
$currentUserId = (int)$_SESSION['user_id'];

if (!$roomId || !$lastCreatedAt) {
    echo json_encode(['error' => 'invalid params']);
    exit;
}

// 参加者チェック
$roomModel = new Room();
$room = $roomModel->findById($roomId);

if (!$room || !in_array($currentUserId, [(int)$room['created_user_id'], (int)$room['invited_user_id']])) {
    echo json_encode(['error' => 'forbidden']);
    exit;
}

$messageModel = new Message();
$messages = $messageModel->findNewMessages($roomId, $lastCreatedAt);

// フロントで使いやすい形に整形
$result = array_map(fn($m) => [
    'id'              => $m['id'],
    'user_id'         => (int)$m['user_id'],
    'sender_username' => $m['sender_username'],
    'content'         => $m['content'],
    'image_path'      => $m['image_path'],
    'created_at'      => $m['created_at'],
    'time'            => date('H:i', strtotime($m['created_at'])),
], $messages);

echo json_encode($result);
