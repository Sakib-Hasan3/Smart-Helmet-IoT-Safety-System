<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <ul>
            <li><a href="users.php" class="text-light">Manage Users</a></li>
            <li><a href="logs.php" class="text-light">View Helmet Logs</a></li>
            <li><a href="auth/logout.php" class="text-danger">Logout</a></li>
        </ul>
    </div>
</body>
</html>
