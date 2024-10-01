<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "link_manager";

// Gmail credentials
define('GMAIL_FROM_NAME', 'LinkSync');
define('GMAIL_USERNAME', 'noreply@linksync.com');
define('GMAIL_PASSWORD', 'noreply@linksync.com');
define('GMAIL_SENDER_EMAIL', 'yourpassword');


// // Zoho SMTP settings
// $zohoConfig = [
//     'username' => 'Yourname',
//     'password' => 'noreply@gmail.com',
//     'fromName' => 'youepassword',
//     'fromEmail' => 'noreply@gmail.com'
// ];


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
