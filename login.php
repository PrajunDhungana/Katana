<?php
// Start the session
session_start();

// Include the database connection file
include_once 'includes/db.php';  // Ensure this path is correct

// Initialize error message variable
$error = '';

// Check if the user clicked the "Continue as Guest" button
if (isset($_POST['guest_login'])) {
    // Set the session as a guest
    $_SESSION['isGuest'] = true;
    $_SESSION['username'] = 'Guest';
    $_SESSION['userID'] = null;

    // Redirect to homepage or a guest-specific page
    header('Location: index.php');
    exit();
}

// Regular login process
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['guest_login'])) {
    // Retrieve the input from the login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query to check if the user exists using the email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password using the 'password_hash' column
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables for logged-in user
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];  // Storing username from the DB
            unset($_SESSION['isGuest']);  // Clear guest status

            // Redirect to the homepage (index.php) after login
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Inline CSS for the background image */
        body {
            font-family: 'Roboto', sans-serif;
            background: url('images/hero-img.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #f5f5f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: rgba(34, 34, 34, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            max-width: 400px;
            width: 100%;
            border: 2px solid #f54242;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 16px;
            text-align: left;
            color: #f5f5f5;
        }

        input[type="email"],
        input[type="password"] {
            padding: 10px;
            font-size: 16px;
            background-color: #333;
            color: #f5f5f5;
            border: none;
            border-bottom: 2px solid #4287f5;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-bottom: 2px solid #f54242;
        }

        button {
            padding: 12px 20px;
            background-color: #4287f5;
            color: #fff;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 16px;
        }

        button:hover {
            background-color: #356ac0;
        }

        p a {
            color: #f54242;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
        }

        /* Styles for the Guest login button */
        .guest-btn {
            background-color: #f54242;
            color: white;
            padding: 12px 20px;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 15px;
        }

        .guest-btn:hover {
            background-color: #d12f2f;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>

        <!-- Display error message if any -->
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <form action="login.php" method="POST">
            <button type="submit" name="guest_login" class="guest-btn">Continue as Guest</button>
        </form>

        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        <p>You are a seller ? <a href="admin/seller_register.php">Register as Seller</a>

    </div>
</body>
</html>
