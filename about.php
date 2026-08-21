<?php
/**
 * About Page - TechFlow
 */
require_once 'config.php';
require_once 'auth.php';

$pageTitle = 'About';

// Pull a few live numbers from the database so this page reflects the real community
$totalPosts = 0;
$totalAuthors = 0;
$totalWords = 0;
$oldestYear = date('Y');

try {
    $conn = getDBConnection();

    if ($res = $conn->query("SELECT COUNT(*) AS c FROM blogPost")) {
        $totalPosts = (int) ($res->fetch_assoc()['c'] ?? 0);
    }
    if ($res = $conn->query("SELECT COUNT(DISTINCT user_id) AS c FROM blogPost")) {
        $totalAuthors = (int) ($res->fetch_assoc()['c'] ?? 0);
    }
    if ($res = $conn->query("SELECT content FROM blogPost")) {
        while ($row = $res->fetch_assoc()) {
            $totalWords += str_word_count(strip_tags($row['content']));
        }
    }
    if ($res = $conn->query("SELECT MIN(created_at) AS d FROM blogPost")) {
        $d = $res->fetch_assoc()['d'] ?? null;
        if ($d) $oldestYear = date('Y', strtotime($d));
    }
} catch (Throwable $e) {
    // If the DB isn't reachable, the page still renders with sensible defaults
}

require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="content-page">

        <div class="page-header">
            <div class="page-eyebrow">About Us</div>
            <h1>Where ideas <span class="gradient-text">flow</span></h1>
            <p class="page-subtitle">
                TechFlow is a community-driven publishing platform built for developers, engineers,
                and technologists who want to write, share, and learn in public.
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-box-value"><?php echo number_format($totalPosts); ?></div>
                <div class="stat-box-label">Articles Published</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value"><?php echo number_format($totalAuthors); ?></div>
                <div class="stat-box-label">Active Writers</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value"><?php echo number_format($totalWords); ?></div>
                <div class="stat-box-label">Words Shared</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value">Since <?php echo escape($oldestYear); ?></div>
                <div class="stat-box-label">Building in Public</div>
            </div>
        </div>

        <div class="content-section">
            <h2>Our Story</h2>
            <p>
                TechFlow started as a simple idea: the best way to understand a technology is to explain it
                to someone else. We built a lightweight, distraction-free place where developers could publish
                tutorials, deep dives, and lessons learned — without fighting a bloated CMS or a paywall.
            </p>
            <p>
                Today the platform is home to writing on web development, artificial intelligence, cloud
                infrastructure, cybersecurity, DevOps, and everything in between. Every article on TechFlow is
                written by a real practitioner sharing what they've actually built, broken, and fixed.
            </p>
        </div>

        <div class="content-section">
            <h2>What We Value</h2>
            <div class="value-grid">
                <div class="value-card">
                    <div class="value-card-icon">✍️</div>
                    <h3>Writer-first</h3>
                    <p>A clean Markdown editor with live preview, so you can focus on the writing instead of the tooling.</p>
                </div>
                <div class="value-card">
                    <div class="value-card-icon">🌍</div>
                    <h3>Open community</h3>
                    <p>Anyone can create a free account and publish. No gatekeeping, no algorithms deciding what gets seen.</p>
                </div>
                <div class="value-card">
                    <div class="value-card-icon">⚡</div>
                    <h3>Fast & simple</h3>
                    <p>Built to load quickly and stay out of your way — no trackers, no clutter, just the content.</p>
                </div>
            </div>
        </div>

        <div class="cta-banner">
            <h2>Have something to teach?</h2>
            <p>Join the writers already sharing their knowledge on TechFlow.</p>
            <?php if (isLoggedIn()): ?>
                <a href="create.php" class="btn btn-primary">📝 Write an Article</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary">🚀 Create a Free Account</a>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
