# MSG - LINEライクなメッセージアプリ

シンプルな1対1のリアルタイムメッセージアプリです。

## デモ

URL：http://xb023281.xbiz.jp/st260319/

## 機能

- ユーザー登録 / ログイン / ログアウト
- ユーザー一覧からトークルームの作成
- テキストメッセージの送受信
- 画像の送受信（JPEG / PNG、5MB以内）
- 自分のメッセージの削除
- 新着メッセージの自動取得（2秒ポーリング）
- テキスト内のURLを自動でリンクに変換
- 退会（論理削除）

## 使用技術

| カテゴリ | 技術 |
|---------|------|
| バックエンド | PHP 7.4 |
| データベース | MySQL |
| フロントエンド | HTML / CSS / JavaScript |
| Webサーバー | Apache（本番）/ PHP組み込みサーバー（開発） |

## ローカル環境構築

### 必要なもの

- PHP 7.4以上
- MySQL

### 手順

**1. リポジトリをクローン**

```bash
git clone <リポジトリURL>
cd line-like-messaging-app
```

**2. データベースを作成**

```bash
mysql -u root -e "CREATE DATABASE messaging_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root messaging_app < schema.sql
```

**3. DB接続情報を設定**

```bash
cp public/st260319/config/database.example.php public/st260319/config/database.php
```

`database.php` を開き、本番サーバーの `your_db_name` などを実際の接続情報に書き換えてください。

**4. サーバーを起動**

```bash
php -S localhost:8000 -t public public/router.php
```

**5. ブラウザでアクセス**

```
http://localhost:8000/st260319/auth/login
```
