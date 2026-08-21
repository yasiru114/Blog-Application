<?php
require_once 'config.php';
require_once 'auth.php';
$pageTitle = 'Terms of Service';
require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="content-page">
        <div class="page-header">
            <div class="page-eyebrow">Legal</div>
            <h1>Terms of Service</h1>
            <p class="page-subtitle">Last updated: <?php echo date('F Y'); ?></p>
        </div>

        <div class="content-section">
            <h2>Using TechFlow</h2>
            <p>By creating an account you agree to use TechFlow respectfully: no spam, no harassment,
            and no content that infringes on someone else's rights. Articles should be your own work
            or properly credited.</p>
        </div>

        <div class="content-section">
            <h2>Your Content</h2>
            <p>You own everything you publish on TechFlow. By posting an article you give us permission
            to display it on the platform so other readers can find and read it. You can edit or remove
            your articles at any time.</p>
        </div>

        <div class="content-section">
            <h2>Account Responsibility</h2>
            <p>You're responsible for keeping your login credentials secure and for anything published
            under your account. Let us know right away if you think your account has been compromised.</p>
        </div>

        <div class="content-section">
            <h2>Moderation</h2>
            <p>We reserve the right to remove content that violates these terms or applicable law, and
            to suspend accounts that repeatedly do so.</p>
        </div>

        <div class="content-section">
            <h2>Changes</h2>
            <p>We may update these terms as TechFlow evolves. Continued use of the platform after a
            change means you accept the updated terms.</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
