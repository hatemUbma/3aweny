<?php
session_start();
include 'confi.php';

// Test DB connection first
if (!$conn) {
    die("DB Connection failed: " . mysqli_connect_error());
}
echo "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "POST received <br>";
    var_dump($_POST); // Show ALL posted data

    $fullName = $_POST['name']   ?? 'MISSING';
    $email    = $_POST['email']  ?? 'MISSING';
    $Phone    = $_POST['number'] ?? 'MISSING';
    $docID    = $_POST['docID']  ?? 'MISSING';
    $passw    = $_POST['passw']  ?? 'MISSING';

    echo "name=$fullName | email=$email | phone=$Phone | docID=$docID | passw=$passw <br>";

   // 1. Add 'userRole' to your INSERT query
$sql = "INSERT INTO user (fullName, email, phone, passw, docID, userRole) VALUES (?, ?, ?, ?, ?, 'helper')";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

// 2. Keep your bind_param as is (since 'helper' is hardcoded in the SQL above)
mysqli_stmt_bind_param($stmt, "ssisi", $fullName, $email, $Phone, $passw, $docID);

$result = mysqli_stmt_execute($stmt);

    if ($result) {
        $newID = mysqli_insert_id($conn);
        $_SESSION['current_user_id'] = $newID; // This is what Step 2 is looking for!
        
        // Automatically redirect to the next step
        header("Location: signuphelper2.php");
        exit();
    } else {
        echo "Execute FAILED: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    echo "";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3aweny - SIGNUP</title>
    <style>
    
    /* --- 1. Global / Body Styling --- */
* { box-sizing: border-box; } /* Crucial for memoir projects */

body, html {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #fcf6ff; /* Match the very light bg color */
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #5a8f72;
}

.page-background {
    width: 100%;
    height: 100%;
    padding: 30px; /* Gives space for the container */
    display: flex;
    justify-content: center;
    align-items: center;
}

/* --- 2. Main Login Container --- */
.login-container {
    background: white;
    width: 1200px; /* Define the max size */
    max-width: 90%; /* For mobile compatibility */
    height: 600px; /* Define a professional height */
    border-radius: 12px; /* Smooth corners */
    display: flex; /* CRUCIAL: Makes panels split left/right */
    overflow: hidden; /* Keeps the gradient contained */
    box-shadow: 0 10px 40px rgba(0,0,0,0.1); /* Soft, pro-shadow */
}

.panel {
    flex: 1; /* Makes both panels take equal (50%) width */
    height: 100%;
    padding: 50px; /* Inner padding */
}


/* --- 3. Left Panel (Welcome) --- */
.welcome-panel {
    /* Updated: Complex Green Gradient from Emerald to Seafoam */
    background: linear-gradient(135deg, #2ecc71 0%, #1abc9c 50%, #27ae60 100%);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 1;
}

.content-wrapper {
    position: relative;
    z-index: 2; /* Keeps text above the graphic */
}

.welcome-title {
    font-size: 2.8rem;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.welcome-text {
    font-size: 1.1rem;
    line-height: 1.6;
    opacity: 0.85; /* Softens the description text */
}

/* This is where you would place the diagonal lines graphic */
.gradient-graphic {
    position: absolute;
    bottom: -30px;
    left: -30px;
    width: 100%;
    height: 100%;
    /* Use a similar image here */
    background-image: url('gradient_shapes.png'); 
    background-size: cover;
    opacity: 0.6;
    z-index: 1;
}


/* --- 4. Right Panel (Form) --- */
.form-panel {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 80px; /* Define form inset */
}

.form-title {
    color: #27ae60; /* Match the primary green */
    text-align: center;
    font-size: 1.1rem;
    letter-spacing: 1px;
    margin-bottom: 40px;
    font-weight: normal;
}

/* --- Input Group (Icon + Input) --- */
.input-group {
    position: relative;
    margin-bottom: 25px;
}

.input-group .icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%); /* Perfectly center icons vertically */
    color: #b98bf9;
    font-size: 1.2rem;
}

.input-group input {
    width: 100%;
    padding: 18px;
    padding-left: 55px; /* Give space for the icon */
    background-color: #f1e4ff; /* Light input background from image */
    border: none;
    border-radius: 50px; /* Make inputs rounded capsules */
    font-size: 1.05rem;
}

.input-group input::placeholder {
    color: #b98bf9;
    opacity: 0.8;
}

/* --- Form Options Row (Check + Forgot) --- */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    font-size: 0.9rem;
    color: #7f8c8d;
}

.forgot-link {
    text-decoration: none;
    color: #7f8c8d;
}

/* CUSTOM CHECKBOX (Matching the design) */
.checkbox-container {
    display: flex;
    align-items: center;
    cursor: pointer;
    position: relative;
    padding-left: 30px;
}

.checkbox-container input { opacity: 0; cursor: pointer; height: 0; width: 0; }

.checkmark {
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    height: 18px;
    width: 18px;
    background-color: white;
    border: 2px solid #a968fd; /* Border color */
    border-radius: 4px;
}

.checkbox-container input:checked ~ .checkmark {
    background-color: #a968fd; /* Fill color when checked */
}


/* --- The Login Button --- */
.btn-login {
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

.btn-login:hover {
    box-shadow: 0 6px 20px rgba(46, 204, 113, 0.6);
    transform: translateY(-2px); /* Button 'lifts up' on touch */
}
</style>
</head>
<body>
    <div class="page-background">
        <div class="login-container">
            
            <div class="panel welcome-panel">
                <div class="content-wrapper">
                    <h1 class="welcome-title">Welcome to 3aweny</h1>
                    <p class="welcome-text">Iwant to be a helper</p>
                </div>
                <div class="gradient-graphic"></div>
            </div>
            
            <div class="panel form-panel">
                <form action="" method="POST">
                    <h2 class="form-title">SIGNUP as helper</h2>

                    <div class="input-group">
                        <i class="fas fa-lock icon"></i>
                        <input type="text" name="name" placeholder="FullName" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-lock icon"></i>
                        <input type="tel" name="number" placeholder="PhoneNumber " minlength="10" maxlength="10"required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-lock icon"></i>
                        <input type="int" name="docID" placeholder="ID-Number" minlength="10" maxlength="10"required>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-user icon"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" name="passw" placeholder="Password" required>
                    </div>


                    
                    <div class="form-options">
                         <button type="submit"  class="btn-login">NEXT</button>
                
                </form>
            </div>
            
        </div> </div>
</body>
</html>