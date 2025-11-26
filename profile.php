<?php
session_start();

// If user is not logged in or session is invalid → redirect to login
// We check for 'is_logged_in' which is set in login.php
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require "db.php";

// Get user email from session (This variable is now set in the corrected login.php)
$userEmail = $_SESSION['user_email'];

// Fetch user details
$sql = "SELECT id, first_name, last_name, email, created_at FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close(); // Close connection after fetching data

// Safety check: if user data couldn't be fetched (e.g., database error), log out
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=session_invalid");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Luxe Jewelry</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Base styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f8f8; color: #333; }
        
        /* Layout and Typography */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
        h1, h2 { color: #1f2937; }
        
        /* Header */
        .header { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; }
        .logo h1 a { color: #333; text-decoration: none; font-size: 1.5rem; font-weight: 700; }
        
        .nav-menu { display: flex; list-style: none; gap: 2rem; }
        .nav-link { color: #1f2937; text-decoration: none; transition: color 0.2s; font-weight: 500; }
        .nav-link:hover { color:  #6b7280; }
        
        .nav-actions { display: flex; align-items: center; gap: 1.5rem; }
        .cart-icon { position: relative; color: #1f2937; font-size: 1.25rem; text-decoration: none; }
        .cart-count { position: absolute; top: -10px; right: -10px; background-color: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: 700; }
        .btn-logout { background-color: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background-color 0.2s; }
        .btn-logout:hover { background-color: #dc2626; }

        /* Profile Specific Styles */
        main { padding-top: 5rem !important; padding-bottom: 5rem !important; }
        main h1 { margin-bottom: 2rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }

        .profile-box {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            display: grid;
            gap: 2rem;
            grid-template-columns: 1fr;
        }

        .profile-info h2 {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            color: #4b5563;
        }

        .profile-info p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-left: 4px solid #6366f1;
            background-color: #eff6ff;
            border-radius: 4px;
        }

        .profile-info strong {
            color: #1f2937;
            display: inline-block;
            min-width: 100px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <nav class="nav">

                <div class="logo">
                    <h1><a href="index.php">Luxe Jewelry</a></h1>
                </div>

                <ul class="nav-menu">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="products.php" class="nav-link">Products</a></li>
                    <li><a href="cart.php" class="nav-link">Cart</a></li>
                    <li><a href="index.php#contact" class="nav-link">Contact</a></li>
                </ul>

                <div class="nav-actions">
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count" id="cartCount">0</span>
                    </a>

                    <a href="edit_profile.php" class="btn btn-primary">
                        <i class="fas fa-edit" ></i> Edit Profile</a>


                            <!-- Delete Account Form (Using POST method for security) -->
                             <form method="POST" action="delete_account.php" onsubmit="return confirm('WARNING: Are you absolutely sure you want to permanently delete your account? This action cannot be undone.');">
                                <button type="submit" class="btn"  color: #1f2937 ; border: none;">
                                    <i class="fa fa-trash" aria-hidden="true"></i>Delete Account 
                                </button>
                            </form>
                        </div>
                    </nav>
                </div>
            </header>

    <!-- MAIN PROFILE SECTION -->
    <main class="container">
        <h1>Your Profile</h1>

        <div class="profile-box">

            <div class="profile-info">
                <h2>Account Details</h2>

                <!-- CORRECTED: Combined first_name and last_name -->
                <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                
                <!-- This was already correct but now relies on DB fetch -->
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                
                <!-- This assumes 'created_at' column exists in your users table -->
                <p><strong>Joined On:</strong> <?= date("F j, Y", strtotime($user['created_at'])) ?></p>
            </div>

            <!-- You can add more sections here, like Order History or Change Password form -->

        </div>
    </main>

    <!-- Placeholder for JavaScript logic -->
    <script>
        // Simple mock function for cart count since 'script.js' is not provided
        function updateCartCount() {
            const countElement = document.getElementById('cartCount');
            // In a real app, this would fetch the actual count from the server
            // For now, let's keep it at 0
            countElement.textContent = 0; 
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>

</body>
</html>