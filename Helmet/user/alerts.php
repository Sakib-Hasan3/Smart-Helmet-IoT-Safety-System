<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];
$result = $conn->query("SELECT type, created_at FROM helmet_alerts WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Alerts</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-dark text-white">
  <div class="container mt-4">
    <h2>Alert Logs</h2>
    <?php if ($result->num_rows > 0): ?>
      <table class="table table-dark table-bordered mt-3">
        <tr><th>Alert Type</th><th>Time</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['type'] ?></td>
            <td><?= $row['created_at'] ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p class="text-warning">No alerts found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
