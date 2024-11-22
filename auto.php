<?php
// Function to fetch Open Graph data from URL
function fetchOpenGraphData($url) {
    // Make request to the URL
    $html = file_get_contents($url);

    // Extract Open Graph data
    $ogData = [];
    if ($html !== false) {
        // Use regular expressions to extract Open Graph tags
        preg_match_all('/<meta[^>]+property=[\'"]og:([^\'"]+)[\'"][^>]+content=[\'"]([^\'"]*)[\'"][^>]*>/i', $html, $matches);

        // Combine property names and values into an associative array
        foreach ($matches[1] as $key => $property) {
            $ogData[$property] = $matches[2][$key];
        }
    }

    return $ogData;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = isset($_POST['url']) ? $_POST['url'] : '';

    try {
        if (empty($url)) {
            throw new Exception("URL is empty");
        }

        // Fetch Open Graph data
        $ogData = fetchOpenGraphData($url);

        // Prepare the response
        $response = [
            'name' => isset($ogData['title']) ? $ogData['title'] : '',
            'description' => isset($ogData['description']) ? $ogData['description'] : '',
            'tags' => isset($ogData['keywords']) ? $ogData['keywords'] : '' // Assuming keywords are provided in Open Graph data
        ];

        // Convert the response to a JSON format and send it back to the client
        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (Exception $e) {
        // Log the error
        error_log('Error in auto.php: ' . $e->getMessage());

        // Send a more detailed error message to the client
        http_response_code(500);
        echo 'Error occurred while fetching information: ' . $e->getMessage();
    }
}
?>