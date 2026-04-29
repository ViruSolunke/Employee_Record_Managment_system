<?php
session_start();
$conn = new mysqli("localhost", "root", "", "employee_mgmt");

if (isset($_POST['upload_doc'])) {
    $user_id = $_SESSION['user_id'];
    $doc_label = mysqli_real_escape_string($conn, $_POST['doc_name']);
    
    // Get emp_id
    $emp_query = $conn->query("SELECT emp_id FROM employees WHERE email = (SELECT email FROM users WHERE id = $user_id)");
    $emp_id = $emp_query->fetch_assoc()['emp_id'];

    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["emp_doc"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Security: Only allow PDF, JPG, PNG, DOCX
    $allowed_types = array("pdf", "jpg", "png", "docx", "jpeg");
    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES["emp_doc"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO documents (emp_id, document_name, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $emp_id, $doc_label, $target_file);
            $stmt->execute();
            header("Location: manage_documents.php?success=uploaded");
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Sorry, only PDF, JPG, PNG & DOCX files are allowed.";
    }
}

// Handle Delete
if (isset($_GET['delete']) && $_SESSION['role'] == 'Admin') {
    $id = $_GET['delete'];
    $res = $conn->query("SELECT file_path FROM documents WHERE id = $id");
    $row = $res->fetch_assoc();
    
    if (unlink($row['file_path'])) { // Delete physical file
        $conn->query("DELETE FROM documents WHERE id = $id");
    }
    header("Location: manage_documents.php?msg=deleted");
}
?>