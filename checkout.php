<?php
// Start session
session_start();

// Include database connection
include_once 'includes/db.php';

// Check if user is logged in or a guest
$isGuest = isset($_GET['guest']) && $_GET['guest'] == 1;
$userID = $isGuest ? $_SERVER['REMOTE_ADDR'] : $_SESSION['userID'];

// Fetch cart items for display
$cart_sql = "SELECT cart.*, products.product_name, products.price, products.image_url 
             FROM cart 
             JOIN products ON cart.productID = products.productID
             WHERE cart.userID = ?";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$cart_items = $stmt->get_result();

$total_amount = 0;
if ($cart_items->num_rows > 0) {
    while ($item = $cart_items->fetch_assoc()) {
        $total_amount += $item['quantity'] * $item['price'];
    }
}

// Handle checkout form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $card_number = $_POST['card_number'];
    $card_expiry = $_POST['card_expiry'];
    $card_cvc = $_POST['card_cvc'];

    // Insert the order into the orders table
    $order_sql = "INSERT INTO orders (userID, total_amount, order_date) VALUES (?, ?, NOW())";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("id", $userID, $total_amount);
    $order_stmt->execute();
    $orderID = $order_stmt->insert_id;

    // Insert order items into orderitems table
    foreach ($cart_items as $item) {
        $orderitems_sql = "INSERT INTO orderitems (orderID, productID, quantity, price) 
                           VALUES (?, ?, ?, ?)";
        $orderitems_stmt = $conn->prepare($orderitems_sql);
        $orderitems_stmt->bind_param("iiid", $orderID, $item['productID'], $item['quantity'], $item['price']);
        $orderitems_stmt->execute();
    }

    // Clear the cart after successful order
    $clear_cart_sql = "DELETE FROM cart WHERE userID = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_sql);
    $clear_cart_stmt->bind_param("i", $userID);
    $clear_cart_stmt->execute();

    // Redirect to a success or order confirmation page
    header('Location: order_success.php?orderID=' . $orderID);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f8f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #333;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
        }

        .navbar a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
        }

        .navbar .logo h1 a {
            color: #f54242;
        }

        .checkout-container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .checkout-container h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        label {
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            padding: 12px 20px;
            background-color: #f54242;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #d12f2f;
        }

        .checkout-summary {
            margin-top: 20px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 10px;
        }

        .checkout-summary h3 {
            margin-bottom: 10px;
        }

        .checkout-summary p {
            font-size: 16px;
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
            <?php if ($isGuest): ?>
                <span>Welcome, Guest!</span>
                <a href="login.php">Login</a>
            <?php else: ?>
                <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
            <a href="cart.php">Cart</a>
        </div>
    </header>

    <!-- Checkout Container -->
    <div class="checkout-container">
        <h2>Checkout</h2>

        <form action="checkout.php" method="POST">
            <!-- Personal Details -->
            <div>
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>

                <label for="city">City</label>
                <input type="text" id="city" name="city" required>

                <label for="zip">ZIP Code</label>
                <input type="text" id="zip" name="zip" required>
            </div>

            <!-- Payment Details -->
            <div>
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" name="card_number" required>

                <label for="card_expiry">Expiry Date (MM/YY)</label>
                <input type="text" id="card_expiry" name="card_expiry" required>

                <label for="card_cvc">CVC</label>
                <input type="text" id="card_cvc" name="card_cvc" required>
            </div>

            <!-- Checkout Summary -->
            <div class="checkout-summary">
                <h3>Order Summary</h3>
                <p>Total Amount: $<?php echo number_format($total_amount, 2); ?></p>
            </div>

            <button type="submit">Complete Purchase</button>
        </form>
    </div>

</body>
</html>
