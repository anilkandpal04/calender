<?php
// PAGE 3: List of reservations / notes
require_once 'includes/functions.php';

// Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
    $events = array_filter(load_events(), fn($e) => $e['id'] !== $_POST['delete_id']);
    save_events($events);
    header('Location: list.php');
    exit;
}

include 'includes/header.php';
$events = load_events();
usort($events, fn($a, $b) => strcmp($a['date'].$a['time'], $b['date'].$b['time']));  // sort by date
$show = $_GET['show'] ?? 'upcoming';
?>
<div class="card">
  <h2>予約一覧</h2>
  <p>
    <a class="btn <?= $show=='upcoming'?'primary':'' ?>" href="?show=upcoming">これから</a>
    <a class="btn <?= $show=='all'?'primary':'' ?>" href="?show=all">すべて</a>
  </p>
  <table class="list">
    <tr><th>日付</th><th>時間</th><th>種類</th><th>タイトル</th><th>メモ</th><th>操作</th></tr>
    <?php $count = 0; foreach ($events as $e):
      if ($show == 'upcoming' && $e['date'] < date('Y-m-d')) continue; $count++; ?>
      <tr>
        <td><?= h($e['date']) ?></td>
        <td><?= h($e['time']) ?></td>
        <td><span class="tag <?= h($e['type']) ?>"><?= $e['type']=='note' ? 'メモ' : '予約' ?></span></td>
        <td><?= h($e['title']) ?></td>
        <td><?= h($e['memo']) ?></td>
        <td>
          <a class="btn small" href="add.php?id=<?= h($e['id']) ?>">編集</a>
          <form method="post" style="display:inline" onsubmit="return confirm('削除しますか？');">
            <input type="hidden" name="delete_id" value="<?= h($e['id']) ?>">
            <button class="btn small danger">削除</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if ($count == 0): ?><tr><td colspan="6" class="empty-msg">予約はありません</td></tr><?php endif; ?>
  </table>
</div>
<?php include 'includes/footer.php'; ?>
