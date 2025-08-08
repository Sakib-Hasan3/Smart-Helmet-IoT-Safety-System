<?php
require_once 'db.php';

function filterLogs($user_id, $from, $to, $sensor) {
    global $conn;

    $sql = "SELECT sensor_type, value, timestamp FROM helmet_logs WHERE user_id = ?";
    $params = [$user_id];
    $types = "i";

    if ($sensor) {
        $sql .= " AND sensor_type LIKE ?";
        $params[] = "%$sensor%";
        $types .= "s";
    }
    if ($from) {
        $sql .= " AND DATE(timestamp) >= ?";
        $params[] = $from;
        $types .= "s";
    }
    if ($to) {
        $sql .= " AND DATE(timestamp) <= ?";
        $params[] = $to;
        $types .= "s";
    }

    $sql .= " ORDER BY timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}
