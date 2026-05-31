<?php
session_start();
include 'confi.php';

// Get category from URL and sanitize
$category = isset($_GET['category']) ? $_GET['category'] : 'General';

// 1. Use a Prepared Statement for security
$stmt = $conn->prepare("SELECT user.userID, user.fullName, help.picture, help.location, help.bio, help.price, help.wilaya
                        FROM help
                        INNER JOIN user ON help.userID = user.userID
                        WHERE help.category = ?");

// 2. Bind the parameter (s = string)
$stmt->bind_param("s", $category);

// 3. Execute and get result
$stmt->execute();
$result = $stmt->get_result();
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


.container {
    width: 100%;
    max-width: 1100px;
    
}

.category-title {
    text-align: center;
    color: #333;
    margin-bottom: 40px;
    font-size: 2rem;
}

.category-title span {
    color: #28a745; /* The Green color from your 3aweny logo */
}

/* The Grid for Helpers */
.helper-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 25px;
}

/* Individual Helper Card */
.helper-card {
    background: #fff;
    border-radius: 15px;
    padding: 30px; /* Increased padding for a better look */
    display: flex;
    flex-direction: column;
    justify-self:center;
    align-items: center;
    text-align: center;
    
    /* This makes it slim */
    width: 100%;
    max-width: 400px; 
    
    /* This centers it if there is only one card */
   
    
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #eee;
    
}

.helper-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

/* Profile Picture Styling */
.profile-img img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #28a745;
    margin-bottom: 15px;
}

/* Text Content */
.helper-info h3 {
    margin: 10px 0 5px 0;
    font-size: 1.4rem;
    color: #222;
}

.location {
    color: #777;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.bio {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.5;
    height: 60px; /* Limits height to keep cards equal */
    overflow: hidden;
    margin-bottom: 20px;
}

.price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 20px;
}

/* Button Styling */
.hire-btn {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 30px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: background 0.3s;
}

.hire-btn:hover {
    background-color: #218838;
}
.r{
    text-align: center;
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 20px;
}
    </style>
    <title>3aweny - Available Helpers</title>
</head>
<body>
    <header>
             <div class="logo"><a href="welcom.php">3aweny</a></div>
        <nav>
            <ul>
                <li><a href="">Help</a></li>
                <li type="button"><a href="welcom.php">log out</a></li>
                <li type="button"><a href="login.php" ></a>
                    <a href="pic/user.jpg" class="user-icon">
                <img src="pic/user.jpg" alt="User"></a></li>
            </ul>
        </nav>
        
    </header>

<div class="container">
    <h2 class="category-title">Available Helpers for: <span><?php echo $category; ?></span></h2>
    
    <div class="helper-grid">
        <?php
        if (mysqli_num_rows($result) > 0) {
            // 4. Output each row of data into a card form
            while($row = mysqli_fetch_assoc($result)) {
    // Convert binary data to base64
    $imageData = base64_encode($row['picture']);
    // Format the source string
    $src = 'data:image/jpeg;base64,' . $imageData;

    echo '
    <div class="helper-card">
        <div class="profile-img">
            <img src="' . $src . '" alt="Profile">
        </div>
        <div class="helper-info">
            <h3>' . htmlspecialchars($row['fullName']) . '</h3>
            <p class="wilaya">📍 ' . htmlspecialchars($row['wilaya']) . '</p>
            <p class="location">📍 ' . htmlspecialchars($row['location']) . '</p>
            <p class="bio">' . htmlspecialchars($row['bio']) . '</p>
            <div class="price">' . htmlspecialchars($row['price']) . ' DA / hour</div>
            <button class="hire-btn" 
                data-helperid="' . $row['userID'] . '" 
                onclick="sendServiceRequest(this)">
            Request Service
        </button>
    </div>
</div>';


        
    
}
        } else {
            echo '<p class="r">No helpers found in this category yet.</p>';
        }
        ?>
    </div>
</div>



<script>
function sendServiceRequest(btn) {
    // This pulls the ID from the data-helperid we added above
    const helperId = btn.getAttribute('data-helperid');

    // Check if the user is logged in using PHP session
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    
    if (!isLoggedIn) {
        alert("Please login first to hire a helper!");
        window.location.href = 'login.php';
        return;
    }

    // Send data to the background PHP file you created (insert_request.php)
    fetch('insert_request.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'hID=' + helperId
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            // Change the look of your hire-btn specifically
            btn.innerText = "Request Valid ✓";
            btn.style.backgroundColor = "#28a745"; // Success green
            btn.style.color = "white";
            btn.disabled = true; // Stop user from clicking multiple times
        } else {
            alert("Error: " + data);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
</body>
</html>

