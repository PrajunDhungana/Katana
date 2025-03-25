<?php
// Start the session
session_start();

// Include the database connection
include_once 'includes/db.php';  // Ensure this path is correct

// Initialize variables
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user into the database
    $sql = "INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        // Redirect to login page after successful signup
        header('Location: login.php');
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
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

        input[type="text"],
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

        input[type="text"]:focus,
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
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create an Account</h2>

        <!-- Show error if any -->
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
