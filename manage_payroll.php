<!-- CREATE TABLE IF NOT EXISTS payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    month_year VARCHAR(20) NOT NULL, -- e.g., "February 2026"
    basic_salary DECIMAL(10, 2) NOT NULL,
    allowance DECIMAL(10, 2) DEFAULT 0.00,
    deduction DECIMAL(10, 2) DEFAULT 0.00,
    net_salary DECIMAL(10, 2) NOT NULL,
    paid_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
); -->

<?php
session_start();

$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['role'] == 'Employee') {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$display_month = date('F Y', strtotime($selected_month));

$query = "SELECT p.*, e.name, e.department 
          FROM payroll p 
          JOIN employees e ON p.emp_id = e.emp_id 
          WHERE p.month_year = '$display_month' 
          ORDER BY p.id DESC";
$payroll_list = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Management | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; --success: #10b981; --danger: #ef4444; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .sidebar-brand { padding: 30px; font-weight: 700; color: var(--accent); font-size: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { list-style: none; padding: 20px; }
        .sidebar-menu li a { color: #94a3b8; text-decoration: none; display: block; padding: 12px 15px; border-radius: 8px; transition: 0.3s; margin-bottom: 5px; }
        .sidebar-menu li a:hover, .sidebar-menu li.active a { background: rgba(255,255,255,0.1); color: white; }

        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; box-sizing: border-box; }
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; padding: 15px; text-align: left; color: #64748b; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }

        .btn-pay { background: var(--accent); color: white; padding: 10px 18px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-pay:hover { opacity: 0.9; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); }
        .modal-content { background: white; width: 450px; margin: 5% auto; padding: 30px; border-radius: 15px; animation: slideIn 0.3s ease-out; position: relative; }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 0.8rem; margin-bottom: 6px; font-weight: 600; color: #475569; }
        .input-group input, .input-group select { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .net-display { background: #f8fafc; border: 2px solid var(--accent) !important; font-weight: 700; color: var(--primary); font-size: 1.1rem; }

        /* Slip View Design */
        .slip-header { text-align: center; border-bottom: 2px solid #eee; margin-bottom: 20px; padding-bottom: 10px; }
        .slip-item { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; }
        .slip-total { margin-top: 15px; padding-top: 10px; border-top: 2px dashed #eee; font-weight: 700; font-size: 1.1rem; color: var(--primary); }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .modal { position: absolute; left: 0; top: 0; background: white; width: 100%; height: auto; }
            .modal-content { border: none; box-shadow: none; width: 100%; margin: 0; padding: 20px; }
        }
    </style>
</head>
<body>

<div class="sidebar no-print">
    <div class="sidebar-brand">ERMS</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="manage_employees.php"><i class="fas fa-users"></i> Employees</a></li>
        <li class="active"><a href="manage_payroll.php"><i class="fas fa-money-bill-wave"></i> Payroll</a></li>
        <li><a href="manage_leaves.php"><i class="fas fa-calendar-alt"></i> Leaves</a></li>
        <li><a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header-box no-print">
        <div>
            <h1 style="margin:0;"><?php echo $display_month; ?> Payroll</h1>
            <p style="color: #64748b; margin: 5px 0 0;">Manage employee salaries</p>
        </div>
        <div style="display: flex; gap: 15px;">
            <input type="month" value="<?php echo $selected_month; ?>" 
                   onchange="window.location.href='manage_payroll.php?month=' + this.value" 
                   style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            
            <button class="btn-pay" onclick="openModal('salaryModal')">
                <i class="fas fa-plus-circle"></i> Run Payroll
            </button>
        </div>
    </div>

    <div class="card no-print">
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Basic</th>
                    <th>Allowance</th>
                    <th>Deduction</th>
                    <th>Net Salary</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($payroll_list->num_rows > 0): ?>
                    <?php while($row = $payroll_list->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600;"><?php echo $row['name']; ?></div>
                            <div style="font-size: 0.8rem; color: #64748b;"><?php echo $row['department']; ?></div>
                        </td>
                        <td>$<?php echo number_format($row['basic_salary'], 2); ?></td>
                        <td style="color: var(--success);">+$<?php echo number_format($row['allowance'], 2); ?></td>
                        <td style="color: var(--danger);">-$<?php echo number_format($row['deduction'], 2); ?></td>
                        <td><strong>$<?php echo number_format($row['net_salary'], 2); ?></strong></td>
                        <td>
                            <button class="btn-pay" style="background:#64748b; font-size: 0.8rem;" 
                                    onclick='showSlip(<?php echo json_encode($row); ?>)'>
                                <i class="fas fa-eye"></i> Slip
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding: 40px; color: #94a3b8;">No payroll records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="salaryModal" class="modal no-print">
    <div class="modal-content">
        <h2 style="margin-top:0;">Process Salary</h2>
        <form action="process_payroll.php" method="POST">
            <div class="input-group">
                <label>Select Employee</label>
                <select name="emp_id" required>
                    <option value="">-- Choose Employee --</option>
                    <?php 
                    $emps = $conn->query("SELECT emp_id, name FROM employees WHERE status='Active'");
                    while($e = $emps->fetch_assoc()) {
                        echo "<option value='".$e['emp_id']."'>".$e['name']."</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="hidden" name="month_year" value="<?php echo $display_month; ?>">
            <div class="input-group"><label>Basic Salary</label><input type="number" name="basic" id="basic" step="0.01" oninput="calcNet()" required></div>
            <div class="input-group"><label>Allowance (+)</label><input type="number" name="allowance" id="allowance" step="0.01" value="0" oninput="calcNet()"></div>
            <div class="input-group"><label>Deduction (-)</label><input type="number" name="deduction" id="deduction" step="0.01" value="0" oninput="calcNet()"></div>
            <div class="input-group"><label>Net Payable</label><input type="number" name="net" id="net" class="net-display" readonly></div>
            <button type="submit" name="save_payroll" class="btn-pay" style="width:100%; justify-content: center;">Confirm & Save</button>
            <button type="button" onclick="closeModal('salaryModal')" style="width:100%; background:none; border:none; margin-top:12px; cursor:pointer; color: #64748b;">Cancel</button>
        </form>
    </div>
</div>

<div id="slipModal" class="modal">
    <div class="modal-content" style="width: 400px; border: 2px solid #eee;">
        <div class="slip-header">
            <h3 style="margin:0; color:var(--primary);">PAYMENT RECEIPT</h3>
            <p style="font-size:0.75rem; color:#64748b; margin:5px 0;">Generated by ERMS Portal</p>
        </div>
        
        <div class="slip-item"><strong>Employee:</strong> <span id="s_name"></span></div>
        <div class="slip-item"><strong>Department:</strong> <span id="s_dept"></span></div>
        <div class="slip-item"><strong>Month:</strong> <span id="s_month"></span></div>
        <hr style="border:0; border-top:1px solid #eee; margin:15px 0;">
        
        <div class="slip-item"><span>Basic Salary</span> <span id="s_basic"></span></div>
        <div class="slip-item" style="color:var(--success);"><span>Total Allowance (+)</span> <span id="s_allow"></span></div>
        <div class="slip-item" style="color:var(--danger);"><span>Total Deduction (-)</span> <span id="s_deduct"></span></div>
        
        <div class="slip-item slip-total"><span>NET PAYABLE</span> <span id="s_net"></span></div>
        
        <div style="margin-top:30px;" class="no-print">
            <button class="btn-pay" onclick="window.print()" style="width:100%; background:var(--success); justify-content: center;">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <button class="btn-pay" onclick="closeModal('slipModal')" style="width:100%; background:none; color:#64748b; border:none; margin-top:10px;">Close</button>
        </div>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display='block'; }
    function closeModal(id) { document.getElementById(id).style.display='none'; }
    
    function calcNet() {
        let b = parseFloat(document.getElementById('basic').value) || 0;
        let a = parseFloat(document.getElementById('allowance').value) || 0;
        let d = parseFloat(document.getElementById('deduction').value) || 0;
        document.getElementById('net').value = (b + a - d).toFixed(2);
    }

    // Function to fill and show the slip modal
    function showSlip(data) {
        document.getElementById('s_name').innerText = data.name;
        document.getElementById('s_dept').innerText = data.department;
        document.getElementById('s_month').innerText = data.month_year;
        document.getElementById('s_basic').innerText = "$" + parseFloat(data.basic_salary).toLocaleString();
        document.getElementById('s_allow').innerText = "+$" + parseFloat(data.allowance).toLocaleString();
        document.getElementById('s_deduct').innerText = "-$" + parseFloat(data.deduction).toLocaleString();
        document.getElementById('s_net').innerText = "$" + parseFloat(data.net_salary).toLocaleString();
        openModal('slipModal');
    }

    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }
</script>

</body>
</html>