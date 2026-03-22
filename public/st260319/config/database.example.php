<?php
if (PHP_SAPI === 'cli-server') {
    // ローカル開発環境
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'messaging_app');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // 本番サーバー
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'your_db_name');
    define('DB_USER', 'your_db_user');
    define('DB_PASS', 'your_db_pass');
}
define('DB_CHARSET', 'utf8mb4');

function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
