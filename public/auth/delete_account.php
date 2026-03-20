<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
$pdo = get_db();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // 論理削除
    $stmt = $pdo->prepare("
        UPDATE users 
        SET deleted_at = NOW() 
        WHERE id = :id
    ");
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    // セッション削除（ログアウト）
    session_destroy();

    // 完了メッセージ
    session_start();
    $_SESSION['success'] = '退会が完了しました';

    header('Location: /auth/login');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = '退会に失敗しました';
    header('Location: /top');
    exit;
}
