<?php
include 'includes/config.php';

// Retrieve the last deleted link from the temporary table
$sql = "SELECT * FROM deleted_links ORDER BY deleted_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Move the link back to the main table
    $sql_move = "INSERT INTO links (url, name, description, tags) VALUES ('{$row['url']}', '{$row['name']}', '{$row['description']}', '{$row['tags']}')";
    if ($conn->query($sql_move) === TRUE) {
        // Delete the link from the temporary table
        $sql_delete = "DELETE FROM deleted_links WHERE id='{$row['id']}'";
        if ($conn->query($sql_delete) === TRUE) {
            // Redirect to user.php after successful undo
            header("Location: user.php");
            exit(); // Terminate script execution
        } else {
            // Display an error message if deletion from temporary table fails
            echo "Error deleting link from deleted links: " . $conn->error;
        }
    } else {
        // Display an error message if moving fails
        echo "Error moving link back to links: " . $conn->error;
    }
} else {
    // Redirect to user.php if no link is found in the temporary table
    header("Location: user.php");
    exit(); // Terminate script execution
}

$conn->close();
?>
