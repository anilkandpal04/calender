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
