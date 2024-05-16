<?php
session_start();

// Check if the user is coming from the OTP verification page
if (!isset($_SESSION['email'])) {
    // Redirect the user back to the sign-up page if the email is not set in session
    header("Location: signup.php");
    exit();
}

require_once '../includes/config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the form
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username already exists
        $error = "Username already taken. Please choose a different username.";
    } else {
        // Validate password
        $passwordPattern = '/^(?=.*[a-z])(?=.*\d).{6,}$/';
        if (preg_match($passwordPattern, $password)) {
            // Password is valid, insert data into the database
            $email = $_SESSION['email'];
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                // Registration successful
                session_unset();
                session_destroy();
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        } else {
            $error = "Password must be at least 6 characters long and contain at least one lowercase letter and one number.";
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password</title>
    <link rel="icon" href="../icons/linksync.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../css/set_password.css">
</head>

<body>
    <div class="container">
        <h1>Set Password</h1>
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form id="set-password-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <input type="text" name="username" id="username" placeholder="Enter Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="Enter Password" required>
            </div>
            <button type="submit" class="btn">Set Password</button>
        </form>
    </div>
    <!-- <script src="script.js"></script> -->
</body>

</html>