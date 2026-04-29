<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

$msg = "";
// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM departments WHERE id = $id");
    $msg = "Department removed successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departments | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-add { background: var(--accent); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .btn-add:hover { background: #2563eb; }

        /* Card Grid */
        .dept-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .dept-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 4px solid var(--accent); transition: 0.3s; }
        .dept-card:hover { transform: translateY(-5px); }
        .dept-card h3 { margin: 0 0 10px 0; color: var(--primary); font-size: 1.25rem; }
        .dept-card p { color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px; }
        
        .card-actions { border-top: 1px solid #f1f5f9; pt: 15px; display: flex; gap: 15px; padding-top: 15px; }
        .card-actions a { text-decoration: none; font-size: 0.9rem; font-weight: 600; cursor: pointer; }
        
        /* Modal */
        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; width: 450px; margin: 10% auto; padding: 30px; border-radius: 15px; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.85rem; }
        .input-group input, .input-group textarea { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem;">ERMS</div>
    <ul style="list-style:none; padding:0 20px;">
        <li><a href="dashboard.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="manage_employees.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-users"></i> Employees</a></li>
        <li style="background:rgba(255,255,255,0.1); border-radius:10px;"><a href="manage_departments.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sitemap"></i> Departments</a></li>
        <li><a href="logout.php" style="color:#f87171; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>Departments</h1>
        <button class="btn-add" onclick="openModal()"><i class="fas fa-plus"></i> Add New Dept</button>
    </div>

    <?php if($msg): ?> <p style="color: #10b981; font-weight: 600;"><?php echo $msg; ?></p> <?php endif; ?>

    <div class="dept-grid">
        <?php
        $res = $conn->query("SELECT * FROM departments ORDER BY department_name ASC");
        while($row = $res->fetch_assoc()):
        ?>
        <div class="dept-card">
            <h3><?php echo $row['department_name']; ?></h3>
            <p><?php echo $row['description'] ?: 'No description provided for this department.'; ?></p>
            <div class="card-actions">
                <a href="javascript:void(0)" onclick='editDept(<?php echo json_encode($row); ?>)' style="color: var(--accent);"><i class="fas fa-edit"></i> Edit</a>
                <a href="manage_departments.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this department?')" style="color: #ef4444;"><i class="fas fa-trash"></i> Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div id="deptModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Add Department</h2>
        <form action="process_dept.php" method="POST">
            <input type="hidden" name="id" id="dept_id">
            <div class="input-group">
                <label>Department Name</label>
                <input type="text" name="dept_name" id="dept_name" required placeholder="e.g. Engineering">
            </div>
            <div class="input-group">
                <label>Description</label>
                <textarea name="description" id="dept_desc" rows="4" placeholder="Brief role of the department..."></textarea>
            </div>
            <button type="submit" name="save_dept" class="btn-add" style="width:100%;">Save Department</button>
            <button type="button" onclick="closeModal()" style="width:100%; margin-top:10px; background:none; border:none; cursor:pointer; color:#64748b;">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('deptModal').style.display = 'block'; }
    function closeModal() { document.getElementById('deptModal').style.display = 'none'; document.querySelector('form').reset(); }
    
    function editDept(data) {
        openModal();
        document.getElementById('modalTitle').innerText = "Edit Department";
        document.getElementById('dept_id').value = data.id;
        document.getElementById('dept_name').value = data.department_name;
        document.getElementById('dept_desc').value = data.description;
    }
</script>
</body>
</html>