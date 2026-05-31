<?php
session_start();
include 'confi.php';

if (isset($_SESSION['user_id']) && isset($_POST['hID'])) {
    $needer = $_SESSION['user_id']; 
    $helper = $_POST['hID'];       

    // Insert into your service_requests table
    $sql = "INSERT INTO service_requests (neederID, helperID, status) VALUES (?, ?, 'pending')";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $needer, $helper);
        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "Database error";
        }
    }
} else {
    echo "Not authorized or missing ID";
}
?>