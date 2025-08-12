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
$latitude = $row['latitude'] ?? 0;
$longitude = $row['longitude'] ?? 0;
$gps_status = $row['gps_status'] ?? 'Unknown';
$timestamp = $row['timestamp'] ?? 'N/A';
?>

<!DOCTYPE html>
<html>
<head>
  <title>GPS Location | Smart Helmet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --white: #ffffff;
    }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: var(--white);
      color: var(--dark);
    }
    
    .sidebar {
      width: 260px;
      background: var(--white);
      position: fixed;
      height: 100vh;
      padding: 1.5rem;
      border-right: 1px solid rgba(0,0,0,0.05);
      box-shadow: 0 0 20px rgba(0,0,0,0.03);
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
    }
    
    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--gray);
      padding: 0.75rem 1rem;
      border-radius: 8px;
      margin-bottom: 0.5rem;
      transition: all 0.2s ease;
      text-decoration: none;
    }
    
    .nav-link:hover, .nav-link.active {
      background: rgba(67, 97, 238, 0.1);
      color: var(--primary);
    }
    
    .content {
      margin-left: 260px;
      padding: 2rem;
    }
    
    .navbar {
      margin-left: 260px;
      background: var(--white);
      padding: 1rem 2rem;
      border-bottom: 1px solid rgba(0,0,0,0.05);
      box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    
    #map {
      height: 400px;
      width: 100%;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .gps-status {
      font-weight: 600;
    }
    
    .gps-status.active {
      color: #28a745;
    }
    
    .gps-status.inactive {
      color: #dc3545;
    }
    
    @media (max-width: 992px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }
      .navbar, .content {
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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold mb-1">Live GPS Tracking</h2>
        <p class="text-muted mb-0">Real-time location of your smart helmet</p>
      </div>
      <span class="badge bg-success">
        <i class="fas fa-circle me-1"></i> Active
      </span>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <h5 class="card-title mb-3"><i class="fas fa-info-circle me-2"></i>Location Details</h5>
            <div class="mb-3">
              <h6 class="text-muted mb-1">Coordinates</h6>
              <p class="fs-5"><?= $latitude ?>, <?= $longitude ?></p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted mb-1">GPS Status</h6>
              <p class="fs-5 gps-status <?= $gps_status === 'Active' ? 'active' : 'inactive' ?>">
                <i class="fas fa-<?= $gps_status === 'Active' ? 'check-circle' : 'times-circle' ?> me-2"></i>
                <?= htmlspecialchars($gps_status) ?>
              </p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted mb-1">Last Updated</h6>
              <p class="fs-5"><?= htmlspecialchars($timestamp) ?></p>
            </div>
            <button class="btn btn-primary mt-2">
              <i class="fas fa-sync-alt me-2"></i>Refresh Location
            </button>
          </div>
          <div class="col-md-8">
            <h5 class="card-title mb-3"><i class="fas fa-map-marked-alt me-2"></i>Location Map</h5>
            <div id="map"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="alert alert-info">
      <i class="fas fa-info-circle me-2"></i>
      This map shows your helmet's last known location. The data updates every 15 seconds automatically.
    </div>
  </main>

  <!-- Google Maps API -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>
  <script>
    function initMap() {
      // Default to Bangladesh center if no coordinates
      let lat = parseFloat("<?= $latitude ?>") || 23.6850;
      let lng = parseFloat("<?= $longitude ?>") || 90.3563;
      
      const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: lat, lng: lng },
        zoom: 15,
        mapTypeId: "roadmap",
        styles: [
          {
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [{ "color": "#444444" }]
          },
          {
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [{ "color": "#f2f2f2" }]
          },
          {
            "featureType": "poi",
            "elementType": "all",
            "stylers": [{ "visibility": "off" }]
          },
          {
            "featureType": "road",
            "elementType": "all",
            "stylers": [{ "saturation": -100 }, { "lightness": 45 }]
          },
          {
            "featureType": "road.highway",
            "elementType": "all",
            "stylers": [{ "visibility": "simplified" }]
          },
          {
            "featureType": "road.arterial",
            "elementType": "labels.icon",
            "stylers": [{ "visibility": "off" }]
          },
          {
            "featureType": "transit",
            "elementType": "all",
            "stylers": [{ "visibility": "off" }]
          },
          {
            "featureType": "water",
            "elementType": "all",
            "stylers": [{ "color": "#d4e6f4" }, { "visibility": "on" }]
          }
        ]
      });

      const marker = new google.maps.Marker({
        position: { lat: lat, lng: lng },
        map: map,
        title: "Helmet Location",
        icon: {
          url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
        }
      });

      const infoWindow = new google.maps.InfoWindow({
        content: `<div style="padding: 10px;">
          <h5 style="margin: 0 0 5px;">Helmet Location</h5>
          <p style="margin: 0;">Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}</p>
        </div>`
      });
      
      marker.addListener("click", () => {
        infoWindow.open(map, marker);
      });
      infoWindow.open(map, marker);
    }
  </script>
</body>
</html>
