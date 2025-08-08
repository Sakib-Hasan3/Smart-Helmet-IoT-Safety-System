<?php
session_start();
require_once '../../backend/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['admin_id'] = $id;
            header("Location: ../dashboard.php");
            exit;
        }
    }

    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h2>Admin Login</h2>
        <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="form-control mb-2">
            <input type="password" name="password" placeholder="Password" required class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
