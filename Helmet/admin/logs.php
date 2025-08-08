<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: auth/login.php");
    exit;
}

require_once '../backend/db.php';
$result = $conn->query("SELECT user_id, sensor_type, value, timestamp FROM helmet_logs ORDER BY timestamp DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sensor Logs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h2>Helmet Sensor Logs</h2>
        <table class="table table-dark table-bordered table-sm">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Sensor Type</th>
                    <th>Value</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $log['user_id'] ?></td>
                        <td><?= htmlspecialchars($log['sensor_type']) ?></td>
                        <td><?= htmlspecialchars($log['value']) ?></td>
                        <td><?= $log['timestamp'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
