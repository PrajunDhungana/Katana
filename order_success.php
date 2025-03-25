<?php
// Start session
session_start();

// Include database connection
include_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

// Get orderID from the query string
$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : null;

// Ensure the orderID is present
if (!$orderID) {
    echo "Invalid order.";
    exit();
}

$userID = $_SESSION['userID'];

// Fetch the order date and total amount for confirmation
$order_sql = "SELECT total_amount, order_date FROM orders WHERE orderID = ? AND userID = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("ii", $orderID, $userID);
$stmt->execute();
$order_result = $stmt->get_result();
$order_info = $order_result->fetch_assoc();

// If no order found, return error
if (!$order_info) {
    echo "No order details found.";
    exit();
}

$order_date = $order_info['order_date'];
$total_amount = $order_info['total_amount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f8f8;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .order-success-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }

        .success-message {
            font-size: 24px;
            color: green;
            margin-bottom: 20px;
        }

        .order-details {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            background-color: #f54242;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #d12f2f;
        }
    </style>
</head>
<body>

    <!-- Order Success Container -->
    <div class="order-success-container">
        <p class="success-message">Order Complete!</p>
        <p class="order-details">
            Order #<?php echo $orderID; ?> has been successfully placed on <?php echo $order_date; ?>.<br>
            Total Amount: $<?php echo number_format($total_amount, 2); ?>
        </p>
        <!-- Link back to homepage -->
        <a href="index.php" class="btn">Continue Shopping</a>
    </div>

</body>
</html>
