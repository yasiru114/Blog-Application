<?php
/**
 * Authentication and Authorization Helper Functions
 */

/**
 * Check if user is currently logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged-in user's ID
 * @return int|null
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get current logged-in user's username
 * @return string|null
 */
function getCurrentUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

/**
 * Get current logged-in user's role
 * @return string|null
 */
function getCurrentUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

/**
 * Redirect to login page if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit();
    }
}

/**
 * Check if the current user owns the given blog post
 * @param int $postUserId - The user_id from the blog post
 * @return bool
 */
function isPostOwner($postUserId) {
    return isLoggedIn() && getCurrentUserId() === (int)$postUserId;
}

/**
 * Redirect with error message
 * @param string $message
 * @param string $redirectUrl
 */
function redirectWithError($message, $redirectUrl = 'index.php') {
    $_SESSION['error_message'] = $message;
    header('Location: ' . $redirectUrl);
    exit();
}

/**
 * Redirect with success message
 * @param string $message
 * @param string $redirectUrl
 */
function redirectWithSuccess($message, $redirectUrl = 'index.php') {
    $_SESSION['success_message'] = $message;
    header('Location: ' . $redirectUrl);
    exit();
}

/**
 * Get and clear flash messages (error/success)
 * @param string $type - 'error' or 'success'
 * @return string|null
 */
function getFlashMessage($type) {
    $key = $type . '_message';
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

/**
 * Sanitize output for HTML display (prevent XSS)
 * @param string $text
 * @return string
 */
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Simple Markdown to HTML converter (basic implementation)
 * For production, consider using a library like Parsedown
 * @param string $text
 * @return string
 */
function markdownToHtml($text) {
    // Escape HTML first
    $html = escape($text);

    // Headers
    $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);

    // Bold
    $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);

    // Italic
    $html = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $html);

    // Inline code
    $html = preg_replace('/`(.+?)`/', '<code>$1</code>', $html);

    // Links
    $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>', $html);

    // Unordered lists
    $html = preg_replace('/^\- (.+)$/m', '<li>$1</li>', $html);
    $html = preg_replace('/(<li>.*<\/li>\n?)+/s', '<ul>$0</ul>', $html);

    // Ordered lists
    $html = preg_replace('/^\d+\. (.+)$/m', '<li>$1</li>', $html);
    $html = preg_replace('/(<li>.*<\/li>\n?)+/s', '<ol>$0</ol>', $html);

    // Line breaks (double newline = paragraph)
    $html = preg_replace('/\n\n+/', '</p><p>', $html);
    $html = '<p>' . $html . '</p>';

    // Clean up empty paragraphs
    $html = preg_replace('/<p>\s*<\/p>/', '', $html);
    $html = preg_replace('/<p>(<h[1-6]>)/', '$1', $html);
    $html = preg_replace('/(<\/h[1-6]>)<\/p>/', '$1', $html);
    $html = preg_replace('/<p>(<ul>)/', '$1', $html);
    $html = preg_replace('/(<\/ul>)<\/p>/', '$1', $html);
    $html = preg_replace('/<p>(<ol>)/', '$1', $html);
    $html = preg_replace('/(<\/ol>)<\/p>/', '$1', $html);

    return $html;
}

/**
 * Generate excerpt from content
 * @param string $content
 * @param int $length
 * @return string
 */
function generateExcerpt($content, $length = 150) {
    $text = strip_tags($content);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Handle a featured-image upload for a blog post.
 * Validates type/size, saves the file into /uploads with a unique name.
 *
 * @param string $inputName - name of the <input type="file"> field
 * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function handleImageUpload($inputName = 'image') {
    // No file selected is not an error - it's just optional
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'filename' => null, 'error' => null];
    }

    $file = $_FILES[$inputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'filename' => null, 'error' => 'Image upload failed. Please try again.'];
    }

    // 5MB max
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'filename' => null, 'error' => 'Image must be smaller than 5MB.'];
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    // Verify the real MIME type (don't trust the client-supplied one)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedTypes[$mimeType])) {
        return ['success' => false, 'filename' => null, 'error' => 'Only JPG, PNG, GIF, and WEBP images are allowed.'];
    }

    $ext = $allowedTypes[$mimeType];
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $uploadDir = __DIR__ . '/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'filename' => null, 'error' => 'Could not save the uploaded image.'];
    }

    return ['success' => true, 'filename' => $filename, 'error' => null];
}

/**
 * Delete a previously uploaded post image from disk, if present.
 * @param string|null $filename
 */
function deletePostImage($filename) {
    if (!empty($filename)) {
        $path = __DIR__ . '/uploads/' . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

/**
 * Get the public URL for a post's featured image, or null.
 * @param string|null $filename
 * @return string|null
 */
function getPostImageUrl($filename) {
    if (empty($filename)) {
        return null;
    }
    return 'uploads/' . rawurlencode($filename);
}
?>