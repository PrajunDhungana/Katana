<?php
// Start the session
session_start();

// Include the database connection
include_once 'includes/db.php';

// Check if the user is a guest
$isGuest = isset($_GET['guest']) && $_GET['guest'] == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katana Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        h1, h2, h3, p {
            margin: 0;
            padding: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f54242;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #d12f2f;
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 15px 30px;
            color: white;
        }

        .navbar .logo h1 a {
            color: #f54242;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
        }

        .navbar .nav-links a {
            color: white;
            margin-left: 20px;
            transition: color 0.3s;
        }

        .navbar .nav-links a:hover {
            color: #f54242;
        }

        /* Search Bar Styles */
        .search-bar {
            display: flex;
            align-items: center;
            background-color: #444;
            padding: 5px;
            border-radius: 5px;
            margin-left: 20px;
        }

        .search-bar input[type="text"] {
            border: none;
            padding: 8px;
            background-color: #444;
            color: white;
            outline: none;
            width: 250px;
        }

        .search-bar button {
            padding: 8px 15px;
            background-color: #f54242;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .search-bar button:hover {
            background-color: #d12f2f;
        }

        /* Dropdown Categories */
        .categories-dropdown {
            position: relative;
            display: inline-block;
        }

        .categories-dropdown-content {
            display: none;
            position: absolute;
            background-color: #f1f1f1;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .categories-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .categories-dropdown-content a:hover {
            background-color: #ddd;
        }

        .categories-dropdown:hover .categories-dropdown-content {
            display: block;
        }

        .categories-dropdown:hover .dropbtn {
            color: #f54242;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            text-align: center;
            color: white;
        }

        .hero-section img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .hero-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-shadow: 2px 2px 5px #000;
        }

        .hero-text h2 {
            font-size: 2.5em;
        }

        /* Products Section */
        .products-section {
            padding: 40px;
            background-color: #fff;
            text-align: center;
        }

        .products-grid {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 10px;
            width: 220px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product-card:hover {
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Footer Section */
        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .footer a {
            color: #f54242;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <header class="navbar">
        <div class="logo">
            <h1><a href="#">KatanaMart</a></h1>
        </div>
        <div class="nav-links">
            <div class="categories-dropdown">
                <a href="#" class="dropbtn">Categories</a>
                <div class="categories-dropdown-content">
                    <a href="category.php?cat_id=1">Traditional Katanas</a>
                    <a href="category.php?cat_id=2">Modern Katanas</a>
                    <a href="category.php?cat_id=3">Anime Katanas</a>
                </div>
            </div>
            <!-- Add search bar here -->
            <form class="search-bar" action="search.php" method="GET">
                <input type="text" name="query" placeholder="Search for Katanas...">
                <button type="submit">Search</button>
            </form>
            <?php if (!$isGuest && isset($_SESSION['username'])): ?>
                <!-- Show logged-in user information -->
                <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
                <a href="logout.php">Logout</a>
            <?php elseif ($isGuest): ?>
                <!-- If user is a guest -->
                <span>Welcome, Guest!</span>
                <a href="login.php">Login</a>
            <?php else: ?>
                <!-- If not logged in and not a guest -->
                <a href="login.php">Login</a>
                <a href="signup.php">Sign up</a>
            <?php endif; ?>
            <a href="cart.php">Cart</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <img src="images/hero-img.jpg" alt="Hero Banner">
        <div class="hero-text">
            <h2>Master the Art of the Katana</h2>
            <p>Explore our exclusive collection of Katanas, crafted with precision and tradition.</p>
            <a href="#featured-products" class="btn">Shop Now</a>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured-products" class="products-section">
        <h2>Best-Selling Katanas</h2>
        <div class="products-grid">
            <?php
            // Fetch products from the database
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img src="images/' . $row['image_url'] . '" alt="' . $row['product_name'] . '">';
                    echo '<h3>' . $row['product_name'] . '</h3>';
                    echo '<p>$' . $row['price'] . '</p>';
                    echo '<a href="cart.php?productID=' . $row['productID'] . '" class="btn">Buy Now</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products available.</p>';
            }
            ?>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-links">
            <a href="#">About Us</a>
            <a href="#">Contact</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Returns</a>
        </div>
        <p>&copy; 2024 KatanaMart - All Rights Reserved</p>
    </footer>

</body>
</html>
