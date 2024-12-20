<?php
// Include database connection
include 'includes/config.php';

// Check if the user is logged in
session_start();
if (isset($_SESSION['username'])) {
    // Redirect to the login page if the user is logged in
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icons/linksync.svg" type="image/svg+xml">
    <title>LinkSync</title>
    <link rel="stylesheet" href="css/land.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <!-- Include GSAP library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <h1 class="animate">Effortlessly Organize Your Links</h1>
            <p class="animate">LinkSync is your personal digital assistant, helping you manage your links with ease.</p>
            <button onclick="redirectToSignup()" class="animate">Get Started</button>
            <button onclick="redirectToguest()" class="animate">Guest</button>
        </div>
    </section>

    <section class="features">
        <h2 class="animate">Key Features</h2>
        <div class="feature-grid">
            <div class="feature animate">
                <img src="icons/easy.svg" alt="Easy Organization">
                <h3>Easy Organization</h3>
                <p>Easily organize your links into categories and folders for quick access.</p>
            </div>
            <div class="feature animate">
                <img src="icons/ux.svg" alt="Intuitive Interface">
                <h3>Intuitive Interface</h3>
                <p>User-friendly interface makes managing your links a breeze.</p>
            </div>
            <div class="feature animate">
                <img src="icons/power.svg" alt="Powerful Search">
                <h3>Powerful Search</h3>
                <p>Quickly find the link you need with our powerful search functionality.</p>
            </div>
        </div>
    </section>

    <section class="testimonial">
        <div class="testimonial-content animate">
            <blockquote>
                <p>"LinkSync has transformed the way I organize my online resources. It's a game-changer!"</p>
                <cite>- John Doe, Productivity Expert</cite>
            </blockquote>
        </div>
    </section>

    <section class="cta">
        <h2 class="animate">Ready to Get Started?</h2>
        <p class="animate">Sign up now to start organizing your links more efficiently!</p>
        <button onclick="redirectToSignup()" class="animate">Sign Up</button>
        <button onclick="redirectToguest()" class="animate">Guest</button>
    </section>

    <?php include 'footer.php'; ?>

    <script>
        gsap.registerPlugin(ScrollTrigger);

        function redirectToSignup() {
            window.location.href = "auth/signup.php";
        }
        function redirectToguest() {
            window.location.href = "guest.php";
        }

        gsap.utils.toArray(".animate").forEach(function(elem) {
            gsap.fromTo(
                elem,
                { opacity: 0, y: 50 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 1,
                    ease: "power2.out",
                    scrollTrigger: {
                        trigger: elem,
                        start: "top 80%",
                        end: "bottom 20%",
                        toggleActions: "play none none reverse"
                    }
                }
            );
        });
    </script>
</body>

</html>