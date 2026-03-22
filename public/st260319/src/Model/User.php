<?php

require_once __DIR__ . '/../../config/database.php';

class User {

    // IDでユーザーを取得
    public function findById(int $id) {
        $pdo  = get_db();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ユーザー名でユーザーを取得
    public function findByUsername(string $username) {
        $pdo  = get_db();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND deleted_at IS NULL');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    // 退会済みを除く全ユーザーを取得
    public function findAll(): array {
        $pdo  = get_db();
        $stmt = $pdo->query('SELECT id, username FROM users WHERE deleted_at IS NULL');
        return $stmt->fetchAll();
    }

    // ユーザー登録
    public function create(string $username, string $password): int {
        $pdo  = get_db();
        $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        return (int) $pdo->lastInsertId();
    }

    // 退会（論理削除）
    public function softDelete(int $id): void {
        $pdo  = get_db();
        $stmt = $pdo->prepare('UPDATE users SET deleted_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}
