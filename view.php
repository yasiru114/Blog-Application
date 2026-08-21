<?php
/**
 * Single Blog Post View - TechFlow
 */
require_once 'config.php';
require_once 'auth.php';

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($postId <= 0) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT bp.id, bp.title, bp.content, bp.image, bp.created_at, bp.updated_at,
           u.id as user_id, u.username
    FROM blogPost bp
    JOIN user u ON bp.user_id = u.id
    WHERE bp.id = ?
");
$stmt->bind_param('i', $postId);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    $pageTitle = 'Post Not Found';
    require_once 'includes/header.php';
    ?>
    <div class="main-content">
        <div class="empty-state">
            <div class="empty-state-icon">🔍</div>
            <h2>Article Not Found</h2>
            <p>The article you're looking for doesn't exist or has been removed.</p>
            <div class="empty-state-actions">
                <a href="index.php" class="btn btn-primary">← Back to Home</a>
            </div>
        </div>
    </div>
    <?php
    require_once 'includes/footer.php';
    exit();
}

$pageTitle = $post['title'];
$isOwner = isLoggedIn() && isPostOwner($post['user_id']);
$renderedContent = markdownToHtml($post['content']);

$plainText = strip_tags($renderedContent);
$wordCount = str_word_count($plainText);
$readTime = max(1, ceil($wordCount / 200));
$initials = strtoupper(substr($post['username'], 0, 2));
$publishedDate = date('F j, Y', strtotime($post['created_at']));
$updatedDate = date('F j, Y', strtotime($post['updated_at']));

