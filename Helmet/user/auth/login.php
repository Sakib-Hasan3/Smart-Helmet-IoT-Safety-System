<?php
session_start();
require_once '../../backend/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: ../dashboard.php");
            exit;
        }
    }

    $error = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required class="form-control mb-2">
            <input type="password" name="password" placeholder="Password" required class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
