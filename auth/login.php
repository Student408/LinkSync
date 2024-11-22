<?php
// Start the session
session_start();

// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Include database connection
include '../includes/config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user data from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Password is correct, create session and redirect to dashboard
            $_SESSION['username'] = $row['username'];
            header("Location: ../index.php");
            exit();
        } else {
            // Password is incorrect
            $error = "Invalid username/email or password";
        }
    } else {
        // User not found
        $error = "User not found";
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../icons/linksync.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username or Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <?php if(isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
        <p class="up-link">New user? <a href="signup.php">Sign up here</a></p>
        <p class="up-link"><a href="forgot_password.php">Forgot Password?</a></p>
        <p class="up-link"><a href="../land.php">Home</a></p>
    </div>
</body>
</html>