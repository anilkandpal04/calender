<?php
// PAGE 2: Add / edit a reservation or note
require_once 'includes/functions.php';
$events = load_events();

// Edit mode: find the existing event
$edit = null;
if (!empty($_GET['id'])) {
    foreach ($events as $e) if ($e['id'] === $_GET['id']) $edit = $e;
}

// Form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = [
        'id'     => $_POST['id'] ?: uniqid(),
        'title'  => trim($_POST['title']),
        'date'   => $_POST['date'],
        'time'   => $_POST['time'],
        'type'   => clean_type($_POST['type'] ?? ''),
        'memo'   => trim($_POST['memo']),
        'remind' => (int)$_POST['remind'],
    ];
    if ($new['title'] !== '' && $new['date'] !== '') {
        $found = false;
        foreach ($events as $k => $e) {
            if ($e['id'] === $new['id']) { $events[$k] = $new; $found = true; }
        }
        if (!$found) $events[] = $new;
        save_events($events);
        header('Location: index.php');
        exit;
    }
}

include 'includes/header.php';
$v = $edit ?? ['id'=>'','title'=>'','date'=>$_GET['date'] ?? date('Y-m-d'),'time'=>'09:00','type'=>'reservation','memo'=>'','remind'=>30];
?>
<div class="card">
  <h2><?= $edit ? '予約を編集' : '予約登録' ?></h2>
  <form method="post">
    <input type="hidden" name="id" value="<?= h($v['id']) ?>">

    <label>タイトル</label>
    <input type="text" name="title" value="<?= h($v['title']) ?>" required placeholder="例：歯医者の予約">

    <div class="row">
      <div><label>日付</label><input type="date" name="date" value="<?= h($v['date']) ?>" required></div>
      <div><label>時間</label><input type="time" name="time" value="<?= h($v['time']) ?>" required></div>
    </div>

    <div class="row">
      <div>
        <label>種類</label>
        <select name="type" id="typeSel">
          <?php foreach (event_types() as $key => $label): ?>
            <option value="<?= h($key) ?>" style="background:<?= type_color($key) ?>;color:#fff" <?= $v['type']==$key?'selected':'' ?>><?= h($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>リマインダー</label>
        <select name="remind">
          <?php foreach ([0=>'なし', 10=>'10分前', 30=>'30分前', 60=>'1時間前', 1440=>'1日前'] as $min => $text): ?>
            <option value="<?= $min ?>" <?= $v['remind']==$min?'selected':'' ?>><?= $text ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <label>メモ</label>
    <textarea name="memo" rows="4"><?= h($v['memo']) ?></textarea>

    <button type="submit" class="btn primary">保存する</button>
    <a href="index.php" class="btn">キャンセル</a>
  </form>
</div>
<script>
// Color the 種類 dropdown to match the selected type
const TYPE_COLORS = <?= json_encode(event_colors()) ?>;
const typeSel = document.getElementById('typeSel');
function paintTypeSel() {
  const c = TYPE_COLORS[typeSel.value] || '#fff';
  typeSel.style.background = c; typeSel.style.color = '#fff'; typeSel.style.borderColor = c;
}
typeSel.addEventListener('change', paintTypeSel);
paintTypeSel();
</script>
<?php include 'includes/footer.php'; ?>
