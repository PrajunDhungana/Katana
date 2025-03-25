<?php
// Start the session
session_start();

// Include the database connection
include_once 'includes/db.php';

// Get the category ID from the URL
$categoryID = isset($_GET['cat_id']) ? $_GET['cat_id'] : 1;

// Fetch category name (optional, if you want to display the category name)
$category_sql = "SELECT category_name FROM categories WHERE category_id = ?";
$stmt = $conn->prepare($category_sql);
$stmt->bind_param("i", $categoryID);
$stmt->execute();
$category_result = $stmt->get_result();
$category_name = $category_result->fetch_assoc()['category_name'];

// Fetch products from the selected category and randomize the order
$product_sql = "SELECT * FROM products WHERE category_id = ? ORDER BY RAND()";
$stmt = $conn->prepare($product_sql);
$stmt->bind_param("i", $categoryID);
$stmt->execute();
$product_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category_name; ?> - KatanaMart</title>
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

        /* Category Section */
        .category-section {
            padding: 40px;
            text-align: center;
            background-color: #fff;
        }

        .category-title {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .products-grid {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
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
            <h1><a href="index.php">KatanaMart</a></h1>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="login.php">Login</a>
        </div>
    </header>

    <!-- Category Section -->
    <section class="category-section">
        <h2 class="category-title"><?php echo $category_name; ?></h2>
        <div class="products-grid">
            <?php
            if ($product_result->num_rows > 0) {
                while ($row = $product_result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img src="images/' . $row['image_url'] . '" alt="' . $row['product_name'] . '">';
                    echo '<h3>' . $row['product_name'] . '</h3>';
                    echo '<p>$' . $row['price'] . '</p>';
                    echo '<a href="cart.php?productID=' . $row['productID'] . '" class="btn">Buy Now</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products available in this category.</p>';
            }
            ?>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-links">
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact</a>
            <a href="#">Privacy Policy</a>
        </div>
        <p>&copy; 2024 KatanaMart - All Rights Reserved</p>
    </footer>

</body>
</html>
