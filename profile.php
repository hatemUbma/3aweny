<?php
session_start();
include 'confi.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

// Fetch the specific fields you requested
$sql = "SELECT fullName, email, userRole, phone, docID FROM user WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// NEW LOGIC: If the user is a helper, fetch their service offers
$offers = null;
if ($user['userRole'] === 'helper') {
    $stmtOffers = $conn->prepare("SELECT helpID, category, price, location FROM help WHERE userID = ?");
    $stmtOffers->bind_param("i", $userID);
    $stmtOffers->execute();
    $offers = $stmtOffers->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - 3aweny</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #5a8f72;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Changed from height to min-height to allow scrolling */
            padding: 40px 0; /* Added padding so it doesn't stick to the top/bottom */
            box-sizing: border-box;
        }
        .profile-container {
            background: white;
            width: 500px; /* Widened slightly to fit the service cards nicely */
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .profile-content {
            padding: 30px;
        }
        .info-row {
            margin-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .info-label {
            color: #27ae60;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-value {
            color: #333;
            font-size: 16px;
            margin-top: 5px;
        }
        .role-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }

        /* --- DASHBOARD STYLES FOR HELPERS --- */
        .services-container {
            display: grid;
            gap: 15px;
            margin-top: 15px;
        }
        .add-card {
            border: 2px dashed #27ae60;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #27ae60;
            transition: 0.3s;
            font-weight: bold;
            display: block;
        }
        .add-card:hover { 
            background: #e8f5e9; 
        }
        .service-item-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 5px solid #27ae60;
        }
        .service-item-card p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .delete-icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #e74c3c;
            transition: transform 0.2s;
        }
        .delete-icon-btn:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-header">
        <h2><?php echo htmlspecialchars($user['fullName']); ?></h2>
        <div class="role-badge"><?php echo ucfirst($user['userRole']); ?></div>
    </div>

    <div class="profile-content">
        <div class="info-row">
            <div class="info-label">Email Address</div>
            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Phone Number</div>
            <div class="info-value"><?php echo htmlspecialchars($user['phone']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Document ID (num of doc)</div>
            <div class="info-value"><?php echo htmlspecialchars($user['docID']); ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Account Status</div>
            <div class="info-value">Active</div>
        </div>

        <?php if ($user['userRole'] === 'helper'): ?>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
            <h3 style="color: #27ae60; margin: 0 0 15px 0;">Manage My Services</h3>
            
            <div class="services-container">
                <a href="add_service.php" class="add-card">+ Add New Offer</a>

                <?php if ($offers && $offers->num_rows > 0): ?>
                    <?php while($row = $offers->fetch_assoc()): ?>
                        <div class="service-item-card">
                            <div>
                                <strong style="color: #333;"><?php echo htmlspecialchars($row['category']); ?></strong>
                                <p><?php echo htmlspecialchars($row['price']); ?> DA/hour - <?php echo htmlspecialchars($row['location']); ?></p>
                            </div>
                            
                            <form action="delete_service.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this offer?');" style="margin: 0;">
                                <input type="hidden" name="helpID" value="<?php echo $row['helpID']; ?>">
                                <button type="submit" class="delete-icon-btn" title="Delete Offer">🗑️</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #7f8c8d; font-size: 14px; margin-top: 10px;">You haven't posted any services yet.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>