<?php
include 'includes/config.php';

// Check if the 'id', 'username', and 'password' parameters are set in the POST request
if (isset($_POST['id'], $_POST['username'], $_POST['password'])) {
    // Sanitize and validate input
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Retrieve the link data from the database using the 'id' parameter
    $sql_select_link = "SELECT * FROM links WHERE id='$id'";
    $result = $conn->query($sql_select_link);

    // Check if the link with the specified 'id' exists
    if ($result->num_rows > 0) {
        // Fetch the link data
        $row = $result->fetch_assoc();

        // Verify user credentials (you may need to adjust this based on your authentication method)
        $sql_select_user = "SELECT * FROM users WHERE username='$username'";
        $user_result = $conn->query($sql_select_user);

        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user_row['password'])) {
                // Move the deleted link to the temporary table
                $sql_move_link = "INSERT INTO deleted_links (url, name, description, tags, deleted_at, username, email, password) VALUES ('{$row['url']}', '{$row['name']}', '{$row['description']}', '{$row['tags']}', NOW(), '$username', '{$user_row['email']}', '{$user_row['password']}')";
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
                echo "Invalid username or password.";
            }
        } else {
            echo "User not found.";
        }
    } else {
        echo "Link not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
