<?php
// development

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "link_manager";

// production

$servername = "sql305.infinityfree.com";
$username = "if0_36361134";
$password = "EIL540QhS7";
$dbname = "if0_36361134_links";


// Gmail credentials

// define('GMAIL_USERNAME', 'lokobiz99@gmail.com');
// define('GMAIL_PASSWORD', 'scbv xxmq tuai xaos');
// define('GMAIL_SENDER_EMAIL', 'lokobiz99@gmail.com');
// define('MAIL_SENDER_HOST', 'smtp.gmail.com');

// Zoho credentials

define('GMAIL_FROM_NAME', 'LinkSync');
define('GMAIL_USERNAME', 'noreply@theloko.me');
define('GMAIL_PASSWORD', 'theloko@noreply');
define('GMAIL_SENDER_EMAIL', 'noreply@theloko.me');
define('MAIL_SENDER_HOST', 'smtp.zoho.in');

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>