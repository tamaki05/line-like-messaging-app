-- データベース作成
CREATE DATABASE IF NOT EXISTS messaging_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE messaging_app;

-- ユーザーテーブル
CREATE TABLE users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at  DATETIME     NULL
);

-- トークルームテーブル
CREATE TABLE rooms (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_user_id  INT UNSIGNED NOT NULL,
    invited_user_id  INT UNSIGNED NOT NULL,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_room (created_user_id, invited_user_id),
    FOREIGN KEY (created_user_id) REFERENCES users(id),
    FOREIGN KEY (invited_user_id) REFERENCES users(id)
);

-- メッセージテーブル
CREATE TABLE messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id     INT UNSIGNED  NOT NULL,
    user_id     INT UNSIGNED  NOT NULL,
    content     TEXT          NULL,
    image_path  VARCHAR(255)  NULL,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
