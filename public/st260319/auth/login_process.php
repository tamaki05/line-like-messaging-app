<?php
session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../src/Model/User.php';
$userModel = new User();

csrf_verify();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// バリデーション
if (!$username || !$password) {
    $_SESSION['error'] = 'ユーザー名とパスワードを入力してください';
    header('Location: ' . $base . 'auth/login');
    exit;
}

// ユーザー取得
$user = $userModel->findByUsername($username);

// ユーザーが存在しない or パスワード不一致
if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'ユーザー名またはパスワードが正しくありません';
    header('Location: ' . $base . 'auth/login');
    exit;
}

// ログイン成功（セッション固定攻撃対策）
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

header('Location: ' . $base . 'chat_list');
exit;
