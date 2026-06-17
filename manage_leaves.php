<!-- CREATE TABLE IF NOT EXISTS leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    leave_type ENUM('Sick', 'Casual', 'Earned', 'Maternity/Paternity') NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    reason TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    applied_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
); -->


<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "employee_mgmt");
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Handle Status Updates (Admin/HR Only)
if (isset($_GET['action']) && isset($_GET['id']) && ($user_role == 'Admin' || $user_role == 'HR')) {
    $id = $_GET['id'];
    $status = ($_GET['action'] == 'approve') ? 'Approved' : 'Rejected';
    $conn->query("UPDATE leaves SET status = '$status' WHERE id = $id");
    header("Location: manage_leaves.php?msg=Status Updated");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Management | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }

        .leave-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.85rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; }

        .btn { background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-weight: 600; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 12px; background: #f1f5f9; color: #64748b; font-size: 0.8rem; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }

        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .bg-pending { background: #fef9c3; color: #854d0e; }
        .bg-approved { background: #dcfce7; color: #15803d; }
        .bg-rejected { background: #fee2e2; color: #b91c1c; }

        .action-link { text-decoration: none; margin-right: 10px; font-weight: 600; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem;">ERMS</div>
    <ul style="list-style:none; padding:0 20px;">
        <li><a href="dashboard.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li style="background:rgba(255,255,255,0.1); border-radius:10px;"><a href="manage_leaves.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-calendar-minus"></i> Leave System</a></li>
        <li><a href="logout.php" style="color:#f87171; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Leave Management</h1>

    <div class="leave-grid">
        <div class="card">
            <h3>Apply for Leave</h3>
            <form action="process_leave.php" method="POST">
                <div class="form-group">
                    <label>Leave Type</label>
                    <select name="leave_type" required>
                        <option>Sick</option>
                        <option>Casual</option>
                        <option>Earned</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" name="from_date" required>
                </div>
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" name="to_date" required>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" rows="3" required></textarea>
                </div>
                <button type="submit" name="apply_leave" class="btn">Submit Request</button>
            </form>
        </div>

        <div class="card">
            <h3><?php echo ($user_role == 'Admin') ? 'Pending Requests' : 'My Leave History'; ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <?php if($user_role == 'Admin' || $user_role == 'HR'): ?><th>Action</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = ($user_role == 'Admin' || $user_role == 'HR') 
                           ? "SELECT l.*, e.name FROM leaves l JOIN employees e ON l.emp_id = e.emp_id ORDER BY l.id DESC"
                           : "SELECT l.*, e.name FROM leaves l JOIN employees e ON l.emp_id = e.emp_id WHERE l.emp_id = (SELECT emp_id FROM employees WHERE email = (SELECT email FROM users WHERE id = $user_id)) ORDER BY l.id DESC";
                    
                    $res = $conn->query($sql);
                    while($row = $res->fetch_assoc()):
                        $status_class = "bg-" . strtolower($row['status']);
                    ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['leave_type']; ?></td>
                        <td><small><?php echo $row['from_date']; ?> to <?php echo $row['to_date']; ?></small></td>
                        <td><span class="badge <?php echo $status_class; ?>"><?php echo $row['status']; ?></span></td>
                        <?php if(($user_role == 'Admin' || $user_role == 'HR') && $row['status'] == 'Pending'): ?>
                        <td>
                            <a href="manage_leaves.php?action=approve&id=<?php echo $row['id']; ?>" class="action-link" style="color: #15803d;"><i class="fas fa-check"></i></a>
                            <a href="manage_leaves.php?action=reject&id=<?php echo $row['id']; ?>" class="action-link" style="color: #b91c1c;"><i class="fas fa-times"></i></a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>