<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password using password_verify()
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: newadmin/index.html");
            } elseif ($user['role'] === 'dentist') {
                header("Location: dentist/dentist.html");
            } elseif ($user['role'] === 'patient') {
                header("Location: patient/patient-dashboard.php");
            } else {
                header("Location: login.php?error=try_again");
            }
            exit();
        } else {
            // Redirect with error message for invalid password
            header("Location: login.php?error=invalid_password");
            exit();
        }
    } else {
        // Redirect with error message for invalid username
        header("Location: login.php?error=username_not_found");
        exit();
    }

    $stmt->close();
}
?>


<!-- modal css -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            background: #f6f9ff;
            color: #444444;
        }
        .error-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(203, 207, 230, 0.4);
        }
        .error-modal-content {
            background-color:rgb(202, 221, 244);
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .error-modal-content .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .error-modal-content .close:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Your existing login form -->

    <!-- Error Modal -->
    <div id="errorModal" class="error-modal">
        <div class="error-modal-content">
            <span class="close">&times;</span>
            <p id="errorMessage"></p>
        </div>
    </div>

    <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const errorModal = document.getElementById('errorModal');
            const errorMessage = document.getElementById('errorMessage');
            const closeBtn = document.getElementsByClassName('close')[0];

            function redirectToLogin() {
                window.location.href = 'pages-login.html';
            }

            if (error === 'invalid_password') {
                errorMessage.textContent = 'Invalid password. Please try again.';
                errorModal.style.display = 'block';
                
                // Automatically redirect after 3 seconds
                setTimeout(redirectToLogin, 3000);
            } else if (error === 'username_not_found') {
                errorMessage.textContent = 'Username not found. Please check your credentials.';
                errorModal.style.display = 'block';
                
                // Automatically redirect after 3 seconds
                setTimeout(redirectToLogin, 3000);
            }

            // Close modal when clicking on <span> (x)
            closeBtn.onclick = redirectToLogin;

            // Close modal if clicked outside of it
            window.onclick = function(event) {
                if (event.target == errorModal) {
                    redirectToLogin();
                }
            }
        }
    </script>
</body>
</html>