<?php
// Include database connection
include 'includes/config.php';

// Check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: land.php");
    exit();
}

// Get the current user's username from the session
$currentUser = $_SESSION['username'];

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the link ID from the request
    $linkId = $_POST['id'];

    // Query the database to check if the user is allowed to delete the link
    $sql = "SELECT * FROM links WHERE id = ? AND (visibility = 'public' OR username = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $linkId, $currentUser);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // The user is allowed to delete the link
        echo "true";
    } else {
        // The user is not allowed to delete the link
        echo "false";
    }

    $stmt->close();
} else {
    // Redirect to the index page if the request is not a POST request
    header("Location: user.php");
    exit();
}