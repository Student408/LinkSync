$(document).ready(function() {
    // Handle username and email submission
    $('#signup-form').on('submit', function(e) {
        e.preventDefault();
        var username = $('#username').val();
        var email = $('#email').val();

        $.ajax({
            type: 'POST',
            url: 'signup.php',
            data: { username: username, email: email },
            success: function(response) {
                response = response.trim(); // Trim the response
                if (response !== '') {
                    try {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        } else {
                            alert(data.message);
                        }
                    } catch (e) {
                        console.error('Error parsing JSON response:', e);
                        alert('An error occurred while processing the server response.');
                    }
                } else {
                    console.error('Empty response from the server.');
                    alert('An error occurred while processing the server response.');
                }
            },
            error: function() {
                alert('An error occurred');
            }
        });
    });
});