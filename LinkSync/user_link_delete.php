<?php
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

// Check if the 'id' and 'password' parameters are set in the POST request
if (isset($_POST['id'], $_POST['password'])) {
    // Sanitize and validate input
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Retrieve the link data from the database using the 'id' parameter
    $sql_select_link = "SELECT * FROM links WHERE id='$id'";
    $result = $conn->query($sql_select_link);

    // Check if the link with the specified 'id' exists and belongs to the current user
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['username'] === $currentUser) {
            // Verify user password (you may need to adjust this based on your authentication method)
            $sql_select_user = "SELECT * FROM users WHERE username='$currentUser'";
            $user_result = $conn->query($sql_select_user);
            if ($user_result->num_rows > 0) {
                $user_row = $user_result->fetch_assoc();
                if (password_verify($password, $user_row['password'])) {
                    // Move the deleted link to the temporary table
                    $sql_move_link = "INSERT INTO deleted_links (url, name, description, tags, deleted_at, username, email, password) VALUES ('{$row['url']}', '{$row['name']}', '{$row['description']}', '{$row['tags']}', NOW(), '$currentUser', '{$user_row['email']}', '{$user_row['password']}')";
                    if ($conn->query($sql_move_link) === TRUE) {
                        // Delete the link from the main table
                        $sql_delete_link = "DELETE FROM links WHERE id='$id'";
                        if ($conn->query($sql_delete_link) === TRUE) {
                            echo "Link deleted successfully and information stored.";
                        } else {
                            echo "Error deleting link: " . $conn->error;
                        }
                    } else {
                        echo "Error moving link to deleted links: " . $conn->error;
                    }
                } else {
                    echo "Invalid password.";
                }
            } else {
                echo "User not found.";
            }
        } else {
            echo "You are not authorized to delete this link.";
        }
    } else {
        echo "Link not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>