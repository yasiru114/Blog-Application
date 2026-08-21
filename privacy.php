<?php
require_once 'config.php';
require_once 'auth.php';
$pageTitle = 'Privacy Policy';
require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="content-page">
        <div class="page-header">
            <div class="page-eyebrow">Legal</div>
            <h1>Privacy Policy</h1>
            <p class="page-subtitle">Last updated: <?php echo date('F Y'); ?></p>
        </div>

        <div class="content-section">
            <h2>What We Collect</h2>
            <p>When you create a TechFlow account we store your username, email address, and a securely
            hashed password. When you publish an article, we store the title, content, and optional
            featured image you upload. We don't collect more than what's needed to run the platform.</p>
        </div>

        <div class="content-section">
            <h2>How We Use It</h2>
            <p>Your account details are used to let you log in, write, and manage your own articles.
            Published articles are public by design — that's the point of a blogging platform — so
            anything you publish is visible to anyone who visits TechFlow.</p>
        </div>

        <div class="content-section">
            <h2>Cookies & Sessions</h2>
            <p>We use a session cookie to keep you signed in while you browse. We don't use third-party
            advertising or tracking cookies.</p>
        </div>

        <div class="content-section">
            <h2>Your Choices</h2>
            <p>You can edit or delete any article you've published at any time from your account. If
            you'd like your account and data removed entirely, contact us and we'll take care of it.</p>
        </div>

        <div class="content-section">
            <h2>Contact</h2>
            <p>Questions about this policy? <a href="contact.php">Get in touch</a> and we'll respond as
            quickly as we can.</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
