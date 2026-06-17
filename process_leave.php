<<<<<<< HEAD
<?php
session_start();
// Enable full error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['apply_leave'])) {
    // 1. Check Session
    if (!isset($_SESSION['user_id'])) {
        die("STOP: You are not logged in. Session User ID is missing.");
    }

    $user_id = $_SESSION['user_id'];

    // 2. Fetch the Email
    $user_res = $conn->query("SELECT email FROM users WHERE id = $user_id");
    $user_data = $user_res->fetch_assoc();
    $email = $user_data['email'];

    // 3. Find Employee ID
    $emp_res = $conn->query("SELECT emp_id FROM employees WHERE email = '$email'");
    
    if ($emp_res->num_rows === 0) {
        die("STOP: No employee found with email: $email. You must add this email to the 'employees' table first.");
    }

    $emp_row = $emp_res->fetch_assoc();
    $emp_id = $emp_row['emp_id'];

    // 4. Capture Form Data
    $type = $_POST['leave_type'];
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];
    $reason = $_POST['reason'];

    // 5. TRY TO INSERT
    try {
        $stmt = $conn->prepare("INSERT INTO leaves (emp_id, leave_type, from_date, to_date, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("issss", $emp_id, $type, $from, $to, $reason);
        
        if ($stmt->execute()) {
            echo "SUCCESS: Data stored. Redirecting...";
            header("Refresh: 2; URL=manage_leaves.php");
        }
    } catch (Exception $e) {
        // This will tell you if a column name is wrong or a constraint failed
        die("DATABASE ERROR: " . $e->getMessage());
    }
} else {
    die("STOP: The form was not submitted correctly. 'apply_leave' button was not detected.");
}
=======
<?php
session_start();
// Enable full error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['apply_leave'])) {
    // 1. Check Session
    if (!isset($_SESSION['user_id'])) {
        die("STOP: You are not logged in. Session User ID is missing.");
    }

    $user_id = $_SESSION['user_id'];

    // 2. Fetch the Email
    $user_res = $conn->query("SELECT email FROM users WHERE id = $user_id");
    $user_data = $user_res->fetch_assoc();
    $email = $user_data['email'];

    // 3. Find Employee ID
    $emp_res = $conn->query("SELECT emp_id FROM employees WHERE email = '$email'");
    
    if ($emp_res->num_rows === 0) {
        die("STOP: No employee found with email: $email. You must add this email to the 'employees' table first.");
    }

    $emp_row = $emp_res->fetch_assoc();
    $emp_id = $emp_row['emp_id'];

    // 4. Capture Form Data
    $type = $_POST['leave_type'];
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];
    $reason = $_POST['reason'];

    // 5. TRY TO INSERT
    try {
        $stmt = $conn->prepare("INSERT INTO leaves (emp_id, leave_type, from_date, to_date, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("issss", $emp_id, $type, $from, $to, $reason);
        
        if ($stmt->execute()) {
            echo "SUCCESS: Data stored. Redirecting...";
            header("Refresh: 2; URL=manage_leaves.php");
        }
    } catch (Exception $e) {
        // This will tell you if a column name is wrong or a constraint failed
        die("DATABASE ERROR: " . $e->getMessage());
    }
} else {
    die("STOP: The form was not submitted correctly. 'apply_leave' button was not detected.");
}
>>>>>>> f85973a80cecedcf69b2e776b7b8d62696968cf0
?>