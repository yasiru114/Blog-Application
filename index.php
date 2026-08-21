<?php
/**
 * Home Page - TechFlow
 * Hero + Blog listing with sidebar
 */
require_once 'config.php';
require_once 'auth.php';

$pageTitle = 'Home';

$conn = getDBConnection();

// Optional topic filter, e.g. index.php?topic=web-development (set by
// clicking "Browse Topics" / "Explore Topic")
$activeTopicSlug = isset($_GET['topic']) ? trim($_GET['topic']) : '';
$activeTopic = $activeTopicSlug !== '' ? getTopicBySlug($activeTopicSlug) : null;

$postsSql = "
    SELECT bp.id, bp.title, bp.content, bp.image, bp.created_at, bp.updated_at,
           u.id as user_id, u.username,
           t.id as topic_id, t.name as topic_name, t.slug as topic_slug, t.icon as topic_icon, t.color as topic_color
    FROM blogPost bp
    JOIN user u ON bp.user_id = u.id
    LEFT JOIN topic t ON bp.topic_id = t.id
";
if ($activeTopic) {
    $postsSql .= " WHERE bp.topic_id = ? ORDER BY bp.created_at DESC";
    $stmt = $conn->prepare($postsSql);
    $stmt->bind_param('i', $activeTopic['id']);
} else {
    $postsSql .= " ORDER BY bp.created_at DESC";
    $stmt = $conn->prepare($postsSql);
}
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get stats (site-wide, not affected by the topic filter above)
$totalPostsResult = $conn->query("SELECT COUNT(*) as cnt FROM blogPost");
$totalPosts = $totalPostsResult ? (int)$totalPostsResult->fetch_assoc()['cnt'] : 0;
$authorsResult = $conn->query("SELECT COUNT(DISTINCT user_id) as cnt FROM blogPost");
$totalAuthors = $authorsResult ? $authorsResult->fetch_assoc()['cnt'] : 0;

// Topics for the "Browse Topics" sidebar - each with a live post count
$topicsWithCounts = getAllTopicsWithCounts();
$totalTopics = count($topicsWithCounts);

require_once 'includes/header.php';
?>

<!-- HERO SECTION -->
<section class="hero" id="hero">
    <canvas id="hero-canvas"></canvas>
    <div class="hero-bg-gradient"></div>
    <div class="hero-content">
        <div class="hero-left">
            <div class="hero-eyebrow">
                <span class="hero-eyebrow-dot"></span>
                A platform for tech writers
            </div>

            <h1 class="hero-title">
                Stories worth<br>
                <span class="gradient-text">reading & sharing.</span>
            </h1>

            <p class="hero-description">
                Build logs, tutorials, and honest write-ups from people actually doing the work — published openly so the next reader doesn't have to start from scratch.
            </p>

            <div class="hero-cta">
                <a href="#articles" class="btn btn-primary btn-lg" id="hero-cta-primary">
                    Read Articles
                </a>
                <a href="<?php echo isLoggedIn() ? 'create.php' : 'register.php'; ?>"
                   class="btn btn-secondary btn-lg" id="hero-cta-secondary">
                    <?php echo isLoggedIn() ? 'Write an Article' : 'Start Writing'; ?>
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-value" data-count="<?php echo $totalPosts; ?>"><?php echo $totalPosts; ?></span>
                    <span class="hero-stat-label">Articles</span>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <span class="hero-stat-value" data-count="<?php echo $totalAuthors; ?>"><?php echo $totalAuthors; ?></span>
                    <span class="hero-stat-label">Writers</span>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <span class="hero-stat-value" data-count="<?php echo $totalTopics; ?>"><?php echo $totalTopics; ?></span>
                    <span class="hero-stat-label">Topics</span>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <span class="hero-stat-value" data-count="100">100</span>
                    <span class="hero-stat-label">% Free</span>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="iso-scene">
                <div class="iso-grid-lines"></div>

                <svg class="iso-connectors" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <line x1="50" y1="50" x2="10" y2="14" class="iso-line iso-line-cloud" />
                    <line x1="50" y1="50" x2="80" y2="14" class="iso-line iso-line-shield" />
                    <line x1="50" y1="50" x2="10" y2="78" class="iso-line iso-line-db" />
                    <line x1="50" y1="50" x2="80" y2="78" class="iso-line iso-line-cpu" />
                    <circle r="1.3" class="iso-dot iso-dot-cloud">
                        <animateMotion dur="3s" repeatCount="indefinite" path="M50,50 L10,14" />
                    </circle>
                    <circle r="1.3" class="iso-dot iso-dot-shield">
                        <animateMotion dur="3.4s" repeatCount="indefinite" path="M50,50 L80,14" />
                    </circle>
                    <circle r="1.3" class="iso-dot iso-dot-db">
                        <animateMotion dur="3.8s" repeatCount="indefinite" path="M50,50 L10,78" />
                    </circle>
                    <circle r="1.3" class="iso-dot iso-dot-cpu">
                        <animateMotion dur="4.2s" repeatCount="indefinite" path="M50,50 L80,78" />
                    </circle>
                </svg>

                <div class="iso-cube">
                    <div class="iso-cube-layer iso-layer-3"></div>
                    <div class="iso-cube-layer iso-layer-2"></div>
                    <div class="iso-cube-layer iso-layer-1"></div>
                    <div class="iso-cube-beam"></div>
                    <div class="iso-cube-core">&lt;/&gt;</div>
                </div>

                <div class="iso-node iso-node-cloud" style="--nx:10%; --ny:14%;" title="Cloud Computing"><span>☁️</span></div>
                <div class="iso-node iso-node-shield" style="--nx:80%; --ny:14%;" title="Security"><span>🛡️</span></div>
                <div class="iso-node iso-node-db" style="--nx:10%; --ny:78%;" title="Data"><span>🗄️</span></div>
                <div class="iso-node iso-node-cpu" style="--nx:80%; --ny:78%;" title="Systems"><span>🧩</span></div>
            </div>
        </div>
    </div>
