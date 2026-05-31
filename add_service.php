<?php
session_start();
include 'confi.php';

// Guard: User must be logged in to add a service
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_info'])) {
    $wilaya   = $_POST['wilaya'];
    $location = $_POST['locat'];
    $bio      = $_POST['bio'];
    $price    = $_POST['price'];
    $category = $_POST['category'];

    $picture = null;
    if (isset($_FILES['pic']) && $_FILES['pic']['tmp_name'] != "") {
        $picture = file_get_contents($_FILES['pic']['tmp_name']);
    }

    $sql = "INSERT INTO help (userID, picture, location, bio, price, category, wilaya) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    $null = NULL;
    mysqli_stmt_bind_param($stmt, "ibssiss", $userID, $null, $location, $bio, $price, $category, $wilaya);

    if ($picture !== null) {
        mysqli_stmt_send_long_data($stmt, 1, $picture);
    }

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: profile.php"); 
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service - 3aweny</title>
    <style>
        /* Keeping your exact styling from the signup page */
        body {
            margin: 0; padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #5a8f72;
            display: flex; justify-content: center; align-items: center;
            height: 100vh;
        }
        .signup-container {
            display: flex; width: 850px; height: 550px;
            background-color: white; border-radius: 15px;
            overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #38ef7d, #11998e);
            color: white; display: flex; flex-direction: column;
            justify-content: center; padding: 40px;
        }
        .right-panel {
            flex: 1; padding: 40px; display: flex;
            flex-direction: column; justify-content: center; align-items: center;
        }
        .right-panel h2 { color: #2ecc71; letter-spacing: 1.5px; margin-bottom: 25px; text-transform: uppercase; font-size: 0.85rem; }
        form { width: 100%; display: flex; flex-direction: column; gap: 12px; }
        input, .custom-input, textarea {
            width: 100%; padding: 12px 20px; border: none;
            border-radius: 25px; background-color: #f0f8f4;
            font-size: 0.9rem; outline: none; box-sizing: border-box;
        }
        textarea { border-radius: 15px; resize: none; }
        .login-btn {
            width: 100%; padding: 15px; border: none; border-radius: 50px;
            background: linear-gradient(to right, #2ecc71, #1abc9c);
            color: white; font-weight: bold; cursor: pointer;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4);
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="signup-container">
    <div class="left-panel">
        <h1>Add a New Service</h1>
        <p>Expand your reach and help more people in your community.</p>
    </div>

    <div class="right-panel">
        <h2>Service Details</h2>
        <form action="add_service.php" method="POST" enctype="multipart/form-data">
            
            <select name="category" required class="custom-input">
                <option value="" disabled selected>Select Category</option>
                <option value="Physical Effort">Physical Effort</option>
                <option value="Babysitting">Babysitting</option>
                <option value="House Cleaning">House Cleaning</option>
                <option value="Patient Care">Patient Care</option>
                <option value="Pet Care">Pet Care</option>
            </select>

            <select name="wilaya" required class="custom-input">
                <option value="" disabled selected>Select Wilaya</option>
                <option value="Alger">Alger</option>
                <option value="Annaba">Annaba</option>
                <option value="Skikda">Skikda</option>
                <option value="Oran">Oran</option>
            </select>
            
            <input type="text" name="locat" placeholder="Specific City/Neighborhood" required>
            <input type="number" name="price" placeholder="Price per Hour (DA)" required>
            <textarea name="bio" placeholder="Describe your experience with this specific service..." rows="3" required></textarea>

            <label style="font-size: 12px; color: #666; margin-left: 10px;">Service Image:</label>
            <input type="file" name="pic" accept="image/*" required>
            
            <button type="submit" name="submit_info" class="login-btn">POST SERVICE</button>
            <a href="profile.php" style="text-align: center; color: #999; font-size: 12px; text-decoration: none; margin-top: 10px;">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>