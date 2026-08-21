<?php
/**
 * Registration Page - TechFlow
 */
require_once 'config.php';
require_once 'auth.php';

$pageTitle = 'Create Account';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    if (empty($errors)) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $checkStmt = $conn->prepare("SELECT username, email FROM user WHERE username = ? OR email = ?");
            $checkStmt->bind_param('ss', $username, $email);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();
            if ($existing['username'] === $username) $errors[] = 'Username is already taken';
            if ($existing['email'] === $email) $errors[] = 'Email is already registered';
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $stmt = $conn->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $username, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            $newUserId = $stmt->insert_id;
            $stmt->close();
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            redirectWithSuccess('🎉 Welcome to TechFlow! Your account is ready.', 'index.php');
        } else {
            $errors[] = 'Registration failed. Please try again.';
            $stmt->close();
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-container" style="max-width:500px;">
        <div class="auth-card" id="register-card">

            <a href="index.php" class="auth-logo">
                <div class="auth-logo-icon">⚡</div>
                <span class="auth-logo-name">Tech<span>Flow</span></span>
            </a>

            <div class="auth-eyebrow">Create account</div>
            <h1>Join TechFlow</h1>
            <p class="auth-subtitle">Start writing and sharing your ideas with the world today</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error" style="margin-bottom:1.25rem;">
                    <span class="alert-icon">⚠️</span>
                    <div><?php foreach ($errors as $error): ?><p><?php echo escape($error); ?></p><?php endforeach; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" class="auth-form" id="register-form">

                <div class="form-group">
                    <label for="username">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Username
                    </label>
                    <div class="input-wrap">
                        <svg class="input-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo escape($_POST['username'] ?? ''); ?>"
                            placeholder="johndoe"
                            required
                            autocomplete="username"
                            minlength="3"
                            pattern="[a-zA-Z0-9_]+"
                        >
                    </div>
                    <small class="form-hint">Letters, numbers, and underscores only</small>
                </div>

                <div class="form-group">
                    <label for="email">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        Email Address
                    </label>
                    <div class="input-wrap">
                        <svg class="input-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo escape($_POST['email'] ?? ''); ?>"
                            placeholder="john@example.com"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Password
                    </label>
                    <div class="input-wrap has-eye">
                        <svg class="input-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Min 6 characters"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                        <button type="button" class="input-eye" aria-label="Toggle password">👁️</button>
                    </div>
                    <div class="pw-strength" id="password-strength-wrap">
                        <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                        <span class="strength-text" id="strength-text"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Confirm Password
                    </label>
                    <div class="input-wrap has-eye">
                        <svg class="input-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Repeat your password"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="input-eye" aria-label="Toggle password">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="register-submit-btn">
                    🚀 Create My Account
                </button>
            </form>

            <div class="auth-footer">
                Already have an account?
                <a href="login.php" id="go-login-link">Sign in →</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>