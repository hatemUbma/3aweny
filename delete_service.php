<?php
session_start();
include 'confi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $hID = $_POST['helpID'];
    $uID = $_SESSION['user_id'];

    // Security check: ensure the helper owns this offer before deleting
    $sql = "DELETE FROM help WHERE helpID = ? AND userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $hID, $uID);
    
    if ($stmt->execute()) {
        header("Location: profile.php"); // Redirect back to profile
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>