<?php
session_start();

// 1. Session Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Database Connection
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

// 3. Fetch User Info
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Fetch current user name from DB
$user_query = $conn->query("SELECT name FROM users WHERE id = $user_id");
$user_data = $user_query->fetch_assoc();
$user_name = $user_data['name'] ?? 'User';

// 4. Fetch Summary Stats (Role-Based)
$total_employees = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$active_employees = $conn->query("SELECT COUNT(*) as count FROM users WHERE status='Active'")->fetch_assoc()['count'];
$total_depts = 8; // Static example, or fetch from your departments table
$pending_leaves = 5; // Static example, or fetch from your leaves table
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ERMS Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --accent: #3b82f6;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f1f5f9;
            --white: #ffffff;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background-color: var(--bg-light);
            color: var(--text-main);
            display: flex;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--white);
            position: fixed;
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 30px 25px;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--accent);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 15px;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            color: #94a3b8;
            text-decoration: none;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 10px;
            transition: var(--transition);
            font-weight: 500;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(59, 130, 246, 0.1);
            color: var(--white);
        }

        .sidebar-menu a:hover i {
            color: var(--accent);
        }

        /* Main Content Area */
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        /* Top Navbar */
        .navbar {
            height: 70px;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .search-bar {
            background: var(--bg-light);
            padding: 8px 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            width: 300px;
        }

        .search-bar input {
            background: transparent;
            border: none;
            outline: none;
            margin-left: 10px;
            width: 100%;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Dashboard Stats */
        .content-body { padding: 40px; }

        .welcome-header { margin-bottom: 30px; }
        .welcome-header h1 { margin: 0; font-size: 1.75rem; color: var(--sidebar-bg); }
        .welcome-header p { color: var(--text-muted); margin-top: 5px; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }

        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }

        .stat-info h3 { font-size: 2rem; margin: 0; font-weight: 700; }
        .stat-info p { color: var(--text-muted); margin: 5px 0 0; font-size: 0.9rem; font-weight: 500; }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .blue { background: #eff6ff; color: #3b82f6; }
        .green { background: #f0fdf4; color: #22c55e; }
        .purple { background: #faf5ff; color: #a855f7; }
        .orange { background: #fff7ed; color: #f97316; }

        /* Recent Activity Table */
        .table-container {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--bg-light);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid var(--bg-light);
            font-size: 0.95rem;
        }

        .status-pill {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active { background: #dcfce7; color: #15803d; }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; }
            .sidebar-header span, .sidebar-menu span { display: none; }
            .main-content { margin-left: 80px; width: calc(100% - 80px); }
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-layer-group"></i>
            <span>ERMS</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <?php if($user_role == 'Admin' || $user_role == 'HR'): ?>
                <li><a href="manage_employees.php"><i class="fas fa-users"></i> <span>Employees</span></a></li>
                <li><a href="manage_departments.php"><i class="fas fa-building"></i> <span>Departments</span></a></li>
                <li><a href="manage_payroll.php"><i class="fas fa-file-invoice-dollar"></i> <span>Payroll</span></a></li>
                <li><a href="manage_attendance.php"><i class="fas fa-calendar-check"></i> <span>Attendance</span></a></li>
            <?php endif; ?>
            <li><a href="manage_leaves.php"><i class="fas fa-calendar-alt"></i> <span>Leave Requests</span></a></li>
            <li><a href="profile_settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li style="margin-top: 50px;">
                <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
            </li>
        </ul>
    </nav>

    <main class="main-content">
        <header class="navbar">
            <div class="search-bar">
                <i class="fas fa-search" style="color: var(--text-muted);"></i>
                <input type="text" placeholder="Search records...">
            </div>
            <div class="user-profile">
                <div class="user-info" style="text-align: right;">
                    <div style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($user_name); ?></div>
                    <div style="color: var(--text-muted); font-size: 0.75rem;"><?php echo $user_role; ?></div>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
            </div>
        </header>

        <div class="content-body">
            <div class="welcome-header">
                <h1>Overview</h1>
                <p>Statistics for the current fiscal period.</p>
            </div>

            <div class="stat-grid">
                <?php if($user_role == 'Admin' || $user_role == 'HR'): ?>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $total_employees; ?></h3>
                        <p>Total Staff</p>
                    </div>
                    <div class="stat-icon blue"><i class="fas fa-user-group"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $total_depts; ?></h3>
                        <p>Departments</p>
                    </div>
                    <div class="stat-icon purple"><i class="fas fa-network-wired"></i></div>
                </div>
                <?php endif; ?>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $active_employees; ?></h3>
                        <p>Active Now</p>
                    </div>
                    <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $pending_leaves; ?></h3>
                        <p>Leaves Pending</p>
                    </div>
                    <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h2 style="font-size: 1.1rem; margin: 0;">Recently Registered Employees</h2>
                    <button style="background: transparent; border: 1px solid #e2e8f0; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-size: 0.8rem;">View All</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Role</th>
                            <th>Email Address</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_users = $conn->query("SELECT name, role, email, status FROM users ORDER BY id DESC LIMIT 5");
                        while($row = $recent_users->fetch_assoc()):
                        ?>
                        <tr>
                            <td style="font-weight: 500;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td style="color: var(--text-muted);"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="status-pill status-active"><?php echo $row['status']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>