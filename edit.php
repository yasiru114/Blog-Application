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
$topicId = '';
$postUserId = 0;
$topics = getAllTopics();

// Fetch existing post
if ($postId > 0) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT id, user_id, title, content, topic_id, image
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
    $topicId = $post['topic_id'];
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
    $topicId = trim($_POST['topic_id'] ?? '');

    // Validation
    if (empty($title)) {
        $errors[] = 'Title is required';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must be less than 255 characters';
    }

    if (empty($content)) {
        $errors[] = 'Blog content is required';
    }

    // (cast every id to int - mysqli can return numeric columns as
    // strings depending on driver config, which broke strict in_array)
    $validTopicIds = array_map('intval', array_column($topics, 'id'));
    if ($topicId === '' || !in_array((int)$topicId, $validTopicIds, true)) {
        $errors[] = 'Please select a topic for your article';
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
        $topicIdInt = (int)$topicId;

        $stmt = $conn->prepare("
            UPDATE blogPost
            SET title = ?, content = ?, topic_id = ?, image = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param('ssisii', $title, $content, $topicIdInt, $newImage, $postId, $postUserId);

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
                <label for="topic_id">🏷️ Topic</label>
                <select id="topic_id" name="topic_id" required>
                    <option value="" disabled <?php echo empty($topicId) ? 'selected' : ''; ?>>Select a topic for your article...</option>
                    <?php foreach ($topics as $t): ?>
                        <option value="<?php echo (int)$t['id']; ?>" <?php echo ((string)$topicId === (string)$t['id']) ? 'selected' : ''; ?>>
                            <?php echo $t['icon']; ?> <?php echo escape($t['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form-hint">Choose the topic that best matches your article — it'll show up under Browse Topics.</span>
            </div>

            <div class="form-group">
                <label for="image">🖼️ Featured Image <span class="label-optional">(optional)</span></label>

                <div class="dropzone<?php echo !empty($currentImage) ? ' has-file' : ''; ?>" id="image-dropzone" tabindex="0" role="button" aria-label="Upload a featured image">
                    <input
                        type="file"
                        id="image"
                        name="image"
                        class="dropzone-input"
                        accept="image/png,image/jpeg,image/gif,image/webp"
                    >

                    <div class="dropzone-content" id="dropzone-content">
                        <div class="dropzone-orbit">
                            <span class="dropzone-orbit-ring"></span>
                            <span class="dropzone-orbit-ring dropzone-orbit-ring-2"></span>
                            <div class="dropzone-icon">⬆️</div>
                        </div>
                        <p class="dropzone-title">Drag &amp; drop your image here</p>
                        <p class="dropzone-sub">or <span class="dropzone-browse">browse files</span> — PNG, JPG, GIF or WEBP, up to 5MB</p>
                    </div>

                    <div class="dropzone-progress"><div class="dropzone-progress-bar" id="dropzone-progress-bar"></div></div>

                    <div class="dropzone-preview" id="dropzone-preview">
                        <img id="image-preview" src="<?php echo !empty($currentImage) ? escape(getPostImageUrl($currentImage)) : ''; ?>" alt="Selected image preview">
                        <div class="dropzone-preview-overlay">
                            <button type="button" class="dropzone-change-btn" id="dropzone-change-btn">🔄 Change</button>
                            <button type="button" class="dropzone-remove-btn" id="dropzone-remove-btn">🗑️ Remove</button>
                        </div>
                    </div>
                    <div class="dropzone-file-meta" id="dropzone-file-meta"><?php echo !empty($currentImage) ? '<span class="dz-dot"></span> Current featured image' : ''; ?></div>
                </div>
                <input type="hidden" name="remove_image" id="remove-image-checkbox" value="0">
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