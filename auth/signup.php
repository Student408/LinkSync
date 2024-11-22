<?php
// Include database connection
require_once '../includes/config.php';
require_once 'smtp/PHPMailerAutoload.php';

// Set session cookie expiration time to 5 minutes from now
$expiration = time() + (5 * 60); // 5 minutes
session_set_cookie_params($expiration);

// Start the session
session_start();

// SMTP Mailer function
function smtp_mailer($email, $subject, $msg)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = MAIL_SENDER_HOST;
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

// // Function to send OTP via email
// function sendOTPEmail($email, $otp)
// {
//     $subject = "OTP Verification for Sign Up";
//     $message = "<p>Your OTP (One-Time Password) for sign up is: <strong>$otp</strong></p>
//                 <p>This OTP is valid for 5 minutes.</p>";
//     return smtp_mailer($email, $subject, $message);
// }

// Function to send OTP via email
function sendOTPEmail($email, $otp) {
    $subject = "OTP Verification for Sign Up";
    $logo = 'https://linksync.free.nf/icons/linksync.png'; // Replace with your actual logo URL
    $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>OTP Verification</title>
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
                    margin-bottom: 30px;
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
                .otp {
                    font-size: 24px;
                    font-weight: bold;
                    color: #007bff;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">
                    <img src="' . $logo . '" alt="LinkSync">
                </div>
                <h2>OTP Verification for Sign Up</h2>
                <p>Your OTP (One-Time Password) for sign up is:</p>
                <p class="otp">' . $otp . '</p>
                <p>This OTP is valid for 5 minutes.</p>
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

    // Prepare and bind SQL statement to check if email already exists
    $stmt_check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_email = $stmt_check_email->get_result();

    if ($result_email->num_rows > 0) {
        // Email already exists
        echo "<script>alert('You already have an account. Please log in.');</script>";
    } else {
        // Generate OTP and send it to the user's email
        $otp = mt_rand(100000, 999999);
        $sendOTPStatus = sendOTPEmail($email, $otp);

        if ($sendOTPStatus == 'Sent') {
            // OTP sent successfully, store OTP, email, and current time in session
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['otp_time'] = time(); // Store current time
            // Redirect to OTP verification page
            header("Location: otp_verification.php");
            exit();
        } else {
            // Error sending OTP
            echo "Error: " . $sendOTPStatus;
        }
    }

    // Close prepared statement
    $stmt_check_email->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="icon" href="../icons/linksync.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../css/signup.css">
</head>

<body>
    <div class="container">
        <h1>Sign Up</h1>
        <form id="signup-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email" required autocomplete="email">
            </div>
            <button type="submit" class="btn">Get OTP</button>
        </form>
        <p class="login-link">already have an account? <a href="login.php">Login here</a></p>
        <p class="login-link"><a href="../land.php">Home</a></p>
    </div>
    <!-- <script src="script.js"></script> -->
</body>

</html>