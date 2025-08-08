<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];
$sql = "SELECT gps FROM helmet_data WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$gps = $result->fetch_assoc()['gps'] ?? "0,0";
?>

<!DOCTYPE html>
<html>
<head>
  <title>GPS Location</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-dark text-white">
  <div class="container mt-5">
    <h2>Current GPS Location</h2>
    <p><strong>Coordinates:</strong> <?= $gps ?></p>
    <p>Use Google Maps or OpenStreetMap manually to check this location.</p>
  </div>
</body>
</html>
