<?php
/**
 * Home Page - TechFlow
 * Hero + Blog listing with sidebar
 */
require_once 'config.php';
require_once 'auth.php';

$pageTitle = 'Home';

$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT bp.id, bp.title, bp.content, bp.image, bp.created_at, bp.updated_at,
           u.id as user_id, u.username
    FROM blogPost bp
    JOIN user u ON bp.user_id = u.id
    ORDER BY bp.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get stats
$totalPosts = count($posts);
$authorsResult = $conn->query("SELECT COUNT(DISTINCT user_id) as cnt FROM blogPost");
$totalAuthors = $authorsResult ? $authorsResult->fetch_assoc()['cnt'] : 0;

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
                    <span class="hero-stat-value" data-count="9">9</span>
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
            <canvas id="network-canvas"></canvas>
        </div>
    </div>
</section>

<!-- ARTICLES SECTION -->
<div class="main-content" id="articles">

    <?php if (empty($posts)): ?>
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
                    <div class="page-eyebrow">Latest Articles</div>
                    <h1>Fresh from the community</h1>
                    <p class="page-subtitle"><?php echo $totalPosts; ?> article<?php echo $totalPosts !== 1 ? 's' : ''; ?> published by our writers</p>
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

        <div class="blog-layout">
            <!-- Articles -->
            <div class="blog-grid" id="articles-grid">
                <?php
                $topicColors = ['#f97316', '#fbbf24', '#f59e0b', '#10b981', '#ef4444', '#fdba74', '#34d399', '#f472b6'];
                $topicsList = ['Web Dev', 'DevOps', 'AI/ML', 'Security', 'Systems', 'Mobile', 'Data', 'Open Source'];
                $idx = 0;
                foreach ($posts as $post):
                    $color = $topicColors[$idx % count($topicColors)];
                    $topic = $topicsList[$idx % count($topicsList)];
                    $idx++;

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
                                <span class="tag" style="--tag-color:<?php echo $color; ?>"><?php echo $topic; ?></span>
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
                                    ❤️ <span class="action-btn-count"><?php echo rand(0, 48); ?></span>
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
                        <?php
                        $topics = [
                            ['name' => 'Web Development', 'color' => '#f97316', 'icon' => '🌐'],
                            ['name' => 'AI & Machine Learning', 'color' => '#fbbf24', 'icon' => '🤖'],
                            ['name' => 'DevOps & Cloud', 'color' => '#10b981', 'icon' => '☁️'],
                            ['name' => 'Security', 'color' => '#f59e0b', 'icon' => '🔒'],
                            ['name' => 'Systems', 'color' => '#ef4444', 'icon' => '⚙️'],
                            ['name' => 'Mobile', 'color' => '#fdba74', 'icon' => '📱'],
                            ['name' => 'Data Science', 'color' => '#34d399', 'icon' => '📊'],
                            ['name' => 'Open Source', 'color' => '#f472b6', 'icon' => '💻'],
                        ];
                        foreach ($topics as $t): ?>
                            <div class="topic-item" id="topic-<?php echo strtolower(str_replace([' ', '&'], ['_', ''], $t['name'])); ?>">
                                <span class="topic-item-name">
                                    <span class="topic-item-dot" style="background:<?php echo $t['color']; ?>"></span>
                                    <?php echo $t['icon']; ?> <?php echo $t['name']; ?>
                                </span>
                                <span class="topic-item-count"><?php echo rand(0, 12); ?></span>
                            </div>
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
                            <span class="topic-item-count">9</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>