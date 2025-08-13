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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidebar">
    <div class="text-center mb-3">
      <img src="../assets/images/logo.png" alt="Logo" width="60">
      <h5>Smart Helmet</h5>
    </div>
    <a href="dashboard.php">Dashboard</a>
    <a href="map.php">Live Location</a>
    <a href="#" class="active">Data Logs</a>
    <a href="alerts.php">Alert History</a>
    <a href="profile.php">Profile</a>
    <a href="settings.php">Settings</a>
    <a href="emergency.php">Emergency</a>
    <a href="auth/logout.php" class="text-danger">Logout</a>
  </div>

  <nav class="navbar">
    <div class="container-fluid">
      <span class="navbar-brand">
        Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
      </span>
      <div class="user-info">
        <span class="notification-icon">🔔</span>
        <img src="../assets/images/user-avatar.png" alt="User" class="user-avatar">
      </div>
    </div>
  </nav>

  <div class="content">
    <div class="page-header">
      <div class="header-content">
        <h1><span class="log-icon">📊</span> Sensor Log History</h1>
        <p>View and filter all sensor data collected from your smart helmet</p>
      </div>
    </div>

    <div class="card log-card">
      <form method="POST" class="log-filter-form">
        <div class="filter-row">
          <div class="filter-group">
            <label>From Date</label>
            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
          </div>
          <div class="filter-group">
            <label>To Date</label>
            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
          </div>
          <div class="filter-group">
            <label>Sensor Type</label>
            <select name="sensor" class="form-select" id="sensorSelect" onchange="toggleCustomSensor(this)">
              <option value="">All Sensor Types</option>
              <option value="speed" <?= $sensor=="speed"?'selected':'' ?>>Speed</option>
              <option value="gas" <?= $sensor=="gas"?'selected':'' ?>>Gas</option>
              <option value="temperature" <?= $sensor=="temperature"?'selected':'' ?>>Temperature</option>
              <option value="vibration" <?= $sensor=="vibration"?'selected':'' ?>>Vibration</option>
              <option value="fall" <?= $sensor=="fall"?'selected':'' ?>>Fall</option>
              <option value="eye_blink" <?= $sensor=="eye_blink"?'selected':'' ?>>Eye Blink</option>
              <option value="heart_rate" <?= $sensor=="heart_rate"?'selected':'' ?>>Heart Rate</option>
              <option value="custom" <?= ($sensor && !in_array($sensor,["speed","gas","temperature","vibration","fall","eye_blink","heart_rate"]))?'selected':'' ?>>Custom...</option>
            </select>
            <input type="text" name="sensor" id="customSensorInput" class="form-control mt-2" placeholder="Enter custom sensor type" style="display:none;" value="<?= htmlspecialchars($sensor) ?>">
          </div>
          <div class="filter-group">
            <button type="submit" class="filter-button">
              <span class="button-icon">🔍</span> Filter
            </button>
          </div>
        </div>
      </form>

      <script>
        function toggleCustomSensor(sel) {
          var customInput = document.getElementById('customSensorInput');
          if(sel.value === 'custom') {
            customInput.style.display = 'block';
            customInput.name = 'sensor';
            customInput.focus();
          } else {
            customInput.style.display = 'none';
            customInput.name = '';
          }
        }
        // On page load, show custom input if needed
        window.addEventListener('DOMContentLoaded', function() {
          var sel = document.getElementById('sensorSelect');
          if(sel.value === 'custom') toggleCustomSensor(sel);
        });
      </script>

      <?php if ($result->num_rows > 0): ?>
        <div class="table-container">
          <table class="log-table">
            <thead>
              <tr>
                <th>Sensor</th>
                <th>Value</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td>
                  <span class="badge sensor-badge sensor-<?= strtolower(preg_replace('/[^a-z0-9]/i','',$row['sensor_type'])) ?>">
                    <?= htmlspecialchars($row['sensor_type']) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($row['value']) ?></td>
                <td><span class="log-time"><?= htmlspecialchars($row['timestamp']) ?></span></td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="no-data">
          <img src="../assets/images/no-data.svg" alt="No data" width="180">
          <h3>No logs found</h3>
          <p>Try adjusting your filters or check back later</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --info: #4895ef;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --light-gray: #e9ecef;
      --border: #dee2e6;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background-color: #ffffff;
      color: #333;
      line-height: 1.6;
    }
    
    .sidebar {
      width: 260px;
      background: #fff;
      position: fixed;
      height: 100vh;
      padding: 20px 0;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
      border-right: 1px solid var(--border);
      z-index: 100;
    }
    
    .sidebar a {
      display: flex;
      align-items: center;
      color: var(--gray);
      padding: 12px 24px;
      text-decoration: none;
      margin: 4px 12px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    
    .sidebar a:hover {
      background-color: rgba(67, 97, 238, 0.1);
      color: var(--primary);
    }
    
    .sidebar a.active {
      background-color: var(--primary);
      color: white;
      font-weight: 500;
    }
    
    .sidebar a.text-danger {
      color: var(--danger);
    }
    
    .sidebar a.text-danger:hover {
      background-color: rgba(247, 37, 133, 0.1);
    }
    
    .sidebar .text-center {
      padding: 0 12px 20px;
      border-bottom: 1px solid var(--border);
      margin-bottom: 16px;
    }
    
    .sidebar h5 {
      margin-top: 12px;
      color: var(--dark);
      font-weight: 600;
    }
    
    .navbar {
      margin-left: 260px;
      background: #fff;
      padding: 0 30px;
      height: 70px;
      display: flex;
      align-items: center;
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 90;
    }
    
    .navbar-brand {
      font-weight: 600;
      color: var(--dark);
      font-size: 1.1rem;
    }
    
    .user-info {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .notification-icon {
      font-size: 1.2rem;
      color: var(--gray);
      cursor: pointer;
    }
    
    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
    }
    
    .content {
      margin-left: 260px;
      padding: 30px;
      min-height: calc(100vh - 70px);
    }
    
    .page-header {
      margin-bottom: 30px;
    }
    
    .page-header h1 {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--dark);
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 8px;
    }
    
    .page-header p {
      color: var(--gray);
      font-size: 0.95rem;
    }
    
    .log-icon {
      font-size: 1.8rem;
      color: var(--primary);
    }
    
    .log-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
      border: 1px solid var(--border);
      padding: 30px;
    }
    
    .filter-row {
      display: flex;
      gap: 16px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }
    
    .filter-group {
      flex: 1;
      min-width: 200px;
    }
    
    .filter-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 0.9rem;
      color: var(--gray);
      font-weight: 500;
    }
    
    .form-control, .form-select {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.95rem;
      transition: all 0.3s;
      background-color: #fff;
    }
    
    .form-control:focus, .form-select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }
    
    .filter-button {
      background-color: var(--primary);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
      margin-top: 24px;
    }
    
    .filter-button:hover {
      background-color: var(--secondary);
      transform: translateY(-1px);
    }
    
    .button-icon {
      font-size: 1rem;
    }
    
    .table-container {
      overflow-x: auto;
      border-radius: 8px;
      border: 1px solid var(--border);
    }
    
    .log-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem;
    }
    
    .log-table th {
      background-color: #f8f9fa;
      color: var(--gray);
      font-weight: 600;
      text-align: left;
      padding: 14px 16px;
      border-bottom: 2px solid var(--border);
    }
    
    .log-table td {
      padding: 12px 16px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }
    
    .log-table tr:last-child td {
      border-bottom: none;
    }
    
    .log-table tr:hover {
      background-color: rgba(67, 97, 238, 0.03);
    }
    
    .sensor-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
      text-transform: capitalize;
    }
    
    .sensor-speed { background-color: #4cc9f0; color: white; }
    .sensor-gas { background-color: #f72585; color: white; }
    .sensor-temperature { background-color: #f8961e; color: white; }
    .sensor-vibration { background-color: #7209b7; color: white; }
    .sensor-fall { background-color: #f94144; color: white; }
    .sensor-eye_blink { background-color: #4895ef; color: white; }
    .sensor-heart_rate { background-color: #43aa8b; color: white; }
    
    .log-time {
      color: var(--gray);
      font-size: 0.9rem;
    }
    
    .no-data {
      text-align: center;
      padding: 40px 20px;
    }
    
    .no-data h3 {
      margin-top: 20px;
      color: var(--dark);
      font-weight: 600;
    }
    
    .no-data p {
      color: var(--gray);
      margin-top: 8px;
    }
    
    @media (max-width: 992px) {
      .sidebar {
        width: 220px;
      }
      
      .content, .navbar {
        margin-left: 220px;
      }
    }
    
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .content, .navbar {
        margin-left: 0;
      }
      
      .filter-row {
        flex-direction: column;
        gap: 12px;
      }
      
      .filter-group {
        min-width: 100%;
      }
      
      .filter-button {
        margin-top: 0;
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</body>
</html>
