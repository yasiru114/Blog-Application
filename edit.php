<?php
/**
 * Edit Existing Blog Post
 */
require_once 'config.php';
require_once 'auth.php';

// Require authentication
requireLogin();

$pageTitle = 'Edit Post';

$errors = [];
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$title = '';
$content = '';
$postUserId = 0;

// Fetch existing post
if ($postId > 0) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT id, user_id, title, content, image
        FROM blogPost
        WHERE id = ?
    ");
    $stmt->bind_param('i', $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    if (!$post) {
        redirectWithError('Post not found', 'index.php');
    }

    $title = $post['title'];
    $content = $post['content'];
    $postUserId = $post['user_id'];
    $currentImage = $post['image'];

    // Check ownership
    if (!isPostOwner($postUserId)) {
        redirectWithError('You do not have permission to edit this post', 'index.php');
    }
} else {
    redirectWithError('Invalid post ID', 'index.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Validation
    if (empty($title)) {
        $errors[] = 'Title is required';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must be less than 255 characters';
    }

    if (empty($content)) {
        $errors[] = 'Blog content is required';
    }

    // Handle image: remove current image, or replace with a new upload
    $newImage = $currentImage;
    if (empty($errors)) {
        if (!empty($_POST['remove_image'])) {
            deletePostImage($currentImage);
            $newImage = null;
        }

        $upload = handleImageUpload('image');
        if (!$upload['success']) {
            $errors[] = $upload['error'];
        } elseif ($upload['filename']) {
            deletePostImage($newImage);
            $newImage = $upload['filename'];
        }
    }

    // Update database
    if (empty($errors)) {
        $conn = getDBConnection();

        $stmt = $conn->prepare("
            UPDATE blogPost
            SET title = ?, content = ?, image = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param('sssii', $title, $content, $newImage, $postId, $postUserId);

        if ($stmt->execute()) {
            $stmt->close();
            redirectWithSuccess('Post updated successfully!', 'view.php?id=' . $postId);
        } else {
            $errors[] = 'Failed to update post. Please try again.';
            $stmt->close();
        }
    }
}

$pageTitle = 'Edit: ' . $title;
require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="editor-page">

        <div class="editor-header">
            <div class="page-eyebrow">Editing Article</div>
            <h1>Edit Article</h1>
            <p class="page-subtitle">Make changes and republish your article</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom:1.5rem;">
                <span class="alert-icon">⚠️</span>
                <div><?php foreach ($errors as $error): ?><p><?php echo escape($error); ?></p><?php endforeach; ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?php echo $postId; ?>" class="blog-form" id="blog-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">📝 Article Title</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?php echo escape($title); ?>"
                    placeholder="Enter a compelling title..."
                    required
                    maxlength="255"
                    autocomplete="off"
                >
            </div>

            <div class="form-group">
                <label for="image">🖼️ Featured Image <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>

                <?php if (!empty($currentImage)): ?>
                    <div id="current-image-wrap" style="margin-bottom:0.75rem;">
                        <img src="<?php echo escape(getPostImageUrl($currentImage)); ?>" alt="Current featured image" style="max-width:280px;border-radius:var(--r,12px);border:1px solid var(--border);display:block;margin-bottom:0.5rem;">
                        <label style="display:flex;align-items:center;gap:0.4rem;font-weight:400;font-size:0.85rem;color:var(--text-sub);">
                            <input type="checkbox" name="remove_image" value="1" id="remove-image-checkbox">
                            Remove current image
                        </label>
                    </div>
                <?php endif; ?>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/png,image/jpeg,image/gif,image/webp"
                >
                <img id="image-preview" src="" alt="" style="display:none;max-width:280px;margin-top:0.75rem;border-radius:var(--r,12px);border:1px solid var(--border);">
            </div>

            <div class="form-group">
                <label>✍️ Content</label>

                <div class="editor-tabs">
                    <button type="button" class="editor-tab active" data-tab="write" id="editor-tab-write">Write</button>
                    <button type="button" class="editor-tab" data-tab="preview" id="editor-tab-preview">Preview</button>
                </div>

                <div class="editor-container">
                    <div class="editor-pane active" id="write-pane">
                        <div class="editor-toolbar">
                            <button type="button" class="toolbar-btn" data-action="bold" title="Bold"><strong>B</strong></button>
                            <button type="button" class="toolbar-btn" data-action="italic" title="Italic"><em>I</em></button>
                            <button type="button" class="toolbar-btn" data-action="code" title="Code">&lt;/&gt;</button>
                            <div class="toolbar-sep"></div>
                            <button type="button" class="toolbar-btn" data-action="h2" title="Heading">H2</button>
                            <button type="button" class="toolbar-btn" data-action="link" title="Link">🔗</button>
                            <button type="button" class="toolbar-btn" data-action="list" title="List">≡</button>
                            <button type="button" class="toolbar-btn" data-action="quote" title="Quote">"</button>
                        </div>
                        <textarea
                            id="content"
                            name="content"
                            placeholder="Write your article here..."
                            required
                            rows="22"
                        ><?php echo escape($content); ?></textarea>
                        <div class="editor-help">
                            <code>**bold**</code>
                            <code>*italic*</code>
                            <code>`code`</code>
                            <code>## heading</code>
                            <code>- list</code>
                            <code>> quote</code>
                            <code>[link](url)</code>
                        </div>
                    </div>

                    <div class="editor-pane" id="preview-pane">
                        <div class="markdown-preview blog-single-content" id="preview-content">
                            <em style="color:var(--text-muted)">Preview will appear here...</em>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="save-btn">💾 Save Changes</button>
                <a href="view.php?id=<?php echo $postId; ?>" class="btn btn-secondary" id="cancel-edit-btn">Cancel</a>
                <a href="delete.php?id=<?php echo $postId; ?>"
                   class="btn btn-danger"
                   style="margin-left:auto;"
                   id="delete-article-btn"
                   onclick="return confirm('Delete this article permanently?');">🗑️ Delete Article</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>