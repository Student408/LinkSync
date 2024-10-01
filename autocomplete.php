<?php
include 'includes/config.php';

if(isset($_POST['query'])) {
    $query = $conn->real_escape_string($_POST['query']);
    $sql = "SELECT DISTINCT name FROM links WHERE name LIKE '%$query%' LIMIT 5"; // Adjust SQL query as needed
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div>" . $row['name'] . "</div>";
        }
    } else {
        echo "No suggestions";
    }
}
?>
