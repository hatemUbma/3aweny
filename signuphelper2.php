<?php
session_start();
include 'confi.php';

// Guard: must have completed step 1
if (!isset($_SESSION['current_user_id'])) {
    die("Error: Please complete step 1 first.");
}

$userID = $_SESSION['current_user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_info'])) {
    $wilaya = $_POST['wilaya'];
    $location = $_POST['locat'];
    $bio      = $_POST['bio'];
    $price    = $_POST['price'];
    $category = $_POST['category'];


    // Handle image upload
    $picture = null;
    if (isset($_FILES['pic']) && $_FILES['pic']['tmp_name'] != "") {
        $picture = file_get_contents($_FILES['pic']['tmp_name']);
    }

    $sql = "INSERT INTO help (userID, picture, location, bio, price, category ,wilaya) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    // i=userID, b=picture(blob), s=location, s=bio, i=price, s=category
    $null = NULL;
    mysqli_stmt_bind_param($stmt, "ibssiss", $userID, $null, $location, $bio, $price, $category,$wilaya);

    // Send blob data
    if ($picture !== null) {
        mysqli_stmt_send_long_data($stmt, 1, $picture);
    }

    if (mysqli_stmt_execute($stmt)) {
        // Clear session after full signup is complete
        unset($_SESSION['current_user_id']);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header("Location: taskType.php?category=" . urlencode($category));
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
    <link rel="stylesheet" href="style.css">
    <style>
      /* General Page Styling */
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #6d9773; /* Dark green outer background from your pic */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Main Container */
.signup-container {
    display: flex;
    width: 850px;
    height: 550px;
    background-color: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

/* Left Panel - Green Gradient Section */
.left-panel {
    flex: 1;
    background: linear-gradient(135deg, #38ef7d, #11998e); /* Vivid green gradient */
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 40px;
}

.left-panel h1 {
    font-size: 2.8rem;
    margin-bottom: 20px;
    line-height: 1.1;
    font-weight: bold;
}

.left-panel p {
    font-size: 1.1rem;
    opacity: 0.9;
    line-height: 1.4;
}

/* Right Panel - Form Section */
.right-panel {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.right-panel h2 {
    color: #2ecc71; /* Accent green for the title */
    font-size: 0.85rem;
    letter-spacing: 1.5px;
    margin-bottom: 25px;
    text-transform: uppercase;
}

/* Form Layout */
form {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Input, Select, and Textarea Styling */
input, .custom-input, textarea {
    width: 100%;
    padding: 12px 20px;
    border: none;
    border-radius: 25px; /* Rounded "pill" shape */
    background-color: #f0eaff; /* The light lavender tint from your pic */
    font-size: 0.9rem;
    outline: none;
    box-sizing: border-box;
    font-family: inherit;
    color: #333;
}

textarea {
    border-radius: 15px; /* Slightly less rounded for text areas */
    resize: none;
}

input::placeholder, textarea::placeholder {
    color: #b0a8c5;
}

/* File Upload Label */
.file-label {
    font-size: 0.8rem
}

.login-btn{
      width: 100%;
    padding: 18px; /* Slightly bigger padding for mobile */
    border: none;
    border-radius: 50px;
    
    /* Updated: A smoother green gradient */
    background: linear-gradient(to right, #2ecc71 0%, #1abc9c 100%);
    color: white;
    font-size: 1.1rem;
    font-weight: bold;
    text-transform: uppercase; /* Makes it more professional */
    
    cursor: pointer; /* Crucial for UX */
    
    /* Soft matching green shadow */
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4); 
    transition: 0.3s ease; /* Smooth animation */
}

    </style>
    <title>Setup Your Service - 3aweny</title>
</head>

<body>
 <div class="signup-container">
    <div class="left-panel">
        <h1>Complete Your Profile</h1>
        <p>Tell the community how you can help them today.</p>
    </div>

    <div class="right-panel">
        <h2>HELPER DETAILS</h2>
        <form action="signuphelper2.php" method="POST" enctype="multipart/form-data">
            
            <select name="category" required class="custom-input">
                <option value="" disabled selected>Select Category</option>
                <option value="Physical Effort">Physical Effort</option>
                <option value="Babysitting">Babysitting</option>
                <option value="House Cleaning">House Cleaning</option>
                <option value="Patient Care">Patient Care</option>
                <option value="Pet Care">Pet Care</option>
            </select>

            
            <select name="wilaya" required class="custom-input">
                <option value="" disabled selected>Select Willaya</option>
                <option value="Alger">Alger</option>
                <option value="Annaba">Annaba</option>
                <option value="Skikda">Skikda</option>
                <option value="Oran">Oran</option>
                <option value="Tamenrast">Tamenrast</option>
            </select>
            <input type="text" name="locat" placeholder="Location (e.g., Algiers, Oran)" required>
             
            
            <input type="number" name="price" placeholder="Price per Hour (DA)" required>
            
            <textarea name="bio" placeholder="Brief Bio / Experience" rows="3" required></textarea>

            <label for="pic" class="tmp_name">Upload Profile Picture</label>
            <input type="file" id="pic" name="pic" accept="image/*" required>
            
            <button type="submit" name="submit_info" class="login-btn">SAVE INFORMATION</button>
        </form>
    </div>
</div>
</body>
</html>