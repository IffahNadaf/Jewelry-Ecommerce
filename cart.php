<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Luxe Jewelry</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <h1><a href="index.php">Luxe Jewelry</a></h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="products.php" class="nav-link">Products</a></li>
                    <li><a href="cart.php" class="nav-link active">Cart</a></li>
                    <li><a href="index.php#contact" class="nav-link">Contact</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count" id="cartCount">0</span>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main class="container" style="padding: 2rem 0;">
        <h1>Shopping Cart</h1>
        
        <div class="cart-container">
            <div class="cart-items" id="cartItems">
                <!-- Cart items loaded via JavaScript -->
            </div>
            
            <div class="cart-summary" id="cartSummary">
                <!-- Cart summary loaded via JavaScript -->
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCartItems();
            updateCartCount();
        });
    </script>
</body>
</html>