</main>
<footer>School Project - Calendar App</footer>
<script>const EVENTS = <?= json_encode(load_events(), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="script.js"></script>
</body>
</html>
