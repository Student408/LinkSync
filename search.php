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


?>
<!DOCTYPE html>
<html>

<head>
    <title>Search Results</title>
    <link rel="icon" href="icons/linksync.svg" type="image/svg+xml">
    <link rel="stylesheet" type="text/css" href="css/search.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search_query').keyup(function() {
                var query = $(this).val();
                if (query !== '') {
                    $.ajax({
                        url: 'autocomplete.php',
                        method: 'POST',
                        data: {
                            query: query
                        },
                        success: function(data) {
                            $('#autocomplete').html(data);
                        }
                    });
                }
            });
        });
    </script>
</head>

<body>
    <header>
        <nav>
        <div class="logo">
                <a href="index.php">LinkSync</a>
                <a href="search.php">Search Plus</a>
            </div>
        </nav>

    <div class="search-container">
        <form action="" method="GET">
            <input type="text" name="query" id="search_query" placeholder="Search..." id="searchInput">

            <button type="submit" id="searchButton">
                <img src="icons/search.svg" alt="Search">
            </button>
            <div id="autocomplete"></div>
        </form>    
    </div>
    <!-- <div class="sorting">
        <h3>Sort by:</h3>
        <ul>
            <li><a href="?query=<?php echo urlencode($query); ?>&sort=relevance">Relevance</a></li>
            <li><a href="?query=<?php echo urlencode($query); ?>&sort=name">Name</a></li>
            <li><a href="?query=<?php echo urlencode($query); ?>&sort=date">Date</a></li>
        </ul>
    </div> -->
    
    <?php
    // Initialize $conditions as an empty array
    $conditions = [];

    // Initialize $query with an empty string
    $query = '';

    // Check if the search query is set and not empty
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        // Sanitize the search query to prevent SQL injection
        $query = $conn->real_escape_string($_GET['query']);

        // Break down the search query into individual keywords
        $keywords = explode(' ', $query);

        // Construct the SQL query with advanced search conditions
        $sql = "SELECT * FROM links WHERE ";
        foreach ($keywords as $keyword) {
            $conditions[] = "name LIKE '%$keyword%' OR description LIKE '%$keyword%' OR tags LIKE '%$keyword%'";
        }
        $sql .= implode(' OR ', $conditions);

        // Sorting
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
        switch ($sort) {
            case 'name':
                $sql .= " ORDER BY name";
                break;
            case 'date':
                $sql .= " ORDER BY created_at DESC";
                break;
            default:
                // Default to relevance sorting based on search term occurrence
                break;
        }

        // Pagination
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $results_per_page = 10; // Set a default value for $results_per_page
        $start_limit = ($page - 1) * $results_per_page;
        $sql .= " LIMIT $start_limit, $results_per_page";

        // Execute the SQL query
        $result = $conn->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                echo "<div class='search-results'>";
                while ($row = $result->fetch_assoc()) {
                    // Highlight search terms in name and description
                    $highlighted_name = highlightSearchTerms($row['name'], $keywords);
                    $highlighted_description = highlightSearchTerms($row['description'], $keywords);
                    echo "<a href='" . $row['url'] . "'><h3>$highlighted_name</h3><p>$highlighted_description</p></a>";
                }
                echo "</div>";
            } else {
                echo "No results found for '$query'.";
            }
        } else {
            echo "Error executing the search query: " . $conn->error;
        }
    } else {
        echo "Please enter a search query.";
    }

    // Function to highlight search terms
    function highlightSearchTerms($text, $keywords)
    {
        foreach ($keywords as $keyword) {
            $text = preg_replace("/\b($keyword)\b/i", "<span class='highlight'>$1</span>", $text);
        }
        return $text;
    }

    // Initialize $results_per_page with a non-zero value
    $results_per_page = 10;

    // Display pagination links
    $sqlCount = "SELECT COUNT(*) AS total FROM links";

    // Add conditions if search query is provided
    if (!empty($conditions)) {
        $sqlCount .= " WHERE " . implode(' OR ', $conditions);
    }

    $resultCount = $conn->query($sqlCount);

    if ($resultCount) {
        $row = $resultCount->fetch_assoc();
        $total_results = $row['total'];
        $total_pages = ceil($total_results / $results_per_page);

        echo "<div class='pagination'><h3>Pagination:</h3><ul>";
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<li><a href='?query=" . urlencode($query) . "&page=$i'>$i</a></li>";
        }
        echo "</ul></div>";
    } else {
        echo "Error counting total results: " . $conn->error;
    }
    ?>
    
</body>

</html>