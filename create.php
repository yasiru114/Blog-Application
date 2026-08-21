<?php
/**
 * Create New Blog Post - TechFlow
 */
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$pageTitle = 'Write Article';

$errors = [];
$title = '';
$content = '';
$topicId = '';
$topics = getAllTopics();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $topicId = trim($_POST['topic_id'] ?? '');

    if (empty($title)) {
        $errors[] = 'Title is required';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must be less than 255 characters';
    }

    if (empty($content)) {
        $errors[] = 'Blog content is required';
    }

    // Validate the chosen topic actually exists
    // (cast every id to int - mysqli can return numeric columns as
    // strings depending on driver config, which broke strict in_array)
    $validTopicIds = array_map('intval', array_column($topics, 'id'));
    if ($topicId === '' || !in_array((int)$topicId, $validTopicIds, true)) {
        $errors[] = 'Please select a topic for your article';
    }

    $uploadedImage = null;
    if (empty($errors)) {
        $upload = handleImageUpload('image');
        if (!$upload['success']) {
            $errors[] = $upload['error'];
        } else {
            $uploadedImage = $upload['filename'];
        }
    }

    if (empty($errors)) {
        $conn = getDBConnection();
        $userId = getCurrentUserId();
        $topicIdInt = (int)$topicId;
        $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content, topic_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issis', $userId, $title, $content, $topicIdInt, $uploadedImage);

        if ($stmt->execute()) {
            $newPostId = $stmt->insert_id;
            $stmt->close();
            redirectWithSuccess('✅ Article published successfully!', 'view.php?id=' . $newPostId);
        } else {
            $errors[] = 'Failed to publish article. Please try again.';
            $stmt->close();
            deletePostImage($uploadedImage);
        }
    }
}

require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="editor-page">

        <div class="editor-header">
            <div class="page-eyebrow">New Article</div>
            <h1>Write & Publish</h1>
            <p class="page-subtitle">Share your knowledge with the TechFlow community</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom:1.5rem;">
                <span class="alert-icon">⚠️</span>
                <div><?php foreach ($errors as $error): ?><p><?php echo escape($error); ?></p><?php endforeach; ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="create.php" class="blog-form" id="blog-form" enctype="multipart/form-data">

            <div class="form-group">
                <label for="title">
                    📝 Article Title
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?php echo escape($title); ?>"
                    placeholder="Write a compelling title that grabs attention..."
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

                <div class="dropzone" id="image-dropzone" tabindex="0" role="button" aria-label="Upload a featured image">
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
                        <img id="image-preview" src="" alt="Selected image preview">
                        <div class="dropzone-preview-overlay">
                            <button type="button" class="dropzone-change-btn" id="dropzone-change-btn">🔄 Change</button>
                            <button type="button" class="dropzone-remove-btn" id="dropzone-remove-btn">🗑️ Remove</button>
                        </div>
                    </div>
                    <div class="dropzone-file-meta" id="dropzone-file-meta"></div>
                </div>
            </div>

            <div class="form-group">
                <label>✍️ Content</label>

                <div class="editor-tabs">
                    <button type="button" class="editor-tab active" data-tab="write" id="editor-tab-write">Write</button>
                    <button type="button" class="editor-tab" data-tab="preview" id="editor-tab-preview">Preview</button>
                </div>

                <div class="editor-container">
                    <!-- Write Tab -->
                    <div class="editor-pane active" id="write-pane">
                        <div class="editor-toolbar">
                            <button type="button" class="toolbar-btn" data-action="bold" title="Bold"><strong>B</strong></button>
                            <button type="button" class="toolbar-btn" data-action="italic" title="Italic"><em>I</em></button>
                            <button type="button" class="toolbar-btn" data-action="code" title="Inline code">&lt;/&gt;</button>
                            <div class="toolbar-sep"></div>
                            <button type="button" class="toolbar-btn" data-action="h2" title="Heading">H2</button>
                            <button type="button" class="toolbar-btn" data-action="link" title="Link">🔗</button>
                            <button type="button" class="toolbar-btn" data-action="list" title="List">≡</button>
                            <button type="button" class="toolbar-btn" data-action="quote" title="Blockquote">"</button>
                        </div>
                        <textarea
                            id="content"
                            name="content"
                            placeholder="Start writing your article here...&#10;&#10;You can use Markdown:&#10;**bold** *italic* `code`&#10;## Heading&#10;- List item&#10;> Blockquote&#10;[link text](url)"
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

                    <!-- Preview Tab -->
                    <div class="editor-pane" id="preview-pane">
                        <div class="markdown-preview blog-single-content" id="preview-content">
                            <em style="color:var(--text-muted)">Preview will appear here...</em>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="publish-btn">
                    🚀 Publish Article
                </button>
                <a href="index.php" class="btn btn-secondary" id="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>