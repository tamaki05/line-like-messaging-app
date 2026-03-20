<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
$pdo = get_db();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// バリデーション
if (!$username || !$password) {
    $_SESSION['error'] = 'ユーザー名とパスワードを入力してください';
    header('Location: /auth/login');
    exit;
}

try {
    // ユーザー取得
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND deleted_at IS NULL");
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch();

    // ユーザーが存在しない or パスワード不一致
    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'ユーザー名またはパスワードが正しくありません';
        header('Location: /auth/login');
        exit;
    }

    // ログイン成功
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    header('Location: /top');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = 'ログインに失敗しました';
    header('Location: /auth/login');
    exit;
}
