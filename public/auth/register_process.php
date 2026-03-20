<?php
session_start();

require_once __DIR__ . '/../../config/database.php'; // DB接続ファイル
$pdo = get_db();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// バリデーション
if (!$username || !$password || !$password_confirm) {
    $_SESSION['error'] = 'すべての項目を入力してください';
    header('Location: /auth/register.php');
    exit;
}

if ($password !== $password_confirm) {
    $_SESSION['error'] = 'パスワードが一致しません';
    header('Location: /auth/register.php');
    exit;
}

try {
    // ユーザー名重複チェック
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch()) {
        $_SESSION['error'] = 'このユーザー名は既に使用されています';
        header('Location: /auth/register.php');
        exit;
    }

    // パスワードハッシュ化
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 登録処理
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password) VALUES (:username, :password)");

    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password', $passwordHash, PDO::PARAM_STR);

    $stmt->execute();

    $_SESSION['success'] = '登録が完了しました！';
    header('Location: /auth/login.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = '登録に失敗しました';
    header('Location: /auth/register.php');
}
