<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT username, email, gender, phone, rider_id, address, emergency_contact_name, emergency_contact_number, heart_problem FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

function field($label, $value, $icon = '') {
    $iconHtml = $icon ? "<span class='me-2'>$icon</span>" : '';
    return "<div class='mb-3'><label class='form-label fw-bold'>$iconHtml$label</label><div class='form-control bg-light'>$value</div></div>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Profile</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
  <div class="sidebar">
    <div class="text-center mb-3 pt-3">
      <img src="../assets/images/logo.png" alt="Logo" width="60" class="mb-2">
      <h5 class="text-primary">Smart Helmet</h5>
    </div>
    <nav class="nav flex-column px-3">
      <a class="nav-link<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?' active':'' ?>" href="dashboard.php">
        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
      </a>
      <a class="nav-link<?= basename($_SERVER['PHP_SELF'])=='map.php'?' active':'' ?>" href="map.php">
        <i class="fas fa-map-marker-alt me-2"></i>Live Location
      </a>
      <a class="nav-link<?= basename($_SERVER['PHP_SELF'])=='logs.php'?' active':'' ?>" href="logs.php">
        <i class="fas fa-database me-2"></i>Data Logs
      </a>
      <a class="nav-link<?= basename($_SERVER['PHP_SELF'])=='alerts.php'?' active':'' ?>" href="alerts.php">
        <i class="fas fa-bell me-2"></i>Alert History
      </a>
      <a class="nav-link active" href="#">
        <i class="fas fa-user me-2"></i>Profile
      </a>
      <a class="nav-link<?= basename($_SERVER['PHP_SELF'])=='settings.php'?' active':'' ?>" href="settings.php">
        <i class="fas fa-cog me-2"></i>Settings
      </a>
      <a class="nav-link<?= basename($_SERVER['PHP_SELF'])=='emergency.php'?' active':'' ?>" href="emergency.php">
        <i class="fas fa-exclamation-triangle me-2"></i>Emergency
      </a>
      <a class="nav-link text-danger" href="auth/logout.php">
        <i class="fas fa-sign-out-alt me-2"></i>Logout
      </a>
    </nav>
  </div>

  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm" style="margin-left:220px;">
    <div class="container-fluid">
      <span class="navbar-brand text-dark fw-bold">
        <i class="fas fa-user-circle me-2"></i>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
      </span>
    </div>
  </nav>

  <div class="content" style="margin-left:220px; padding:40px 20px; min-height:90vh; background:#f8f9fa;">
    <div class="card bg-white shadow p-4 mb-4" style="max-width:900px; margin:auto; border:none; border-radius:12px;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="font-size:1.8rem; color:#2c3e50;">
          <i class="fas fa-id-card me-2 text-primary"></i>Profile Information
        </h2>
        <a href="settings.php" class="btn btn-primary">
          <i class="fas fa-edit me-1"></i> Edit Profile
        </a>
      </div>
      
      <div class="row mb-2">
        <div class="col-md-4 text-center mb-4 mb-md-0">
          <div class="position-relative" style="width:fit-content; margin:0 auto;">
            <img src="../assets/images/logo.png" alt="Profile" width="120" class="rounded-circle shadow" style="border:3px solid #e9ecef; padding:5px; background:#fff;">
            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2" style="border:2px solid #fff;"></span>
          </div>
          <div class="mt-3">
            <h3 class="fw-bold mb-1" style="color:#2c3e50;"><?= htmlspecialchars($user['username'] ?? 'User') ?></h3>
            <div class="badge bg-primary bg-opacity-10 text-primary py-2 px-3 rounded-pill">
              Rider ID: <?= htmlspecialchars($user['rider_id'] ?? '-') ?>
            </div>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="profile-label"><i class="fas fa-envelope me-2 text-muted"></i>Email</div>
              <div class="profile-value"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="profile-label"><i class="fas fa-venus-mars me-2 text-muted"></i>Gender</div>
              <div class="profile-value"><?= htmlspecialchars($user['gender'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="profile-label"><i class="fas fa-phone me-2 text-muted"></i>Phone</div>
              <div class="profile-value"><?= htmlspecialchars($user['phone'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="profile-label"><i class="fas fa-map-marker-alt me-2 text-muted"></i>Address</div>
              <div class="profile-value"><?= htmlspecialchars($user['address'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="profile-label"><i class="fas fa-exclamation-circle me-2 text-muted"></i>Emergency Contact</div>
              <div class="profile-value"><?= htmlspecialchars($user['emergency_contact_name'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="profile-label"><i class="fas fa-phone-volume me-2 text-muted"></i>Emergency Number</div>
              <div class="profile-value"><?= htmlspecialchars($user['emergency_contact_number'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="profile-label"><i class="fas fa-heart me-2 text-muted"></i>Heart Condition</div>
              <div class="profile-value">
                <?= htmlspecialchars($user['heart_problem'] ?? '-') ?>
                <?php if($user['heart_problem']): ?>
                  <span class="badge bg-danger bg-opacity-10 text-danger ms-2">Important</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="profile-label"><i class="fas fa-shield-alt me-2 text-muted"></i>Account Status</div>
              <div class="profile-value">
                <span class="badge bg-success bg-opacity-10 text-success py-2">Active</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="mt-4 pt-3 border-top">
        <h5 class="fw-bold mb-3" style="color:#2c3e50;"><i class="fas fa-history me-2 text-primary"></i>Recent Activity</h5>
        <div class="list-group">
          <div class="list-group-item border-0 py-3">
            <div class="d-flex justify-content-between">
              <span><i class="fas fa-check-circle text-success me-2"></i> Last login: Today, 10:45 AM</span>
              <small class="text-muted">2 hours ago</small>
            </div>
          </div>
          <div class="list-group-item border-0 py-3">
            <div class="d-flex justify-content-between">
              <span><i class="fas fa-bell text-warning me-2"></i> New alert: Heart rate spike detected</span>
              <small class="text-muted">Yesterday</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa !important;
    }
    
    .sidebar {
      width: 220px; 
      background: #fff;
      position: fixed; 
      height: 100vh; 
      box-shadow: 0 0 15px rgba(0,0,0,0.03);
      border-right: 1px solid #e9ecef;
    }
    
    .sidebar .nav-link {
      color: #495057;
      padding: 12px 15px;
      border-radius: 8px;
      margin: 2px 10px;
      font-size: 0.95rem;
      transition: all 0.2s;
    }
    
    .sidebar .nav-link:hover {
      background: #f1f3f5;
      color: #2c3e50;
    }
    
    .sidebar .nav-link.active {
      background: #4361ee;
      color: white;
      box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
    }
    
    .sidebar .nav-link.text-danger:hover {
      background: rgba(220, 53, 69, 0.1);
    }
    
    .profile-label {
      color: #6c757d;
      font-weight: 500;
      font-size: 0.9rem;
      margin-bottom: 4px;
    }
    
    .profile-value {
      background: #f8f9fa;
      color: #2c3e50;
      border-radius: 8px;
      padding: 10px 15px;
      font-weight: 500;
      border: 1px solid #e9ecef;
    }
    
    .navbar {
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .card {
      border: none;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    
    @media (max-width: 992px) {
      .content, .navbar {
        margin-left: 0;
      }
      
      .sidebar {
        width: 0;
        overflow: hidden;
      }
    }
  </style>
</body>
</html>
