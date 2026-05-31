<?php
session_start();
include 'confi.php';

// Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$myID = $_SESSION['user_id'];

// --- Handle Form Actions ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. HELPER ACTIONS: Accept or Refuse
    if (isset($_POST['accept_request']) || isset($_POST['refuse_request'])) {
        $reqID = $_POST['requestID'];
        $statusUpdate = isset($_POST['accept_request']) ? 'accepted' : 'rejected';

        $updateSql = "UPDATE service_requests SET status = ? WHERE requestID = ? AND helperID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sii", $statusUpdate, $reqID, $myID);
        $updateStmt->execute();
    }

    // 2. NEEDER ACTION: Delete/Cancel Request
    if (isset($_POST['delete_request'])) {
        $reqID = $_POST['requestID'];
        
        // Security: Ensure only the person who SENT the request (neederID) can delete it
        $deleteSql = "DELETE FROM service_requests WHERE requestID = ? AND neederID = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $reqID, $myID);
        
        if ($deleteStmt->execute()) {
            header("Location: requests.php");
            exit();
        }
    }
}

// --- Fetch Data ---
// 1. Requests RECEIVED (User is the Helper)
$sql_received = "SELECT r.requestID, r.request_date, r.status, u.fullName, u.email, u.phone 
                FROM service_requests r
                JOIN user u ON r.neederID = u.userID
                WHERE r.helperID = ?
                ORDER BY r.request_date DESC";
$stmt1 = $conn->prepare($sql_received);
$stmt1->bind_param("i", $myID);
$stmt1->execute();
$received_result = $stmt1->get_result();

// 2. Requests SENT (User is the Needer)
$sql_sent = "SELECT r.requestID, r.request_date, r.status, u.fullName, u.email, u.phone 
             FROM service_requests r
             JOIN user u ON r.helperID = u.userID
             WHERE r.neederID = ?
             ORDER BY r.request_date DESC";
$stmt2 = $conn->prepare($sql_sent);
$stmt2->bind_param("i", $myID);
$stmt2->execute();
$sent_result = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests - 3aweny</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #5a8f72; /* Your dark green theme */
            margin: 0;
            display: flex;
            justify-content: center;
            padding: 50px 0;
            min-height: 100vh;
        }
        .requests-container { width: 90%; max-width: 650px; }
        
        h2.section-title { 
            color: white; 
            border-bottom: 2px solid rgba(255,255,255,0.2); 
            padding-bottom: 10px; 
            margin: 40px 0 20px 0;
        }

        .request-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            position: relative;
        }

        .card-received { border-left: 6px solid #2ecc71; }
        .card-sent { border-left: 6px solid #3498db; }

        .request-header { display: flex; justify-content: space-between; align-items: center; }
        
        .status-badge {
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 10px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }

        .btn-group { display: flex; gap: 10px; margin-top: 15px; }
        
        .accept-btn {
            background: linear-gradient(to right, #2ecc71, #1abc9c);
            color: white; border: none; padding: 10px 20px;
            border-radius: 25px; font-weight: bold; cursor: pointer;
        }
        
        .refuse-btn {
            background-color: #f39c12;
            color: white; border: none; padding: 10px 20px;
            border-radius: 25px; font-weight: bold; cursor: pointer;
        }

        .delete-btn {
            background-color: #e74c3c; /* Red for deletion */
            color: white; border: none; padding: 10px 20px;
            border-radius: 25px; font-weight: bold; cursor: pointer;
            transition: 0.3s;
        }
        .delete-btn:hover { background-color: #c0392b; }

        .info-group { margin-bottom: 8px; font-size: 0.95rem; color: #555; }
        .info-label { color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>

<div class="requests-container">
    
    <!-- 1. INCOMING REQUESTS SECTION -->
    <h2 class="section-title">Requests Received (Help Others)</h2>

    <?php if ($received_result->num_rows > 0): ?>
        <?php while($row = $received_result->fetch_assoc()): ?>
            <div class="request-card card-received">
                <div class="request-header">
                    <h3 style="margin:0;"><?php echo htmlspecialchars($row['fullName']); ?></h3>
                    <span class="status-badge status-<?php echo $row['status']; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                </div>
                <p class="info-group"><span class="info-label">Email:</span> <?php echo htmlspecialchars($row['email']); ?></p>
                <p class="info-group"><span class="info-label">Phone:</span> <?php echo htmlspecialchars($row['phone']); ?></p>

                <?php if ($row['status'] == 'pending'): ?>
                    <form method="POST" class="btn-group">
                        <input type="hidden" name="requestID" value="<?php echo $row['requestID']; ?>">
                        <button type="submit" name="accept_request" class="accept-btn">ACCEPT</button>
                        <button type="submit" name="refuse_request" class="refuse-btn">REFUSE</button>
                    </form>
                <?php else: ?>
                    <p style="margin-top: 15px; font-weight: bold; color: #777;">Status: <?php echo ucfirst($row['status']); ?></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color: white; opacity: 0.8;">No incoming requests.</p>
    <?php endif; ?>


    <!-- 2. SENT REQUESTS SECTION -->
    <h2 class="section-title">My Sent Requests (Status)</h2>

    <?php if ($sent_result->num_rows > 0): ?>
        <?php while($row = $sent_result->fetch_assoc()): ?>
            <div class="request-card card-sent">
                <div class="request-header">
                    <h3 style="margin:0;">Helper: <?php echo htmlspecialchars($row['fullName']); ?></h3>
                    <span class="status-badge status-<?php echo $row['status']; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                </div>
                <p style="margin-top: 10px; color: #555;">Sent on: <b><?php echo date('M d, Y', strtotime($row['request_date'])); ?></b></p>
                
                <?php if ($row['status'] == 'accepted'): ?>
                    <p class="info-group" style="background: #e8f5e9; padding: 10px; border-radius: 8px;">
                        <span class="info-label">Contact Helper:</span> <?php echo htmlspecialchars($row['phone']); ?>
                    </p>
                <?php endif; ?>

                <!-- NEEDER DELETE BUTTON -->
                <form method="POST" style="margin-top: 15px;">
                    <input type="hidden" name="requestID" value="<?php echo $row['requestID']; ?>">
                    <button type="submit" name="delete_request" class="delete-btn" onclick="return confirm('Are you sure you want to delete/cancel this request?')">
                        <?php echo ($row['status'] == 'pending') ? 'CANCEL REQUEST' : 'DELETE HISTORY'; ?>
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color: white; opacity: 0.8;">You haven't sent any requests yet.</p>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 30px;">
        <a href="logedinPage.php" style="color: white; text-decoration: none; font-weight: bold;">← Back to Dashboard</a>
    </div>

</div>

</body>
</html>