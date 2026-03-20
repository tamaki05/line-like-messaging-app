<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>MSG</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="layout">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="main">
        </div>
    </div>
</body>
</html>
