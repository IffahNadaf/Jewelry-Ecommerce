<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Luxe Jewelry</title>
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
                    <li><a href="products.php" class="nav-link active">Products</a></li>
                    <li><a href="cart.php" class="nav-link">Cart</a></li>
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
        <div class="products-header">
            <h1>Our Collection</h1>
            <div class="filters">
                <input type="text" id="searchInput" placeholder="Search products..." class="search-input">
                <select id="categoryFilter" class="filter-select">
                    <option value="all">All Categories</option>
                    <option value="rings">Rings</option>
                    <option value="necklaces">Necklaces</option>
                    <option value="earrings">Earrings</option>
                    <option value="bracelets">Bracelets</option>
                     </select>
                <select id="sortBy" class="filter-select">
                    <option value="name">Sort by Name</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="rating">Highest Rated</option>
                </select>
            </div>
        </div>
        
        <div class="products-grid" id="allProducts">
            <!-- Products loaded via JavaScript -->
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAllProducts();
            updateCartCount();
            setupProductFilters();
        });
    </script>
</body>
</html>