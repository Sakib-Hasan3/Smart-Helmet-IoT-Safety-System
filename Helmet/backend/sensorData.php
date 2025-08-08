<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION["user_id"];

// Example: fetch latest sensor data from helmet_data table
$sql = "SELECT gas, rain, ir, vibration, fall, eye_blink, speed, gps FROM helmet_data 
        WHERE user_id = ? 
        ORDER BY timestamp DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data ?: []);
