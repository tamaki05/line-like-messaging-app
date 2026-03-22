<?php
session_start();

require_once __DIR__ . '/../src/Model/User.php';
$userModel = new User();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// バリデーション
if (!$username || !$password || !$password_confirm) {
    $_SESSION['error'] = 'すべての項目を入力してください';
    header('Location: register');
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $_SESSION['error'] = 'ユーザー名は英数字とアンダースコアのみ使用できます';
    header('Location: register');
    exit;
}

if (mb_strlen($password) < 8) {
    $_SESSION['error'] = 'パスワードは8文字以上で入力してください';
    header('Location: register');
    exit;
}

if ($password !== $password_confirm) {
    $_SESSION['error'] = 'パスワードが一致しません';
    header('Location: register');
    exit;
}

// ユーザー名重複チェック
if ($userModel->findByUsername($username)) {
    $_SESSION['error'] = 'このユーザー名は既に使用されています';
    header('Location: register');
    exit;
}

// 登録処理
$userModel->create($username, $password);

$_SESSION['success'] = '登録が完了しました！';
header('Location: login');
exit;
