<<<<<<< HEAD
<?php
// 1. SESSION LOGIC
session_start();

// 2. REDIRECT IF LOGGED IN
// If a session exists, the user shouldn't see the landing page; send them to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERMS | Enterprise Employee Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #0f172a;
            --accent-blue: #3b82f6;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            background-color: #fff;
            overflow-x: hidden;
        }

        /* --- Navbar --- */
        .navbar {
            padding: 20px 0;
            transition: all 0.3s ease;
            background: #fff;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-dark);
            letter-spacing: -1px;
        }
        .navbar-brand span { color: var(--accent-blue); }
        
        .nav-link {
            font-weight: 500;
            color: var(--text-main);
            margin: 0 15px;
        }

        /* --- Hero Section --- */
        .hero-section {
            padding: 120px 0 80px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            position: relative;
            clip-path: ellipse(150% 100% at 50% 0%);
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 25px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: #cbd5e1;
            margin-bottom: 40px;
            max-width: 600px;
        }

        .btn-corporate {
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-login {
            background-color: var(--accent-blue);
            border: none;
            color: white;
        }
        .btn-login:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
            color: white;
        }
        .btn-outline-white {
            border: 2px solid rgba(255,255,255,0.2);
            color: white;
        }
        .btn-outline-white:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        /* --- Feature Cards --- */
        .feature-card {
            border: none;
            padding: 40px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: 0.4s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            background: #eff6ff;
            color: var(--accent-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
            margin-bottom: 25px;
        }

        /* --- Role Section --- */
        .role-highlight {
            background-color: var(--bg-light);
            padding: 100px 0;
        }
        .role-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            border-left: 5px solid var(--accent-blue);
        }

        footer {
            background: var(--primary-dark);
            color: #94a3b8;
            padding: 40px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">ERMS<span>.</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#roles">Portals</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="login.php" class="btn btn-corporate btn-login">Login to System</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7" data-aos="fade-right">
                    <h1 class="hero-title">Smart & Secure Employee Record Management System</h1>
                    <p class="hero-subtitle">
                        Centralize employee data, manage attendance, payroll, and reports with enterprise-level security. Built for modern HR teams.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="login.php" class="btn btn-corporate btn-login">Login to System</a>
                        <a href="#features" class="btn btn-corporate btn-outline-white">Explore Features</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block" data-aos="zoom-in">
                    <div class="text-center">
                        <i class="fas fa-shield-halved fa-10x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-5 mt-5">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h6 class="text-primary fw-bold text-uppercase">Core Capabilities</h6>
                <h2 class="fw-bold">Everything you need in one place</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-users-gear"></i></div>
                        <h5>Employee Management</h5>
                        <p class="text-muted">Comprehensive digital records for every staff member.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-calendar-check"></i></div>
                        <h5>Attendance Tracking</h5>
                        <p class="text-muted">Automated leave workflows and daily logs.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-money-bill-transfer"></i></div>
                        <h5>Payroll Engine</h5>
                        <p class="text-muted">Generate salary slips and manage tax deductions.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-user-shield"></i></div>
                        <h5>Secure Access</h5>
                        <p class="text-muted">Granular role-based permissions for data safety.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="roles" class="role-highlight">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold">Tailored Experience for Every User</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="flip-left">
                    <div class="role-card">
                        <i class="fas fa-user-tie fa-2x mb-3 text-primary"></i>
                        <h4>Admin Panel</h4>
                        <p class="text-muted">Ultimate oversight. Control system configurations, user roles, and high-level analytics.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="flip-left" data-aos-delay="100">
                    <div class="role-card" style="border-left-color: #10b981;">
                        <i class="fas fa-address-book fa-2x mb-3 text-success"></i>
                        <h4>HR Management</h4>
                        <p class="text-muted">Efficiently manage hiring, employee profiles, attendance approvals, and payroll processing.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="flip-left" data-aos-delay="200">
                    <div class="role-card" style="border-left-color: #f59e0b;">
                        <i class="fas fa-user-circle fa-2x mb-3 text-warning"></i>
                        <h4>Employee Portal</h4>
                        <p class="text-muted">Self-service access. View personal records, download pay slips, and request leaves.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container text-center">
            <p>&copy; 2026 ERMS Corporate. All rights reserved.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="text-white-50"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="text-white-50"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
=======
<?php
// 1. SESSION LOGIC
session_start();

// 2. REDIRECT IF LOGGED IN
// If a session exists, the user shouldn't see the landing page; send them to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERMS | Enterprise Employee Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #0f172a;
            --accent-blue: #3b82f6;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            background-color: #fff;
            overflow-x: hidden;
        }

        /* --- Navbar --- */
        .navbar {
            padding: 20px 0;
            transition: all 0.3s ease;
            background: #fff;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-dark);
            letter-spacing: -1px;
        }
        .navbar-brand span { color: var(--accent-blue); }
        
        .nav-link {
            font-weight: 500;
            color: var(--text-main);
            margin: 0 15px;
        }

        /* --- Hero Section --- */
        .hero-section {
            padding: 120px 0 80px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            position: relative;
            clip-path: ellipse(150% 100% at 50% 0%);
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 25px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: #cbd5e1;
            margin-bottom: 40px;
            max-width: 600px;
        }

        .btn-corporate {
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-login {
            background-color: var(--accent-blue);
            border: none;
            color: white;
        }
        .btn-login:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
            color: white;
        }
        .btn-outline-white {
            border: 2px solid rgba(255,255,255,0.2);
            color: white;
        }
        .btn-outline-white:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        /* --- Feature Cards --- */
        .feature-card {
            border: none;
            padding: 40px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: 0.4s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            background: #eff6ff;
            color: var(--accent-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
            margin-bottom: 25px;
        }

        /* --- Role Section --- */
        .role-highlight {
            background-color: var(--bg-light);
            padding: 100px 0;
        }
        .role-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            border-left: 5px solid var(--accent-blue);
        }

        footer {
            background: var(--primary-dark);
            color: #94a3b8;
            padding: 40px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">ERMS<span>.</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#roles">Portals</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="login.php" class="btn btn-corporate btn-login">Login to System</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7" data-aos="fade-right">
                    <h1 class="hero-title">Smart & Secure Employee Record Management System</h1>
                    <p class="hero-subtitle">
                        Centralize employee data, manage attendance, payroll, and reports with enterprise-level security. Built for modern HR teams.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="login.php" class="btn btn-corporate btn-login">Login to System</a>
                        <a href="#features" class="btn btn-corporate btn-outline-white">Explore Features</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block" data-aos="zoom-in">
                    <div class="text-center">
                        <i class="fas fa-shield-halved fa-10x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-5 mt-5">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h6 class="text-primary fw-bold text-uppercase">Core Capabilities</h6>
                <h2 class="fw-bold">Everything you need in one place</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-users-gear"></i></div>
                        <h5>Employee Management</h5>
                        <p class="text-muted">Comprehensive digital records for every staff member.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-calendar-check"></i></div>
                        <h5>Attendance Tracking</h5>
                        <p class="text-muted">Automated leave workflows and daily logs.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-money-bill-transfer"></i></div>
                        <h5>Payroll Engine</h5>
                        <p class="text-muted">Generate salary slips and manage tax deductions.</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-user-shield"></i></div>
                        <h5>Secure Access</h5>
                        <p class="text-muted">Granular role-based permissions for data safety.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="roles" class="role-highlight">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold">Tailored Experience for Every User</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="flip-left">
                    <div class="role-card">
                        <i class="fas fa-user-tie fa-2x mb-3 text-primary"></i>
                        <h4>Admin Panel</h4>
                        <p class="text-muted">Ultimate oversight. Control system configurations, user roles, and high-level analytics.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="flip-left" data-aos-delay="100">
                    <div class="role-card" style="border-left-color: #10b981;">
                        <i class="fas fa-address-book fa-2x mb-3 text-success"></i>
                        <h4>HR Management</h4>
                        <p class="text-muted">Efficiently manage hiring, employee profiles, attendance approvals, and payroll processing.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="flip-left" data-aos-delay="200">
                    <div class="role-card" style="border-left-color: #f59e0b;">
                        <i class="fas fa-user-circle fa-2x mb-3 text-warning"></i>
                        <h4>Employee Portal</h4>
                        <p class="text-muted">Self-service access. View personal records, download pay slips, and request leaves.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container text-center">
            <p>&copy; 2026 ERMS Corporate. All rights reserved.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="text-white-50"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="text-white-50"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
>>>>>>> f85973a80cecedcf69b2e776b7b8d62696968cf0
</html>