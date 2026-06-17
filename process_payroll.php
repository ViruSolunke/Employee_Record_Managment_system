<<<<<<< HEAD
<?php
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['save_payroll'])) {
    $emp_id = $_POST['emp_id'];
    $month = $_POST['month_year'];
    $basic = $_POST['basic'];
    $allowance = $_POST['allowance'];
    $deduction = $_POST['deduction'];
    $net = $_POST['net'];

    // Check if salary for this month already exists
    $check = $conn->query("SELECT id FROM payroll WHERE emp_id = $emp_id AND month_year = '$month'");
    
    if ($check->num_rows > 0) {
        die("Error: Salary for this month has already been processed. <a href='manage_payroll.php'>Go back</a>");
    }

    $stmt = $conn->prepare("INSERT INTO payroll (emp_id, month_year, basic_salary, allowance, deduction, net_salary) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdddd", $emp_id, $month, $basic, $allowance, $deduction, $net);

    if ($stmt->execute()) {
        header("Location: manage_payroll.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
=======
<?php
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['save_payroll'])) {
    $emp_id = $_POST['emp_id'];
    $month = $_POST['month_year'];
    $basic = $_POST['basic'];
    $allowance = $_POST['allowance'];
    $deduction = $_POST['deduction'];
    $net = $_POST['net'];

    // Check if salary for this month already exists
    $check = $conn->query("SELECT id FROM payroll WHERE emp_id = $emp_id AND month_year = '$month'");
    
    if ($check->num_rows > 0) {
        die("Error: Salary for this month has already been processed. <a href='manage_payroll.php'>Go back</a>");
    }

    $stmt = $conn->prepare("INSERT INTO payroll (emp_id, month_year, basic_salary, allowance, deduction, net_salary) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdddd", $emp_id, $month, $basic, $allowance, $deduction, $net);

    if ($stmt->execute()) {
        header("Location: manage_payroll.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
>>>>>>> f85973a80cecedcf69b2e776b7b8d62696968cf0
