// Reminder: check every minute if an event is coming soon
function checkReminders() {
  const now = new Date();
  const box = document.getElementById('reminder');
  let html = '';

  EVENTS.forEach(e => {
    if (!e.remind || e.remind <= 0) return;
    const start = new Date(e.date + 'T' + e.time);
    const minutesLeft = Math.round((start - now) / 60000);

    // Is it inside the reminder window?
    if (minutesLeft >= 0 && minutesLeft <= e.remind) {
      html += '<div class="alert">⏰ あと ' + minutesLeft + ' 分：<b>' + escapeHtml(e.title) + '</b> (' + e.time + ')</div>';

      // Browser notification (only once per event)
      if ('Notification' in window && Notification.permission === 'granted' && !localStorage.getItem('notified_' + e.id)) {
        new Notification('リマインダー', { body: e.title + ' - ' + e.time });
        localStorage.setItem('notified_' + e.id, '1');
      }
    }
  });
  box.innerHTML = html;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Ask permission for notifications
if ('Notification' in window && Notification.permission === 'default') {
  Notification.requestPermission();
}

checkReminders();
setInterval(checkReminders, 60000);
