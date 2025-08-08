<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION["user_id"];

// Get recent alerts for this user (e.g., last 10 alerts)
$sql = "SELECT type, created_at FROM helmet_alerts 
        WHERE user_id = ? 
        ORDER BY created_at DESC LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$icons = [
    'gas' => '🔥',
    'fall' => '🆘',
    'rain' => '🌧️',
    'drowsiness' => '😴',
    'vibration' => '💥',
    'speed' => '🏍️'
];

$alerts = [];

while ($row = $result->fetch_assoc()) {
    $alerts[] = [
        'type' => $row['type'],
        'message' => ucfirst($row['type']) . " alert!",
        'icon' => $icons[$row['type']] ?? '⚠️',
        'time' => date("H:i", strtotime($row['created_at']))
    ];
}

echo json_encode($alerts);
