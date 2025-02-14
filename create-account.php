<?php
session_start(); // Start the session

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
USE PHPMailer\PHPMailer\SMTP;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "esmile_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debugging: Check if form data is being received
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';

    // Get form data
    $First_name = trim($_POST['first_name']);
    $Middle_name = trim($_POST['middle_name']);
    $Last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $Email = trim($_POST['email']);
    $Password = $_POST['password'];
    $region = trim($_POST['region']);
    $province = trim($_POST['province']);
    $city_municipality = trim($_POST['city_municipality']);
    $barangay = trim($_POST['barangay']);
    $DOB = $_POST['dob'];
    $Mobile_no = trim($_POST['mobile_no']);
    $Sex = trim($_POST['sex']);
    $Telephone_no = trim($_POST['telephone_no']);
    $Height_feet = (int)$_POST['height_feet'];
    $Height_inches = (int)$_POST['height_inches'];
    $Weight = floatval($_POST['weight_kg']);
    $Blood_type = trim($_POST['blood_type']);

    // Combine height into a single float value (e.g., 5'10" = 5.10)
    $Height = $Height_feet + ($Height_inches / 12);

    // Validate input
    if (
        empty($First_name) || empty($Last_name) || empty($username) || empty($Email) || empty($Password) ||
        empty($region) || empty($province) || empty($city_municipality) || empty($barangay) || 
        empty($DOB) || empty($Mobile_no) || empty($Sex) || empty($Telephone_no) || 
        empty($Height) || empty($Weight) || empty($Blood_type)
    ) {
        echo "<script>alert('All fields are required'); window.history.back();</script>";
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($Password, PASSWORD_DEFAULT);

    // Prepare SQL query to insert user data
    $stmt = $conn->prepare(
        "INSERT INTO patient_details (
            First_name, Middle_name, Last_name, username, password, Email, region, province, 
            city_municipality, barangay, DOB, Mobile_no, Sex, Telephone_no, 
            height, weight, blood_type
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error); // Check for errors in preparation
    }

    // Bind parameters
    $stmt->bind_param(
        "sssssssssssssdsss", 
        $First_name, $Middle_name, $Last_name, $username, $hashed_password, $Email, 
        $region, $province, $city_municipality, $barangay, $DOB, $Mobile_no, $Sex, 
        $Telephone_no, $Height, $Weight, $Blood_type
    );

    // Execute query and check for success
    if ($stmt->execute()) {
        // Execute the UPDATE query to set user_id in patient_details
        $update_query = "
            UPDATE patient_details pd
            JOIN users u 
            ON pd.username = u.username
            SET pd.user_id = u.user_id
            WHERE pd.user_id IS NULL;
        ";

        if ($conn->query($update_query) === TRUE) {
            $_SESSION['success'] = "Account created successfully and user_id updated!";
        } else {
            $_SESSION['error'] = "Account created successfully, but user_id update failed: " . $conn->error;
        }

        header('Location: pages-login.html');
        exit();
    } else {
        $_SESSION['error'] = "Error creating account: " . $stmt->error; // Display detailed error
        header('Location: create-account.html');
        exit();
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>