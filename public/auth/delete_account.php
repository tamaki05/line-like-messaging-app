<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

require_once __DIR__ . '/../../src/Model/User.php';
$userModel = new User();

$userId = (int)$_SESSION['user_id'];

// 論理削除
$userModel->softDelete($userId);

// セッション削除（ログアウト）
session_destroy();

// 完了メッセージ
session_start();
$_SESSION['success'] = '退会が完了しました';

header('Location: /auth/login');
exit;
