<?php

include 'includes/config.php';



if (isset($_GET['id'])) {

    $id = $_GET['id'];

    

    // Update click count

    $sql = "UPDATE links SET clicks = clicks + 1 WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    

    // Get the URL

    $sql = "SELECT url FROM links WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    

    if ($row = $result->fetch_assoc()) {

        $url = $row['url'];

        // Redirect to the URL

        header("Location: " . $url);

        exit();

    }

}



// If something went wrong, redirect back to the main page

header("Location: index.php");

exit();

?>