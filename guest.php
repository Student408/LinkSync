<?php
// Include database connection
include 'includes/config.php';

// Fetch public links from the database
$sql = "SELECT * FROM links WHERE visibility = 'public' ORDER BY clicks DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>LinkSync</title>
    <link rel="icon" href="icons/linksync.svg" type="image/svg+xml">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=San+Francisco">
    <link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
    <?php include 'index-header.php'; ?>

    <main>
        <div class="search-container">
            <input type="text" placeholder="Search..." id="searchInput">
            <button id="searchButton">
                <img src="icons/search.svg" alt="Search">
            </button>
        </div>

        <div class="link-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='link'>";
                    echo "<a href='track_click.php?id=" . $row['id'] . "' target='_blank'>" . $row['name'] . "</a>";
                    echo "<p>" . $row['description'] . "</p>";
                    if (!empty($row['tags'])) {
                        echo "<p class='tags'>Tags: " . $row['tags'] . "</p>";
                    }
                    echo "</div>"; // Close link
                }
            } else {
                echo "<p class='no-links'>No links found.</p>";
            }
            ?>
        </div>
    </main>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const links = document.querySelectorAll('.link');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            links.forEach(link => {
                const linkName = link.querySelector('a').textContent.toLowerCase();
                const linkDescription = link.querySelector('p').textContent.toLowerCase();
                const linkTags = link.querySelector('.tags') ? link.querySelector('.tags').textContent.toLowerCase() : '';

                if (linkName.includes(searchTerm) || linkDescription.includes(searchTerm) || linkTags.includes(searchTerm)) {
                    link.style.display = 'block';
                } else {
                    link.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>