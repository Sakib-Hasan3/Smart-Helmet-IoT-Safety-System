<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT gps FROM helmet_data 
        WHERE user_id = ? 
        ORDER BY timestamp DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data && isset($data["gps"])) {
    [$lat, $lng] = explode(",", $data["gps"]); // Expected format: "12.3456,78.9012"
    echo json_encode(["lat" => $lat, "lng" => $lng]);
} else {
    echo json_encode(["lat" => null, "lng" => null]);
}
