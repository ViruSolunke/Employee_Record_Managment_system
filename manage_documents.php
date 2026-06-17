<<<<<<< HEAD
<!-- CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
); -->

<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "employee_mgmt");
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Get the internal emp_id for the logged-in user
$emp_query = $conn->query("SELECT emp_id FROM employees WHERE email = (SELECT email FROM users WHERE id = $user_id)");
$emp_data = $emp_query->fetch_assoc();
$current_emp_id = $emp_data['emp_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Documents | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }

        .upload-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border: 2px dashed #e2e8f0; text-align: center; }
        
        .file-input { margin-bottom: 15px; }
        .btn-upload { background: var(--accent); color: white; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }

        .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .doc-item { background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: relative; transition: 0.3s; }
        .doc-item:hover { transform: translateY(-5px); }
        .doc-icon { font-size: 3rem; color: #cbd5e1; margin-bottom: 10px; }
        .doc-name { font-size: 0.9rem; font-weight: 600; color: var(--primary); word-break: break-all; margin-bottom: 10px; }
        
        .btn-download { color: var(--accent); text-decoration: none; font-size: 0.8rem; font-weight: 600; }
        .btn-delete { color: #ef4444; position: absolute; top: 10px; right: 10px; font-size: 0.8rem; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem;">ERMS</div>
    <ul style="list-style:none; padding:0 20px;">
        <li><a href="dashboard.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-home"></i> Dashboard</a></li>
        <li style="background:rgba(255,255,255,0.1); border-radius:10px;"><a href="manage_documents.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-file-alt"></i> Documents</a></li>
        <li><a href="logout.php" style="color:#f87171; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Document Management</h1>

    <div class="upload-card">
        <h3>Upload New Document</h3>
        <form action="process_document.php" method="POST" enctype="multipart/form-data">
            <div class="file-input">
                <input type="text" name="doc_name" placeholder="Document Label (e.g. Passport)" required style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-right: 10px;">
                <input type="file" name="emp_doc" required>
            </div>
            <button type="submit" name="upload_doc" class="btn-upload"><i class="fas fa-cloud-upload-alt"></i> Upload File</button>
        </form>
    </div>

    <div class="doc-grid">
        <?php
        // Admins see all docs, Employees see only their own
        $sql = ($user_role == 'Admin') 
               ? "SELECT * FROM documents" 
               : "SELECT * FROM documents WHERE emp_id = $current_emp_id";
        
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()):
        ?>
        <div class="doc-item">
            <?php if($user_role == 'Admin'): ?>
                <a href="process_document.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this file?')"><i class="fas fa-trash"></i></a>
            <?php endif; ?>
            <div class="doc-icon"><i class="fas fa-file-pdf"></i></div>
            <div class="doc-name"><?php echo htmlspecialchars($row['document_name']); ?></div>
            <a href="<?php echo $row['file_path']; ?>" class="btn-download" download><i class="fas fa-download"></i> Download</a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
=======
<!-- CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id) ON DELETE CASCADE
); -->

<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "employee_mgmt");
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Get the internal emp_id for the logged-in user
$emp_query = $conn->query("SELECT emp_id FROM employees WHERE email = (SELECT email FROM users WHERE id = $user_id)");
$emp_data = $emp_query->fetch_assoc();
$current_emp_id = $emp_data['emp_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Documents | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; }

        .upload-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border: 2px dashed #e2e8f0; text-align: center; }
        
        .file-input { margin-bottom: 15px; }
        .btn-upload { background: var(--accent); color: white; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }

        .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .doc-item { background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: relative; transition: 0.3s; }
        .doc-item:hover { transform: translateY(-5px); }
        .doc-icon { font-size: 3rem; color: #cbd5e1; margin-bottom: 10px; }
        .doc-name { font-size: 0.9rem; font-weight: 600; color: var(--primary); word-break: break-all; margin-bottom: 10px; }
        
        .btn-download { color: var(--accent); text-decoration: none; font-size: 0.8rem; font-weight: 600; }
        .btn-delete { color: #ef4444; position: absolute; top: 10px; right: 10px; font-size: 0.8rem; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem;">ERMS</div>
    <ul style="list-style:none; padding:0 20px;">
        <li><a href="dashboard.php" style="color:#94a3b8; text-decoration:none; display:block; padding:15px;"><i class="fas fa-home"></i> Dashboard</a></li>
        <li style="background:rgba(255,255,255,0.1); border-radius:10px;"><a href="manage_documents.php" style="color:white; text-decoration:none; display:block; padding:15px;"><i class="fas fa-file-alt"></i> Documents</a></li>
        <li><a href="logout.php" style="color:#f87171; text-decoration:none; display:block; padding:15px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Document Management</h1>

    <div class="upload-card">
        <h3>Upload New Document</h3>
        <form action="process_document.php" method="POST" enctype="multipart/form-data">
            <div class="file-input">
                <input type="text" name="doc_name" placeholder="Document Label (e.g. Passport)" required style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-right: 10px;">
                <input type="file" name="emp_doc" required>
            </div>
            <button type="submit" name="upload_doc" class="btn-upload"><i class="fas fa-cloud-upload-alt"></i> Upload File</button>
        </form>
    </div>

    <div class="doc-grid">
        <?php
        // Admins see all docs, Employees see only their own
        $sql = ($user_role == 'Admin') 
               ? "SELECT * FROM documents" 
               : "SELECT * FROM documents WHERE emp_id = $current_emp_id";
        
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()):
        ?>
        <div class="doc-item">
            <?php if($user_role == 'Admin'): ?>
                <a href="process_document.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this file?')"><i class="fas fa-trash"></i></a>
            <?php endif; ?>
            <div class="doc-icon"><i class="fas fa-file-pdf"></i></div>
            <div class="doc-name"><?php echo htmlspecialchars($row['document_name']); ?></div>
            <a href="<?php echo $row['file_path']; ?>" class="btn-download" download><i class="fas fa-download"></i> Download</a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
>>>>>>> f85973a80cecedcf69b2e776b7b8d62696968cf0
</html>