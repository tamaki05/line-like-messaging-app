<?php

require_once __DIR__ . '/../../config/database.php';

class Room {

    // IDでルームを取得（参加ユーザー名も含む）
    public function findById(int $id) {
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

    // 指定ユーザーが参加している全ルームを取得（最新メッセージも含む）
    public function findByUserId(int $userId): array {
        $pdo  = get_db();
        $stmt = $pdo->prepare('
            SELECT
                rooms.*,
                u1.username AS created_username,
                u2.username AS invited_username,
                latest_msg.content    AS latest_content,
                latest_msg.image_path AS latest_image_path,
                latest_msg.created_at AS latest_created_at
            FROM rooms
            INNER JOIN users u1 ON rooms.created_user_id = u1.id
            INNER JOIN users u2 ON rooms.invited_user_id = u2.id
            LEFT JOIN (
                SELECT m1.room_id, m1.content, m1.image_path, m1.created_at
                FROM messages m1
                INNER JOIN (
                    SELECT room_id, MAX(id) AS max_id
                    FROM messages
                    GROUP BY room_id
                ) m2 ON m1.room_id = m2.room_id AND m1.id = m2.max_id
            ) AS latest_msg ON latest_msg.room_id = rooms.id
            WHERE rooms.created_user_id = ? OR rooms.invited_user_id = ?
            ORDER BY rooms.updated_at DESC
        ');
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }

    // 2ユーザー間のルームを取得（既存チェックに使用）
    public function findByUsers(int $createdUserId, int $invitedUserId) {
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
