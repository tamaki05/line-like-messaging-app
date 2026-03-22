<?php
session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../src/Model/User.php';
$userModel = new User();

csrf_verify();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base . 'auth/login');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// 論理削除
$userModel->softDelete($userId);

// セッション削除（ログアウト）
session_destroy();

// 完了メッセージ
session_start();
$_SESSION['success'] = '退会が完了しました';

header('Location: ' . $base . 'auth/login');
exit;
