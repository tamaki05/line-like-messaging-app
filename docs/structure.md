# ディレクトリ構造

## 概要

PHP + MySQL（フレームワークなし）で構築するLINEライクなメッセージアプリのディレクトリ構成です。

## 構成

```
line-like-messaging-app/
├── public/          # Webサーバーの公開ルート（index.phpなど）
├── src/
│   ├── Controller/  # リクエスト処理・ロジック
│   ├── Model/       # DB操作
│   └── View/        # HTML表示（テンプレート）
├── assets/
│   ├── css/         # スタイルシート
│   ├── js/          # JavaScriptファイル
│   └── images/      # 静的画像
├── config/          # DB接続情報など（Git管理外）
├── uploads/         # ユーザーがアップロードした画像（Git管理外）
├── docs/            # 仕様書・ドキュメント
├── .gitignore
└── README.md
```

## Gitignore対象

| パス | 理由 |
|------|------|
| `/uploads/*` | ユーザーの投稿画像はGit管理しない |
| `/config/database.php` | DB接続情報（パスワードなど）を含むため |
| `*.log` | ログファイル |
| `.DS_Store` | macOSのシステムファイル |
