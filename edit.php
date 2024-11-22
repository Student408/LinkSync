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

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    // Sanitize the 'id' parameter to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Retrieve the link data from the database using the 'id' parameter
    $sql = "SELECT * FROM links WHERE id='$id'";
    $result = $conn->query($sql);

    // Check if the link with the specified 'id' exists
    if ($result->num_rows > 0) {
        // Fetch the link data
        $row = $result->fetch_assoc();

        // Check if the current user owns the link
        if ($row['username'] !== $currentUser) {
            // Show a popup indicating that the user cannot edit the link
            echo "<script>alert('You are not authorized to edit this link.'); window.location.href = 'index.php';</script>";
            exit(); // Terminate script execution
        }
    } else {
        // Redirect to index.php if no link with the specified 'id' is found
        header("Location: index.php");
        exit(); // Terminate script execution
    }
} else {
    // Redirect to index.php if 'id' parameter is not set in the URL
    header("Location: index.php");
    exit(); // Terminate script execution
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Link</title>
    <link rel="icon" href="icons/linksync.svg" type="image/svg+xml">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=San+Francisco">
    <link rel="stylesheet" type="text/css" href="css/edit.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">LinkSync</a>
            </div>
            <ul>
                <li><a href="index.php">Links</a></li>
                <li><a href="add.php">Add</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Edit Link</h2>
            <form action="edit_process.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <div class="form-group">
                    <label for="url">URL:</label>
                    <input type="text" id="url" name="url" value="<?php echo $row['url']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description" value="<?php echo $row['description']; ?>">
                </div>
                <div class="form-group">
                    <label for="tags">Tags:</label>
                    <input type="text" id="tags" name="tags" value="<?php echo $row['tags']; ?>">
                </div>
                <button type="submit">Update Link</button>
            </form>
        </div>
    </main>
</body>
</html>