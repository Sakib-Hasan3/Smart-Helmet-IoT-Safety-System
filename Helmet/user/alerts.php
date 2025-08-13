<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}
require_once '../backend/db.php';

$user_id = $_SESSION["user_id"];
$result = $conn->query("SELECT type, created_at, is_read FROM helmet_alerts WHERE user_id = $user_id ORDER BY created_at DESC");

// Mark all alerts as read when page loads
if ($result->num_rows > 0) {
    $conn->query("UPDATE helmet_alerts SET is_read = 1 WHERE user_id = $user_id AND is_read = 0");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Alerts</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <div class="text-center mb-3">
      <img src="../assets/images/logo.png" alt="Logo" width="60">
      <h5>Smart Helmet</h5>
    </div>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
    <a href="map.php"><i class="fas fa-map-marker-alt me-2"></i>Live Location</a>
    <a href="logs.php"><i class="fas fa-database me-2"></i>Data Logs</a>
    <a href="#" class="active"><i class="fas fa-bell me-2"></i>Alert History</a>
    <a href="profile.php"><i class="fas fa-user me-2"></i>Profile</a>
    <a href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a>
    <a href="emergency.php"><i class="fas fa-exclamation-triangle me-2"></i>Emergency</a>
    <a href="auth/logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
  </div>

  <nav class="navbar">
    <div class="container-fluid">
      <span class="navbar-brand">
        Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
      </span>
      <div class="user-info">
        <div class="notification-badge" id="unreadCount"></div>
        <i class="fas fa-bell notification-icon"></i>
        <img src="../assets/images/user-avatar.png" alt="User" class="user-avatar">
      </div>
    </div>
  </nav>

  <div class="content">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-bell text-danger me-3"></i> Alert History</h1>
        <p>Review all safety alerts and notifications from your smart helmet</p>
      </div>
      <div class="header-actions">
        <button class="btn btn-outline-secondary" id="filterBtn">
          <i class="fas fa-filter me-2"></i>Filter
        </button>
        <button class="btn btn-outline-danger" id="clearAllBtn">
          <i class="fas fa-trash-alt me-2"></i>Clear All
        </button>
      </div>
    </div>

    <div class="card alert-container">
      <div class="filter-panel" id="filterPanel">
        <div class="filter-group">
          <label>Alert Type</label>
          <select class="form-select" id="alertTypeFilter">
            <option value="all">All Alerts</option>
            <option value="gas">Gas Leak</option>
            <option value="fall">Fall Detection</option>
            <option value="drowsiness">Drowsiness</option>
            <option value="rain">Rain Warning</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="filter-group">
          <label>Date Range</label>
          <input type="date" class="form-control" id="dateFromFilter">
        </div>
        <div class="filter-group">
          <input type="date" class="form-control" id="dateToFilter">
        </div>
        <div class="filter-group">
          <label>Status</label>
          <select class="form-select" id="statusFilter">
            <option value="all">All Status</option>
            <option value="read">Read</option>
            <option value="unread">Unread</option>
          </select>
        </div>
        <button class="btn btn-primary" id="applyFilterBtn">
          <i class="fas fa-check me-2"></i>Apply
        </button>
      </div>

      <?php
      // Alert type to icon/color mapping
      function alertIcon($type) {
        $map = [
          'gas' => ['fas fa-fire','text-danger','Gas Leak'],
          'fall' => ['fas fa-exclamation-triangle','text-danger','Fall Detected'],
          'drowsiness' => ['fas fa-bed','text-warning','Drowsiness'],
          'rain' => ['fas fa-cloud-rain','text-primary','Rain Warning'],
        ];
        $t = strtolower($type);
        foreach($map as $k=>$v) if(strpos($t,$k)!==false) return $v;
        return ['fas fa-exclamation','text-secondary','Other'];
      }
      ?>
      
      <div class="alert-list-header">
        <div class="alert-count">
          <?= $result->num_rows ?> alert<?= $result->num_rows != 1 ? 's' : '' ?>
        </div>
        <div class="alert-sort">
          <select class="form-select" id="sortAlerts">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
          </select>
        </div>
      </div>

      <?php if ($result->num_rows > 0): ?>
        <div class="alert-list" id="alertList">
        <?php while ($row = $result->fetch_assoc()):
          list($icon,$color,$label) = alertIcon($row['type']); 
          $isRead = isset($row['is_read']) && $row['is_read'] ? 'read' : '';
          ?>
          <div class="alert-card <?= $isRead ?>" data-type="<?= strtolower($label) ?>" data-date="<?= date('Y-m-d', strtotime($row['created_at'])) ?>">
            <div class="alert-icon">
              <i class="<?= $icon ?> <?= $color ?>"></i>
            </div>
            <div class="alert-content">
              <div class="alert-title">
                <span class="badge <?= $color ?>"><?= $label ?></span>
                <span class="alert-time"><?= htmlspecialchars($row['created_at']) ?></span>
              </div>
              <div class="alert-message"><?= htmlspecialchars($row['type']) ?></div>
            </div>
            <div class="alert-actions">
              <button class="btn btn-sm btn-outline-secondary mark-btn" title="Mark as <?= $isRead ? 'unread' : 'read' ?>">
                <i class="fas fa-<?= $isRead ? 'envelope' : 'envelope-open' ?>"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger delete-btn" title="Delete">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>
        <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="no-alerts">
          <img src="../assets/images/no-alerts.svg" alt="No alerts" width="200">
          <h3>No alerts found</h3>
          <p>You don't have any alerts yet. All clear!</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Toggle filter panel
    document.getElementById('filterBtn').addEventListener('click', function() {
      document.getElementById('filterPanel').classList.toggle('active');
    });

    // Filter alerts (this would be implemented with AJAX in a real application)
    document.getElementById('applyFilterBtn').addEventListener('click', function() {
      const typeFilter = document.getElementById('alertTypeFilter').value;
      const dateFrom = document.getElementById('dateFromFilter').value;
      const dateTo = document.getElementById('dateToFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      const sortOrder = document.getElementById('sortAlerts').value;
      
      // In a real app, you would make an AJAX call here
      // For demo, we'll just show/hide elements
      const alerts = document.querySelectorAll('.alert-card');
      alerts.forEach(alert => {
        const alertType = alert.dataset.type;
        const alertDate = alert.dataset.date;
        const isRead = alert.classList.contains('read');
        
        let show = true;
        
        // Filter by type
        if (typeFilter !== 'all' && !alertType.includes(typeFilter)) {
          show = false;
        }
        
        // Filter by date
        if (dateFrom && alertDate < dateFrom) {
          show = false;
        }
        if (dateTo && alertDate > dateTo) {
          show = false;
        }
        
        // Filter by status
        if (statusFilter === 'read' && !isRead) {
          show = false;
        }
        if (statusFilter === 'unread' && isRead) {
          show = false;
        }
        
        alert.style.display = show ? 'flex' : 'none';
      });
      
      // Update alert count
      const visibleAlerts = document.querySelectorAll('.alert-card[style="display: flex;"]').length;
      document.querySelector('.alert-count').textContent = 
        `${visibleAlerts} alert${visibleAlerts !== 1 ? 's' : ''}`;
    });

    // Clear all alerts
    document.getElementById('clearAllBtn').addEventListener('click', function() {
      if (confirm('Are you sure you want to delete all alerts?')) {
        // In a real app, you would make an AJAX call here
        document.getElementById('alertList').innerHTML = `
          <div class="no-alerts">
            <img src="../assets/images/no-alerts.svg" alt="No alerts" width="200">
            <h3>No alerts found</h3>
            <p>You don't have any alerts yet. All clear!</p>
          </div>
        `;
        document.querySelector('.alert-count').textContent = '0 alerts';
      }
    });

    // Sort alerts
    document.getElementById('sortAlerts').addEventListener('change', function() {
      const sortOrder = this.value;
      const alertList = document.getElementById('alertList');
      const alerts = Array.from(alertList.querySelectorAll('.alert-card'));
      
      alerts.sort((a, b) => {
        const dateA = new Date(a.querySelector('.alert-time').textContent);
        const dateB = new Date(b.querySelector('.alert-time').textContent);
        return sortOrder === 'newest' ? dateB - dateA : dateA - dateB;
      });
      
      // Re-append sorted alerts
      alerts.forEach(alert => alertList.appendChild(alert));
    });

    // Mark as read/unread
    document.addEventListener('click', function(e) {
      if (e.target.closest('.mark-btn')) {
        const alertCard = e.target.closest('.alert-card');
        alertCard.classList.toggle('read');
        
        const icon = e.target.closest('.mark-btn').querySelector('i');
        if (alertCard.classList.contains('read')) {
          icon.classList.replace('fa-envelope', 'fa-envelope-open');
          icon.parentNode.title = 'Mark as unread';
        } else {
          icon.classList.replace('fa-envelope-open', 'fa-envelope');
          icon.parentNode.title = 'Mark as read';
        }
        
        // In a real app, you would make an AJAX call here to update the status
      }
      
      // Delete alert
      if (e.target.closest('.delete-btn')) {
        const alertCard = e.target.closest('.alert-card');
        if (confirm('Are you sure you want to delete this alert?')) {
          // In a real app, you would make an AJAX call here
          alertCard.remove();
          
          // Update alert count
          const visibleAlerts = document.querySelectorAll('.alert-card').length;
          document.querySelector('.alert-count').textContent = 
            `${visibleAlerts} alert${visibleAlerts !== 1 ? 's' : ''}`;
            
          if (visibleAlerts === 0) {
            document.getElementById('alertList').innerHTML = `
              <div class="no-alerts">
                <img src="../assets/images/no-alerts.svg" alt="No alerts" width="200">
                <h3>No alerts found</h3>
                <p>You don't have any alerts yet. All clear!</p>
              </div>
            `;
          }
        }
      }
    });
  </script>

  <style>
    :root {
      --primary: #4361ee;
      --primary-light: rgba(67, 97, 238, 0.1);
      --secondary: #3f37c9;
      --danger: #f72585;
      --danger-light: rgba(247, 37, 133, 0.1);
      --warning: #f8961e;
      --warning-light: rgba(248, 150, 30, 0.1);
      --success: #4cc9f0;
      --info: #4895ef;
      --dark: #212529;
      --gray: #6c757d;
      --light-gray: #e9ecef;
      --border: #dee2e6;
      --border-radius: 12px;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }
    
    body {
      background-color: #f8f9fa;
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
      background-color: var(--primary-light);
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
      background-color: var(--danger-light);
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
    
    .sidebar i {
      width: 24px;
      text-align: center;
      margin-right: 12px;
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
      position: relative;
    }
    
    .notification-icon {
      font-size: 1.2rem;
      color: var(--gray);
      cursor: pointer;
    }
    
    .notification-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: var(--danger);
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      font-weight: bold;
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
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 30px;
      flex-wrap: wrap;
      gap: 20px;
    }
    
    .header-content h1 {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--dark);
      display: flex;
      align-items: center;
      margin-bottom: 8px;
    }
    
    .header-content p {
      color: var(--gray);
      font-size: 0.95rem;
    }
    
    .header-content i {
      margin-right: 16px;
    }
    
    .header-actions {
      display: flex;
      gap: 12px;
    }
    
    .btn {
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s;
      border: 1px solid transparent;
      font-size: 0.95rem;
    }
    
    .btn-outline-secondary {
      background-color: transparent;
      border-color: var(--gray);
      color: var(--gray);
    }
    
    .btn-outline-secondary:hover {
      background-color: var(--light-gray);
    }
    
    .btn-outline-danger {
      background-color: transparent;
      border-color: var(--danger);
      color: var(--danger);
    }
    
    .btn-outline-danger:hover {
      background-color: var(--danger-light);
    }
    
    .btn-primary {
      background-color: var(--primary);
      color: white;
      border-color: var(--primary);
    }
    
    .btn-primary:hover {
      background-color: var(--secondary);
      transform: translateY(-1px);
    }
    
    .alert-container {
      background: #fff;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
      border: 1px solid var(--border);
      padding: 30px;
      overflow: hidden;
    }
    
    .filter-panel {
      display: none;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
      padding: 20px;
      background-color: var(--light-gray);
      border-radius: 8px;
    }
    
    .filter-panel.active {
      display: grid;
    }
    
    .filter-group {
      display: flex;
      flex-direction: column;
    }
    
    .filter-group label {
      margin-bottom: 8px;
      font-size: 0.9rem;
      color: var(--gray);
      font-weight: 500;
    }
    
    .form-select, .form-control {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.95rem;
      transition: all 0.3s;
      background-color: #fff;
    }
    
    .form-select:focus, .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }
    
    .alert-list-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border);
    }
    
    .alert-count {
      font-weight: 600;
      color: var(--dark);
    }
    
    .alert-sort .form-select {
      width: auto;
      min-width: 160px;
    }
    
    .alert-list {
      max-height: 60vh;
      overflow-y: auto;
      padding-right: 8px;
    }
    
    .alert-card {
      display: flex;
      align-items: center;
      padding: 16px;
      margin-bottom: 12px;
      border-radius: 8px;
      background-color: #fff;
      border: 1px solid var(--border);
      transition: all 0.3s;
    }
    
    .alert-card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transform: translateY(-2px);
    }
    
    .alert-card.read {
      background-color: var(--light-gray);
      opacity: 0.8;
    }
    
    .alert-icon {
      font-size: 1.5rem;
      margin-right: 16px;
      width: 40px;
      text-align: center;
    }
    
    .alert-content {
      flex-grow: 1;
    }
    
    .alert-title {
      display: flex;
      align-items: center;
      margin-bottom: 6px;
      flex-wrap: wrap;
      gap: 12px;
    }
    
    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
    }
    
    .text-danger {
      color: var(--danger);
    }
    
    .bg-danger {
      background-color: var(--danger);
      color: white;
    }
    
    .text-warning {
      color: var(--warning);
    }
    
    .bg-warning {
      background-color: var(--warning);
      color: white;
    }
    
    .text-primary {
      color: var(--primary);
    }
    
    .bg-primary {
      background-color: var(--primary);
      color: white;
    }
    
    .text-secondary {
      color: var(--gray);
    }
    
    .bg-secondary {
      background-color: var(--gray);
      color: white;
    }
    
    .alert-time {
      font-size: 0.85rem;
      color: var(--gray);
    }
    
    .alert-message {
      font-weight: 500;
      color: var(--dark);
    }
    
    .alert-actions {
      display: flex;
      gap: 8px;
      margin-left: 16px;
    }
    
    .btn-sm {
      padding: 6px 10px;
      font-size: 0.85rem;
    }
    
    .no-alerts {
      text-align: center;
      padding: 40px 20px;
    }
    
    .no-alerts img {
      opacity: 0.7;
      margin-bottom: 20px;
    }
    
    .no-alerts h3 {
      margin-bottom: 8px;
      color: var(--dark);
      font-weight: 600;
    }
    
    .no-alerts p {
      color: var(--gray);
    }
    
    /* Scrollbar styling */
    .alert-list::-webkit-scrollbar {
      width: 8px;
    }
    
    .alert-list::-webkit-scrollbar-track {
      background: var(--light-gray);
      border-radius: 4px;
    }
    
    .alert-list::-webkit-scrollbar-thumb {
      background: var(--gray);
      border-radius: 4px;
    }
    
    .alert-list::-webkit-scrollbar-thumb:hover {
      background: var(--dark);
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
      
      .filter-panel {
        grid-template-columns: 1fr;
      }
      
      .header-actions {
        width: 100%;
      }
      
      .btn {
        flex-grow: 1;
      }
    }
  </style>
</body>
</html>
