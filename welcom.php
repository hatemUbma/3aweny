<?php
session_start();
include 'confi.php';

// Only run if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Use the ?? operator to provide a fallback if a field is missing
    $fullName = $_POST['name'] ?? '';
    $email    = $_POST['email'] ?? '';
    $number   = $_POST['number'] ?? '';
    $docID    = $_POST['docID'] ?? '';
    $passw    = $_POST['passw'] ?? '';

    // Check if the name is empty before trying to save
    if (!empty($fullName)) {
        $sql = "INSERT INTO user (fullName, email, phone, docID, passw) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        // s = string, i = integer. Adjust based on your DB types.
        mysqli_stmt_bind_param($stmt, "ssiss", $fullName, $email, $number, $docID, $passw);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>3aweny - Welcome page</title>
    
    
    <style>
             body {
font-family: sans-serif;
margin: 0;
padding: 0;
background-color: #cdcdff;
color: #333;
line-height: 1.4;
}

header {
background-color: #13b651;
color: #333;
padding: 1rem 2rem;
display: flex;
justify-content: space-between;
align-items: center;
box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.logo a{
color: inherit;
text-decoration: none;

}

.logo {
font-size: 1.5rem;
font-weight: bold;
color: beige;
align-items: flex-start;
align-content: flex-start;

}


nav ul {
list-style: none;
padding: 0;
margin: 0;
display: flex;

flex-direction: row;
}

nav ul li {
margin-left: 1.5rem;
}

nav ul li a {
text-decoration: none;
color: beige;
transition: color 0.3s ease;
}

nav ul li a:hover {
color: #2ecc71;
}

.login-icon {
font-size: 0.8rem;
}

.user-icon img {
width: 24px;
height: 24px;
border-radius: 50%;
vertical-align: middle;
}

.cnt1 {
display: flex;           /* Enables Flexbox */
  flex-direction: column;  /* Stacks items top-to-bottom */
  
  justify-content: center; /* Centers vertically (because direction is column) */
  align-items: center;     /* Centers horizontally */
  
  min-height: 50vh;       /* CRITICAL: Makes the container the full height of the screen */
  text-align: center;    
}

.but1{
display: flex;
flex-direction: column;
align-items: center;
gap: 20px;
width: 250px; 
  
  /* 2. Add padding to make them taller and "roomier" */
  padding: 20px; 
  
  /* 3. Increase font size */
  font-size: 18px;
  font-weight: bold;
}

.btn-needy{
  padding: 20px; 
  font-size: 18px;
  font-weight: bold;
  border-radius: 10px;
  border: 2px solid #333;
  background-color: white;
  cursor: pointer;
}

.btn-helper{
  padding: 20px; 
  font-size: 18px;
  font-weight: bold;
  border-radius: 10px;
  border: 2px solid #333;
  background-color: white;
  cursor: pointer;
}

.title{
    display: flex;
    justify-content: center;
    align-items:center;
}

.sub-title{
    display: flex;
    justify-content: center;
    align-items:center;
}









body {
    background-color: #f9fbfd; /* Light gray background like standard web apps */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
 
}

.main-container {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    
}

.landing-title { color: #2c3e50; font-size: 2.5rem; margin-bottom: 0.5rem; }
.landing-subtitle { color: #7f8c8d; font-size: 1.1rem; margin-bottom: 3rem; }

/* --- THE GRID LAYOUT (for .pic) --- */
.pic {
    display: flex;         /* Enables Flexbox */
    flex-wrap: wrap;       /* CRITICAL: Allows items to move to the next line */
    justify-content: center; /* Centers the boxes horizontally */
    gap: 25px;             /* Space between the boxes (vertical and horizontal) */
    padding: 20px;
    max-width: 1200px;     /* Optional: Keeps the grid from getting too wide */
    margin: 0 auto;        /* Centers the whole container on the page */
}

.pic a {
    text-decoration: none;
    color: inherit;
    /* This tells each link to take up roughly 30% of the width on desktop, 
       but allows it to grow or shrink as needed */
    flex: 1 1 300px;       
    max-width: 350px;      /* Prevents cards from becoming too wide on large screens */
    display: flex;
}

/* --- THE SERVICE BOXES --- */
.service-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08); /* Soft shadow for 'card' look */
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    width: 300px; /* Fixed width for the cards */
}

/* Interactivity: Slight hover effect to make it feel responsive */
.service-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Style the images so they fill the card */
.service-box img {
    width: 100%;
    height: 180px; /* Fixed height so all images match */
    object-fit: cover; /* This prevents the image from squishing */
    border-radius: 8px; /* Rounded corners for the image itself */
    margin-bottom: 15px;
}

/* Style the titles and descriptions */
.service-box h3 { color: #27ae60; margin: 10px 0 5px 0; }
.service-box p { color: #7f8c8d; font-size: 0.95rem; margin-top: 0; padding: 0 5px; }


/* --- THE TRICK TO CREATE THE 3x2 APPEARANCE --- */
/* On desktops (screens wider than 960px), we define the 3-column grid explicitly
   and center the bottom 2 elements. */
@media (min-width: 960px) {
    .pic {
        grid-template-columns: repeat(3, 1fr); /* Force 3 columns */
        justify-content: center; /* Center the entire grid */
    }

    /* Target the last two service boxes (4 and 5) */
    .service-box:nth-child(4) {
        grid-column: 1 / 2; /* Position card 4 normally in col 1 */
        justify-self: end;  /* Align it toward the center */
        margin-right: -45px; /* Adjust spacing manually to center the 2nd row */
    }

    .service-box:nth-child(5) {
        grid-column: 2 / 3; /* Position card 5 in col 2 (under card 2) */
        justify-self: start; /* Align it toward the center */
        margin-left: 45px;  /* Adjust spacing manually */
    }
}

footer {
background-color: #13b651;
padding: 2rem;
text-align: center;
}

.footer-links {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
gap: 1rem;
margin-bottom: 1.5rem;
text-align: left;
}

.footer-links h3 {
font-size: 1rem;
margin-bottom: 0.7rem;
color: #eee;
display: flex;
justify-content: center;
}

.footer-links ul {
list-style: none;
padding: 0;
margin: 0;
display: flex;
align-items: center;
justify-content: center;
flex-direction: row;
gap: 40px;

}

.footer-links ul li {
margin-bottom: 0.5rem;
}

.footer-links ul li a {
text-decoration: none;
color: #ccc;
font-size: 0.9rem;
transition: color 0.3s ease;
}

.footer-links ul li a:hover {
color: #fff;
}

footer p {
font-size: 0.8rem;
color: #e0e0e0;
margin-top: 1rem;
}

.space{ min-height: 10vh; 

}
    </style>
     
</head>
<body>
    <div class="app-container">
      
        <header>
             <div class="logo"><a href="welcom.php">3aweny</a></div>
        <nav>
            <ul>
                <li><a href="">Help</a></li>
                <li type="button"><a href="signup.php">SignUp</a></li>
                <li type="button"><a href="login.php" >Login</a>
                    <a href="" class="user-icon">
                <img src="pic/user.jpg" alt="User"></a></li>
            </ul>
        </nav>
        </header>

        <h2 class="title">3aweny — Put Your Skills to Work, Help Your Community.</h1>
        <h4 class="sub-title">Find local tasks near you. Whether you need a hand with house chores or want to earn by helping others, 3aweny connects you instantly.</h4>
       


        <div class="main-container">
    <h1 class="landing-title">Our Services</h1>
    <p class="landing-subtitle">How can you help, or how do you need help today?</p>

    <div class="pic">
        <a href="login.php">
            <div class="service-box">
                <img src="pic/physical.jpg" alt="Physical labor service">
                <h3>Physical Effort</h3>
                 <p>Moving, lifting, gardening, or manual labor assistance.</p>
            </div>
        </a>

        <a href="login.php">
            <div class="service-box">
                 <img src="pic/babysitting.jpg" alt="Babysitting service">
                <h3>Babysitting</h3>
                 <p>Trusted care for your children when you are busy.</p>
             </div>
        </a>

        <a href="login.php">
            <div class="service-box">
                <img src="pic/clean.jpg" alt="Cleaning service">
                <h3>House Cleaning</h3>
                <p>Chores, laundry, and deep cleaning for your home.</p>
            </div>
        </a>

        <a href="login.php">
            <div class="service-box">
                 <img src="pic/patientcare.jpg" alt="Patient care service">
                <h3>Patient Care</h3>
                <p>Assistance and support for sick or elderly neighbors.</p>
            </div>
        </a>

        <a href="login.php">
            <div class="service-box">
                <img src="pic/animalcare.jpg" alt="Animal care service">
                <h3>Pet Care</h3>
                <p>Dog walking, feeding, and sitting for your animals.</p>
            </div>
        </a>
    </div>

    </div> </div>




         
        <div class="space"></div>
</body>

<footer>
<div class="footer-links">
            <div class="company">
                <h3>Company</h3>
                <ul>
                    <li><a href="#">Who are we?</a></li>
                    <li><a href="#">Affiliation / Contact us</a></li>
                    <li><a href="#">Terms of use</a></li>
                    <li><a href="#">Privacy policy</a></li>
                </ul>
            </div>
            
        </div>
        <p>&copy; 2025 3aweny. All rights reserved.</p>
</footer>
</html> 