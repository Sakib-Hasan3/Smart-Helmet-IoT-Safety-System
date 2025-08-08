<?php
require_once 'db.php';

function sendEmergencyAlert($user_id) {
    global $conn;

    // Get latest GPS
    $stmt = $conn->prepare("SELECT gps FROM helmet_data WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $gps = $result->fetch_assoc()['gps'] ?? '0,0';

    // Save alert
    $alert = $conn->prepare("INSERT INTO emergency_alerts (user_id, gps_location) VALUES (?, ?)");
    $alert->bind_param("is", $user_id, $gps);
    return $alert->execute();
}
