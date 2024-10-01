<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "link_manager";

// Gmail credentials
// define('GMAIL_USERNAME', 'yur@gmail.com');
// define('GMAIL_PASSWORD', 'scbv xxmq tuai xaos');
// define('GMAIL_SENDER_EMAIL', 'yur@gmail.com');


define('GMAIL_FROM_NAME', 'LinkSync');
define('GMAIL_USERNAME', 'noreply@theloko.me');
define('GMAIL_PASSWORD', 'theloko@noreply');
define('GMAIL_SENDER_EMAIL', 'noreply@theloko.me');


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
