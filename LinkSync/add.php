<?php
// Include database connection
include 'includes/config.php';

// Check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: auth/login.php");
    exit();
}

// Logout logic
if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to the login page
    header("Location: auth/login.php");
    exit();
}

// Retrieve the username from the session
$username = $_SESSION['username'];

// Fetch the email associated with the username from the users table
$sql_select_user = "SELECT email FROM users WHERE username='$username'";
$user_result = $conn->query($sql_select_user);

if ($user_result->num_rows > 0) {
    $user_row = $user_result->fetch_assoc();
    $email = $user_row['email'];
} else {
    // Redirect to login page if the username is not found
    header("Location: auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add New Link</title>
    <link rel="icon" href="icons/linksync.svg" type="image/svg+xml">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=San+Francisco">
    <link rel="stylesheet" type="text/css" href="css/add.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <a href="user.php">LinkSync</a>
            </div>
            <ul>
                <li><a href="user.php">Links</a></li>
                <li><a href="add.php" class="active">Add</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Add New Link</h2>
            <form id="addForm" method="POST">
                <div class="form-group">
                    <label for="url">URL:</label>
                    <input type="text" id="url" name="url" required>
                    <!-- Add button for autocomplete -->
                    <button type="button" id="autocompleteBtn">Autocomplete</button>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description">
                </div>
                <div class="form-group">
                    <label for="tags">Tags:</label>
                    <input type="text" id="tags" name="tags">
                </div>
                <div class="form-group">
                    <label for="visibility">Visibility:</label>
                    <select id="visibility" name="visibility" required>
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
                <!-- Include hidden input fields for username and email -->
                <input type="hidden" name="username" value="<?php echo $username; ?>">
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <button type="submit" id="submitBtn">Add Link</button>
            </form>
        </div>
    </main>

    <script>
        $(document).ready(function() {
            // Handle form submission using AJAX
            $('#addForm').submit(function(event) {
                event.preventDefault(); // Prevent default form submission

                $.ajax({
                    type: 'POST',
                    url: 'add_process.php',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        console.log('Response:', response);
                        if (response.trim() === 'duplicate') {
                            alert('Link with the same name or URL already exists.');
                        } else if (response.trim() === 'success') {
                            var addedUsername = '<?php echo urlencode($username); ?>';
                            console.log('Added username:', addedUsername);
                            if (confirm('Link added successfully with username: ' + addedUsername + '. Click OK to continue.')) {
                                // Redirect to add.php with success parameter
                                window.location.href = 'add.php?added=' + addedUsername;
                            }
                        } else {
                            alert('Error occurred while adding the link.');
                        }
                    },
                    error: function() {
                        alert('Error occurred while processing the request.');
                    }
                });
            });

            // Autocomplete button click event
            $('#autocompleteBtn').click(function() {
                // Trigger autocomplete functionality
                autoComplete();
            });

            // Auto-complete fields based on URL
            function autoComplete() {
                var url = $('#url').val();
                if (url) {
                    $.ajax({
                        type: 'POST',
                        url: 'auto.php',
                        data: {
                            url: url
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#name').val(data.name);
                            $('#description').val(data.description);
                            $('#tags').val(data.tags);
                        },
                        error: function() {
                            alert('Error occurred while fetching information.');
                        }
                    });
                } else {
                    // Clear the fields if the URL is empty
                    $('#name').val('');
                    $('#description').val('');
                    $('#tags').val('');
                }
            }
        });
    </script>
</body>

</html>