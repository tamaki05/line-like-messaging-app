<?php
// PHP組み込みサーバー用ルーター
// 起動コマンド: php -S localhost:8000 -t public/ public/router.php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 実在する静的ファイル（CSS/JS/画像）はそのまま返す
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// .phpを付けたファイルが存在すれば読み込む
$phpFile = __DIR__ . $uri . '.php';
if (file_exists($phpFile)) {
    require $phpFile;
    return true;
}

// サブディレクトリのindex.phpを探す
$indexFile = __DIR__ . rtrim($uri, '/') . '/index.php';
if (file_exists($indexFile)) {
    require $indexFile;
    return true;
}

http_response_code(404);
echo '404 Not Found';
