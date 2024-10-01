<?php
// Include database connection
include 'includes/config.php';

function fetchKeywordsFromURL($url) {
    $html = @file_get_contents($url);
    if ($html === false) return [];

    $keywords = [];

    // Try to get keywords from meta tags
    preg_match('/<meta name="keywords" content="(.*?)"/', $html, $matches);
    if (isset($matches[1])) {
        $keywords = array_merge($keywords, explode(',', $matches[1]));
    }

    // Try to get keywords from Open Graph tags
    preg_match('/<meta property="og:keywords" content="(.*?)"/', $html, $matches);
    if (isset($matches[1])) {
        $keywords = array_merge($keywords, explode(',', $matches[1]));
    }

    // Extract words from title
    preg_match('/<title>(.*?)<\/title>/', $html, $matches);
    if (isset($matches[1])) {
        $keywords = array_merge($keywords, explode(' ', $matches[1]));
    }

    // Clean and return unique keywords
    $keywords = array_map('trim', $keywords);
    $keywords = array_filter($keywords, function($keyword) {
        return strlen($keyword) > 2;
    });
    return array_unique($keywords);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query = $_POST['query'];
    $url = isset($_POST['url']) ? $_POST['url'] : '';
    
    $suggestions = [];

    // Get suggestions from URL
    if (!empty($url)) {
        $urlKeywords = fetchKeywordsFromURL($url);
        foreach ($urlKeywords as $keyword) {
            if (stripos($keyword, $query) !== false) {
                $suggestions[] = $keyword;
            }
        }
    }

    // Get suggestions from database
    $sql = "SELECT DISTINCT tags FROM links WHERE tags LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $tags = explode(',', $row['tags']);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (stripos($tag, $query) !== false && !in_array($tag, $suggestions)) {
                $suggestions[] = $tag;
            }
        }
    }

    // Limit to top 5 suggestions
    $suggestions = array_slice($suggestions, 0, 5);

    // Return the suggestions as JSON
    header('Content-Type: application/json');
    echo json_encode($suggestions);
} else {
    // If not a POST request or query not set, return an empty array
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>