require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="blog-view-layout">

        <!-- Article -->
        <article class="blog-single" id="article-single">
            <?php if (!empty($post['image'])): ?>
                <div class="blog-single-hero has-image">
                    <img src="<?php echo escape(getPostImageUrl($post['image'])); ?>" alt="<?php echo escape($post['title']); ?>" style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
            <?php else: ?>
                <div class="blog-single-hero"></div>
            <?php endif; ?>
            <div class="blog-single-inner">

                <div class="blog-single-tags">
                    <span class="tag">📄 Article</span>
                    <span class="tag" style="background:rgba(251,191,36,0.15);border-color:rgba(251,191,36,0.2);color:#fbbf24;">⏱ <?php echo $readTime; ?> min read</span>
                </div>

                <header class="blog-single-header">
                    <h1><?php echo escape($post['title']); ?></h1>
                </header>

                <div class="blog-single-meta">
                    <div class="meta-item">
                        <span class="meta-label">Author</span>
                        <span class="meta-value">
                            <span style="display:flex;align-items:center;gap:0.4rem;">
                                <span style="width:22px;height:22px;background:linear-gradient(135deg,var(--accent),var(--accent-2));border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:700;color:white;"><?php echo $initials; ?></span>
                                <?php echo escape($post['username']); ?>
                            </span>
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Published</span>
                        <span class="meta-value"><?php echo $publishedDate; ?></span>
                    </div>
                    <?php if ($updatedDate !== $publishedDate): ?>
                    <div class="meta-item">
                        <span class="meta-label">Updated</span>
                        <span class="meta-value"><?php echo $updatedDate; ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="meta-item">
                        <span class="meta-label">Word Count</span>
                        <span class="meta-value"><?php echo number_format($wordCount); ?> words</span>
                    </div>
                </div>

                <?php if ($isOwner): ?>
                    <div class="blog-single-actions">
                        <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm" id="edit-article-btn">
                            ✏️ Edit Article
                        </a>
                        <a href="delete.php?id=<?php echo $post['id']; ?>"
                           class="btn btn-danger btn-sm" id="delete-article-btn"
                           onclick="return confirm('Are you sure you want to delete this article permanently?');">
                            🗑️ Delete
                        </a>
                    </div>
                <?php endif; ?>

                <div class="blog-single-content" id="article-content">
                    <?php echo $renderedContent; ?>
                </div>

                <footer class="blog-single-footer">
                    <a href="index.php" class="btn btn-secondary" id="back-to-home-btn">← All Articles</a>

                    <div style="display:flex;gap:0.5rem;margin-left:auto;">
                        <button class="action-btn like-btn" id="article-like-btn" title="Like this article">
                            ❤️ Like <span class="action-btn-count"><?php echo rand(5, 80); ?></span>
                        </button>
                        <button class="action-btn bookmark-btn" id="article-bookmark-btn" title="Bookmark">
                            🔖 Save
                        </button>
                        <button class="action-btn" id="article-share-btn" title="Share" onclick="navigator.clipboard?.writeText(location.href); window.TechFlow?.showToast('🔗 Link copied!', 'success');">
                            🔗 Share
                        </button>
                    </div>
                </footer>

                <!-- Comments Section -->
                <div class="comments-section" id="comments-section">
                    <div class="comments-section-header">
                        <h3>Comments</h3>
                        <span class="comments-count" id="comments-count">0</span>
                    </div>

                    <?php if (isLoggedIn()): ?>
                        <form class="comment-form" id="comment-form">
                            <textarea
                                placeholder="Share your thoughts on this article..."
                                rows="3"
                                maxlength="1000"
                                id="comment-textarea"
                                aria-label="Write a comment"
                            ></textarea>
                            <div class="comment-form-actions">
                                <button type="submit" class="btn btn-primary btn-sm" id="comment-submit-btn">
                                    💬 Post Comment
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div style="padding:1rem;background:var(--bg-glass);border:1px solid var(--border);border-radius:var(--radius);text-align:center;margin-bottom:1rem;">
                            <p style="color:var(--text-secondary);font-size:0.9rem;margin-bottom:0.75rem;">
                                Sign in to join the discussion
                            </p>
                            <div style="display:flex;gap:0.5rem;justify-content:center;">
                                <a href="login.php" class="btn btn-primary btn-sm" id="comment-login-btn">Login</a>
                                <a href="register.php" class="btn btn-secondary btn-sm" id="comment-register-btn">Sign Up</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="comments-list" id="comments-list"></div>
                </div>

            </div>
        </article>

        <!-- Sidebar -->
        <aside class="sidebar">

            <!-- Author card -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <div class="sidebar-card-header-icon">👤</div>
                    <h3>Written by</h3>
                </div>
                <div class="sidebar-cta">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,var(--accent),var(--accent-2));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:1.1rem;">
                            <?php echo $initials; ?>
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--text-primary);"><?php echo escape($post['username']); ?></div>
                            <div style="font-size:0.78rem;color:var(--text-muted);">TechFlow Author</div>
                        </div>
                    </div>
                    <p class="sidebar-cta-desc">Sharing knowledge and insights with the TechFlow community.</p>
                </div>
            </div>

            <!-- Topics -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <div class="sidebar-card-header-icon">🏷️</div>
                    <h3>Explore Topics</h3>
                </div>
                <div class="topic-list">
                    <?php
                    $topics = [
                        ['name' => 'Web Development', 'color' => '#f97316'],
                        ['name' => 'AI & ML', 'color' => '#fbbf24'],
                        ['name' => 'DevOps', 'color' => '#10b981'],
                        ['name' => 'Security', 'color' => '#f59e0b'],
                        ['name' => 'Systems', 'color' => '#ef4444'],
                        ['name' => 'Mobile', 'color' => '#fdba74'],
                    ];
                    foreach ($topics as $t): ?>
                        <div class="topic-item">
                            <span class="topic-item-name">
                                <span class="topic-item-dot" style="background:<?php echo $t['color']; ?>"></span>
                                <?php echo $t['name']; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Share card -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <div class="sidebar-card-header-icon">🔗</div>
                    <h3>Share Article</h3>
                </div>
                <div class="sidebar-cta">
                    <p class="sidebar-cta-desc">Found this article helpful? Share it with others!</p>
                    <button class="btn btn-primary btn-block" id="sidebar-share-btn"
                            onclick="navigator.clipboard?.writeText(location.href); window.TechFlow?.showToast('🔗 Link copied to clipboard!', 'success');">
                        🔗 Copy Link
                    </button>
                </div>
            </div>

        </aside>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>