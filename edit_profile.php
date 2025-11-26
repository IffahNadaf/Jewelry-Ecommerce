<?php
// CRITICAL: Start the session first
session_start();

// 1. Authentication Check
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$userId = $_SESSION['user_id'] ?? null;
$statusMessage = '';
$isError = false;

// Redirect if User ID is missing (shouldn't happen if auth passes)
if (!$userId) {
    header("Location: login.php?error=no_user_id");
    exit();
}

// --- INITIAL FETCH (To populate the form) ---
$sqlFetch = "SELECT first_name, last_name, email FROM users WHERE id = ?";
$stmtFetch = $conn->prepare($sqlFetch);
$stmtFetch->bind_param("i", $userId);
$stmtFetch->execute();
$result = $stmtFetch->get_result();
$user = $result->fetch_assoc();
$stmtFetch->close();

if (!$user) {
    // Should only happen if user was deleted externally
    header("Location: logout.php"); 
    exit();
}

// --- HANDLE FORM SUBMISSION (UPDATE Logic) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get and Sanitize Input
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    // 2. Simple Validation (More robust validation can be added)
    if (empty($firstName) || empty($lastName) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statusMessage = "Please fill out all required fields with a valid email address.";
        $isError = true;
    } else {
        $updateFields = [];
        $bindTypes = '';
        $bindValues = [];

        // Check if name changed
        if ($firstName !== $user['first_name']) {
            $updateFields[] = "first_name = ?";
            $bindTypes .= 's';
            $bindValues[] = $firstName;
        }
        if ($lastName !== $user['last_name']) {
            $updateFields[] = "last_name = ?";
            $bindTypes .= 's';
            $bindValues[] = $lastName;
        }

        // Check if email changed AND if the new email is unique (crucial security step)
        if ($email !== $user['email']) {
            // Check for existing email address
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkStmt->bind_param("si", $email, $userId);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                $statusMessage = "Error: That email address is already in use by another account.";
                $isError = true;
            }
            $checkStmt->close();

            if (!$isError) {
                $updateFields[] = "email = ?";
                $bindTypes .= 's';
                $bindValues[] = $email;
                // If email changes, the session email must be updated later
            }
        }
        
        // Handle password change
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                 $statusMessage = "Error: Password must be at least 6 characters long.";
                 $isError = true;
            } else {
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateFields[] = "password_hash = ?";
                $bindTypes .= 's';
                $bindValues[] = $passwordHash;
            }
        }

        // --- Database Update Execution ---
        if (!$isError && !empty($updateFields)) {
            $sqlUpdate = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $bindTypes .= 'i';
            $bindValues[] = $userId;
            
            $stmtUpdate = $conn->prepare($sqlUpdate);
            
            // Dynamically bind parameters
            $stmtUpdate->bind_param($bindTypes, ...$bindValues);
            
            if ($stmtUpdate->execute()) {
                $statusMessage = "Profile updated successfully!";
                $isError = false;
                
                // Update the session variables if email or name changed
                $_SESSION['username'] = $firstName;
                if ($email !== $user['email']) {
                    $_SESSION['user_email'] = $email;
                }
                
                // Re-fetch the user data to show the new values in the form instantly
                $user['first_name'] = $firstName;
                $user['last_name'] = $lastName;
                $user['email'] = $email;

            } else {
                $statusMessage = "Database Update Error: Could not save changes.";
                $isError = true;
                error_log("DB Update Error: " . $stmtUpdate->error);
            }
            $stmtUpdate->close();
        } elseif (!$isError) {
            $statusMessage = "No changes detected.";
            $isError = false;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Luxe Jewelry</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
             font-family: 'Inter',
              sans-serif; 
              background-color: #f3f4f6; 
              display: flex; 
              justify-content: center;
               align-items: center; 
               min-height: 100vh;
               padding: 20px;}

        .container { 
            max-width: 500px;
             width: 100%; 
             background: white;
             padding: 30px;
             border-radius: 12px;
             box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }

        h1 { 
            color: #1f2937;
            margin-bottom: 20px;
            text-align: center; }

        .form-group {
            margin-bottom: 1.5rem; }

        .form-label { 
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 600; }

        .form-input { 
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem; 
            transition: border-color 0.3s; }

        .form-input:focus {
            outline: none;
            border-color: #161d3bff; }

        .btn { 
            padding: 12px 20px; 
            border-radius: 8px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: background-color 0.3s, box-shadow 0.3s; 
            width: 100%; 
            text-decoration: none;
            /* FIX: Use flexbox for centering text and icon */
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-submit { 
            background-color: #1f2937;
            color: white;
            border: none;
            margin-top: 10px; }

        .btn-submit:hover {
            background-color: #374151; }

        .btn-back { 
            background-color: #1f2937; /* Dark background */
            color: white; /* White text */
            border: none; /* Removed border */
            margin-top: 10px;
            text-decoration: underline; /* Added underline as per the image */
        }
            
        .status-message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; font-weight: 500; }
        .status-success { background-color: #d1fae5; color: #059669; border: 1px solid #34d399; }
        .status-error { background-color: #fee2e2; color: #ef4444; border: 1px solid #fca5a5; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Your Profile</h1>

        <?php if ($statusMessage): ?>
            <div class="status-message <?php echo $isError ? 'status-error' : 'status-success'; ?>">
                <?php echo htmlspecialchars($statusMessage); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit_profile.php">
            
            <div class="form-group">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-input" required 
                       value="<?php echo htmlspecialchars($user['first_name']); ?>">
            </div>

            <div class="form-group">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-input" required 
                       value="<?php echo htmlspecialchars($user['last_name']); ?>">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-input" required 
                       value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">New Password (Leave blank to keep current)</label>
                <input type="password" name="new_password" id="new_password" class="form-input" placeholder="Enter new password">
            </div>

            <button type="submit" class="btn btn-submit">Save Changes</button>
            <a href="profile.php" class="btn btn-back" >
                <i class="fas fa-arrow-left"></i> Cancel and Go Back</a>
        </form>
    </div>
</body>
</html>