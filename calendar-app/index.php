<?php
// PAGE 1: Calendar (month view)
include 'includes/header.php';

$m = $_GET['m'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $m)) $m = date('Y-m');

$first    = strtotime($m . '-01');
$daysInM  = date('t', $first);
$startDow = date('w', $first);            // 0 = Sunday
$prev     = date('Y-m', strtotime('-1 month', $first));
$next     = date('Y-m', strtotime('+1 month', $first));

// Group events by date
$byDate = [];
foreach (load_events() as $e) $byDate[$e['date']][] = $e;
?>
<div class="cal-nav">
  <a class="btn" href="?m=<?= $prev ?>">◀ 前の月</a>
  <h2><?= date('Y年n月', $first) ?></h2>
  <a class="btn" href="?m=<?= $next ?>">次の月 ▶</a>
</div>

<table class="calendar">
  <tr><th class="sun">日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th class="sat">土</th></tr>
  <tr>
<?php
for ($i = 0; $i < $startDow; $i++) echo '<td class="empty"></td>';   // blank cells
for ($d = 1; $d <= $daysInM; $d++) {
    $date = sprintf('%s-%02d', $m, $d);
    $cls  = ($date == date('Y-m-d')) ? 'today' : '';
    echo "<td class='$cls'><a href='add.php?date=$date' class='daynum'>$d</a>";
    foreach ($byDate[$date] ?? [] as $e) {
        echo "<a href='add.php?id=" . h($e['id']) . "' class='tag " . h($e['type']) . "'>" . h($e['time']) . " " . h($e['title']) . "</a>";
    }
    echo "</td>";
    if (($startDow + $d) % 7 == 0 && $d < $daysInM) echo "</tr><tr>";  // new week
}
while (($startDow + $daysInM) % 7 != 0) { echo '<td class="empty"></td>'; $startDow++; }
?>
  </tr>
</table>
<p class="legend"><span class="tag reservation">予約</span> <span class="tag note">メモ</span> 日付をクリックすると登録できます</p>
<?php include 'includes/footer.php'; ?>
