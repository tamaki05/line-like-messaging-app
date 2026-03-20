<?php

require_once __DIR__ . '/../../config/database.php';

class Room {

    // IDでルームを取得（参加ユーザー名も含む）
    public function findById(int $id): array|false {
        $pdo  = get_db();
        $stmt = $pdo->prepare('
            SELECT
                rooms.*,
                u1.username AS created_username,
                u2.username AS invited_username
            FROM rooms
            INNER JOIN users u1 ON rooms.created_user_id = u1.id
            INNER JOIN users u2 ON rooms.invited_user_id = u2.id
            WHERE rooms.id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // 指定ユーザーが参加している全ルームを取得
    public function findByUserId(int $userId): array {
        $pdo  = get_db();
        $stmt = $pdo->prepare('
            SELECT
                rooms.*,
                u1.username AS created_username,
                u2.username AS invited_username
            FROM rooms
            INNER JOIN users u1 ON rooms.created_user_id = u1.id
            INNER JOIN users u2 ON rooms.invited_user_id = u2.id
            WHERE rooms.created_user_id = ? OR rooms.invited_user_id = ?
            ORDER BY rooms.updated_at DESC
        ');
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }

    // 2ユーザー間のルームを取得（既存チェックに使用）
    public function findByUsers(int $createdUserId, int $invitedUserId): array|false {
        $pdo  = get_db();
        $stmt = $pdo->prepare('
            SELECT * FROM rooms
            WHERE (created_user_id = ? AND invited_user_id = ?)
               OR (created_user_id = ? AND invited_user_id = ?)
        ');
        $stmt->execute([$createdUserId, $invitedUserId, $invitedUserId, $createdUserId]);
        return $stmt->fetch();
    }

    // ルーム作成
    public function create(int $createdUserId, int $invitedUserId): int {
        $pdo  = get_db();
        $stmt = $pdo->prepare('INSERT INTO rooms (created_user_id, invited_user_id) VALUES (?, ?)');
        $stmt->execute([$createdUserId, $invitedUserId]);
        return (int) $pdo->lastInsertId();
    }

    // ルームのupdated_atを更新（メッセージ送信時に呼び出す）
    public function touch(int $id): void {
        $pdo  = get_db();
        $stmt = $pdo->prepare('UPDATE rooms SET updated_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }
}
