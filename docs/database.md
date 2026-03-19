# データベース設計

## 使用DB

- MySQL 9.6.0（ローカル開発）
- データベース名（本番）: `xb023281_st260319`
- データベース名（ローカル）: `messaging_app`

---

## テーブル一覧

| テーブル名 | 概要 |
|------------|------|
| `users` | ユーザーアカウント情報 |
| `rooms` | 1:1トークルーム |
| `messages` | メッセージ本文 |

---

## テーブル詳細

### users（ユーザー）

| カラム名 | 型 | NULL | デフォルト | 説明 |
|----------|----|------|------------|------|
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | - | 主キー |
| `username` | VARCHAR(50) | NO | - | ユーザー名（一意） |
| `password` | VARCHAR(255) | NO | - | ハッシュ化パスワード |
| `created_at` | DATETIME | NO | CURRENT_TIMESTAMP | 登録日時 |
| `updated_at` | DATETIME | NO | CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | 最終更新日時 |
| `deleted_at` | DATETIME | YES | NULL | 退会日時（論理削除） |

---

### rooms（トークルーム）

1:1のトークルームを管理します。同じユーザーペアで複数のルームを作れないようUNIQUE制約を設けます。自分自身とのルーム作成はPHP側でバリデーションします。

| カラム名 | 型 | NULL | デフォルト | 説明 |
|----------|----|------|------------|------|
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | - | 主キー |
| `created_user_id` | INT UNSIGNED | NO | - | トークを開始したユーザーID |
| `invited_user_id` | INT UNSIGNED | NO | - | 招待されたユーザーID |
| `created_at` | DATETIME | NO | CURRENT_TIMESTAMP | 作成日時 |
| `updated_at` | DATETIME | NO | CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | 最終メッセージ日時（自動更新） |

**制約：** `UNIQUE KEY unique_room (created_user_id, invited_user_id)`

---

### messages（メッセージ）

| カラム名 | 型 | NULL | デフォルト | 説明 |
|----------|----|------|------------|------|
| `id` | INT UNSIGNED AUTO_INCREMENT | NO | - | 主キー |
| `room_id` | INT UNSIGNED | NO | - | トークルームID |
| `user_id` | INT UNSIGNED | NO | - | 送信者のユーザーID |
| `content` | TEXT | YES | NULL | メッセージ本文（URLはフロントでリンク化） |
| `image_path` | VARCHAR(255) | YES | NULL | 添付画像パス（任意） | 
| `created_at` | DATETIME | NO | CURRENT_TIMESTAMP | 送信日時 |

---

## ER図（テキスト）

```
users
  │
  └─── rooms（created_user_id / invited_user_id）
            │
            └─── messages（room_id, user_id）
                      │
                      └─── image_path（/uploads/）
```

---

## 備考

- パスワードは `password_hash()` でハッシュ化して保存
- 退会はレコード削除ではなく `deleted_at` に日時を入れる論理削除
- メッセージ内URLのリンク化はフロントエンド（JavaScript）で処理
- リアルタイム受信はポーリング（一定間隔でAjaxリクエスト）で実装予定
