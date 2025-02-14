<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "esmile_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// Insert data into the database
$sql = "INSERT INTO contact_submissions (name, email, subject, message)
        VALUES ('$name', '$email', '$subject', '$message')";

if ($conn->query($sql) === TRUE) {
    // Send email
    $to = "carladriancabral1@gmail.com";
    $headers = "From: " . $email;
    mail($to, $subject, $message, $headers);

    echo "Form submitted successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>