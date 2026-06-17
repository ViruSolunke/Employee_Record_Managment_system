<<<<<<< HEAD
<?php
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['save_employee'])) {
    $id = $_POST['emp_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dept = $_POST['department'];
    $status = $_POST['status'];

    if (!empty($id)) {
        // UPDATE EXISTING
        $stmt = $conn->prepare("UPDATE employees SET name=?, email=?, department=?, status=? WHERE emp_id=?");
        $stmt->bind_param("ssssi", $name, $email, $dept, $status, $id);
    } else {
        // INSERT NEW
        $stmt = $conn->prepare("INSERT INTO employees (name, email, department, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $dept, $status);
    }

    if ($stmt->execute()) {
        header("Location: manage_employees.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
=======
<?php
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['save_employee'])) {
    $id = $_POST['emp_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dept = $_POST['department'];
    $status = $_POST['status'];

    if (!empty($id)) {
        // UPDATE EXISTING
        $stmt = $conn->prepare("UPDATE employees SET name=?, email=?, department=?, status=? WHERE emp_id=?");
        $stmt->bind_param("ssssi", $name, $email, $dept, $status, $id);
    } else {
        // INSERT NEW
        $stmt = $conn->prepare("INSERT INTO employees (name, email, department, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $dept, $status);
    }

    if ($stmt->execute()) {
        header("Location: manage_employees.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
>>>>>>> f85973a80cecedcf69b2e776b7b8d62696968cf0
?>