<?php
// Database credentials (UPDATE THESE WITH YOUR ACTUAL DETAILS)
$dbHost = 'localhost'; 
$dbUser = 'root';  
$dbPass = ''; 
$dbName = 'luxe_jewelry_db'; // Ensure this matches the DB you created

// Create a new MySQLi connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if ($conn->connect_error) {
    // Stop script execution if connection fails
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8 for proper encoding
$conn->set_charset("utf8mb4");

// The $conn object is now available to any file that includes db.php
?>