<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: auth/login.php");
  exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION['user_id'];
// $user_id = 1; // <- uncomment for quick testing

/* ------------------ Fetch data ------------------ */
// Last 10 rows for charts
$histStmt = $conn->prepare("
  SELECT timestamp, speed, temperature
  FROM helmet_data
  WHERE user_id = ?
  ORDER BY timestamp DESC
  LIMIT 10
");
$histStmt->bind_param("i", $user_id);
$histStmt->execute();
$histRes = $histStmt->get_result();

$rows = [];
while ($r = $histRes->fetch_assoc()) { $rows[] = $r; }

// Latest row for cards/suggestions
$latestStmt = $conn->prepare("
  SELECT *
  FROM helmet_data
  WHERE user_id = ?
  ORDER BY timestamp DESC
  LIMIT 1
");
$latestStmt->bind_param("i", $user_id);
$latestStmt->execute();
$data = $latestStmt->get_result()->fetch_assoc();

/* ------------------ Prepare chart arrays (oldest -> newest) ------------------ */
$labels = [];       // times like "14:05"
$speedData = [];    // numeric speeds
$tempData  = [];    // numeric temps

foreach (array_reverse($rows) as $row) {
  $labels[]    = isset($row['timestamp']) ? date('H:i', strtotime($row['timestamp'])) : '';
  $speedData[] = is_numeric($row['speed']) ? (float)$row['speed'] : 0;
  $tempData[]  = is_numeric($row['temperature']) ? (float)$row['temperature'] : 0;
}

/* ------------------ Smart Ride Suggestions (compute once) ------------------ */
$speedTip = $gasTip = $tempTip = null;
if (!empty($data)) {
  $speed = (float)($data['speed'] ?? 0);
  if     ($speed >= 80) $speedTip = "⚠️ High speed ({$speed} km/h). Slow down to stay safe.";
  elseif ($speed >= 60) $speedTip = "👍 Good pace ({$speed} km/h). Stay alert.";
  else                  $speedTip = "✅ Safe speed ({$speed} km/h). Keep going.";

  $temp = (float)($data['temperature'] ?? 0);
  if     ($temp >= 35) $tempTip = "🥵 It's hot ({$temp}°C). Stay hydrated.";
  elseif ($temp >= 25) $tempTip = "🙂 Comfortable ({$temp}°C). Enjoy your ride.";
  else                 $tempTip = "🧥 It's cool ({$temp}°C). Wear a jacket.";

  $gas = (float)($data['gas'] ?? 0);
  if     ($gas >= 100) $gasTip = "⚠️ Gas level {$gas} ppm high. Ventilate.";
  elseif ($gas >= 50)  $gasTip = "🔆 Moderate gas ({$gas} ppm). Keep vents open.";
  else                 $gasTip = "✅ Air clean ({$gas} ppm). Great!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>User Dashboard | Smart Helmet</title>

  <!-- Auto-refresh to pick up new inserts -->
  <meta http-equiv="refresh" content="15">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Chart.js UMD (global Chart variable) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --bg: #ffffff;
      --panel: #f8f9fa;
      --border: #dee2e6;
      --accent: #0d6efd;
      --accent-2: #0b5ed7;
      --text: #212529;
      --muted: #6c757d;
      --danger: #dc3545;
      --warning: #ffc107;
    }
    
    body { 
      background: var(--bg); 
      color: var(--text);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    
    .sidebar {
      width: 240px; 
      background: var(--panel); 
      position: fixed; 
      height: 100vh; 
      padding: 20px 16px;
      border-right: 1px solid var(--border);
      z-index: 1000;
    }
    
    .brand {
      display: flex; 
      gap: 10px; 
      align-items: center; 
      margin-bottom: 24px;
    }
    
    .brand img { 
      width: 40px; 
      height: 40px; 
      border-radius: 8px; 
    }
    
    .brand h5 { 
      margin: 0; 
      color: var(--accent); 
      font-weight: 700; 
      letter-spacing: 0.3px;
      font-size: 1.1rem;
    }
    
    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--muted); 
      padding: 12px 14px; 
      border-radius: 8px; 
      margin-bottom: 4px; 
      text-decoration: none;
      transition: all 0.2s ease;
    }
    
    .nav-link:hover, .nav-link.active { 
      background: rgba(13, 110, 253, 0.1); 
      color: var(--accent); 
    }
    
    .nav-link i {
      width: 20px;
      text-align: center;
    }
    
    .content { 
      margin-left: 240px; 
      padding: 24px; 
    }
    
    .navbar {
      margin-left: 240px; 
      background: var(--panel); 
      border-bottom: 1px solid var(--border); 
      color: var(--text);
      padding: 16px 24px;
    }
    
    .card-box {
      background: white; 
      border: 1px solid var(--border); 
      border-radius: 12px; 
      padding: 20px;
      margin-bottom: 20px;
      transition: transform 0.25s ease, box-shadow 0.25s ease; 
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      height: 100%;
    }
    
    .card-box:hover { 
      transform: translateY(-4px); 
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1); 
      border-color: rgba(13, 110, 253, 0.3);
    }
    
    .sensor-value { 
      font-size: 2rem; 
      font-weight: 700; 
      color: var(--accent); 
      margin-top: 8px;
    }
    
    .sensor-icon {
      font-size: 1.5rem;
      margin-right: 10px;
      color: var(--accent);
    }
    
    .section-title { 
      margin-top: 24px; 
      margin-bottom: 16px; 
      font-weight: 600;
      color: var(--accent);
    }
    
    .suggest-card {
      background: white;
      border: 1px solid var(--border); 
      border-radius: 12px; 
      padding: 20px;
      height: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .suggest-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      border-color: rgba(13, 110, 253, 0.3);
    }
    
    .badge-dot {
      width: 10px; 
      height: 10px; 
      border-radius: 50%; 
      background: var(--accent); 
      display: inline-block; 
      margin-right: 8px;
    }
    
    .btn-accent { 
      background: var(--accent); 
      border: none;
      padding: 10px 20px;
      font-weight: 600;
      letter-spacing: 0.5px;
      color: white;
    }
    
    .btn-accent:hover { 
      background: var(--accent-2); 
      color: white;
    }
    
    .alert-badge {
      background: rgba(220, 53, 69, 0.1);
      border-left: 3px solid var(--danger);
    }
    
    .chart-container {
      position: relative;
      height: 250px;
      width: 100%;
    }
    
    @media (max-width: 992px) {
      .sidebar { 
        position: relative; 
        width: 100%; 
        height: auto; 
        border-right: none;
      }
      
      .navbar { 
        margin-left: 0; 
      }
      
      .content { 
        margin-left: 0; 
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="brand">
      <img src="../assets/images/logo.png" alt="Logo">
      <h5>Smart Helmet</h5>
    </div>
    <a class="nav-link active" href="#">
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a class="nav-link" href="map.php">
      <i class="fas fa-map-marker-alt"></i> Live Location
    </a>
    <a class="nav-link" href="logs.php">
      <i class="fas fa-database"></i> Data Logs
    </a>
    <a class="nav-link" href="alerts.php">
      <i class="fas fa-bell"></i> Alert History
    </a>
    <a class="nav-link" href="profile.php">
      <i class="fas fa-user"></i> Profile
    </a>
    <a class="nav-link" href="settings.php">
      <i class="fas fa-cog"></i> Settings
    </a>
    <a class="nav-link text-danger" href="auth/logout.php">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </aside>

  <!-- Top Navbar -->
  <nav class="navbar navbar-light">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h5">
        <span class="badge-dot"></span>
        Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Rider') ?>
      </span>
      <span class="text-muted">
        <i class="fas fa-sync-alt me-1"></i> Live updates every 15s
      </span>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="content container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">Live Dashboard</h2>
        <p class="text-muted">Real-time helmet sensor monitoring</p>
      </div>
      <span class="badge bg-success">
        <i class="fas fa-circle me-1"></i> Connected
      </span>
    </div>

    <?php if (!empty($data)): ?>
      <!-- Sensor Cards -->
      <div class="row g-4">
        <?php
          $sensorIcons = [
            'speed' => 'tachometer-alt',
            'gas' => 'wind',
            'rain' => 'cloud-rain',
            'gps' => 'map-marker-alt',
            'temperature' => 'temperature-low',
            'vibration' => 'wave-square',
            'eye_blink' => 'eye',
            'heart_rate' => 'heartbeat'
          ];
          
          $sensors = [
            'speed'=>'km/h','gas'=>'ppm','rain'=>'%','gps'=>'sat',
            'temperature'=>'°C','vibration'=>'g','eye_blink'=>'/min','heart_rate'=>'bpm'
          ];
          
          foreach ($sensors as $key=>$unit): ?>
            <div class="col-12 col-md-6 col-xl-3">
              <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="mb-0">
                    <i class="fas fa-<?= $sensorIcons[$key] ?> sensor-icon"></i>
                    <?= ucwords(str_replace('_',' ',$key)) ?>
                  </h6>
                  <span class="text-muted small"><?= htmlspecialchars(date('H:i', strtotime($data['timestamp'] ?? ''))) ?></span>
                </div>
                <div class="sensor-value">
                  <?= htmlspecialchars($data[$key] ?? '--') ?> <span class="fs-6 text-muted"><?= $unit ?></span>
                </div>
              </div>
            </div>
        <?php endforeach; ?>
      </div>

      <!-- Charts -->
      <div class="row mt-4 g-4">
        <div class="col-12 col-lg-6">
          <div class="card-box">
            <h4 class="section-title">
              <i class="fas fa-tachometer-alt me-2"></i>Speed Over Time
            </h4>
            <div class="chart-container">
              <canvas id="speedChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="card-box">
            <h4 class="section-title">
              <i class="fas fa-temperature-low me-2"></i>Temperature Over Time
            </h4>
            <div class="chart-container">
              <canvas id="tempChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Smart Suggestions -->
      <h4 class="section-title mt-4">
        <i class="fas fa-lightbulb me-2"></i>Smart Ride Suggestions
      </h4>
      <div class="row g-4">
        <?php
          $suggestions = [
            ['heading'=>'Speed Safety','icon'=>'🏎️','tip'=>$speedTip, 'color'=>'#0d6efd'],
            ['heading'=>'Air Quality','icon'=>'🌬️','tip'=>$gasTip, 'color'=>'#198754'],
            ['heading'=>'Comfort Temp','icon'=>'🌡️','tip'=>$tempTip, 'color'=>'#6f42c1'],
          ];
          foreach ($suggestions as $s): ?>
            <div class="col-12 col-md-6 col-xl-4">
              <div class="suggest-card h-100">
                <div class="d-flex align-items-center mb-3">
                  <span class="me-2" style="font-size:1.8rem;"><?= $s['icon'] ?></span>
                  <h5 class="mb-0" style="color: <?= $s['color'] ?>"><?= $s['heading'] ?></h5>
                </div>
                <p class="mb-0"><?= htmlspecialchars($s['tip']) ?></p>
              </div>
            </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning mt-3">
        <i class="fas fa-exclamation-triangle me-2"></i>
        No sensor data found for your account. Please ensure your helmet is connected.
      </div>
    <?php endif; ?>

    <!-- Live Alerts -->
    <h4 class="section-title mt-4">
      <i class="fas fa-bell me-2"></i>Live Alerts
    </h4>
    <?php
      $alertQuery = $conn->prepare("
        SELECT type, created_at
        FROM helmet_alerts
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 3
      ");
      $alertQuery->bind_param("i", $user_id);
      $alertQuery->execute();
      $alerts = $alertQuery->get_result();
      
      if ($alerts->num_rows === 0): ?>
        <div class="alert alert-light">
          <i class="fas fa-info-circle me-2"></i> No alerts detected. All systems normal.
        </div>
    <?php else:
        while ($r = $alerts->fetch_assoc()): 
          $alertClass = strpos(strtolower($r['type']), 'warning') !== false ? 'warning' : 'danger';
          ?>
          <div class="alert alert-<?= $alertClass ?> alert-dismissible fade show">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars(ucfirst($r['type'])) ?></strong>
              </div>
              <span class="text-muted small"><?= htmlspecialchars(date('H:i', strtotime($r['created_at']))) ?></span>
            </div>
          </div>
    <?php   endwhile;
      endif; ?>

    <!-- Emergency -->
    <h4 class="section-title mt-4">
      <i class="fas fa-exclamation-triangle me-2"></i>Emergency Actions
    </h4>
    <div class="d-flex gap-3">
      <form method="POST" action="emergency.php">
        <button type="submit" class="btn btn-danger btn-lg">
          <i class="fas fa-bell me-2"></i>Send Emergency Alert
        </button>
      </form>
      <button class="btn btn-outline-primary btn-lg">
        <i class="fas fa-phone-alt me-2"></i>Call Emergency Contact
      </button>
    </div>
  </main>

  <!-- Replace the Charts JS section with this code -->
<script>
    document.addEventListener('DOMContentLoaded', function(){
      <?php if (!empty($data) && count($labels) > 0): ?>
        const labels = <?= json_encode($labels) ?>;
        const speedData = <?= json_encode($speedData, JSON_NUMERIC_CHECK) ?>;
        const tempData = <?= json_encode($tempData, JSON_NUMERIC_CHECK) ?>;
        
        // Common chart configuration
        const chartOptions = {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderColor: 'rgba(255, 255, 255, 0.2)',
              borderWidth: 1,
              padding: 10,
              cornerRadius: 6,
              displayColors: false
            }
          },
          scales: {
            x: {
              grid: { 
                display: false,
                drawBorder: false
              },
              ticks: {
                color: '#666',
                font: {
                  size: 12
                }
              }
            },
            y: {
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false,
                tickLength: 10
              },
              ticks: {
                color: '#666',
                font: {
                  size: 12
                },
                padding: 10,
                callback: function(value) {
                  return value;
                }
              },
              beginAtZero: false
            }
          },
          elements: {
            line: {
              tension: 0.4, // This creates the smooth curve
              borderWidth: 3,
              backgroundColor: 'rgba(58, 134, 255, 0.1)',
              fill: true
            },
            point: {
              radius: 4,
              backgroundColor: '#fff',
              borderWidth: 2,
              hoverRadius: 6
            }
          }
        };

        // Speed Chart
        new Chart(document.getElementById('speedChart'), {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Speed (km/h)',
              data: speedData,
              borderColor: '#3a86ff',
              pointBorderColor: '#3a86ff',
              pointHoverBackgroundColor: '#3a86ff'
            }]
          },
          options: chartOptions
        });

        // Temperature Chart
        new Chart(document.getElementById('tempChart'), {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Temperature (°C)',
              data: tempData,
              borderColor: '#ff006e',
              backgroundColor: 'rgba(255, 0, 110, 0.1)',
              pointBorderColor: '#ff006e',
              pointHoverBackgroundColor: '#ff006e'
            }]
          },
          options: chartOptions
        });
      <?php endif; ?>
    });
</script>
</body>
</html>
