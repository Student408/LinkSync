<?php
// Include database connection
require_once '../includes/config.php';
require_once 'smtp/PHPMailerAutoload.php';

// SMTP Mailer function
function smtp_mailer($email, $subject, $msg)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    // $mail->Host = "smtp.gmail.com";
    $mail->Host = "smtp.zoho.in";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';

    // Replace with your Gmail email and password from a secure source
    $mail->Username = GMAIL_USERNAME;
    $mail->Password = GMAIL_PASSWORD;
    $mail->SetFrom(GMAIL_SENDER_EMAIL, "LinkSync");

    
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($email);
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false
        )
    );

    if (!$mail->Send()) {
        return $mail->ErrorInfo;
    } else {
        return 'Sent';
    }
}

// Function to send password reset link via email
function sendPasswordResetLink($email, $resetToken, $ip_address) {
    $resetLink = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $resetToken;
    $subject = "Password Reset";
    $logo = 'https://linksync.free.nf/icons/linksync.png';
    $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Password Reset</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f5f5f5; 
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 40px auto;
                    background-color: #FFF;
                    padding: 40px;
                    border-radius: 5px;
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                }
                .logo {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo img {
                    max-width: 25px;
                    max-height: 25px;
                }
                h2 {
                    text-align: center;
                    color: #333;
                }
                p {
                    line-height: 1.5;
                    color: #555;
                }

                a{
                    color: #ffffff;
                    text-decoration: none;
                }
                .btn {
                    display: inline-block;
                    background-color: #007bff;
                    color: #ffffff;
                    text-decoration: none;
                    padding: 12px 24px;
                    border-radius: 5px;
                    transition: background-color 0.3s ease;
                    font-size: 16px;
                }
                .btn:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">
                    <a LinkSync> </a><img src="' . $logo . '" alt="LinkSync">
                </div>
                <h2>Password Reset</h2>
                <p>Dear User,</p>
                <p>You recently requested to reset your password. Your IP address is: ' . $ip_address . '</p>
                <p>Click the button below to reset your password:</p>
                <p style="text-align: center; margin-top: 30px;"><a href="' . $resetLink . '" class="btn">Reset Password</a></p>
                <p style="margin-top: 30px;">If you did not request a password reset, please ignore this email.</p>
                <p>Thank you for your understanding.</p>
                <p>Best regards,<br>LinkSync</p>
            </div>
        </body>
        </html>
    ';

    return smtp_mailer($email, $subject, $message);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email from the form
    $email = $_POST['email'];

    // Retrieve user's IP address
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check if the user has already made three requests within the last 6 hours
    $rate_limit_check_sql = "SELECT COUNT(*) AS request_count FROM password_reset_requests WHERE email = ? AND request_time >= DATE_SUB(NOW(), INTERVAL 6 HOUR)";
    $stmt_rate_limit_check = $conn->prepare($rate_limit_check_sql);
    $stmt_rate_limit_check->bind_param("s", $email);
    $stmt_rate_limit_check->execute();
    $result_rate_limit_check = $stmt_rate_limit_check->get_result();
    $rate_limit_row = $result_rate_limit_check->fetch_assoc();

    if ($rate_limit_row['request_count'] >= 2) {
        $error = "You have reached the limit of password reset requests for the next 6 hours. Please try again later.";
        // Close prepared statement
        $stmt_rate_limit_check->close();
    } else {
        // Prepare and bind SQL statement to check if email exists
        $stmt_check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        $result_email = $stmt_check_email->get_result();

        if ($result_email->num_rows > 0) {
            $row = $result_email->fetch_assoc();
            // Generate a reset token and store it in the database
            $resetToken = bin2hex(random_bytes(16)); // Generate a random 32-character token
            $updateTokenSql = "UPDATE users SET reset_token = ? WHERE email = ?";
            $stmt_update_token = $conn->prepare($updateTokenSql);
            if ($stmt_update_token) {
                $stmt_update_token->bind_param("ss", $resetToken, $email);
                $stmt_update_token->execute();

                $sendLinkStatus = sendPasswordResetLink($email, $resetToken, $ip_address);

                if ($sendLinkStatus == 'Sent') {
                    // Password reset link sent successfully
                    $success = "Password reset link has been sent to your email.";

                    // Log the password reset request along with IP address
                    $log_request_sql = "INSERT INTO password_reset_requests (email, ip_address) VALUES (?, ?)";
                    $stmt_log_request = $conn->prepare($log_request_sql);
                    $stmt_log_request->bind_param("ss", $email, $ip_address);
                    $stmt_log_request->execute();
                    $stmt_log_request->close();
                } else {
                    // Error sending email
                    $error = "Error: " . $sendLinkStatus;
                }

                // Close the prepared statement
                $stmt_update_token->close();
            } else {
                $error = "Error preparing the SQL statement: " . $conn->error;
            }
        } else {
            // Email does not exist
            $error = "The email address is not registered with us.";
        }

        // Close prepared statement
        $stmt_check_email->close();
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
    <title>Forgot Password</title>
    <link rel="icon" href="../icons/linksync.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../css/signup.css">
</head>

<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <?php if (isset($success)) : ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form id="forgot-password-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email" required autocomplete="email">
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <p class="login-link">Remember your password? <a href="login.php">Login here</a></p>
        <p class="login-link"><a href="../land.php">Home</a></p>
    </div>
    <!-- <script src="script.js"></script> -->
</body>

</html>
