<?php
session_start();

// 1. Include the database connection file
require_once 'db.php'; // IMPORTANT: This makes the $conn object available

// Check if the user is already logged in.
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

$signupError = '';
$formData = []; 

/**
 * Checks if a given email address already exists in the 'users' table.
 */
function emailExists($conn, $email) {
    // Use prepared statements for security against SQL injection
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

/**
 * Inserts a new user into the 'users' table with a hashed password.
 */
function saveNewUser($conn, $userData) {
    // Hash the password securely
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_number, password_hash) VALUES (?, ?, ?, ?, ?)");
    
    // Bind parameters: sssss (5 strings)
    $stmt->bind_param("sssss", 
        $userData['firstName'], 
        $userData['lastName'], 
        $userData['email'], 
        $userData['phone'], 
        $hashedPassword
    );
    
    $success = $stmt->execute();
    $stmt->close();
    
    return $success;
}


// 2. Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize all POST data
    $formData['firstName'] = trim($_POST['firstName'] ?? '');
    $formData['lastName'] = trim($_POST['lastName'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['phone'] = trim($_POST['phone'] ?? '');
    $formData['password'] = $_POST['password'] ?? '';
    $formData['confirmPassword'] = $_POST['confirmPassword'] ?? '';
    $formData['agreeTerms'] = isset($_POST['agreeTerms']);

    $errors = [];

    // --- Validation ---
    if (empty($formData['firstName'])) { $errors[] = "First name is required."; }
    if (empty($formData['lastName'])) { $errors[] = "Last name is required."; }
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (strlen($formData['password']) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if ($formData['password'] !== $formData['confirmPassword']) {
        $errors[] = "Passwords do not match.";
    }
    if (!$formData['agreeTerms']) {
        $errors[] = "You must agree to the Terms of Service.";
    }

    // Database Check: Check if email already exists
    if (empty($errors) && emailExists($conn, $formData['email'])) {
        $errors[] = "This email is already registered. Please login instead.";
    }

    if (!empty($errors)) {
        // If there are errors, compile them into a single message
        $signupError = implode(" | ", $errors);
    } else {
        // Validation SUCCESS! Save user to database
        if (saveNewUser($conn, $formData)) {
            // Success: Redirect to login page
            header('Location: login.php?signup=success');
            exit();
        } else {
            // Database insertion failure 
            $signupError = "Failed to create account due to a server error. Please try again. Database error: " . $conn->error;
        }
    }
}

// 4. Close the database connection when the script finishes its work
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Luxe Jewelry</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS for the form styling */
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
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            flex: 1;
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
        
        .auth-links {
            text-align: center;
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
            background: #1f2937;
            padding: 8px 16px;
            border-radius: 8px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
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
                <h1>Create Account</h1>
                <p>Join Luxe Jewelry today</p>
            </div>

            <!-- PHP ERROR MESSAGE DISPLAY -->
            <?php if ($signupError): ?>
                <div class="server-error"><?php echo htmlspecialchars($signupError); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="signup.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" name="firstName" id="firstName" class="form-input" required value="<?php echo htmlspecialchars($formData['firstName'] ?? ''); ?>">
                        <div class="error-message" id="firstNameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" name="lastName" id="lastName" class="form-input" required value="<?php echo htmlspecialchars($formData['lastName'] ?? ''); ?>">
                        <div class="error-message" id="lastNameError"></div>
                        </div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-input" required value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>">
                    <div class="error-message" id="emailError"></div>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-input" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
                    <div class="error-message" id="phoneError"></div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-input" required>
                    <div class="error-message" id="passwordError"></div>
                </div>
                <div class="form-group">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" class="form-input" required>
                    <div class="error-message" id="confirmPasswordError"></div>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" name="agreeTerms" id="agreeTerms" required <?php echo ($formData['agreeTerms'] ?? false) ? 'checked' : ''; ?>>
                    <label for="agreeTerms">I agree to the Terms of Service and Privacy Policy</label>
                </div>
                
                <button type="submit" class="auth-btn">Create Account</button>
                
                <div class="auth-links">
                    <p>Already have an account? <a href="login.php">Sign in here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>