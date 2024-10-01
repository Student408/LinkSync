<?php
include 'includes/config.php';

// Retrieve form data
$url = mysqli_real_escape_string($conn, $_POST['url']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$tags = mysqli_real_escape_string($conn, $_POST['tags']);
$username = mysqli_real_escape_string($conn, $_POST['username']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$visibility = mysqli_real_escape_string($conn, $_POST['visibility']);

// Check if the name and URL already exist in the links table
$sql_check_duplicate = "SELECT * FROM links WHERE name='$name' OR url='$url'";
$result = $conn->query($sql_check_duplicate);

if ($result->num_rows > 0) {
    // Link with the same name or URL already exists
    echo 'duplicate';
} else {
    // Insert the new link into the links table
    $sql_insert_link = "INSERT INTO links (url, name, description, tags, username, email, added_date, visibility) VALUES ('$url', '$name', '$description', '$tags', '$username', '$email', NOW(), '$visibility')";

    if ($conn->query($sql_insert_link) === TRUE) {
        echo 'success';
    } else {
        echo "Error: " . $sql_insert_link . "<br>" . $conn->error;
    }
}

$conn->close();
?>