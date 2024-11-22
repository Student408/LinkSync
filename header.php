<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkSync</title>
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="icons/logo.svg" alt="LinkSync">
            <a href="index.php"> LinkSync</a>
        </div>
        <p class="subtitle">A simple and intuitive tool to organize your links</p>
        <?php
        // Check if a session is not already active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user is logged in
        if (isset($_SESSION['username'])) {
            // User is logged in, show a logout link
            echo '<p class="user-info">Welcome, ' . $_SESSION['username'] . '! <a href="logout.php" class="logout-link">Logout</a></p>';
        } else {
            // User is not logged in, show a login link
            echo '<p class="user-info"><a href="auth/login.php" class="login-link">Login</a></p>';
        }
        ?>
        
        
    </header>
    <!-- Add your main content here -->
</body>
</html>