<?php
// Data is saved in a JSON file (no database needed)
define('DATA_FILE', __DIR__ . '/../data/events.json');

function load_events() {
    if (!file_exists(DATA_FILE)) return [];
    $data = json_decode(file_get_contents(DATA_FILE), true);
    return is_array($data) ? $data : [];
}
function save_events($events) {
    file_put_contents(DATA_FILE, json_encode(array_values($events), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}
// Escape text so it is safe to show in HTML
function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Event types: key => label (colors are in style.css as .tag.<key>)
function event_types() {
    return [
        'reservation' => '予約',
        'note'        => 'メモ',
        'most'        => '最重要',
        'important'   => '重要',
        'normal'      => '普通',
    ];
}
function clean_type($t) { return array_key_exists($t, event_types()) ? $t : 'reservation'; }
function type_label($t) { $a = event_types(); return $a[$t] ?? $a['reservation']; }

// Colors for each type (used inline so they work even if style.css is cached)
function event_colors() {
    return [
        'reservation' => '#3b6fd4',
        'note'        => '#27a56b',
        'most'        => '#d92d2d',
        'important'   => '#e8890c',
        'normal'      => '#7a8599',
    ];
}
function type_color($t) { $c = event_colors(); return $c[$t] ?? $c['reservation']; }
