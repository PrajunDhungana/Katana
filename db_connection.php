<?php
// Database connection settings
$servername = "localhost";  // Your server name (e.g., 'localhost' for local development)
$username = "root";         // Your MySQL username (default is 'root' on WAMP)
$password = "";             // Your MySQL password (leave blank for default in WAMP)
$dbname = "katana_store";   // Your database name

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
