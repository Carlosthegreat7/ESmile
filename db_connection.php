<?php
// Database credentials
$host = "localhost"; // Server hostname
$username = "root";  // Default XAMPP username
$password = "123123";      // Default XAMPP password (leave blank)
$database = "esmile_db"; // Your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);



// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "";
}
?>
