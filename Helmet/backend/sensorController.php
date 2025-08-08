<?php
require_once 'db.php';

function getLatestSensorData($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM helmet_data WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
