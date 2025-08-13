<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];
$sql = "SELECT latitude, longitude, gps_status, timestamp FROM helmet_data WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$latitude = $row['latitude'] ?? 23.8103; // Default to Dhaka coordinates
$longitude = $row['longitude'] ?? 90.4125;
$gps_status = $row['gps_status'] ?? 'Unknown';
$timestamp = $row['timestamp'] ?? 'N/A';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Live Location | Smart Helmet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #eef2ff;
      --secondary: #3f37c9;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --white: #ffffff;
      --success: #28a745;
      --warning: #ffc107;
      --danger: #dc3545;
      --border-radius: 8px;
      --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      --transition: all 0.3s ease;
    }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: var(--white);
      color: var(--dark);
      line-height: 1.6;
    }
    
    .sidebar {
      width: 280px;
      background: var(--white);
      position: fixed;
      height: 100vh;
      padding: 1.5rem;
      border-right: 1px solid rgba(0,0,0,0.05);
      box-shadow: var(--box-shadow);
      z-index: 100;
      transition: var(--transition);
    }
    
    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .brand img {
      width: 38px;
      height: 38px;
      border-radius: 10px;
    }
    
    .brand h5 {
      margin: 0;
      font-weight: 700;
      color: var(--primary);
      font-size: 1.2rem;
    }
    
    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--gray);
      padding: 0.75rem 1rem;
      border-radius: var(--border-radius);
      margin-bottom: 0.5rem;
      transition: var(--transition);
      text-decoration: none;
      font-weight: 500;
    }
    
    .nav-link:hover, .nav-link.active {
      background: var(--primary-light);
      color: var(--primary);
    }
    
    .nav-link i {
      width: 20px;
      text-align: center;
    }
    
    .content {
      margin-left: 280px;
      padding: 2rem;
      background-color: var(--white);
      min-height: 100vh;
    }
    
    .navbar {
      margin-left: 280px;
      background: var(--white);
      padding: 1rem 2rem;
      border-bottom: 1px solid rgba(0,0,0,0.05);
      box-shadow: 0 2px 10px rgba(0,0,0,0.02);
      position: sticky;
      top: 0;
      z-index: 99;
    }
    
    .card {
      border: none;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      transition: var(--transition);
      background-color: var(--white);
    }
    
    .card:hover {
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .card-header {
      background-color: var(--white);
      border-bottom: 1px solid rgba(0,0,0,0.05);
      padding: 1.5rem;
    }
    
    .card-body {
      padding: 2rem;
    }
    
    .map-header {
      display: flex;
      align-items: center;
      margin-bottom: 2rem;
    }
    
    .map-icon {
      font-size: 2rem;
      color: var(--primary);
      margin-right: 1.5rem;
      background: var(--primary-light);
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }
    
    #map {
      height: 500px;
      width: 100%;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      z-index: 1;
    }
    
    .location-info {
      background: var(--primary-light);
      border-radius: var(--border-radius);
      padding: 1.5rem;
      margin-top: 1.5rem;
    }
    
    .info-item {
      margin-bottom: 1rem;
    }
    
    .info-label {
      font-weight: 600;
      color: var(--gray);
      margin-bottom: 0.25rem;
      font-size: 0.9rem;
    }
    
    .info-value {
      font-size: 1.1rem;
      font-weight: 500;
    }
    
    .gps-status {
      display: inline-flex;
      align-items: center;
      padding: 0.35rem 0.75rem;
      border-radius: 20px;
      font-weight: 500;
      font-size: 0.85rem;
    }
    
    .gps-status.active {
      background: rgba(40, 167, 69, 0.1);
      color: var(--success);
    }
    
    .gps-status.inactive {
      background: rgba(220, 53, 69, 0.1);
      color: var(--danger);
    }
    
    .refresh-btn {
      background: var(--primary);
      color: white;
      border: none;
      border-radius: var(--border-radius);
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .refresh-btn:hover {
      background: var(--secondary);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(67, 97, 238, 0.2);
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
      .sidebar {
        position: fixed;
        width: 100%;
        height: auto;
        bottom: 0;
        top: auto;
        height: 70px;
        padding: 0.5rem;
        display: flex;
        align-items: center;
      }
      
      .brand {
        display: none;
      }
      
      .nav {
        flex-direction: row;
        width: 100%;
        justify-content: space-around;
        gap: 0;
      }
      
      .nav-link {
        flex-direction: column;
        font-size: 0.7rem;
        padding: 0.5rem;
        margin-bottom: 0;
      }
      
      .nav-link i {
        font-size: 1.2rem;
        margin-bottom: 0.2rem;
      }
      
      .navbar, .content {
        margin-left: 0;
        margin-bottom: 70px;
      }
      
      .content {
        padding: 1.5rem;
      }
      
      #map {
        height: 400px;
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
    <nav class="nav flex-column">
      <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a class="nav-link active" href="#"><i class="fas fa-map-marker-alt"></i> Live Location</a>
      <a class="nav-link" href="logs.php"><i class="fas fa-database"></i> Data Logs</a>
      <a class="nav-link" href="alerts.php"><i class="fas fa-bell"></i> Alert History</a>
      <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Profile</a>
      <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
      <a class="nav-link text-danger" href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </aside>

  <!-- Top Navbar -->
  <nav class="navbar navbar-light">
    <div class="container-fluid">
      <span class="navbar-brand fw-bold">
        Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
      </span>
      <span class="text-muted small">
        <i class="fas fa-clock me-1"></i> <?= date('l, F j, Y') ?>
      </span>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="content">
    <div class="card">
      <div class="card-body">
        <div class="map-header">
          <div class="map-icon">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <div>
            <h2 class="mb-1">Live Helmet Location</h2>
            <p class="text-muted mb-0">Real-time tracking of your smart helmet</p>
          </div>
        </div>
        
        <div id="map"></div>
        
        <div class="location-info">
          <div class="row">
            <div class="col-md-4">
              <div class="info-item">
                <div class="info-label">Coordinates</div>
                <div class="info-value"><?= $latitude ?>, <?= $longitude ?></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-item">
                <div class="info-label">GPS Status</div>
                <div class="info-value">
                  <span class="gps-status <?= $gps_status === 'Active' ? 'active' : 'inactive' ?>">
                    <i class="fas fa-<?= $gps_status === 'Active' ? 'check-circle' : 'times-circle' ?> me-1"></i>
                    <?= htmlspecialchars($gps_status) ?>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-item">
                <div class="info-label">Last Updated</div>
                <div class="info-value"><?= htmlspecialchars($timestamp) ?></div>
              </div>
            </div>
          </div>
          <button class="refresh-btn mt-3" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Refresh Location
          </button>
        </div>
      </div>
    </div>
  </main>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // Initialize map with default Dhaka coordinates
    const lat = <?= $latitude ?>;
    const lng = <?= $longitude ?>;
    
    const map = L.map('map').setView([lat, lng], 15);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Custom helmet icon
    const helmetIcon = L.icon({
      iconUrl: '../assets/images/helmet-marker.png',
      iconSize: [32, 32],
      iconAnchor: [16, 32],
      popupAnchor: [0, -32]
    });
    
    // Add marker with custom icon
    const marker = L.marker([lat, lng], { icon: helmetIcon }).addTo(map)
      .bindPopup('<b>Helmet Location</b><br>Last known position')
      .openPopup();
    
    // Add circle around marker for accuracy visualization
    L.circle([lat, lng], {
      color: '#4361ee',
      fillColor: '#eef2ff',
      fillOpacity: 0.3,
      radius: 50
    }).addTo(map);
    
    // Auto-refresh every 15 seconds
    setTimeout(function() {
      window.location.reload();
    }, 15000);
  </script>
</body>
</html>
