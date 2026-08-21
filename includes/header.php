<?php
/**
 * Common Header - TechFlow
 */
require_once __DIR__ . '/../auth.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? escape($pageTitle) . ' — ' . APP_NAME : APP_NAME; ?></title>
    <meta name="description" content="TechFlow — Where ideas flow. A modern platform for tech writers.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%237c3aed%22/><text y=%22.85em%22 font-size=%2266%22 x=%2250%22 text-anchor=%22middle%22>⚡</text></svg>">
    <script>
        (function() {
            var t = localStorage.getItem('tf-theme');
            if (t !== 'light') {
                t = 'dark';
                localStorage.setItem('tf-theme', 'dark');
            }
            document.documentElement.setAttribute('data-theme', t);
            if (t === 'light') {
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.remove('light');
            }
        })();
    </script>
</head>
<body>
    <script>
        (function() {
            var t = localStorage.getItem('tf-theme') || 'dark';
            if (t === 'light') {
                document.body.classList.add('light');
            } else {
                document.body.classList.remove('light');
            }
        })();
    </script>

    <nav class="navbar" id="main-nav">
        <div class="nav-container">

            <a href="index.php" class="nav-brand">
                <div class="nav-brand-logo">⚡</div>
                <span class="nav-brand-text">Tech<span>Flow</span></span>
            </a>

            <div class="nav-links-primary">
                <a href="index.php" class="nav-link">Home</a>
                <a href="index.php#articles" class="nav-link">Topics</a>
                <a href="index.php#articles" class="nav-link">Explore</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="contact.php" class="nav-link">Contact</a>
            </div>

            <div class="nav-search-wrap">
                <span class="nav-search-icon">🔍</span>
                <input type="text" class="nav-search" id="nav-search-input"
                       placeholder="Search articles..." autocomplete="off">
                <div class="search-results" id="search-results"></div>
            </div>

            <div class="nav-menu" id="nav-menu">
                <button class="nav-icon-btn" id="nav-search-icon-btn" title="Search" type="button">🔍</button>
                <button class="theme-toggle nav-icon-btn" id="theme-toggle" title="Toggle theme">🌙</button>

                <?php if (isLoggedIn()): ?>
                    <a href="create.php" class="nav-link">✍️ Write</a>
                    <div class="nav-user-badge">
                        <div class="nav-user-avatar"><?php echo strtoupper(substr(getCurrentUsername(), 0, 2)); ?></div>
                        <span class="nav-user-name"><?php echo escape(getCurrentUsername()); ?></span>
                    </div>
                    <a href="logout.php" class="btn-nav-outline" id="nav-logout-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link" id="nav-login-btn">Log In</a>
                    <a href="register.php" class="btn-nav-primary" id="nav-register-btn">Start Writing</a>
                <?php endif; ?>
            </div>

            <button class="nav-toggle" id="nav-toggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <main id="main-content">
        <?php
        $errorMsg = getFlashMessage('error');
        $successMsg = getFlashMessage('success');
        if ($errorMsg): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <p><?php echo escape($errorMsg); ?></p>
            </div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <p><?php echo escape($successMsg); ?></p>
            </div>
        <?php endif; ?>
