<?php
/**
 * Contact Page - TechFlow
 */
require_once 'config.php';
require_once 'auth.php';

$pageTitle = 'Contact';
$errors = [];
$name = '';
$email = '';
$message = '';
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name)) $errors[] = 'Please enter your name.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (empty($message)) $errors[] = 'Please write a message.';

    if (empty($errors)) {
        // No mail server is configured in this environment, so the message is
        // simply acknowledged here. Wire this up to mail()/an API when deploying.
        $submitted = true;
        $name = $email = $message = '';
    }
}

require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="content-page">

        <div class="page-header">
            <div class="page-eyebrow">Get In Touch</div>
            <h1>Contact <span class="gradient-text">TechFlow</span></h1>
            <p class="page-subtitle">
                Questions, feedback, or partnership ideas — we'd love to hear from you.
            </p>
        </div>

        <?php if ($submitted): ?>
            <div class="alert alert-success" style="margin-bottom:1.5rem;">
                <span class="alert-icon">✅</span>
                <p>Thanks for reaching out! We'll get back to you soon.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom:1.5rem;">
                <span class="alert-icon">⚠️</span>
                <div><?php foreach ($errors as $error): ?><p><?php echo escape($error); ?></p><?php endforeach; ?></div>
            </div>
        <?php endif; ?>

        <div class="contact-layout">

            <form method="POST" action="contact.php" class="blog-form">
                <div class="form-group">
                    <label for="name">👤 Your Name</label>
                    <input type="text" id="name" name="name" value="<?php echo escape($name); ?>" placeholder="Jane Doe" required>
                </div>
                <div class="form-group">
                    <label for="email">📧 Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo escape($email); ?>" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label for="message">💬 Message</label>
                    <textarea id="message" name="message" rows="6" placeholder="How can we help?" required><?php echo escape($message); ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">📨 Send Message</button>
                </div>
            </form>

            <div class="contact-info-card">
                <div class="contact-info-row">
                    <div class="contact-info-icon">📧</div>
                    <div>
                        <h4>Email</h4>
                        <a href="mailto:hello@techflow.dev">hello@techflow.dev</a>
                    </div>
                </div>
                <div class="contact-info-row">
                    <div class="contact-info-icon">✍️</div>
                    <div>
                        <h4>Write for us</h4>
                        <p>Create a free account and publish directly — no pitch needed.</p>
                    </div>
                </div>
                <div class="contact-info-row">
                    <div class="contact-info-icon">🐛</div>
                    <div>
                        <h4>Found a bug?</h4>
                        <p>Let us know what happened and we'll look into it.</p>
                    </div>
                </div>
                <div class="contact-info-row">
                    <div class="contact-info-icon">⏱️</div>
                    <div>
                        <h4>Response time</h4>
                        <p>We typically reply within 1–2 business days.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