</section>

<!-- ARTICLES SECTION -->
<div class="main-content" id="articles">

    <?php if (empty($posts) && !$activeTopic): ?>
        <div class="empty-state reveal">
            <div class="empty-state-icon">📝</div>
            <h2>No articles yet</h2>
            <p>Be the first to share your knowledge with the TechFlow community!</p>
            <div class="empty-state-actions">
                <?php if (isLoggedIn()): ?>
                    <a href="create.php" class="btn btn-primary" id="empty-write-btn">Write First Article</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary" id="empty-register-btn">Join TechFlow</a>
                    <a href="login.php" class="btn btn-secondary" id="empty-login-btn">Login</a>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>

        <div class="page-header reveal">
            <div class="page-header-inner">
                <div>
                    <div class="page-eyebrow"><?php echo $activeTopic ? 'Topic' : 'Latest Articles'; ?></div>
                    <h1><?php echo $activeTopic ? ($activeTopic['icon'] . ' ' . escape($activeTopic['name'])) : 'Fresh from the community'; ?></h1>
                    <p class="page-subtitle"><?php echo count($posts); ?> article<?php echo count($posts) !== 1 ? 's' : ''; ?><?php echo $activeTopic ? ' in this topic' : ' published by our writers'; ?></p>
                </div>
                <?php if (isLoggedIn()): ?>
                    <a href="create.php" class="btn btn-primary" id="write-article-btn">
                        ✍️ Write Article
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary" id="join-btn">
                        🚀 Join & Write
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($activeTopic): ?>
            <div class="topic-filter-banner reveal">
                <span>🔎 Exploring articles tagged <strong><?php echo $activeTopic['icon']; ?> <?php echo escape($activeTopic['name']); ?></strong></span>
                <a href="index.php" class="topic-filter-clear">✕ Clear filter</a>
            </div>
        <?php endif; ?>

        <?php if (empty($posts) && $activeTopic): ?>
            <div class="empty-state reveal">
                <div class="empty-state-icon">🏷️</div>
                <h2>No articles in this topic yet</h2>
                <p>Be the first to publish an article under <?php echo escape($activeTopic['name']); ?>!</p>
                <div class="empty-state-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="create.php" class="btn btn-primary" id="empty-write-btn">Write an Article</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary" id="empty-register-btn">Join TechFlow</a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-secondary">← All Articles</a>
                </div>
            </div>
        <?php else: ?>

        <div class="blog-layout">
            <!-- Articles -->
            <div class="blog-grid" id="articles-grid">
                <?php
                foreach ($posts as $post):
                    $hasTopic = !empty($post['topic_id']);
                    $color = $hasTopic ? $post['topic_color'] : '#8b8b9e';
                    $topic = $hasTopic ? $post['topic_name'] : 'Uncategorized';
                    $topicIcon = $hasTopic ? $post['topic_icon'] : '🏷️';

                    $plainText = strip_tags(markdownToHtml($post['content']));
                    $excerpt = generateExcerpt($plainText, 180);
                    $wordCount = str_word_count($plainText);
                    $readTime = max(1, ceil($wordCount / 200));
                    $initials = strtoupper(substr($post['username'], 0, 2));
                    $formattedDate = date('M j, Y', strtotime($post['created_at']));
                    $isOwner = isLoggedIn() && isPostOwner($post['user_id']);
                ?>
                    <article class="blog-card reveal" id="article-<?php echo $post['id']; ?>">
                        <?php if (!empty($post['image'])): ?>
                            <a href="view.php?id=<?php echo $post['id']; ?>" class="blog-card-image-link">
                                <img class="blog-card-image" src="<?php echo escape(getPostImageUrl($post['image'])); ?>" alt="<?php echo escape($post['title']); ?>" loading="lazy">
                            </a>
                        <?php endif; ?>

                        <div class="blog-card-top">
                            <div class="blog-card-tags">
                                <?php if ($hasTopic): ?>
                                    <a href="index.php?topic=<?php echo urlencode($post['topic_slug']); ?>" class="tag" style="--tag-color:<?php echo $color; ?>"><?php echo $topicIcon; ?> <?php echo escape($topic); ?></a>
                                <?php else: ?>
                                    <span class="tag" style="--tag-color:<?php echo $color; ?>"><?php echo $topicIcon; ?> <?php echo escape($topic); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="blog-card-read-time">⏱ <?php echo $readTime; ?> min read</span>
                        </div>

                        <h2 class="blog-card-title">
                            <a href="view.php?id=<?php echo $post['id']; ?>" id="article-link-<?php echo $post['id']; ?>">
                                <?php echo escape($post['title']); ?>
                            </a>
                        </h2>

                        <p class="blog-card-excerpt"><?php echo escape($excerpt); ?></p>

                        <div class="blog-card-footer">
                            <div class="blog-card-author">
                                <div class="author-avatar"><?php echo $initials; ?></div>
                                <div class="author-info">
                                    <div class="author-name"><?php echo escape($post['username']); ?></div>
                                    <div class="author-date"><?php echo $formattedDate; ?></div>
                                </div>
                            </div>

                            <div class="blog-card-actions-row">
                                <button class="action-btn like-btn" id="like-<?php echo $post['id']; ?>" title="Like this article">
                                    ❤️
                                </button>
                                <button class="action-btn bookmark-btn" id="bookmark-<?php echo $post['id']; ?>" title="Bookmark">
                                    🔖
                                </button>
                                <a href="view.php?id=<?php echo $post['id']; ?>" class="action-btn" id="read-<?php echo $post['id']; ?>" title="Read article">
                                    Read →
                                </a>
                            </div>
                        </div>

                        <?php if ($isOwner): ?>
                            <div class="blog-card-owner-actions">
                                <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-ghost btn-sm" id="edit-<?php echo $post['id']; ?>">✏️ Edit</a>
                                <a href="delete.php?id=<?php echo $post['id']; ?>"
                                   class="btn btn-danger btn-sm" id="delete-<?php echo $post['id']; ?>"
                                   onclick="return confirm('Delete this article permanently?');">🗑️ Delete</a>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar" id="sidebar">

                <?php if (!isLoggedIn()): ?>
                <!-- Publish CTA -->
                <div class="sidebar-card reveal">
                    <div class="sidebar-card-header">
                        <div class="sidebar-card-header-icon">✍️</div>
                        <h3>Publish Your Work</h3>
                    </div>
                    <div class="sidebar-cta">
                        <div class="sidebar-cta-title">Share your knowledge</div>
                        <p class="sidebar-cta-desc">Write tech articles, tutorials, and engineering insights. Join hundreds of writers on TechFlow.</p>
                        <a href="register.php" class="btn btn-primary btn-block" id="sidebar-cta-btn">🚀 Get Started Free</a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Topics -->
                <div class="sidebar-card reveal">
                    <div class="sidebar-card-header">
                        <div class="sidebar-card-header-icon">🏷️</div>
                        <h3>Browse Topics</h3>
                    </div>
                    <div class="topic-list" id="topic-list">
                        <?php foreach ($topicsWithCounts as $t):
                            $isActiveTopic = $activeTopic && $activeTopic['slug'] === $t['slug'];
                        ?>
                            <a href="index.php?topic=<?php echo urlencode($t['slug']); ?>"
                               class="topic-item<?php echo $isActiveTopic ? ' active' : ''; ?>"
                               id="topic-<?php echo escape($t['slug']); ?>"
                               title="Explore <?php echo escape($t['name']); ?> articles">
                                <span class="topic-item-name">
                                    <span class="topic-item-dot" style="background:<?php echo $t['color']; ?>"></span>
                                    <?php echo $t['icon']; ?> <?php echo escape($t['name']); ?>
                                </span>
                                <span class="topic-item-count"><?php echo (int)$t['post_count']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Stats -->
                <div class="sidebar-card reveal">
                    <div class="sidebar-card-header">
                        <div class="sidebar-card-header-icon">📈</div>
                        <h3>Platform Stats</h3>
                    </div>
                    <div class="topic-list">
                        <div class="topic-item">
                            <span class="topic-item-name">📝 Articles Published</span>
                            <span class="topic-item-count" data-count="<?php echo $totalPosts; ?>"><?php echo $totalPosts; ?></span>
                        </div>
                        <div class="topic-item">
                            <span class="topic-item-name">✍️ Writers</span>
                            <span class="topic-item-count" data-count="<?php echo $totalAuthors; ?>"><?php echo $totalAuthors; ?></span>
                        </div>
                        <div class="topic-item">
                            <span class="topic-item-name">🏷️ Topics</span>
                            <span class="topic-item-count"><?php echo $totalTopics; ?></span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>