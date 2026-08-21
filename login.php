<?php
/**
 * Login Page - TechFlow
 */
require_once 'config.php';
require_once 'auth.php';

$pageTitle = 'Login';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($usernameOrEmail)) $errors[] = 'Username or email is required';
    if (empty($password)) $errors[] = 'Password is required';

    if (empty($errors)) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM user WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit();
        } else {
            $errors[] = 'Invalid username/email or password';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card" id="login-card">

            <a href="index.php" class="auth-logo">
                <div class="auth-logo-icon">⚡</div>
                <span class="auth-logo-name">Tech<span>Flow</span></span>
            </a>

            <div class="auth-eyebrow">Welcome back</div>
            <h1>Sign in to TechFlow</h1>
            <p class="auth-subtitle">Continue sharing your ideas with the world</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error" style="margin-bottom:1.25rem;">
                    <span class="alert-icon">⚠️</span>
                    <div><?php foreach ($errors as $error): ?><p><?php echo escape($error); ?></p><?php endforeach; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="auth-form" id="login-form">
                <div class="form-group">
                    <label for="username_or_email">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Username or Email
                    </label>
                    <div class="input-wrap">
                        <svg class="input-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <input
                            type="text"
                            id="username_or_email"
                            name="username_or_email"
                            value="<?php echo escape($_POST['username_or_email'] ?? ''); ?>"
                            placeholder="johndoe or john@example.com"
                            required
                            autocomplete="username"
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
                            placeholder="Your password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="input-eye" aria-label="Toggle password visibility">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="login-submit-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Sign In
                </button>
            </form>

            <div class="auth-footer">
                Don't have an account?
                <a href="register.php" id="go-register-link">Create one free →</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>