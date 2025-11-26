<?php
// Start the session at the very beginning of the PHP script
session_start();

// 1. Include the database connection file
require_once 'db.php'; // IMPORTANT: This makes the $conn object available

// Check if the user is already logged in. If so, redirect to the home page.
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

$loginError = '';

/**
 * Fetches user data (id, first_name, email, and password_hash) from the database
 * based on the provided email using prepared statements.
 */
function getUserDataByEmail($conn, $email) {
    // Use prepared statements for security
    $stmt = $conn->prepare("SELECT id, first_name, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    $stmt->close();
    
    if ($userData) {
        // Map DB columns to session keys for consistency
        return [
            'id' => $userData['id'],
            'username' => $userData['first_name'], // Using first_name as the session username
            'password_hash' => $userData['password_hash'],
            'email' => $userData['email']
        ];
    }
    
    return null;
}

// 2. Handle form submission (when the user clicks "Sign In")
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($email) || empty($password)) {
        $loginError = "Please enter both email and password.";
    } else {
        // Fetch user data from the real database
        $userData = getUserDataByEmail($conn, $email);

        // Verify password using password_verify() against the hash fetched from the DB
        if ($userData && password_verify($password, $userData['password_hash'])) {
            // Authentication SUCCESS!

            // 3. Create Session Variables
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['user_email'] = $userData['email'];
            $_SESSION['is_logged_in'] = true;

            // 4. Redirect to the home page (index.php)
            header('Location: index.php');
            exit();
        } else {
            // Authentication FAILED (Invalid credentials)
            // Use a generic error message for security
            $loginError = "Invalid email or password.";
        }
    }
}

// 5. Close the database connection when the script finishes its work
$conn->close();

// Check for success message after sign up redirection
if (isset($_GET['signup']) && $_GET['signup'] === 'success') {
    $loginError = "Account created successfully! Please sign in.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Luxe Jewelry</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            padding: 2rem;
        }
        
        .auth-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .auth-header h1 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-input.error {
            border-color: #ef4444;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        /* Styling for the server-side error message block */
        .server-error {
            background-color: #fee2e2;
            color: #ef4444;
            border: 1px solid #fca5a5;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            text-align: center;
        }
        
        .auth-btn {
            width: 100%;
            padding: 14px;
            background: #1f2937;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            margin-bottom: 1.5rem;
        }
        .auth-btn:hover {
            transform: translateY(-2px);
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-to-home {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background:#1f2937;
            padding: 8px 16px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <a href="index.php" class="back-to-home">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>
        
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>
            
            <!-- PHP ERROR/SUCCESS MESSAGE DISPLAY -->
            <?php if ($loginError): ?>
                <div class="server-error"><?php echo htmlspecialchars($loginError); ?></div>
            <?php endif; ?>
            
            <!-- FORM ACTION POINTS TO ITSELF FOR PROCESSING -->
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <!-- Set the value if the user submitted it, so they don't lose it on error -->
                    <input type="email" name="email" id="email" class="form-input" required placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <div class="error-message" id="emailError"></div>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <!-- Password should never be pre-filled on error -->
                    <input type="password" name="password" id="password" class="form-input" required placeholder="Enter your password">
                    <div class="error-message" id="passwordError"></div>
                </div>
                
                <button type="submit" class="auth-btn">Sign In</button>
                
                <div class="auth-links">
                    <p>Don't have an account? <a href="signup.php">Create one here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>