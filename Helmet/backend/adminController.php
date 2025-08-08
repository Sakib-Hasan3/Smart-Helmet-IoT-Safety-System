<?php
require_once 'db.php';

function getAllUsers() {
    global $conn;
    return $conn->query("SELECT id, username, email FROM users");
}

function getAllLogs() {
    global $conn;
    return $conn->query("SELECT user_id, sensor_type, value, timestamp FROM helmet_logs ORDER BY timestamp DESC");
}
