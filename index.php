<?php
// CRITICAL: MUST be the very first thing in the file
session_start();

// Define variables to be used in the HTML/PHP block
$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
// Safely display the username or default to 'Guest' using null coalescing operator (??)
$userName = htmlspecialchars($_SESSION['username'] ?? 'Guest');

// You might include db.php here if you need product data
// require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxe Jewelry - Exquisite Handcrafted Jewelry</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Assuming styles.css contains the necessary CSS for the layout and buttons -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <h1>Luxe Jewelry</h1>
                </div>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="#home" class="nav-link active">Home</a></li>
                    <li><a href="#products" class="nav-link">Products</a></li>
                    <li><a href="cart.php" class="nav-link">Cart</a></li>
                    <li><a href="#contact" class="nav-link">Contact</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count" id="cartCount">0</span>
                    </a>
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <!-- DYNAMIC AUTHENTICATION BLOCK -->
                    <?php if ($isLoggedIn): ?>
                        <!-- User is Logged In: Show Welcome message, Profile, and Logout -->
                        <div id="userProfile" style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>Welcome, <?php echo $userName; ?>!</span>
                            <a href="profile.php" class="btn btn-primary">Profile</a>
                            <!-- Link to the logout script (logout.php) -->
                            <a href="logout.php" class="btn btn-secondary">Logout</a>
                        </div>
                    <?php else: ?>
                        <!-- User is NOT Logged In: Show Login and Sign Up buttons -->
                        <div id="authButtons" style="display: flex; gap: 0.25rem;">
                            <a href="login.php" class="btn btn-secondary">Login</a>
                            <a href="signup.php" class="btn btn-primary">Sign Up</a>
                        </div>
                    <?php endif; ?>
                    <!-- END DYNAMIC AUTHENTICATION BLOCK -->

                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h2 class="hero-title">Exquisite Jewelry</h2>
            <p class="hero-subtitle">Discover our collection of handcrafted jewelry pieces that celebrate life's precious moments</p>
            <a href="#products" class="btn btn-primary">Shop Now</a>
        </div>
    </section>
    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Free Shipping</h3>
                    <p>Free shipping on orders over $500</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Lifetime Warranty</h3>
                    <p>Comprehensive warranty on all pieces</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3>Easy Returns</h3>
                    <p>30-day return policy</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products" id="products">
        <div class="container">
            <div class="section-header">
                <h2>Featured Collection</h2>
                <p>Handpicked pieces from our finest collection</p>
            </div>
            <div class="products-grid" id="featuredProducts">
                <!-- Products loaded via JavaScript -->
            </div>
            <div class="section-footer">
                <a href="products.php" class="btn btn-primary">View All Products</a>
            </div>
        </div>
    </section>
      <!-- Newsletter -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Updated</h2>
                <p>Subscribe to our newsletter for exclusive offers</p>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" id="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Luxe Jewelry</h3>
                    <p>Crafting beautiful jewelry since 1985.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#products">Products</a></li>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p>Jewelry Lane<br>goa , India <br>Phone: 1234567890</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Luxe Jewelry. All rights reserved.</p>
            </div>
        </div>
    </footer>
      <script src="script.js"></script>
</body>
</html>