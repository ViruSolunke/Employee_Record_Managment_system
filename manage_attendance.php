<<<<<<< HEAD
<!-- CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present', 'Absent', 'Leave') NOT NULL,
    UNIQUE KEY unique_attendance (emp_id, attendance_date),
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
); -->


<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'Employee') {
    header("Location: dashboard.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

// Default to today's date if not selected
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch all employees and their attendance status for the selected date
$query = "SELECT e.emp_id, e.name, e.department, a.status 
          FROM employees e 
          LEFT JOIN attendance a ON e.emp_id = a.emp_id AND a.attendance_date = '$selected_date'
          ORDER BY e.name ASC";
$employees = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }

        .control-panel { 
            background: white; padding: 20px; border-radius: 12px; 
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .date-picker { padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; }

        table { width: 100%; background: white; border-collapse: collapse; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th { background: #f1f5f9; padding: 15px; text-align: left; color: #64748b; }
        td { padding: 15px; border-top: 1px solid #f1f5f9; }

        /* Status Radio Styling */
        .status-group { display: flex; gap: 10px; }
        .status-btn { 
            padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; 
            cursor: pointer; border: 2px solid #e2e8f0; transition: 0.3s;
        }

        /* Color Badges */
        input[value="Present"]:checked + .status-btn { background: #dcfce7; color: #15803d; border-color: #22c55e; }
        input[value="Absent"]:checked + .status-btn { background: #fee2e2; color: #b91c1c; border-color: #ef4444; }
        input[value="Leave"]:checked + .status-btn { background: #fef9c3; color: #854d0e; border-color: #f1c40f; }
        
        .save-btn { background: var(--accent); color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; float: right; margin-top: 20px; }
        input[type="radio"] { display: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem;">ERMS</div>
    <ul style="list-style:none; padding:0 20px;">
        <li><a href="dashboard.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="manage_employees.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-users"></i> Employees</a></li>
        <li style="background:rgba(255,255,255,0.1); border-radius:10px;"><a href="manage_attendance.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-calendar-check"></i> Attendance</a></li>
        <li><a href="logout.php" style="color:#f87171; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="control-panel">
        <h1>Attendance Management</h1>
        <form method="GET" id="dateForm">
            <label>Select Date: </label>
            <input type="date" name="date" class="date-picker" value="<?php echo $selected_date; ?>" onchange="document.getElementById('dateForm').submit()">
        </form>
    </div>

    <form action="process_attendance.php" method="POST">
        <input type="hidden" name="att_date" value="<?php echo $selected_date; ?>">
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Status (<?php echo date('d M, Y', strtotime($selected_date)); ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $employees->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo $row['name']; ?></strong></td>
                    <td><?php echo $row['department']; ?></td>
                    <td>
                        <div class="status-group">
                            <label>
                                <input type="radio" name="status[<?php echo $row['emp_id']; ?>]" value="Present" <?php if($row['status'] == 'Present') echo 'checked'; ?> required>
                                <span class="status-btn">Present</span>
                            </label>
                            <label>
                                <input type="radio" name="status[<?php echo $row['emp_id']; ?>]" value="Absent" <?php if($row['status'] == 'Absent') echo 'checked'; ?>>
                                <span class="status-btn">Absent</span>
                            </label>
                            <label>
                                <input type="radio" name="status[<?php echo $row['emp_id']; ?>]" value="Leave" <?php if($row['status'] == 'Leave') echo 'checked'; ?>>
                                <span class="status-btn">Leave</span>
                            </label>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit" name="submit_attendance" class="save-btn"><i class="fas fa-save"></i> Save Attendance</button>
    </form>
</div>

</body>
=======
<!-- CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present', 'Absent', 'Leave') NOT NULL,
    UNIQUE KEY unique_attendance (emp_id, attendance_date),
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
); -->


<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'Employee') {
    header("Location: dashboard.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

// Default to today's date if not selected
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch all employees and their attendance status for the selected date
$query = "SELECT e.emp_id, e.name, e.department, a.status 
          FROM employees e 
          LEFT JOIN attendance a ON e.emp_id = a.emp_id AND a.attendance_date = '$selected_date'
          ORDER BY e.name ASC";
$employees = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }

        .control-panel { 
            background: white; padding: 20px; border-radius: 12px; 
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .date-picker { padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; }

        table { width: 100%; background: white; border-collapse: collapse; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th { background: #f1f5f9; padding: 15px; text-align: left; color: #64748b; }
        td { padding: 15px; border-top: 1px solid #f1f5f9; }

        /* Status Radio Styling */
        .status-group { display: flex; gap: 10px; }
        .status-btn { 
            padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; 
            cursor: pointer; border: 2px solid #e2e8f0; transition: 0.3s;
        }

        /* Color Badges */
        input[value="Present"]:checked + .status-btn { background: #dcfce7; color: #15803d; border-color: #22c55e; }
        input[value="Absent"]:checked + .status-btn { background: #fee2e2; color: #b91c1c; border-color: #ef4444; }
        input[value="Leave"]:checked + .status-btn { background: #fef9c3; color: #854d0e; border-color: #f1c40f; }
        
        .save-btn { background: var(--accent); color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; float: right; margin-top: 20px; }
        input[type="radio"] { display: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem;">ERMS</div>
    <ul style="list-style:none; padding:0 20px;">
        <li><a href="dashboard.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="manage_employees.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-users"></i> Employees</a></li>
        <li style="background:rgba(255,255,255,0.1); border-radius:10px;"><a href="manage_attendance.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-calendar-check"></i> Attendance</a></li>
        <li><a href="logout.php" style="color:#f87171; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="control-panel">
        <h1>Attendance Management</h1>
        <form method="GET" id="dateForm">
            <label>Select Date: </label>
            <input type="date" name="date" class="date-picker" value="<?php echo $selected_date; ?>" onchange="document.getElementById('dateForm').submit()">
        </form>
    </div>

    <form action="process_attendance.php" method="POST">
        <input type="hidden" name="att_date" value="<?php echo $selected_date; ?>">
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Status (<?php echo date('d M, Y', strtotime($selected_date)); ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $employees->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo $row['name']; ?></strong></td>
                    <td><?php echo $row['department']; ?></td>
                    <td>
                        <div class="status-group">
                            <label>
                                <input type="radio" name="status[<?php echo $row['emp_id']; ?>]" value="Present" <?php if($row['status'] == 'Present') echo 'checked'; ?> required>
                                <span class="status-btn">Present</span>
                            </label>
                            <label>
                                <input type="radio" name="status[<?php echo $row['emp_id']; ?>]" value="Absent" <?php if($row['status'] == 'Absent') echo 'checked'; ?>>
                                <span class="status-btn">Absent</span>
                            </label>
                            <label>
                                <input type="radio" name="status[<?php echo $row['emp_id']; ?>]" value="Leave" <?php if($row['status'] == 'Leave') echo 'checked'; ?>>
                                <span class="status-btn">Leave</span>
                            </label>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit" name="submit_attendance" class="save-btn"><i class="fas fa-save"></i> Save Attendance</button>
    </form>
</div>

</body>
>>>>>>> f85973a80cecedcf69b2e776b7b8d62696968cf0
</html>