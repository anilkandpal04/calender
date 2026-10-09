<?php require_once __DIR__ . '/functions.php'; $page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>マイカレンダー</title>
  <link rel="stylesheet" href="style.css?v=<?= @filemtime(__DIR__ . '/../style.css') ?>">
</head>
<body>
<header>
  <h1>📅 マイカレンダー</h1>
  <nav>
    <a href="index.php" class="<?= $page=='index.php'?'active':'' ?>">ホーム</a>
    <a href="calendar.php" class="<?= $page=='calendar.php'?'active':'' ?>">カレンダー</a>
    <a href="add.php" class="<?= $page=='add.php'?'active':'' ?>">予約登録</a>
  </nav>
</header>
<div id="reminder"></div>
<main>
