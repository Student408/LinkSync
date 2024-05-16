<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "link_manager";

// Gmail credentials
// define('GMAIL_USERNAME', 'yur@gmail.com');
// define('GMAIL_PASSWORD', 'scbv xxmq tuai xaos');
// define('GMAIL_SENDER_EMAIL', 'yur@gmail.com');


define('GMAIL_FROM_NAME', 'Yourname');
define('GMAIL_USERNAME', 'noreply@gmail.com');
define('GMAIL_PASSWORD', 'yourpassword');
define('GMAIL_SENDER_EMAIL', 'noreply@gmail.com');


// Zoho SMTP settings
$zohoConfig = [
    'username' => 'Yourname',
    'password' => 'noreply@gmail.com',
    'fromName' => 'youepassword',
    'fromEmail' => 'noreply@gmail.com'
];


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
