<?php
require_once '../includes/config.php';

// Check if the token is provided in the URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    // Redirect to forgot password page if token is not provided
    header("Location: forgot_password.php");
    exit();
}

$token = $_GET['token'];

// Prepare and bind SQL statement to check if the token exists
$stmt_check_token = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
$stmt_check_token->bind_param("s", $token);
$stmt_check_token->execute();
$result_token = $stmt_check_token->get_result();

if ($result_token->num_rows > 0) {
    // Token is valid, proceed to reset password form
} else {
    // Token is invalid or expired
    echo "<script>alert('Invalid or expired token. Please request a new password reset link.');</script>";
    header("Location: forgot_password.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve new password from the form
    $newPassword = $_POST['new_password'];

    // Validate new password
    $passwordPattern = '/^(?=.*[a-z])(?=.*\d).{6,}$/';
    if (preg_match($passwordPattern, $newPassword)) {
        // Password is valid, update the password in the database
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
        $stmt_update_password = $conn->prepare($sql);
        if ($stmt_update_password) {
            $stmt_update_password->bind_param("ss", $hashedPassword, $token);

            if ($stmt_update_password->execute()) {
                // Password reset successful
                echo "<script>alert('Password reset successful. You can now log in with your new password.');</script>";
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }

            // Close the prepared statement
            $stmt_update_password->close();
        } else {
            $error = "Error preparing the SQL statement: " . $conn->error;
        }
    } else {
        $error = "Password must be at least 6 characters long and contain at least one lowercase letter and one number.";
    }
}

$stmt_check_token->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="../icons/linksync.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../css/signup.css">
</head>

<body>
    <div class="container">
        <h1>Reset Password</h1>
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form id="reset-password-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?token=' . $token); ?>">
            <div class="form-group">
                <input type="password" name="new_password" id="new_password" placeholder="Enter New Password" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
    </div>
    <!-- <script src="script.js"></script> -->
</body>

</html>