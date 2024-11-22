<?php
// Set session cookie expiration time to 5 minutes from now
// $expiration = time() + (5 * 60); // 5 minutes
// session_set_cookie_params($expiration);

// Start the session
session_start();

// Check if the session has expired
if (isset($_SESSION['otp']) && isset($_SESSION['email'])) {
    $otpTime = $_SESSION['otp_time'];
    $currentTime = time();
    $timeDiff = $currentTime - $otpTime;

    // If session has expired (5 minutes have passed)
    if ($timeDiff > (5 * 60)) {
        // Destroy the session
        session_unset();
        session_destroy();

        // Redirect the user back to the sign-up page
        header("Location: signup.php");
        exit();
    }
} else {
    // Redirect the user back to the sign-up page if OTP or email is not set in session
    header("Location: signup.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve OTP entered by the user
    $enteredOTP = isset($_POST['otp']) ? $_POST['otp'] : '';

    // Validate OTP
    if (!empty($enteredOTP) && $enteredOTP == (string)$_SESSION['otp']) {
        // OTP verification successful, redirect the user to set password page
        header("Location: set_password.php");
        exit();
    } else {
        // OTP verification failed, display error message
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="icon" href="../icons/linksync.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../css/otp_verification.css">
</head>

<body>
    <div class="container">
        <h1>OTP Verification</h1>
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form id="otp-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <input type="text" name="otp" id="otp" placeholder="Enter OTP" required autocomplete="off">
            </div>
            <button type="submit" class="btn">Verify OTP</button>
        </form>
        <p class="login-link"><a href="signup.php">Resend OTP</a></p>
    </div>
    <!-- <script src="script.js"></script> -->
</body>

</html>