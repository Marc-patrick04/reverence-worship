<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['test'] = $_POST['value'];
    echo "Session set: " . $_SESSION['test'];
} else {
    echo "Session ID: " . session_id() . "<br>";
    echo "Test value: " . ($_SESSION['test'] ?? 'not set') . "<br>";
    echo '<form method="POST"><input name="value"><button type="submit">Set Session</button></form>';
}