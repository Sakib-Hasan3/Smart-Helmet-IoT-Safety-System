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
  <title>Smart Helmet – IoT Safety System for Motorcycle Riders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Advanced IoT-powered safety monitoring system for motorcycle riders with real-time alerts, GPS tracking, and emergency response">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #3a86ff;
      --primary-dark: #2667cc;
      --secondary: #8338ec;
      --accent: #ff006e;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --light-gray: #e9ecef;
      --border: #dee2e6;
    }
    
    body {
      background: #fff;
      color: var(--dark);
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
      line-height: 1.6;
    }

    .navbar {
      background: rgba(255, 255, 255, 0.98) !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 15px rgba(0,0,0,0.05);
      border-bottom: 1px solid var(--border);
    }

    .navbar-brand img {
      height: 40px;
    }

    .nav-link {
      color: var(--dark) !important;
      font-weight: 500;
      position: relative;
      margin: 0 8px;
    }

    .nav-link:hover {
      color: var(--primary) !important;
    }

    .nav-link.active {
      color: var(--primary) !important;
    }

    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 100%;
      height: 2px;
      background: var(--primary);
    }

    .hero {
      text-align: center;
      padding: 150px 20px 100px;
      background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(58,134,255,0.05) 0%, rgba(255,255,255,0) 70%);
      z-index: 0;
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .hero h1 {
      font-size: 3.2rem;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 20px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero .subheading {
      color: var(--gray);
      font-size: 1.2rem;
      margin-bottom: 40px;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    .btn-custom {
      margin: 0 10px;
      padding: 12px 28px;
      font-weight: 600;
      border-radius: 8px;
      transition: all 0.3s ease;
      border: none;
    }

    .btn-primary-custom {
      background: var(--primary);
      color: white;
      box-shadow: 0 4px 15px rgba(58, 134, 255, 0.3);
    }

    .btn-primary-custom:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(58, 134, 255, 0.4);
    }

    .btn-outline-custom {
      border: 2px solid var(--primary);
      color: var(--primary);
      background: transparent;
    }

    .btn-outline-custom:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(58, 134, 255, 0.2);
    }

    .section {
      padding: 80px 0;
    }

    .section-title {
      font-weight: 700;
      margin-bottom: 15px;
      position: relative;
      display: inline-block;
      color: var(--dark);
    }

    .section-title::after {
      content: "";
      position: absolute;
      bottom: -8px;
      left: 0;
      width: 50px;
      height: 3px;
      background: var(--primary);
    }

    .section-subtitle {
      color: var(--gray);
      margin-bottom: 40px;
      max-width: 600px;
    }

    .feature-card {
      background: white;
      border-radius: 12px;
      padding: 30px;
      text-align: center;
      transition: all 0.3s ease;
      border: 1px solid var(--border);
      height: 100%;
      box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    }

    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.08);
      border-color: var(--primary);
    }

    .feature-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 20px;
    }

    .feature-card h5 {
      font-weight: 600;
      margin-bottom: 15px;
      color: var(--dark);
    }

    .feature-card p {
      color: var(--gray);
      font-size: 0.95rem;
    }

    .status-box {
      background: white;
      border-radius: 12px;
      padding: 30px;
      text-align: center;
      margin-top: 60px;
      border: 1px solid var(--border);
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .status-box h4 {
      font-weight: 600;
      margin-bottom: 25px;
      color: var(--dark);
    }

    .status-box h5 {
      color: var(--gray);
      font-size: 1rem;
      margin-bottom: 8px;
    }

    .status-box p {
      font-size: 1.6rem;
      font-weight: bold;
      color: var(--dark);
    }

    .demo-img {
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 15px 30px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
    }

    .demo-img:hover {
      transform: scale(1.02);
    }

    .step-number {
      background: var(--primary);
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      margin-right: 15px;
      flex-shrink: 0;
    }

    .testimonial-card {
      background: white;
      border-radius: 12px;
      padding: 30px;
      border: 1px solid var(--border);
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      height: 100%;
    }

    .testimonial-text {
      font-style: italic;
      margin-bottom: 20px;
      color: var(--dark);
    }

    .testimonial-author {
      font-weight: 600;
      color: var(--dark);
    }

    .testimonial-role {
      color: var(--gray);
      font-size: 0.9rem;
    }

    .testimonial-img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }

    .cta-section {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      padding: 80px 0;
      color: white;
    }

    .cta-section .section-title {
      color: white;
    }

    .cta-section .section-title::after {
      background: white;
    }

    .cta-section .section-subtitle {
      color: rgba(255,255,255,0.8);
    }

    .btn-light-custom {
      background: white;
      color: var(--primary);
      font-weight: 600;
      padding: 12px 30px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .btn-light-custom:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255,255,255,0.2);
    }

    .footer {
      background: var(--dark);
      padding: 80px 0 30px;
      color: white;
    }

    .footer-logo {
      height: 40px;
      margin-bottom: 20px;
    }

    .footer-links h5 {
      font-weight: 600;
      margin-bottom: 20px;
      color: white;
    }

    .footer-links ul {
      list-style: none;
      padding: 0;
    }

    .footer-links li {
      margin-bottom: 10px;
    }

    .footer-links a {
      color: rgba(255,255,255,0.7);
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer-links a:hover {
      color: white;
    }

    .social-icons a {
      display: inline-block;
      width: 40px;
      height: 40px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
      text-align: center;
      line-height: 40px;
      margin-right: 10px;
      transition: all 0.3s ease;
      color: white;
    }

    .social-icons a:hover {
      background: var(--primary);
      transform: translateY(-3px);
    }

    .copyright {
      border-top: 1px solid rgba(255,255,255,0.1);
      padding-top: 20px;
      margin-top: 40px;
      color: rgba(255,255,255,0.5);
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .hero {
        padding: 120px 20px 80px;
      }
      
      .hero h1 {
        font-size: 2.5rem;
      }
      
      .section {
        padding: 60px 0;
      }
      
      .btn-custom {
        display: block;
        width: 80%;
        margin: 10px auto;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="assets/images/logo.png" alt="Smart Helmet Logo">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#features">Features</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#how-it-works">How It Works</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#testimonials">Testimonials</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="user/auth/login.php">Login</a>
          </li>
        </ul>
        <a href="user/auth/register.php" class="btn btn-primary-custom btn-custom ms-lg-3">Get Started</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container hero-content">
      <img src="assets/images/logo.png" alt="Smart Helmet Logo" width="140" class="mb-4">
      <h1>Ride Smarter, Ride Safer</h1>
      <p class="subheading">Our IoT-powered safety system provides real-time monitoring, smart alerts, and emergency response to protect motorcycle riders like never before.</p>
      <div class="d-flex justify-content-center flex-wrap">
        <a href="user/auth/register.php" class="btn btn-primary-custom btn-custom">Get Started Now</a>
        <a href="#how-it-works" class="btn btn-outline-custom btn-custom">See How It Works</a>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="section" id="features">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title">Smart Safety Features</h2>
        <p class="section-subtitle">Our advanced IoT helmet comes packed with features designed to keep you safe on every ride</p>
      </div>
      
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-activity"></i></div>
            <h5>Live Monitoring</h5>
            <p>Real-time tracking of gas levels, rain detection, vibration alerts, and rider health metrics for complete situational awareness.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-bell-fill"></i></div>
            <h5>Smart Alerts</h5>
            <p>Instant notifications for drowsiness detection, speed limit warnings, and potential safety hazards on your route.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <h5>GPS Tracking</h5>
            <p>Precise location tracking with route history and emergency location sharing to your trusted contacts.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-shield-fill-check"></i></div>
            <h5>Emergency Response</h5>
            <p>Automatic crash detection with immediate alerts to emergency contacts and nearby response teams.</p>
          </div>
        </div>
      </div>

      <div class="status-box">
        <h4 class="mb-4">Real-Time System Status</h4>
        <div class="row text-center">
          <div class="col-6 col-md-3">
            <h5>Active Users</h5>
            <p style="color: var(--primary);">1,284+</p>
          </div>
          <div class="col-6 col-md-3">
            <h5>Alerts Today</h5>
            <p style="color: #28a745;">428</p>
          </div>
          <div class="col-6 col-md-3">
            <h5>Response Time</h5>
            <p style="color: #fd7e14;">&lt;500ms</p>
          </div>
          <div class="col-6 col-md-3">
            <h5>System Uptime</h5>
            <p style="color: #17a2b8;">99.97%</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Demo Section -->
  <section class="section bg-light" id="how-it-works">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h2 class="section-title">How It Works</h2>
          <p class="section-subtitle">The Smart Helmet system combines advanced sensors with intelligent software to protect riders</p>
          
          <div class="d-flex mb-4">
            <div class="step-number">1</div>
            <div>
              <h5>Wear Your Smart Helmet</h5>
              <p class="text-muted">Our lightweight, comfortable helmet contains all the necessary sensors and connectivity.</p>
            </div>
          </div>
          
          <div class="d-flex mb-4">
            <div class="step-number">2</div>
            <div>
              <h5>Connect to Our App</h5>
              <p class="text-muted">Pair with your smartphone to access real-time data and configure your safety preferences.</p>
            </div>
          </div>
          
          <div class="d-flex">
            <div class="step-number">3</div>
            <div>
              <h5>Ride With Confidence</h5>
              <p class="text-muted">The system monitors your environment and alerts you to potential dangers automatically.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <img src="https://via.placeholder.com/600x400/ffffff/3a86ff?text=Smart+Helmet+Dashboard" alt="Smart Helmet Dashboard" class="img-fluid demo-img">
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="section" id="testimonials">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title">What Riders Say</h2>
        <p class="section-subtitle">Hear from motorcycle enthusiasts who trust our Smart Helmet system</p>
      </div>
      
      <div class="row g-4">
        <div class="col-md-4">
          <div class="testimonial-card">
            <div class="testimonial-text">
              "The Smart Helmet saved me when I had an accident on a remote road. The automatic crash detection alerted emergency services with my exact location."
            </div>
            <div class="d-flex align-items-center">
              <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="testimonial-img">
              <div>
                <div class="testimonial-author">Michael R.</div>
                <div class="testimonial-role">Adventure Rider</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="testimonial-card">
            <div class="testimonial-text">
              "As someone who rides daily for work, the fatigue detection has been a game-changer. It's made my commute much safer."
            </div>
            <div class="d-flex align-items-center">
              <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User" class="testimonial-img">
              <div>
                <div class="testimonial-author">Sarah K.</div>
                <div class="testimonial-role">Daily Commuter</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="testimonial-card">
            <div class="testimonial-text">
              "My family feels much better knowing I have the Smart Helmet. The live tracking gives them peace of mind on my long trips."
            </div>
            <div class="d-flex align-items-center">
              <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="User" class="testimonial-img">
              <div>
                <div class="testimonial-author">David L.</div>
                <div class="testimonial-role">Touring Enthusiast</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="container text-center">
      <h2 class="section-title">Ready to Ride Safer?</h2>
      <p class="section-subtitle">Join thousands of riders who trust Smart Helmet for their safety. Get started in minutes.</p>
      <a href="user/auth/register.php" class="btn btn-light-custom mt-3">Get Your Smart Helmet Now</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <img src="assets/images/logo.png" alt="Smart Helmet Logo" class="footer-logo">
          <p>Advanced IoT-powered safety monitoring for motorcycle riders. Real-time alerts, GPS tracking, and emergency response in one intelligent system.</p>
          <div class="social-icons mt-4">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <div class="footer-links">
            <h5>Product</h5>
            <ul>
              <li><a href="#">Features</a></li>
              <li><a href="#">How It Works</a></li>
              <li><a href="#">Pricing</a></li>
              <li><a href="#">FAQ</a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <div class="footer-links">
            <h5>Company</h5>
            <ul>
              <li><a href="#">About Us</a></li>
              <li><a href="#">Our Team</a></li>
              <li><a href="#">Careers</a></li>
              <li><a href="#">Contact</a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <div class="footer-links">
            <h5>Legal</h5>
            <ul>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Terms of Service</a></li>
              <li><a href="#">Cookie Policy</a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-2 col-md-4">
          <div class="footer-links">
            <h5>Support</h5>
            <ul>
              <li><a href="#">Help Center</a></li>
              <li><a href="#">Community</a></li>
              <li><a href="#">Contact Us</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="copyright text-center">
        &copy; 2023 Smart Helmet IoT Safety System. All rights reserved.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
    
    // Navbar background change on scroll
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 2px 15px rgba(0,0,0,0.1)';
      } else {
        navbar.style.boxShadow = 'none';
      }
    });

    // Change navbar link active state on scroll
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-link');
    
    window.addEventListener('scroll', () => {
      let current = '';
      
      sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        
        if (pageYOffset >= sectionTop - 200) {
          current = section.getAttribute('id');
        }
      });
      
      navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
          link.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>
