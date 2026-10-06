<?php
// ==============================================================================
// StudentHub - Student Registration Page (pages/register.php)
// Simple, beginner-friendly PHP registration page
// ==============================================================================
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Registration - Student Hub</title>

    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/register.css">

</head>

<body>

    <!-- Header Frame -->
    <iframe src="header.html"
            class="header-frame"
            title="Student Hub Header"></iframe>


    <!-- Top Action Bar -->
    <div class="top-action-bar">

        <a href="../index.html" class="home-nav-btn">
            🏠 Home
        </a>

        <button id="theme-toggle"
                class="theme-btn-compact"
                type="button"
                aria-label="Switch to dark theme">
            🌙 Dark Mode
        </button>

    </div>


    <!-- Navigation Bar -->
    <nav id="main-nav" aria-label="Main navigation">

        <button id="menu-toggle"
                class="mobile-menu-btn"
                type="button"
                aria-expanded="false"
                aria-label="Toggle navigation">
            ☰ Menu
        </button>

        <div id="nav-links">

            <a href="dashboard.html">DASHBOARD</a>
            <a href="course.html">COURSES</a>
            <a href="event.html">EVENTS</a>
            <a href="faq.html">FAQ</a>
            <a href="contact.html">CONTACT</a>
            <a href="profile.html">PROFILE</a>
            <a href="register.php" class="active">REGISTER</a>
            <a href="login.html">LOGIN</a>

        </div>

    </nav>


    <!-- Page Title -->
    <h1 class="page-title">STUDENT REGISTRATION</h1>


    <!-- Registration Form -->
    <main class="register-main">

        <div class="register-box">

            <form id="registerForm" action="register_process.php" method="POST" novalidate>

                <div class="register-row">

                    <label for="name">
                        Full Name:
                    </label>

                    <div class="field-container">
                        <input type="text"
                               id="name"
                               name="name"
                               placeholder="Enter your full name"
                               required>
                        <span class="error-msg" id="nameError"></span>
                    </div>

                </div>


                <div class="register-row">

                    <label for="username">
                        Username:
                    </label>

                    <div class="field-container">
                        <input type="text"
                               id="username"
                               name="username"
                               placeholder="Choose a username (3-15 chars)"
                               required>
                        <span class="error-msg" id="usernameError"></span>
                    </div>

                </div>


                <div class="register-row">

                    <label for="email">
                        Email Address:
                    </label>

                    <div class="field-container">
                        <input type="email"
                               id="email"
                               name="email"
                               placeholder="student@gmail.com"
                               required>
                        <span class="error-msg" id="emailError"></span>
                    </div>

                </div>


                <div class="register-row">

                    <label for="password">
                        Password:
                    </label>

                    <div class="field-container">
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Min 8 chars (1 upper, 1 lower, 1 digit)"
                               required>
                        <span class="error-msg" id="passwordError"></span>
                    </div>

                </div>


                <div class="register-row">

                    <label for="confirmPassword">
                        Confirm Password:
                    </label>

                    <div class="field-container">
                        <input type="password"
                               id="confirmPassword"
                               name="confirm_password"
                               placeholder="Re-enter password"
                               required>
                        <span class="error-msg" id="confirmPasswordError"></span>
                    </div>

                </div>


                <button type="submit"
                        class="register-button">
                    Complete Registration
                </button>


                <p class="register-login">

                    Already have an account?

                    <a href="login.html">
                        Login here
                    </a>

                </p>

            </form>

        </div>

    </main>


    <!-- Portal Footer -->
    <footer>

        &copy; 2026 STUDENT HUB &bull;
        CHARUSAT University &bull;
        All Rights Reserved.

    </footer>


    <!-- Scripts -->
    <script src="../js/script.js" defer></script>
    <script src="../js/register.js" defer></script>

</body>

</html>
