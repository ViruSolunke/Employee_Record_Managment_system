<?php
session_start();

// 1. Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "employee_mgmt";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $password = $_POST['password'];

    // 2. Query to find user by email or name
    $sql = "SELECT id, name, password, role, status FROM users WHERE email = ? OR name = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 3. Verify Password & Status
        if (password_verify($password, $user['password'])) {
            if ($user['status'] === 'Active') {
                
                // 4. Store Session Data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // 5. Redirect based on Role
                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: login.php?error=Account is disabled. Contact HR.");
            }
        } else {
            header("Location: login.php?error=Invalid password.");
        }
    } else {
        header("Location: login.php?error=User not found.");
    }
    $stmt->close();
}
$conn->close();
?>