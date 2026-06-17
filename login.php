<!-- http://localhost/Employee_Record_Managment_system/login.php -->


<!-- CREATE DATABASE employee_mgmt;
USE employee_mgmt;

CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'HR', 'Employee') NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
);

-- Insert a test user (Password is 'admin123')
INSERT INTO users (name, email, password, role, status) 
VALUES ('System Admin', 'admin@corp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Active'); -->


<!-- CREATE DATABASE IF NOT EXISTS employee_mgmt;
USE employee_mgmt;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'HR', 'Employee') NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
); -->

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

$message = "";
$msg_type = ""; // Success or Error

// --- HANDLE REGISTRATION ---
if (isset($_POST['register'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role  = $_POST['role'];
    $pass  = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $message = "Email already exists!";
        $msg_type = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $pass, $role);
        if ($stmt->execute()) {
            $message = "Registration successful! Please login.";
            $msg_type = "success";
        }
    }
}

// --- HANDLE LOGIN ---
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php"); // Redirect to your dashboard
            exit();
        } else {
            $message = "Invalid password.";
            $msg_type = "error";
        }
    } else {
        $message = "User not found.";
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Corporate ERMS | Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #2c3e50; --accent: #3498db; --bg: #f4f7f6; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        .container { background: #fff; width: 400px; padding: 2rem; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); position: relative; }
        h2 { text-align: center; color: var(--primary); margin-top: 0; }
        
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 0.9rem; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }

        .input-group { position: relative; margin-bottom: 15px; }
        .input-group i { position: absolute; left: 15px; top: 38px; color: #888; }
        label { display: block; margin-bottom: 5px; font-size: 0.85rem; color: #666; font-weight: 600; }
        input, select { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; outline: none; transition: 0.3s; }
        input:focus { border-color: var(--accent); box-shadow: 0 0 8px rgba(52, 152, 219, 0.2); }

        .btn { width: 100%; padding: 12px; background: var(--primary); color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .btn:hover { background: #1a252f; transform: translateY(-1px); }
        
        .toggle-link { text-align: center; margin-top: 20px; font-size: 0.9rem; }
        .toggle-link a { color: var(--accent); text-decoration: none; font-weight: 600; cursor: pointer; }
        
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="container">
    <?php if($message): ?>
        <div class="alert <?php echo $msg_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div id="login-form">
        <h2>Corporate Login</h2>
        <form method="POST">
            <div class="input-group">
                <label>Email Address</label>
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn"><i class="fas fa-sign-in-alt"></i> Sign In</button>
        </form>
        <div class="toggle-link">
            New employee? <a onclick="toggleForms()">Create Account</a>
        </div>
    </div>

    <div id="register-form" class="hidden">
        <h2>Register Member</h2>
        <form method="POST">
            <div class="input-group">
                <label>Full Name</label>
                <i class="fas fa-user"></i>
                <input type="text" name="fullname" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Role</label>
                <i class="fas fa-briefcase"></i>
                <select name="role" style="padding-left: 40px;" required>
                    <option value="Employee">Employee</option>
                    <option value="HR">HR</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div class="input-group">
                <label>Create Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="register" class="btn"><i class="fas fa-user-plus"></i> Register</button>
        </form>
        <div class="toggle-link">
            Already registered? <a onclick="toggleForms()">Back to Login</a>
        </div>
    </div>
</div>

<script>
    function toggleForms() {
        document.getElementById('login-form').classList.toggle('hidden');
        document.getElementById('register-form').classList.toggle('hidden');
    }
</script>

</body>
</html>