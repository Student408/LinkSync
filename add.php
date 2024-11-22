<?php

// Include database connection

include 'includes/config.php';



// Check if the user is logged in

session_start();

if (!isset($_SESSION['username'])) {

    header("Location: auth/login.php");

    exit();

}



// Logout logic

if (isset($_POST['logout'])) {

    session_destroy();

    header("Location: auth/login.php");

    exit();

}



// Retrieve user info securely

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT email FROM users WHERE username=?");

$stmt->bind_param("s", $username);

$stmt->execute();

$user_result = $stmt->get_result();



if ($user_result->num_rows > 0) {

    $user_row = $user_result->fetch_assoc();

    $email = $user_row['email'];

} else {

    header("Location: auth/login.php");

    exit();

}

?>



<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <title>Add New Link</title>

    <link rel="icon" href="icons/linksync.svg" type="image/svg+xml">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=San+Francisco">

    <link rel="stylesheet" type="text/css" href="css/add.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" async></script>

    <style>

        /* Optimized styling */

        #tagSuggestions {

            position: absolute;

            border: 1px solid #ccc;

            max-height: 150px;

            overflow-y: auto;

            display: none;

            background-color: white;

            z-index: 1000;

        }

        #tagSuggestions div {

            padding: 5px;

            cursor: pointer;

        }

        #tagSuggestions div:hover {

            background-color: #f0f0f0;

        }

    </style>

</head>



<body>

    <header>

        <nav>

            <div class="logo">

                <a href="index.php">LinkSync</a>

            </div>

            <ul>

                <li><a href="index.php">Links</a></li>

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

                    <div id="tagSuggestions"></div>

                </div>

                <div class="form-group">

                    <label for="visibility">Visibility:</label>

                    <select id="visibility" name="visibility" required>

                        <option value="public">Public</option>

                        <option value="private">Private</option>

                    </select>

                </div>

                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">

                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                <button type="submit" id="submitBtn">Add Link</button>

            </form>

        </div>

    </main>



    <script>

        $(document).ready(function() {

            var $tagInput = $('#tags');

            var $tagSuggestions = $('#tagSuggestions');



            $('#addForm').submit(function(event) {

                event.preventDefault();



                $.ajax({

                    type: 'POST',

                    url: 'add_process.php',

                    data: $(this).serialize(),

                    success: function(response) {

                        if (response.trim() === 'duplicate') {

                            alert('Link with the same name or URL already exists.');

                        } else if (response.trim() === 'success') {

                            if (confirm('Link added successfully. Click OK to continue.')) {

                                window.location.href = 'add.php?added=' + encodeURIComponent('<?php echo $username; ?>');

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



            $('#autocompleteBtn').click(autoComplete);



            function autoComplete() {

                var url = $('#url').val();

                if (url) {

                    $.ajax({

                        type: 'POST',

                        url: 'auto.php',

                        data: { url: url },

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

                    $('#name, #description, #tags').val('');

                }

            }



            $tagInput.on('input', function() {

                var query = $(this).val().split(',').pop().trim();

                if (query.length > 0) {

                    $.ajax({

                        url: 'get_tag_suggestions.php',

                        method: 'POST',

                        data: { query: query, url: $('#url').val() },

                        success: function(data) {

                            $tagSuggestions.empty().show();

                            data.forEach(function(tag) {

                                $tagSuggestions.append('<div>' + tag + '</div>');

                            });

                        }

                    });

                } else {

                    $tagSuggestions.hide();

                }

            });



            $tagSuggestions.on('click', 'div', function() {

                var selectedTag = $(this).text();

                var currentTags = $tagInput.val().split(',').map(tag => tag.trim());

                currentTags.pop();

                currentTags.push(selectedTag);

                $tagInput.val(currentTags.join(', '));

                $tagSuggestions.hide();

                $tagInput.focus().setSelectionRange($tagInput.val().length, $tagInput.val().length);

            });



            $(document).on('click', function(e) {

                if (!$(e.target).closest('#tags, #tagSuggestions').length) {

                    $tagSuggestions.hide();

                }

            });

        });

    </script>

</body>



</html>

