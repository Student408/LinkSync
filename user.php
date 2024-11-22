<?php
// Include database connection
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

// Logout logic
if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to the login page
    header("Location: land.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>LinkSync</title>
    <link rel="icon" href="icons/linksync.svg" type="image/svg+xml">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=San+Francisco">
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <script>
        function confirmDelete(linkId) {
            var password = prompt("Enter your password:");

            if (password !== null) {
                // Check if the current user is allowed to delete the link
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "check_permission.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Check the response from the server
                        if (xhr.responseText === "true") {
                            var result = confirm("Are you sure you want to delete this link?");

                            if (result) {
                                // Send AJAX request to handle link deletion
                                var deleteXhr = new XMLHttpRequest();
                                deleteXhr.open("POST", "user_link_delete.php", true);
                                deleteXhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                deleteXhr.onreadystatechange = function() {
                                    if (deleteXhr.readyState === 4 && deleteXhr.status === 200) {
                                        // Handle response from the server
                                        alert(deleteXhr.responseText);
                                        // Reload the page to reflect the changes
                                        location.reload();
                                    }
                                };
                                deleteXhr.send("id=" + linkId + "&password=" + password);
                            }
                        } else {
                            alert("You are not authorized to delete this link.");
                        }
                    }
                };
                xhr.send("id=" + linkId);
            }
        }
    </script>
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
            $sql = "SELECT * FROM links WHERE (visibility = 'public' OR username = ?) ORDER BY clicks DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $currentUser);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='link'>";
                    echo "<a href='track_click.php?id=" . $row['id'] . "' target='_blank'>" . $row['name'] . "</a>";
                    echo "<p>" . $row['description'] . "</p>";
                    if (!empty($row['tags'])) {
                        echo "<p class='tags'>Tags: " . $row['tags'] . "</p>";
                    }
                    // echo "<p class='clicks'>Clicks: " . $row['clicks'] . "</p>";
                    echo "<div class='link-actions'>";
                    echo "<a href='edit.php?id=" . $row['id'] . "' class='edit-btn'>Edit</a>";
                    echo "<a href='#' onclick='confirmDelete(" . $row['id'] . ")' class='delete-btn'>Delete</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='no-links'>No links found.</p>";
            }
            ?>
        </div>
    </main>

    <script>
        // Theme switch functionality
        const themeSwitch = document.getElementById('themeSwitch');
        const body = document.body;

        themeSwitch.addEventListener('change', () => {
            body.classList.toggle('dark-mode');
        });

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