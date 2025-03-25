<?php
// Start session
session_start();

// Include database connection
include_once 'includes/db.php';

// Check if the user is a guest (if no session userID is set, it's a guest)
$isGuest = !isset($_SESSION['userID']);

// If guest tries to add to cart or access cart, redirect to login
if ($isGuest) {
    echo "<script>
        alert('Please log in to add items to the cart.');
        window.location.href = 'login.php';
        </script>";
    exit();
}

// Fetch the user ID of the logged-in user
$userID = $_SESSION['userID'];

// Handle adding items to the cart
if (isset($_GET['productID'])) {
    $productID = $_GET['productID'];
    $quantity = 1; // Default quantity for new items

    // Check if product already exists in the cart
    $check_sql = "SELECT * FROM cart WHERE userID = ? AND productID = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $userID, $productID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If product exists in cart, update quantity
        $update_sql = "UPDATE cart SET quantity = quantity + 1 WHERE userID = ? AND productID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $userID, $productID);
        $update_stmt->execute();
    } else {
        // Insert new item into the cart
        $insert_sql = "INSERT INTO cart (userID, productID, quantity, added_at) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $userID, $productID, $quantity);
        $insert_stmt->execute();
    }

    header('Location: cart.php');
    exit();
}

// Handle removing items from the cart
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];

    // Delete the product from the cart
    $remove_sql = "DELETE FROM cart WHERE userID = ? AND productID = ?";
    $remove_stmt = $conn->prepare($remove_sql);
    $remove_stmt->bind_param("ii", $userID, $remove_id);
    $remove_stmt->execute();

    header('Location: cart.php');
    exit();
}

// Handle updating the cart quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $productID => $quantity) {
        if ($quantity > 0) {
            // Update quantity in the cart
            $update_qty_sql = "UPDATE cart SET quantity = ? WHERE userID = ? AND productID = ?";
            $stmt = $conn->prepare($update_qty_sql);
            $stmt->bind_param("iii", $quantity, $userID, $productID);
            $stmt->execute();
        }
    }
    header('Location: cart.php');
    exit();
}

// Fetch cart items
$cart_sql = "SELECT cart.*, products.product_name, products.price, products.image_url 
             FROM cart 
             JOIN products ON cart.productID = products.productID
             WHERE cart.userID = ?";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$cart_items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
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

        .cart-container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .cart-img {
            width: 100px;
        }

        .total-price {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #f54242;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #d12f2f;
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
            <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
            <a href="logout.php">Logout</a>
            <a href="cart.php">Cart</a>
        </div>
    </header>

    <!-- Cart Container -->
    <div class="cart-container">
        <h2>Your Cart</h2>
        <form method="POST" action="cart.php">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_amount = 0;

                    if ($cart_items->num_rows > 0) {
                        while ($item = $cart_items->fetch_assoc()) {
                            $item_total = $item['quantity'] * $item['price'];
                            $total_amount += $item_total;

                            echo '<tr>';
                            echo '<td><img src="images/' . $item['image_url'] . '" class="cart-img" alt="' . $item['product_name'] . '"></td>';
                            echo '<td>' . $item['product_name'] . '</td>';
                            echo '<td><input type="number" name="quantities[' . $item['productID'] . ']" value="' . $item['quantity'] . '" min="1"></td>';
                            echo '<td>$' . $item['price'] . '</td>';
                            echo '<td>$' . number_format($item_total, 2) . '</td>';
                            echo '<td><a href="cart.php?remove_id=' . $item['productID'] . '" class="btn">Remove</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">Your cart is empty!</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="total-price">
                Total: $<?php echo number_format($total_amount, 2); ?>
            </div>
            <button type="submit" name="update_cart" class="btn">Update Cart</button>
            <a href="checkout.php" class="btn">Proceed to Checkout</a>
        </form>
    </div>

</body>
</html>
