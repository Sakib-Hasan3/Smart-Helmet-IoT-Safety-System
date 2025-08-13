<?php
require_once '../../backend/db.php';

// (Optional) CSRF protection
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // (Optional) CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request. Please try again.";
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];
        $gender = $_POST['gender'];
        $heart_problem = $_POST['heart_problem'];
        $phone = trim($_POST['phone']);
        $rider_id = trim($_POST['rider_id']);
        $address = trim($_POST['address']);
        $emergency_name = trim($_POST['emergency_name']);   // <-- FIX: define variable
        $emergency_phone = trim($_POST['emergency_phone']);

        if ($password !== $confirm) {
            $error = "Passwords do not match.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Email already registered.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);

                // If your table has an is_active column and you want new users active by default:
                // $stmt = $conn->prepare("INSERT INTO users (username, email, password, gender, heart_problem, phone, rider_id, address, emergency_name, emergency_phone, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");

                $stmt = $conn->prepare("INSERT INTO users (username, email, password, gender, heart_problem, phone, rider_id, address, emergency_name, emergency_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssss", $username, $email, $hash, $gender, $heart_problem, $phone, $rider_id, $address, $emergency_name, $emergency_phone);
                $stmt->execute();

                // Clear CSRF token to prevent double submits via back button
                unset($_SESSION['csrf_token']);

                header("Location: login.php?registered=1");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up – Smart Helmet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    :root{
      --primary:#3a86ff;
      --primary-dark:#2667cc;
      --light:#f8f9fa;
      --dark:#212529;
      --gray:#6c757d;
      --border:#e6e9ef;
      --shadow:0 10px 30px rgba(13,27,62,.08);
    }

    /* ---------- Navbar (same style as login) ---------- */
    .navbar-smart{
      background:#ffffff;
      border-bottom:1px solid var(--border);
      position:sticky; top:0; z-index:1020;
      box-shadow:0 2px 12px rgba(0,0,0,.03);
    }
    .navbar-brand span{ font-weight:700; letter-spacing:.2px; color:var(--dark); }
    .nav-link{ font-weight:500; color:#4b5563 !important; padding:.5rem .9rem !important; border-radius:.5rem; }
    .nav-link:hover{ background:#f3f4f6; color:#111827 !important; }
    .nav-link.active{ color:#111827 !important; background:#eef2ff; }

    /* ---------- Page ---------- */
    body{
      background:linear-gradient(180deg,#f6f9ff 0%, #ffffff 40%);
      font-family:'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    .login-main-row{
      min-height:calc(100vh - 64px); /* minus navbar */
      display:flex; align-items:stretch;
    }

    /* ---------- Illustration ---------- */
    .login-image-col{
      background:linear-gradient(135deg,#eaf3ff 0%,#f8f9fa 100%);
      display:flex; align-items:center; justify-content:center;
      flex:1 1 0%; min-width:0; padding:3rem 2rem; border-right:1px solid var(--border);
    }
    .login-image-col img{
      max-width:86%; max-height:72vh;
      border-radius:18px; box-shadow:var(--shadow);
    }

    /* ---------- Form ---------- */
    .login-form-col{
      background:#ffffff;
      display:flex; align-items:center; justify-content:center;
      flex:1 1 0%; min-width:0; padding:3rem 2rem;
    }
    .login-form-box{
      width:100%; max-width:720px; background:#fff; border-radius:16px;
      padding: 36px 28px;
      box-shadow:var(--shadow);
      border:1px solid var(--border);
    }
    .login-title{
      font-size:1.9rem; font-weight:800; color:#0f172a; margin-bottom:.25rem; text-align:center; letter-spacing:.2px;
    }
    .login-subtitle{
      color:#6b7280; text-align:center; margin-bottom:1.25rem;
    }
    .login-error{
      color:#b42318; background:#fee4e2; border:1px solid #fecdca;
      padding:.75rem 1rem; border-radius:.5rem; margin-bottom:1rem;
    }
    .form-control, select.form-control{
      height:50px; border-radius:10px; border:1px solid #e3e7ee;
      padding:.9rem 1.1rem; margin-bottom:1rem; background:#fff;
    }
    .form-control:focus, select.form-control:focus{
      border-color:var(--primary);
      box-shadow:0 0 0 .25rem rgba(58,134,255,.18);
    }
    .btn-login{
      background-color:var(--primary); border:none; color:#fff; font-weight:700; letter-spacing:.3px;
      padding:.85rem; border-radius:10px; width:100%; transition:transform .25s ease, filter .25s ease;
    }
    .btn-login:hover{ background-color:var(--primary-dark); transform:translateY(-1px); }
    .login-links{ text-align:center; margin-top:1rem; color:#6b7280; }
    .login-links a{ color:var(--primary); text-decoration:none; font-weight:600; }
    .login-links a:hover{ text-decoration:underline; }

    @media (max-width: 900px) {
      .login-main-row{ flex-direction:column; }
      .login-image-col{ border-right:none; border-bottom:1px solid var(--border); min-height:240px; }
      .login-form-box{ margin: 24px auto; }
    }
  </style>
</head>
<body>
  <!-- Navigation Bar (same as login) -->
  <nav class="navbar navbar-expand-lg navbar-smart">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../../index.php">
        <img src="../../assets/images/logo.png" alt="Logo" width="34" class="me-2">
        <span>Smart Helmet</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link active" href="#">Sign Up</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="login-main-row">
    <div class="login-image-col">
      <img src="../../assets/images/biker-placeholder.png" alt="Biker with Helmet">
    </div>

    <div class="login-form-col">
      <div class="login-form-box">
        <div class="login-title">Sign Up</div>
        <div class="login-subtitle">Create your Smart Helmet account</div>

        <?php if (isset($error)) echo "<div class='login-error'>".htmlspecialchars($error)."</div>"; ?>

        <form method="POST" autocomplete="off">
          <!-- CSRF token -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <input type="text" name="username" placeholder="Username" required class="form-control">
            </div>
            <div class="col-12 col-md-6">
              <input type="email" name="email" placeholder="Email address" required class="form-control" autocomplete="off">
            </div>
            <div class="col-12 col-md-6">
              <input type="password" name="password" placeholder="Password" required class="form-control" autocomplete="new-password">
            </div>
            <div class="col-12 col-md-6">
              <input type="password" name="confirm" placeholder="Confirm Password" required class="form-control" autocomplete="new-password">
            </div>
            <div class="col-12 col-md-6">
              <select name="gender" class="form-control" required>
                <option value="" disabled selected>Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <select name="heart_problem" class="form-control" required>
                <option value="" disabled selected>Any Heart Problem?</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <input type="text" name="phone" placeholder="Phone Number" required class="form-control">
            </div>
            <div class="col-12 col-md-6">
              <input type="text" name="rider_id" placeholder="Rider ID / Helmet ID" required class="form-control">
            </div>
            <div class="col-12 col-md-6">
              <input type="text" name="address" placeholder="Address or Location" required class="form-control">
            </div>
            <div class="col-12 col-md-6">
              <input type="text" name="emergency_name" placeholder="Emergency Contact Name" required class="form-control">
            </div>
            <div class="col-12 col-md-6">
              <input type="text" name="emergency_phone" placeholder="Emergency Contact Number" required class="form-control">
            </div>
          </div>

          <button type="submit" class="btn btn-login mt-2 w-100">Register</button>
        </form>

        <div class="login-links">
          <span>Already have an account?</span> <a href="login.php">Login</a> |
          <a href="../../index.php">Back to Home</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
