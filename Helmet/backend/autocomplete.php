<?php
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo json_encode([]);
    exit;
}

$query = "%" . trim($_GET['query']) . "%";

// Using DISTINCT to avoid duplicates
$sql = "SELECT DISTINCT sensor_type FROM helmet_logs WHERE sensor_type LIKE ? LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $query);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row['sensor_type'];
}

echo json_encode($suggestions);
