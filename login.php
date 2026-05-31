<?php
session_start();
include 'confi.php';
$msg = "";
$msg_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['passw'];
    $sql = "SELECT userID, fullName, passw FROM user WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if ($password === $user['passw']) {
                $_SESSION['user_id'] = $user['userID'];
                $_SESSION['user_name'] = $user['fullName'];
                
                header("Location: logedinPage.php"); // Redirect to your main page
                exit();
            } else {
              $msg = "Error: Something went wrong!";
              $msg_class = "error-bar";
            }
        } else {
         $msg = "ERROR! check your email,password.";
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3aweny - LOG IN</title>
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
.error-bar {
   background-color: #ffebee; /* Soft red bg */
    color: #c62828;            /* Sharp red text */
    padding: 12px;
    border-radius: 25px;       /* Match your input style */
    border: 1px solid #ef9a9a;
    margin-bottom: 20px;
    text-align: center;
    font-size: 0.9rem;
    width: 100%;
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
    flex-direction: column; /* This stacks everything vertically */
    justify-content: center;
    align-items: center;
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
                    <p class="welcome-text">
                        3aweny is a dedicated platform designed to bridge the gap between people in need and those ready to help. Whether it is home cleaning, patient care, or manual labor, our mission is to foster a spirit of mutual aid. We make sure that help is always just around the corner.
                    </p>
                </div>
                <div class="gradient-graphic"></div>
            </div>
            
            <div class="panel form-panel">
                
                 <?php if (!empty($msg)): ?>
                     <div class="error-bar">
                       <?php echo $msg; ?>
                     </div>
                 <?php endif; ?>

                <form action="login.php" method="POST">
                    <h2 class="form-title">USER LOGIN</h2>
                    
                    <div class="input-group">
                        <i class="fas fa-user icon"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" name="passw" placeholder="Password" required>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember">
                            Remember
                            <span class="checkmark"></span>
                        </label>
                        <a href="forgot_password.html" class="forgot-link">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login">LOGIN</button>
                </form>
                <a href="signup.php" class="forgot-link">no account signup</a>
            
            </div>
                    
        </div> </div>
</body>
</html>

<script>
    setTimeout(function() {
        let bar = document.querySelector('.error-bar');
        if(bar) {
            bar.style.transition = "opacity 0.5s ease";
            bar.style.opacity = "0";
            setTimeout(() => bar.remove(), 500);
        }
    }, 4000);
</script>
