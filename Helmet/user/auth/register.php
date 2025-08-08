<?php
require_once '../../backend/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hash);
            $stmt->execute();
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="form-control mb-2">
            <input type="email" name="email" placeholder="Email" required class="form-control mb-2">
            <input type="password" name="password" placeholder="Password" required class="form-control mb-2">
            <input type="password" name="confirm" placeholder="Confirm Password" required class="form-control mb-2">
            <button type="submit" class="btn btn-success">Register</button>
        </form>
    </div>
</body>
</html>
