<?php
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['save_dept'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['dept_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    if (!empty($id)) {
        // Update Logic
        $sql = "UPDATE departments SET department_name='$name', description='$desc' WHERE id=$id";
    } else {
        // Check for duplicates
        $check = $conn->query("SELECT id FROM departments WHERE department_name = '$name'");
        if ($check->num_rows > 0) {
            die("Error: Department name already exists. <a href='manage_departments.php'>Go back</a>");
        }
        $sql = "INSERT INTO departments (department_name, description) VALUES ('$name', '$desc')";
    }

    if ($conn->query($sql)) {
        header("Location: manage_departments.php");
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>