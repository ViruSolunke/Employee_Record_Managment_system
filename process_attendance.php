<<<<<<< HEAD
<?php
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['submit_attendance'])) {
    $date = $_POST['att_date'];
    $statuses = $_POST['status']; // Array of [emp_id => status]

    foreach ($statuses as $emp_id => $status) {
        // Use REPLACE INTO to either insert a new record or update the existing one for that date
        $stmt = $conn->prepare("INSERT INTO attendance (emp_id, attendance_date, status) 
                                VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE status = ?");
        $stmt->bind_param("isss", $emp_id, $date, $status, $status);
        $stmt->execute();
    }

    header("Location: manage_attendance.php?date=$date&success=1");
}
=======
<?php
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['submit_attendance'])) {
    $date = $_POST['att_date'];
    $statuses = $_POST['status']; // Array of [emp_id => status]

    foreach ($statuses as $emp_id => $status) {
        // Use REPLACE INTO to either insert a new record or update the existing one for that date
        $stmt = $conn->prepare("INSERT INTO attendance (emp_id, attendance_date, status) 
                                VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE status = ?");
        $stmt->bind_param("isss", $emp_id, $date, $status, $status);
        $stmt->execute();
    }

    header("Location: manage_attendance.php?date=$date&success=1");
}
>>>>>>> f85973a80cecedcf69b2e776b7b8d62696968cf0
?>