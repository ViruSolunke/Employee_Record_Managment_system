<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'Employee') {
    header("Location: dashboard.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

// Filtering Logic
$dept_filter = isset($_GET['dept']) ? $_GET['dept'] : '';
$where_clause = $dept_filter ? "WHERE department = '$dept_filter'" : "";

// Fetch Stats
$total_emp = $conn->query("SELECT COUNT(*) as count FROM employees $where_clause")->fetch_assoc()['count'];
$avg_salary = $conn->query("SELECT AVG(net_salary) as avg FROM payroll")->fetch_assoc()['avg'];
$pending_leaves = $conn->query("SELECT COUNT(*) as count FROM leaves WHERE status='Pending'")->fetch_assoc()['count'];

// Data for Chart (Attendance for the last 7 days)
$chart_data = $conn->query("SELECT attendance_date, COUNT(*) as count FROM attendance WHERE status='Present' GROUP BY attendance_date LIMIT 7");
$dates = []; $counts = [];
while($row = $chart_data->fetch_assoc()){
    $dates[] = $row['attendance_date'];
    $counts[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports & Analytics | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }
        
        .filter-card { background: white; padding: 20px; border-radius: 12px; display: flex; gap: 20px; align-items: flex-end; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        
        .report-table { width: 100%; background: white; border-collapse: collapse; border-radius: 12px; overflow: hidden; }
        .report-table th, .report-table td { padding: 15px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        
        .btn-print { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
        .btn-export { background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-left: 10px; }
        
        @media print {
            .sidebar, .filter-card, .btn-print, .btn-export { display: none !important; }
            .main-content { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem;">ERMS</div>
    <ul style="list-style:none; padding:0 20px;">
        <li><a href="dashboard.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-home"></i> Dashboard</a></li>
        <li style="background:rgba(255,255,255,0.1); border-radius:10px;"><a href="reports.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-file-contract"></i> Reports</a></li>
        <li><a href="logout.php" style="color:#f87171; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header" style="display:flex; justify-content:space-between; align-items:center;">
        <h1>Company Reports</h1>
        <div>
            <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> PDF Report</button>
            <button class="btn-export" onclick="exportTableToCSV('report.csv')"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>

    <form class="filter-card" method="GET">
        <div style="flex:1">
            <label style="font-size:0.8rem; font-weight:600;">Department</label><br>
            <select name="dept" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                <option value="">All Departments</option>
                <option value="IT">IT</option>
                <option value="HR">HR</option>
                <option value="Finance">Finance</option>
            </select>
        </div>
        <button type="submit" class="btn-print" style="background:var(--accent)">Apply Filter</button>
    </form>

    <div class="stats-grid">
        <div class="stat-box"><h3><?php echo $total_emp; ?></h3><p>Total Staff</p></div>
        <div class="stat-box"><h3>$<?php echo number_format($avg_salary, 2); ?></h3><p>Avg. Salary</p></div>
        <div class="stat-box"><h3><?php echo $pending_leaves; ?></h3><p>Open Leave Requests</p></div>
    </div>

    <div class="stat-box" style="margin-bottom:30px;">
        <canvas id="attendanceChart" height="100"></canvas>
    </div>

    <table class="report-table" id="reportTable">
        <thead>
            <tr>
                <th>Emp ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Joining Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $employees = $conn->query("SELECT * FROM employees $where_clause");
            while($row = $employees->fetch_assoc()):
            ?>
            <tr>
                <td>#<?php echo $row['emp_id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['department']; ?></td>
                <td><?php echo $row['joining_date']; ?></td>
                <td><?php echo $row['status']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
// Chart.js Animation Logic
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Daily Attendance Count',
            data: <?php echo json_encode($counts); ?>,
            borderColor: '#3b82f6',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(59, 130, 246, 0.1)'
        }]
    }
});

// CSV Export Logic
function exportTableToCSV(filename) {
    let csv = [];
    let rows = document.querySelectorAll("table tr");
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length; j++) row.push(cols[j].innerText);
        csv.push(row.join(","));
    }
    let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    let downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

</body>
</html>