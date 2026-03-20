<?php

require_once __DIR__ . '/../../config/database.php';

class Message {

    // ルームIDに紐づく全メッセージを取得（送信者名も含む）
    public function findByRoomId(int $roomId): array {
        $pdo  = get_db();
        $stmt = $pdo->prepare('
            SELECT
                messages.*,
                users.username AS sender_username
            FROM messages
            INNER JOIN users ON messages.user_id = users.id
            WHERE messages.room_id = ?
            ORDER BY messages.created_at ASC
        ');
        $stmt->execute([$roomId]);
        return $stmt->fetchAll();
    }

    // 指定日時以降の新着メッセージを取得（ポーリングで使用）
    public function findNewMessages(int $roomId, string $lastCreatedAt): array {
        $pdo  = get_db();
        $stmt = $pdo->prepare('
            SELECT
                messages.*,
                users.username AS sender_username
            FROM messages
            INNER JOIN users ON messages.user_id = users.id
            WHERE messages.room_id = ? AND messages.created_at > ?
            ORDER BY messages.created_at ASC
        ');
        $stmt->execute([$roomId, $lastCreatedAt]);
        return $stmt->fetchAll();
    }

    // メッセージ送信（テキストのみ）
    public function create(int $roomId, int $userId, string $content): int {
        $pdo  = get_db();
        $stmt = $pdo->prepare('INSERT INTO messages (room_id, user_id, content) VALUES (?, ?, ?)');
        $stmt->execute([$roomId, $userId, $content]);
        return (int) $pdo->lastInsertId();
    }

    // メッセージ送信（画像あり）
    public function createWithImage(int $roomId, int $userId, string $content, string $imagePath): int {
        $pdo  = get_db();
        $stmt = $pdo->prepare('INSERT INTO messages (room_id, user_id, content, image_path) VALUES (?, ?, ?, ?)');
        $stmt->execute([$roomId, $userId, $content, $imagePath]);
        return (int) $pdo->lastInsertId();
    }

    // ルームの最新メッセージを1件取得（チャット一覧に使用）
    public function findLatestByRoomId(int $roomId): array|false {
        $pdo  = get_db();
        $stmt = $pdo->prepare('
            SELECT * FROM messages
            WHERE room_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ');
        $stmt->execute([$roomId]);
        return $stmt->fetch();
    }

    // IDでメッセージを取得
    public function findById(int $id): array|false {
        $pdo  = get_db();
        $stmt = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // メッセージ削除
    public function delete(int $id): void {
        $pdo  = get_db();
        $stmt = $pdo->prepare('DELETE FROM messages WHERE id = ?');
        $stmt->execute([$id]);
    }
}
