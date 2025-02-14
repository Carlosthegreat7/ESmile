<?php
require_once 'db_connection.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get input data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Role: admin, dentist, or patient
    $email = $_POST['email'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL to insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, Email, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $username, $hashed_password, $role, $email);

    // Execute and check success
    if ($stmt->execute()) {
        echo "New user inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
