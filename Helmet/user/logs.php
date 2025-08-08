<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];

$from = $_POST['from'] ?? '';
$to = $_POST['to'] ?? '';
$sensor = $_POST['sensor'] ?? '';

$query = "SELECT sensor_type, value, timestamp FROM helmet_logs WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($sensor) {
    $query .= " AND sensor_type LIKE ?";
    $params[] = "%$sensor%";
    $types .= "s";
}
if ($from) {
    $query .= " AND DATE(timestamp) >= ?";
    $params[] = $from;
    $types .= "s";
}
if ($to) {
    $query .= " AND DATE(timestamp) <= ?";
    $params[] = $to;
    $types .= "s";
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sensor Logs</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-dark text-white">
  <div class="container mt-4">
    <h2>Filter Logs</h2>
    <form method="POST" class="row g-2">
      <div class="col-md-3">
        <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
      </div>
      <div class="col-md-3">
        <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
      </div>
      <div class="col-md-4">
        <input type="text" name="sensor" class="form-control" placeholder="Sensor type" value="<?= htmlspecialchars($sensor) ?>">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-success">Filter</button>
      </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
      <table class="table table-dark table-bordered mt-3">
        <tr><th>Sensor</th><th>Value</th><th>Time</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['sensor_type'] ?></td>
            <td><?= $row['value'] ?></td>
            <td><?= $row['timestamp'] ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p class="text-warning mt-3">No logs found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
