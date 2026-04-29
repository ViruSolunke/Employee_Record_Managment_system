<!-- CREATE TABLE IF NOT EXISTS employees (
    emp_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(50),
    designation VARCHAR(50),
    salary DECIMAL(10, 2),
    joining_date DATE,
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
); -->


<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'Employee') {
    header("Location: dashboard.php"); // Restrict access to Admin/HR only
    exit();
}
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM employees WHERE emp_id = $id");
    header("Location: manage_employees.php?msg=Deleted Successfully");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Employees | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f1f5f9; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        /* Reusing Sidebar style from Dashboard */
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }

        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .search-box { padding: 10px 15px; border-radius: 8px; border: 1px solid #ddd; width: 300px; }
        
        .btn-add { background: var(--accent); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; border:none; cursor:pointer; }
        
        /* Table Styles */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 0.85rem; }
        td { padding: 15px; border-top: 1px solid #f1f5f9; font-size: 0.9rem; }
        
        .action-btns a { margin-right: 10px; color: #64748b; transition: 0.3s; }
        .btn-edit:hover { color: var(--accent); }
        .btn-delete:hover { color: #ef4444; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; width: 500px; margin: 50px auto; padding: 30px; border-radius: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 0.85rem; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="padding:30px; font-weight:700; color:var(--accent);">ERMS ADMIN</div>
    <ul style="list-style:none; padding:0 20px;">
        <li><a href="dashboard.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-home"></i> Dashboard</a></li>
        <li style="background:rgba(255,255,255,0.1); border-radius:8px;"><a href="manage_employees.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-users"></i> Employees</a></li>
        <li><a href="logout.php" style="color:#f87171; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header-actions">
        <h1>Employee List</h1>
        <div>
            <input type="text" id="searchInput" class="search-box" placeholder="Search by name or dept..." onkeyup="filterTable()">
            <button class="btn-add" onclick="openModal('add')"><i class="fas fa-plus"></i> Add Employee</button>
        </div>
    </div>

    <div class="table-container">
        <table id="employeeTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM employees ORDER BY emp_id DESC");
                while($row = $res->fetch_assoc()):
                ?>
                <tr>
                    <td>#<?php echo $row['emp_id']; ?></td>
                    <td><strong><?php echo $row['name']; ?></strong><br><small><?php echo $row['email']; ?></small></td>
                    <td><?php echo $row['department']; ?></td>
                    <td><?php echo $row['designation']; ?></td>
                    <td><span style="color: <?php echo $row['status']=='Active'?'#22c55e':'#ef4444'; ?>"><?php echo $row['status']; ?></span></td>
                    <td class="action-btns">
                        <a href="javascript:void(0)" class="btn-edit" onclick='editEmployee(<?php echo json_encode($row); ?>)'><i class="fas fa-edit"></i></a>
                        <a href="manage_employees.php?delete=<?php echo $row['emp_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="empModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Add New Employee</h2>
        <form action="process_employee.php" method="POST">
            <input type="hidden" name="emp_id" id="emp_id">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Department</label>
                    <select name="department" id="department">
                        <option>IT</option><option>HR</option><option>Finance</option><option>Sales</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="save_employee" class="btn-add" style="width:100%; margin-top:20px;">Save Record</button>
            <button type="button" onclick="closeModal()" style="width:100%; margin-top:10px; background:none; border:none; cursor:pointer; color:#64748b;">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('empModal').style.display = 'block'; }
    function closeModal() { document.getElementById('empModal').style.display = 'none'; document.querySelector('form').reset(); }

    function editEmployee(data) {
        openModal();
        document.getElementById('modalTitle').innerText = "Edit Employee";
        document.getElementById('emp_id').value = data.emp_id;
        document.getElementById('name').value = data.name;
        document.getElementById('email').value = data.email;
        document.getElementById('department').value = data.department;
        document.getElementById('status').value = data.status;
    }

    function filterTable() {
        let input = document.getElementById("searchInput").value.toUpperCase();
        let rows = document.getElementById("employeeTable").getElementsByTagName("tr");
        for (let i = 1; i < rows.length; i++) {
            rows[i].style.display = rows[i].innerText.toUpperCase().includes(input) ? "" : "none";
        }
    }
</script>
</body>
</html>