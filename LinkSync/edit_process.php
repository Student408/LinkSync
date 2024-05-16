<?php
include 'includes/config.php';

$id = $_POST['id'];
$url = $_POST['url'];
$name = $_POST['name'];
$description = $_POST['description'];
$tags = $_POST['tags'];

$sql = "UPDATE links SET url='$url', name='$name', description='$description', tags='$tags' WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    header("Location: user.php");
} else {
    echo "Error updating link: " . $conn->error;
}

$conn->close();
?>
