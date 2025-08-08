<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM helmet_data WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-dark text-white">
  <div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($_SESSION["username"]) ?>!</h2>
    <h4 class="mt-4">Latest Helmet Sensor Data</h4>

    <?php if ($data): ?>
      <table class="table table-dark table-bordered mt-3">
        <tr><th>Sensor</th><th>Value</th></tr>
        <tr><td>Gas</td><td><?= $data['gas'] ?></td></tr>
        <tr><td>Rain</td><td><?= $data['rain'] ?></td></tr>
        <tr><td>IR</td><td><?= $data['ir'] ?></td></tr>
        <tr><td>Vibration</td><td><?= $data['vibration'] ?></td></tr>
        <tr><td>Fall</td><td><?= $data['fall'] ?></td></tr>
        <tr><td>Eye Blink</td><td><?= $data['eye_blink'] ?></td></tr>
        <tr><td>Speed</td><td><?= $data['speed'] ?></td></tr>
        <tr><td>GPS</td><td><?= $data['gps'] ?></td></tr>
        <tr><td>Timestamp</td><td><?= $data['timestamp'] ?></td></tr>
      </table>
    <?php else: ?>
      <p class="text-warning mt-3">No sensor data available.</p>
    <?php endif; ?>

    <a href="auth/logout.php" class="btn btn-danger mt-3">Logout</a>
  </div>
</body>
</html>
