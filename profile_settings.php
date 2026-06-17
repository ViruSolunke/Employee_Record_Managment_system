<<<<<<< HEAD
<?php
// 1. SESSION AND SECURITY BLOCK
session_start();

// 2. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. ACCESS CONTROL
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// 4. FETCH CURRENT USER DATA
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 5. HANDLE PROFILE UPDATE
if (isset($_POST['update_profile'])) {
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $update->bind_param("ssi", $new_name, $new_email, $user_id);
    
    if ($update->execute()) {
        $_SESSION['user_name'] = $new_name; // Update session name for the UI
        $msg = "Profile updated successfully!";
        header("Refresh:1; url=profile_settings.php");
    }
}

// 6. HANDLE PASSWORD CHANGE
if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_pass'];
    $new_pass = $_POST['new_pass'];
    
    // Verify current password
    $check = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $hashed_pass = $check->get_result()->fetch_assoc()['password'];

    if (password_verify($current_pass, $hashed_pass)) {
        $new_hashed = password_hash($new_pass, PASSWORD_BCRYPT);
        $update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_pass->bind_param("si", $new_hashed, $user_id);
        $update_pass->execute();
        $msg = "Password changed successfully!";
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        /* Sidebar */
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .sidebar-brand { padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem; }
        .sidebar-menu { list-style:none; padding:0 20px; }
        .sidebar-menu a { color:#94a3b8; text-decoration:none; display:block; padding:15px; transition: 0.3s; }
        .sidebar-menu li.active a { background:rgba(255,255,255,0.1); border-radius:10px; color:white; }

        /* Content */
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; box-sizing: border-box; }
        .settings-container { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        .profile-header { text-align: center; margin-bottom: 20px; }
        .avatar-lg { width: 100px; height: 100px; background: var(--accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px; font-weight: bold; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: #64748b; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box; }
        
        .btn-save { background: var(--accent); color: white; padding: 12px 25px; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; width: 100%; transition: 0.3s; }
        .btn-save:hover { background: #2563eb; }
        
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: #dcfce7; color: #15803d; }
        .alert-error { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">ERMS</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="active"><a href="profile_settings.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
        <li><a href="logout.php" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Account Settings</h1>

    <?php if($msg): ?> <div class="alert alert-success"><?php echo $msg; ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="alert alert-error"><?php echo $error; ?></div> <?php endif; ?>

    <div class="settings-container">
        <div class="card">
            <div class="profile-header">
                <div class="avatar-lg"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                <h2 style="margin: 10px 0 5px;"><?php echo htmlspecialchars($user['name']); ?></h2>
                <span style="background: #eff6ff; color: var(--accent); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;"><?php echo $user['role']; ?></span>
            </div>
            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 20px 0;">
            <p style="font-size: 0.9rem; color: #64748b;"><i class="fas fa-envelope" style="margin-right: 10px;"></i> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="card">
            <form method="POST">
                <h3>Personal Information</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn-save">Update Profile</button>
            </form>

            <hr style="margin: 40px 0; border: 0; border-top: 1px solid #f1f5f9;">

            <form method="POST">
                <h3>Change Password</h3>
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_pass" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_pass" required minlength="6">
                </div>
                <button type="submit" name="change_password" class="btn-save" style="background: var(--primary);">Change Password</button>
            </form>
        </div>
    </div>
</div>

</body>
=======
<?php
// 1. SESSION AND SECURITY BLOCK
session_start();

// 2. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. ACCESS CONTROL
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// 4. FETCH CURRENT USER DATA
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 5. HANDLE PROFILE UPDATE
if (isset($_POST['update_profile'])) {
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $update->bind_param("ssi", $new_name, $new_email, $user_id);
    
    if ($update->execute()) {
        $_SESSION['user_name'] = $new_name; // Update session name for the UI
        $msg = "Profile updated successfully!";
        header("Refresh:1; url=profile_settings.php");
    }
}

// 6. HANDLE PASSWORD CHANGE
if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_pass'];
    $new_pass = $_POST['new_pass'];
    
    // Verify current password
    $check = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $hashed_pass = $check->get_result()->fetch_assoc()['password'];

    if (password_verify($current_pass, $hashed_pass)) {
        $new_hashed = password_hash($new_pass, PASSWORD_BCRYPT);
        $update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_pass->bind_param("si", $new_hashed, $user_id);
        $update_pass->execute();
        $msg = "Password changed successfully!";
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | ERMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #3b82f6; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        /* Sidebar */
        .sidebar { width: 260px; height: 100vh; background: var(--primary); color: white; position: fixed; }
        .sidebar-brand { padding:30px; font-weight:700; color:var(--accent); font-size:1.5rem; }
        .sidebar-menu { list-style:none; padding:0 20px; }
        .sidebar-menu a { color:#94a3b8; text-decoration:none; display:block; padding:15px; transition: 0.3s; }
        .sidebar-menu li.active a { background:rgba(255,255,255,0.1); border-radius:10px; color:white; }

        /* Content */
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 40px; box-sizing: border-box; }
        .settings-container { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        .profile-header { text-align: center; margin-bottom: 20px; }
        .avatar-lg { width: 100px; height: 100px; background: var(--accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px; font-weight: bold; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: #64748b; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box; }
        
        .btn-save { background: var(--accent); color: white; padding: 12px 25px; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; width: 100%; transition: 0.3s; }
        .btn-save:hover { background: #2563eb; }
        
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: #dcfce7; color: #15803d; }
        .alert-error { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">ERMS</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="active"><a href="profile_settings.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
        <li><a href="logout.php" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Account Settings</h1>

    <?php if($msg): ?> <div class="alert alert-success"><?php echo $msg; ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="alert alert-error"><?php echo $error; ?></div> <?php endif; ?>

    <div class="settings-container">
        <div class="card">
            <div class="profile-header">
                <div class="avatar-lg"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                <h2 style="margin: 10px 0 5px;"><?php echo htmlspecialchars($user['name']); ?></h2>
                <span style="background: #eff6ff; color: var(--accent); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;"><?php echo $user['role']; ?></span>
            </div>
            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 20px 0;">
            <p style="font-size: 0.9rem; color: #64748b;"><i class="fas fa-envelope" style="margin-right: 10px;"></i> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="card">
            <form method="POST">
                <h3>Personal Information</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn-save">Update Profile</button>
            </form>

            <hr style="margin: 40px 0; border: 0; border-top: 1px solid #f1f5f9;">

            <form method="POST">
                <h3>Change Password</h3>
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_pass" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_pass" required minlength="6">
                </div>
                <button type="submit" name="change_password" class="btn-save" style="background: var(--primary);">Change Password</button>
            </form>
        </div>
    </div>
</div>

</body>
>>>>>>> f85973a80cecedcf69b2e776b7b8d62696968cf0
</html>