<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Fetch last GPS
    $sql = "SELECT gps FROM helmet_data WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $gps = $result->fetch_assoc()['gps'] ?? "0,0";

    $alert = $conn->prepare("INSERT INTO emergency_alerts (user_id, gps_location) VALUES (?, ?)");
    $alert->bind_param("is", $user_id, $gps);
    $alert->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Emergency Alert</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-dark text-white">
  <div class="container mt-5">
    <h2>Send Emergency Alert</h2>
    <form method="POST">
        <button type="submit" class="btn btn-danger btn-lg">🚨 Send Emergency Alert</button>
    </form>
    <?php if (isset($success)): ?>
      <p class="text-success mt-3">Alert sent successfully with latest GPS!</p>
    <?php endif; ?>
  </div>
</body>
</html>
