<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit;
}
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Smart Helmet – IoT Safety System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #0d1117;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }

    .hero {
      text-align: center;
      padding: 80px 20px 40px;
    }

    .hero h1 {
      font-size: 2.8rem;
      font-weight: bold;
    }

    .hero .subheading {
      color: #c9d1d9;
      font-size: 1.1rem;
      margin-bottom: 30px;
    }

    .btn-custom {
      margin: 0 10px;
      padding: 10px 20px;
      font-weight: 500;
    }

    .feature-card {
      background: #161b22;
      border-radius: 10px;
      padding: 25px;
      text-align: center;
      transition: all 0.3s ease;
      border: 1px solid #30363d;
    }

    .feature-card:hover {
      background: #21262d;
      transform: translateY(-5px);
    }

    .feature-icon {
      font-size: 2rem;
      margin-bottom: 10px;
    }

    .status-box {
      background: #161b22;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      margin-top: 40px;
    }

    .status-box h5 {
      color: #c9d1d9;
      font-size: 0.95rem;
      margin-bottom: 5px;
    }

    .status-box p {
      font-size: 1.4rem;
      font-weight: bold;
    }

    .top-nav {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    .top-nav a {
      margin-left: 10px;
    }
  </style>
</head>
<body>

  <div class="top-nav">
    <a href="user/auth/login.php" class="btn btn-outline-light btn-sm">Login</a>
    <a href="user/auth/register.php" class="btn btn-primary btn-sm">Sign Up</a>
  </div>

  <div class="hero">
    <img src="assets/images/logo.png" alt="Smart Helmet Logo" width="80" class="mb-3">
    <h1><span style="color: #3fb950;">Smart Helmet</span> <br> IoT Safety System</h1>
    <p class="subheading">Advanced IoT-powered safety monitoring for motorcycle riders. Real-time alerts, GPS tracking, and emergency response in one intelligent helmet.</p>
    <a href="user/dashboard.php" class="btn btn-success btn-custom">Access Dashboard</a>
    <a href="#" class="btn btn-outline-light btn-custom">Learn More</a>
  </div>

  <div class="container mt-5">
    <div class="row text-center g-4">
      <div class="col-md-3">
        <div class="feature-card text-info">
          <div class="feature-icon">📈</div>
          <h5>Live Monitoring</h5>
          <p>Real-time sensor data from gas, rain, vibration, and health monitors.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card text-success">
          <div class="feature-icon">🔔</div>
          <h5>Smart Alerts</h5>
          <p>Instant notifications for drowsiness, speed limits, and safety hazards.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card text-warning">
          <div class="feature-icon">📍</div>
          <h5>GPS Tracking</h5>
          <p>Live location tracking with route history and emergency location sharing.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-card text-danger">
          <div class="feature-icon">🛡️</div>
          <h5>Emergency Response</h5>
          <p>Automatic crash detection with alerts and location sharing.</p>
        </div>
      </div>
    </div>

    <div class="status-box mt-5">
      <h4 class="mb-3">Live System Status</h4>
      <div class="row text-center">
        <div class="col-3">
          <h5>Active Sensors</h5>
          <p class="text-success">8</p>
        </div>
        <div class="col-3">
          <h5>Uptime</h5>
          <p class="text-primary">99.9%</p>
        </div>
        <div class="col-3">
          <h5>Monitoring</h5>
          <p class="text-warning">24/7</p>
        </div>
        <div class="col-3">
          <h5>Response</h5>
          <p class="text-danger">&lt;500ms</p>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
