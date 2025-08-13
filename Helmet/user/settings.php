<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT email, phone, notification_pref, theme_pref FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $notification_pref = $_POST['notification_pref'] ?? 'all';
    $theme_pref = $_POST['theme_pref'] ?? 'light';
    $update = $conn->prepare("UPDATE users SET phone=?, notification_pref=?, theme_pref=? WHERE id=?");
    $update->bind_param("sssi", $phone, $notification_pref, $theme_pref, $user_id);
    if ($update->execute()) {
        $success = 'Settings updated successfully!';
        $user['phone'] = $phone;
        $user['notification_pref'] = $notification_pref;
        $user['theme_pref'] = $theme_pref;
    } else {
        $success = 'Failed to update settings.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Settings</title>
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
      <a class="nav-link<?= basename($_SERVER['PHP_SELF'])=='profile.php'?' active':'' ?>" href="profile.php">
        <i class="fas fa-user me-2"></i>Profile
      </a>
      <a class="nav-link active" href="#">
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
        <i class="fas fa-cog me-2 text-primary"></i>Account Settings
      </span>
    </div>
  </nav>

  <div class="content" style="margin-left:220px; padding:40px 20px; min-height:90vh; background:#f8f9fa;">
    <div class="card bg-white shadow p-4 mb-4" style="max-width:600px; margin:auto; border:none; border-radius:12px;">
      <h2 class="fw-bold mb-4" style="font-size:1.8rem; color:#2c3e50;">
        <i class="fas fa-user-cog text-primary me-2"></i>Settings
      </h2>
      
      <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center py-2">
          <i class="fas fa-check-circle me-2"></i>
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" autocomplete="off">
        <div class="mb-4">
          <label class="form-label fw-semibold text-muted">Account Information</label>
          <div class="card border-light p-3 bg-light">
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-envelope text-muted"></i></span>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '-') ?>" readonly>
              </div>
            </div>
            <div class="mb-0">
              <label class="form-label">Phone Number</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-phone text-muted"></i></span>
                <input type="text" name="phone" class="form-control" 
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                       placeholder="Enter phone number">
              </div>
            </div>
          </div>
        </div>
        
        <div class="mb-4">
          <label class="form-label fw-semibold text-muted">Notification Preferences</label>
          <div class="card border-light p-3 bg-light">
            <div class="mb-3">
              <label class="form-label">Alert Notifications</label>
              <select name="notification_pref" class="form-select">
                <option value="all" <?= ($user['notification_pref']??'all')=='all'?'selected':'' ?>>
                  <i class="fas fa-bell me-2"></i> All Alerts
                </option>
                <option value="critical" <?= ($user['notification_pref']??'all')=='critical'?'selected':'' ?>>
                  Critical Alerts Only
                </option>
                <option value="none" <?= ($user['notification_pref']??'all')=='none'?'selected':'' ?>>
                  No Notifications
                </option>
              </select>
            </div>
          </div>
        </div>
        
        <div class="mb-4">
          <label class="form-label fw-semibold text-muted">Appearance</label>
          <div class="card border-light p-3 bg-light">
            <div class="mb-0">
              <label class="form-label">Theme Preference</label>
              <select name="theme_pref" class="form-select">
                <option value="light" <?= ($user['theme_pref']??'light')=='light'?'selected':'' ?>>
                  Light Mode
                </option>
                <option value="dark" <?= ($user['theme_pref']??'light')=='dark'?'selected':'' ?>>
                  Dark Mode
                </option>
              </select>
            </div>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
          <i class="fas fa-save me-2"></i>Save Changes
        </button>
      </form>
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
    
    .form-label {
      color: #495057;
      font-weight: 500;
      font-size: 0.95rem;
      margin-bottom: 6px;
    }
    
    .navbar {
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .card {
      border: none;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    
    .input-group-text {
      border-right: none;
    }
    
    .form-control:focus, .form-select:focus {
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
      border-color: #4361ee;
    }
    
    .btn-primary {
      background-color: #4361ee;
      border: none;
      padding: 10px;
      font-size: 1rem;
    }
    
    .btn-primary:hover {
      background-color: #3a56d4;
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