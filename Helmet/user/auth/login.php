<?php
session_start();
require_once '../../backend/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please fill in both email and password.";
    } else {
        // Look up the user by email
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                // Success: set session and go to dashboard
                session_regenerate_id(true);
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                header("Location: ../dashboard.php");
                exit;
            }
        }
        // Generic error to avoid email enumeration
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Smart Helmet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root{
      --primary:#3a86ff;
      --primary-dark:#2667cc;
      --secondary:#8338ec;
      --light:#f8f9fa;
      --dark:#212529;
      --gray:#6c757d;
      --border:#e6e9ef;
      --shadow:0 10px 30px rgba(13,27,62,.08);
    }

    /* ---------- Navbar ---------- */
    .navbar-smart{
      background:#ffffff;
      border-bottom:1px solid var(--border);
      position:sticky;
      top:0;
      z-index:1020;
      box-shadow:0 2px 12px rgba(0,0,0,.03);
    }
    .navbar-brand span{
      font-weight:700;
      letter-spacing:.2px;
      color:var(--dark);
    }
    .nav-link{
      font-weight:500;
      color:#4b5563 !important;
      padding:.5rem .9rem !important;
      border-radius:.5rem;
    }
    .nav-link:hover{ background:#f3f4f6; color:#111827 !important; }
    .nav-link.active{ color:#111827 !important; background:#eef2ff; }
    .btn-cta{
      background:linear-gradient(90deg,var(--primary),#4f46e5);
      border:none;
      color:#fff !important;
      font-weight:600;
      padding:.5rem 1rem;
      border-radius:.6rem;
      box-shadow:0 6px 16px rgba(58,134,255,.25);
    }
    .btn-cta:hover{ filter:brightness(.95); }

    /* ---------- Page ---------- */
    body{
      background:linear-gradient(180deg,#f6f9ff 0%, #ffffff 40%);
      font-family:'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    .login-container{
      display:flex;
      min-height:calc(100vh - 64px); /* minus navbar */
    }

    /* ---------- Illustration ---------- */
    .login-illustration{
      flex:1;
      background:linear-gradient(135deg,#eaf3ff 0%,#f8f9fa 100%);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:3rem 2rem;
      border-right:1px solid var(--border);
    }
    .login-illustration .wrap{
      text-align:center;
      max-width:560px;
    }
    .login-illustration img{
      max-width:86%;
      height:auto;
      border-radius:18px;
      box-shadow:var(--shadow);
      animation:float 6s ease-in-out infinite;
    }
    .illustration-text{
      max-width:480px;
      margin:1.75rem auto 0;
    }
    .illustration-text h2{
      font-weight:800;
      color:#0f172a;
      margin-bottom:.5rem;
      letter-spacing:.2px;
    }
    .illustration-text p{
      color:#64748b;
      font-size:1.05rem;
    }
    @keyframes float{
      0%,100%{ transform:translateY(0) }
      50%{ transform:translateY(-16px) }
    }

    /* ---------- Form ---------- */
    .login-form-container{
      flex:1;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:3rem 2rem;
      background:#ffffff;
    }
    .login-form{
      width:100%;
      max-width:440px;
      background:#fff;
      border-radius:16px;
      padding:2.5rem;
      box-shadow:var(--shadow);
      border:1px solid var(--border);
    }
    .logo{ text-align:center; margin-bottom:1.25rem; }
    .logo img{ height:58px; }

    .login-title{
      font-size:1.9rem;
      font-weight:800;
      color:#0f172a;
      margin-bottom:.25rem;
      text-align:center;
      letter-spacing:.2px;
    }
    .login-subtitle{
      color:#6b7280;
      text-align:center;
      margin-bottom:1.75rem;
    }

    .form-label{ font-weight:600; color:#111827; }
    .form-control{
      height:50px;
      border-radius:10px;
      border:1px solid #e3e7ee;
      padding:.9rem 1.1rem;
      margin-bottom:1.1rem;
      background:#fff;
    }
    .form-control:focus{
      border-color:var(--primary);
      box-shadow:0 0 0 .25rem rgba(58,134,255,.18);
    }
    .password-container{ position:relative; }
    .password-toggle{
      position:absolute;
      right:14px; top:50%;
      transform:translateY(-50%);
      cursor:pointer; color:#9aa4b2;
    }
    .password-toggle:hover{ color:#6b7280; }

    .options{
      display:flex; justify-content:space-between; align-items:center;
      margin-bottom:1.2rem; color:#4b5563;
    }
    .options a{ color:var(--primary); text-decoration:none; }
    .options a:hover{ text-decoration:underline; }

    .btn-login{
      width:100%;
      padding:.85rem;
      border-radius:10px;
      background-color:var(--primary);
      border:none; font-weight:700; letter-spacing:.3px;
      transition:transform .25s ease, filter .25s ease;
      color:#fff;
    }
    .btn-login:hover{ background-color:var(--primary-dark); transform:translateY(-1px); }

    .divider{
      display:flex; align-items:center; margin:1.4rem 0; color:#94a3b8;
    }
    .divider::before,.divider::after{
      content:''; flex:1; border-bottom:1px solid #e7ebf2;
    }
    .divider span{ padding:0 .9rem; }

    .btn-google{
      width:100%; padding:.8rem; border-radius:10px;
      border:1px solid #e7ebf2; background:#fff; font-weight:700; color:#0f172a;
    }
    .btn-google:hover{ background:#f8faff; }

    .footer-links{
      text-align:center; margin-top:1.25rem; color:#6b7280;
    }
    .footer-links a{ color:var(--primary); text-decoration:none; font-weight:600; }
    .footer-links a:hover{ text-decoration:underline; }

    /* ---------- Responsive ---------- */
    @media (max-width: 992px){
      .login-container{ flex-direction:column; }
      .login-illustration{ border-right:none; border-bottom:1px solid var(--border); }
      .login-form{ margin:1.25rem auto 0; }
      .navbar .btn-cta{ margin-top:.5rem; }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-smart">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../../index.php">
        <img src="../../assets/images/logo.png" alt="Smart Helmet" width="34" class="me-2">
        <span>Smart Helmet</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="../dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link active" href="#">Login</a></li>
          <li class="nav-item ms-lg-2">
            <a class="btn btn-cta" href="../auth/register.php"><i class="fa-solid fa-user-plus me-2"></i>Sign Up</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page -->
  <div class="login-container">
    <!-- Illustration -->
    <div class="login-illustration">
      <div class="wrap">
        <img src="../../assets/images/biker-placeholder.png" alt="Smart Helmet Illustration">
        <div class="illustration-text">
          <h2>Ride Smarter, Ride Safer</h2>
          <p>Connect to your Smart Helmet dashboard to access real-time metrics, safety alerts, and ride analytics.</p>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="login-form-container">
      <div class="login-form">
        <div class="logo">
          <img src="../../assets/images/logo.png" alt="Smart Helmet Logo">
        </div>
        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">Sign in to continue to your dashboard</p>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              placeholder="you@example.com"
              required
              value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
            >
          </div>

          <div class="mb-3 password-container">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              class="form-control"
              id="password"
              name="password"
              placeholder="••••••••"
              required
            >
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
          </div>

          <div class="options">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe">
              <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            <a href="#">Forgot password?</a>
          </div>

          <button type="submit" class="btn btn-login">
            <i class="fas fa-sign-in-alt me-2"></i>Login
          </button>

          <div class="divider"><span>OR</span></div>

          <button type="button" class="btn btn-google">
            <i class="fab fa-google me-2"></i>Continue with Google
          </button>

          <div class="footer-links">
            Don’t have an account? <a href="../auth/register.php">Sign up</a><br>
            <a href="../../index.php"><i class="fas fa-arrow-left me-1"></i>Back to Home</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts (password-eye toggle only) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const togglePassword=document.querySelector('#togglePassword');
    const password=document.querySelector('#password');
    if(togglePassword){
      togglePassword.addEventListener('click',function(){
        const type=password.getAttribute('type')==='password'?'text':'password';
        password.setAttribute('type',type);
        this.classList.toggle('fa-eye-slash');
      });
    }
  </script>
</body>
</html>
