<?php

session_start();

// ログイン済みならトーク一覧へ、未ログインならログイン画面へ
if (isset($_SESSION['user_id'])) {
    header('Location: /top');
} else {
    header('Location: /login');
}
exit;
