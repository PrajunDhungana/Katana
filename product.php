<?php

session_start();
require_once 'db_connection.php'; // Include your database connection file

// Check if the user is logged in (optional depending on your logic)
if (!isset($_SESSION['userID'])) {
    // Redirect to login page if the user is not logged in
    header('Location: login.php');
    exit;
}

// Get the productID from the URL
if (isset($_GET['productID'])) {
    $productID = intval($_GET['productID']);
    
    // Fetch product details from the database
    $query = "SELECT * FROM Products WHERE productID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "No product specified.";
    exit;
}

// Check if the user added the product to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    
    // Assume addToCart() is defined in a separate file or here
    function addToCart($userID, $productID, $quantity, $conn) {
        // Check if product is already in the cart
        $checkCartQuery = "SELECT * FROM Cart WHERE userID = ? AND productID = ?";
        $stmt = $conn->prepare($checkCartQuery);
        $stmt->bind_param("ii", $userID, $productID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the quantity if the product already exists in the cart
            $updateQuery = "UPDATE Cart SET quantity = quantity + ? WHERE userID = ? AND productID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("iii", $quantity, $userID, $productID);
            $updateStmt->execute();
        } else {
            // Insert a new row if the product is not in the cart
            $insertQuery = "INSERT INTO Cart (userID, productID, quantity) VALUES (?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("iii", $userID, $productID, $quantity);
            $insertStmt->execute();
        }
    }

    // Add the product to the cart for the logged-in user
    $userID = $_SESSION['userID'];
    addToCart($userID, $productID, $quantity, $conn);

    // Redirect to the cart or show a success message
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Katana Marketplace</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to your custom CSS -->
</head>
<body>

<!-- Navigation Bar -->
<header class="navbar">
    <div class="logo">
        <h1><a href="#">KatanaMart</a></h1>
    </div>
    <div class="search-bar">
        <input type="text" placeholder="Search for Katanas, Accessories...">
        <button>Search</button>
    </div>
    <div class="nav-links">
        <a href="#">Login</a>
        <a href="#">Orders</a>
        <a href="#">Cart</a>
    </div>
</header>

<!-- Product Details Section -->
<section class="product-details-section">
    <div class="product-image">
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
    </div>
    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
        <h2>$<?php echo number_format($product['price'], 2); ?></h2>
        
        <!-- Add to Cart Form -->
        <form action="product.php?productID=<?php echo $productID; ?>" method="post">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1">
            <button type="submit">Add to Cart</button>
        </form>
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
    <div class="footer-social">
        <a href="#">Facebook</a>
        <a href="#">Instagram</a>
        <a href="#">Twitter</a>
    </div>
    <p>&copy; 2024 KatanaMart - All Rights Reserved</p>
</footer>

</body>
</html>

