    </main>

    <!-- Writing Modal -->
    <div class="modal-overlay" id="writing-modal-overlay" role="dialog" aria-modal="true">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title">✍️ Start Writing on TechFlow</span>
                <button class="modal-close" aria-label="Close">✕</button>
            </div>
            <div class="modal-body">
                <p style="color:var(--text-sub);font-size:0.9rem;margin-bottom:1.5rem;line-height:1.65;">
                    Ready to share your knowledge? Jump into the full editor and publish your article to the TechFlow community.
                </p>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    <?php if (isLoggedIn()): ?>
                        <a href="create.php" class="btn btn-primary">📝 Open Editor</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary">🚀 Create Free Account</a>
                        <a href="login.php" class="btn btn-secondary">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-top">
                <div>
                    <a href="index.php" class="footer-brand-logo">
                        <div class="footer-logo-icon">⚡</div>
                        <span class="footer-logo-name">Tech<span>Flow</span></span>
                    </a>
                    <p class="footer-brand-desc">A premium platform for tech writers to share articles, tutorials, and engineering insights.</p>
                </div>

                <div>
                    <div class="footer-col-title">Topics</div>
                    <div class="footer-links">
                        <a href="index.php" class="footer-link">🌐 Web Development</a>
                        <a href="index.php" class="footer-link">🤖 AI & Machine Learning</a>
                        <a href="index.php" class="footer-link">🔒 Security</a>
                        <a href="index.php" class="footer-link">☁️ DevOps & Cloud</a>
                    </div>
                </div>

                <div>
                    <div class="footer-col-title">Platform</div>
                    <div class="footer-links">
                        <a href="index.php" class="footer-link">All Articles</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="create.php" class="footer-link">Write Article</a>
                            <a href="logout.php" class="footer-link">Sign Out</a>
                        <?php else: ?>
                            <a href="register.php" class="footer-link">Create Account</a>
                            <a href="login.php" class="footer-link">Sign In</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <div class="footer-col-title">About</div>
                    <div class="footer-links">
                        <a href="#" class="footer-link">About TechFlow</a>
                        <a href="#" class="footer-link">Privacy Policy</a>
                        <a href="#" class="footer-link">Terms of Service</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© <?php echo date('Y'); ?> TechFlow — Where Ideas Flow</span>
                <div class="footer-bottom-links">
                    <a href="#" class="footer-bottom-link">Privacy</a>
                    <a href="#" class="footer-bottom-link">Terms</a>
                    <a href="#" class="footer-bottom-link">Built with PHP & ❤️</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
