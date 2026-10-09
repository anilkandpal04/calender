<?php
// PAGE 1: Home - important notices on top, today's schedule, then everything else by date
require_once 'includes/functions.php';

// Delete an event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
    $left = array_filter(load_events(), fn($e) => $e['id'] !== $_POST['delete_id']);
    save_events($left);
    header('Location: index.php');
    exit;
}

include 'includes/header.php';

$today = date('Y-m-d');
$now   = date('Y-m-d H:i');
$show  = (($_GET['show'] ?? '') === 'all') ? 'all' : 'upcoming';

function jp_date($d) {
    $wd = ['日','月','火','水','木','金','土'];
    $t = strtotime($d);
    return date('n月j日', $t) . ' (' . $wd[date('w', $t)] . ')';
}

// Sort by date + time (same time: 最重要 before 重要 before the rest)
$rank = ['most' => 0, 'important' => 1];
$events = load_events();
usort($events, function ($a, $b) use ($rank) {
    $c = strcmp($a['date'] . $a['time'], $b['date'] . $b['time']);
    if ($c !== 0) return $c;
    return ($rank[$a['type'] ?? ''] ?? 9) <=> ($rank[$b['type'] ?? ''] ?? 9);
});

$visible   = array_values(array_filter($events, fn($e) => $show == 'all' || $e['date'] >= $today));
$important = array_values(array_filter($visible, fn($e) => isset($rank[$e['type'] ?? ''])));
$others    = array_values(array_filter($visible, fn($e) => !isset($rank[$e['type'] ?? ''])));
$todayList = array_values(array_filter($events, fn($e) => $e['date'] === $today));

$next = null;
foreach ($todayList as $e) { if ($e['date'] . ' ' . $e['time'] >= $now) { $next = $e; break; } }

// One event row
function event_row($e, $showDate) {
    $past = ($e['date'] . ' ' . $e['time']) < date('Y-m-d H:i');
    ?>
    <div class="ev <?= $past ? 'past' : '' ?>">
      <div class="ev-time"><?php if ($showDate) echo h(jp_date($e['date'])) . '<br>'; echo h($e['time']); ?></div>
      <div class="ev-main">
        <span class="tag <?= h($e['type']) ?>" style="background:<?= type_color($e['type']) ?>"><?= h(type_label($e['type'])) ?></span>
        <strong><?= h($e['title']) ?></strong>
        <?php if (($e['memo'] ?? '') !== ''): ?><div class="ev-memo"><?= h($e['memo']) ?></div><?php endif; ?>
      </div>
      <div class="ev-actions">
        <a class="btn small" href="add.php?id=<?= h($e['id']) ?>">編集</a>
        <form method="post" style="display:inline" onsubmit="return confirm('削除しますか？');">
          <input type="hidden" name="delete_id" value="<?= h($e['id']) ?>">
          <button class="btn small danger">削除</button>
        </form>
      </div>
    </div>
    <?php
}
?>

<p>
  <a class="btn <?= $show=='upcoming'?'primary':'' ?>" href="?show=upcoming">これから</a>
  <a class="btn <?= $show=='all'?'primary':'' ?>" href="?show=all">すべて</a>
</p>

<!-- Important notices: always on top, sorted by date -->
<div class="card important-box">
  <h2>🔔 重要なお知らせ <span class="count"><?= count($important) ?>件</span></h2>
  <?php if (!$important): ?>
    <p class="empty-msg">重要なお知らせはありません</p>
  <?php else: foreach ($important as $e) event_row($e, true); endif; ?>
</div>

<!-- Today -->
<div class="card today-card">
  <div class="today-date"><?= h(jp_date($today)) ?></div>
  <div class="today-title">今日の予定 <?= count($todayList) ?>件</div>
  <?php if (!$todayList): ?>
    <p class="empty-msg">今日の予定はありません</p>
  <?php else: foreach ($todayList as $e): ?>
    <div class="today-row">
      <span class="dot" style="background:<?= type_color($e['type']) ?>"></span>
      <?= h($e['time']) ?> <?= h($e['title']) ?>
    </div>
  <?php endforeach; ?>
    <div class="today-next"><?= $next ? '次の予定 → ' . h($next['time']) : '本日の予定は終了しました' ?></div>
  <?php endif; ?>
</div>

<!-- Everything else, grouped by date -->
<div class="card">
  <h2>📋 すべての予定・メモ <span class="count"><?= count($others) ?>件</span></h2>
  <?php if (!$others): ?>
    <p class="empty-msg">予定はありません</p>
  <?php else:
    $lastDate = '';
    foreach ($others as $e):
      if ($e['date'] !== $lastDate): $lastDate = $e['date']; ?>
        <h3 class="date-head"><?= h(jp_date($e['date'])) ?><?= $e['date'] === $today ? ' <span class="today-badge">今日</span>' : '' ?></h3>
      <?php endif;
      event_row($e, false);
    endforeach;
  endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
