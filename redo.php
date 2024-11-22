<?php
include 'includes/config.php';

// Retrieve the last deleted link from the temporary table
$sql = "SELECT * FROM links ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Move the link back to the temporary table
    $sql_move = "INSERT INTO deleted_links (url, name, description, tags) VALUES ('{$row['url']}', '{$row['name']}', '{$row['description']}', '{$row['tags']}')";
    if ($conn->query($sql_move) === TRUE) {
        // Delete the link from the main table
        $sql_delete = "DELETE FROM links WHERE id='{$row['id']}'";
        if ($conn->query($sql_delete) === TRUE) {
            // Redirect to index.php after successful redo
            header("Location: index.php");
            exit(); // Terminate script execution
        } else {
            // Display an error message if deletion fails
            echo "Error deleting link: " . $conn->error;
        }
    } else {
        // Display an error message if moving fails
        echo "Error moving link to deleted links: " . $conn->error;
    }
} else {
    // Redirect to index.php if no link is found in the main table
    header("Location: index.php");
    exit(); // Terminate script execution
}

$conn->close();
?>
