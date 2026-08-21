/**
 * TechFlow - Interactive JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    initThemeToggle();
    initMobileNav();
    initHeroCanvas();
    initNetworkCanvas();
    initScrollReveal();
    initSearch();
    initEditorTabs();
    initMarkdownPreview();
    initFormEnhancements();
    initActionButtons();
    initComments();
    initWritingModal();
    initStatsCounter();
    initToasts();
});

/* ===========================
   THEME TOGGLE
=========================== */
function initThemeToggle() {
    const btn = document.querySelector('.theme-toggle');
    if (!btn) return;

    let stored = localStorage.getItem('tf-theme');
    if (stored !== 'light') stored = 'dark';
    applyTheme(stored);

    btn.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme') || 'dark';
        const next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        btn.style.transition = 'transform 0.4s ease';
        btn.style.transform = 'rotate(360deg)';
        setTimeout(() => { btn.style.transform = ''; }, 420);
    });
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    if (theme === 'light') {
        document.documentElement.classList.add('light');
        document.body.classList.add('light');
    } else {
        document.documentElement.classList.remove('light');
        document.body.classList.remove('light');
    }
    localStorage.setItem('tf-theme', theme);
    const btn = document.querySelector('.theme-toggle');
    if (btn) {
        btn.textContent = theme === 'dark' ? '🌙' : '☀️';
        btn.title = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
    }
}



/* ===========================
   MOBILE NAV
=========================== */
function initMobileNav() {
    const toggle = document.querySelector('.nav-toggle');
    const menu = document.querySelector('.nav-menu');
    if (!toggle || !menu) return;

    const searchIconBtn = document.getElementById('nav-search-icon-btn');
    const searchInput = document.getElementById('nav-search-input');
    if (searchIconBtn && searchInput) {
        searchIconBtn.addEventListener('click', () => {
            searchInput.closest('.nav-search-wrap')?.classList.toggle('mobile-active');
            searchInput.focus();
        });
    }

    toggle.addEventListener('click', e => {
        e.stopPropagation();
        menu.classList.toggle('active');
        toggle.classList.toggle('active');
    });

    document.addEventListener('click', e => {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('active');
            toggle.classList.remove('active');
        }
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            menu.classList.remove('active');
            toggle.classList.remove('active');
        }
    });
}


/* ===========================
   HERO CANVAS (floating particles)
=========================== */
function initHeroCanvas() {
    const canvas = document.getElementById('hero-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let particles = [];
    let raf;

    function resize() {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    }

    function createParticles() {
        particles = [];
        const count = Math.min(60, Math.floor((canvas.width * canvas.height) / 18000));
        for (let i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                r: Math.random() * 2 + 0.5,
                vx: (Math.random() - 0.5) * 0.35,
                vy: (Math.random() - 0.5) * 0.35,
                opacity: Math.random() * 0.5 + 0.1,
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const isDark = document.documentElement.getAttribute('data-theme') !== 'light';

        particles.forEach(p => {
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < 0) p.x = canvas.width;
            if (p.x > canvas.width) p.x = 0;
            if (p.y < 0) p.y = canvas.height;
            if (p.y > canvas.height) p.y = 0;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = isDark
                ? `rgba(139, 92, 246, ${p.opacity})`
                : `rgba(124, 58, 237, ${p.opacity * 0.4})`;
            ctx.fill();
        });

        // Draw connections
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 120) {
                    const alpha = (1 - dist / 120) * 0.12;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.strokeStyle = isDark
                        ? `rgba(139, 92, 246, ${alpha})`
                        : `rgba(124, 58, 237, ${alpha * 0.5})`;
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }
        }

        raf = requestAnimationFrame(draw);
    }

    resize();
    createParticles();
    draw();

    const ro = new ResizeObserver(() => { resize(); createParticles(); });
    ro.observe(canvas.parentElement);
}


/* ===========================
   NETWORK CANVAS (hero right side)
=========================== */
function initNetworkCanvas() {
    const canvas = document.getElementById('network-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const topics = [
        { label: 'Web Dev', color: '#7c3aed', x: 0.55, y: 0.2, size: 10 },
        { label: 'AI/ML', color: '#06b6d4', x: 0.82, y: 0.3, size: 9 },
        { label: 'Security', color: '#f59e0b', x: 0.72, y: 0.65, size: 8 },
        { label: 'DevOps', color: '#10b981', x: 0.35, y: 0.55, size: 8 },
        { label: 'Systems', color: '#ef4444', x: 0.88, y: 0.55, size: 7 },
        { label: 'Mobile', color: '#8b5cf6', x: 0.2, y: 0.35, size: 7 },
        { label: 'Data', color: '#06b6d4', x: 0.62, y: 0.82, size: 9 },
        { label: 'Cloud', color: '#34d399', x: 0.45, y: 0.78, size: 7 },
        { label: 'Open Source', color: '#f472b6', x: 0.15, y: 0.7, size: 6 },
    ];

    const connections = [
        [0, 1], [0, 3], [0, 5], [1, 2], [1, 4], [2, 6],
        [3, 7], [3, 8], [4, 2], [6, 7], [5, 8],
    ];

    let time = 0;
    let nodes;

    function buildNodes(w, h) {
        nodes = topics.map(t => ({
            ...t,
            cx: t.x * w,
            cy: t.y * h,
            pulse: Math.random() * Math.PI * 2,
        }));
    }

    function resize() {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        buildNodes(canvas.width, canvas.height);
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        time += 0.012;
        const isDark = document.documentElement.getAttribute('data-theme') !== 'light';

        // Connections
        connections.forEach(([i, j]) => {
            const a = nodes[i], b = nodes[j];
            const grad = ctx.createLinearGradient(a.cx, a.cy, b.cx, b.cy);
            grad.addColorStop(0, a.color + '55');
            grad.addColorStop(1, b.color + '55');
            ctx.beginPath();
            ctx.moveTo(a.cx, a.cy);
            ctx.lineTo(b.cx, b.cy);
            ctx.strokeStyle = grad;
            ctx.lineWidth = isDark ? 1.2 : 0.8;
            ctx.stroke();

            // Traveling dot
            const t2 = (Math.sin(time + i * 0.7 + j * 0.3) + 1) / 2;
            const tx = a.cx + (b.cx - a.cx) * t2;
            const ty = a.cy + (b.cy - a.cy) * t2;
            ctx.beginPath();
            ctx.arc(tx, ty, 2, 0, Math.PI * 2);
            ctx.fillStyle = isDark ? '#8b5cf6cc' : '#7c3aed99';
            ctx.fill();
        });

        // Nodes
        nodes.forEach(n => {
            const pulse = Math.sin(time * 1.5 + n.pulse) * 2;
            const r = n.size + pulse;

            // Glow
            const grd = ctx.createRadialGradient(n.cx, n.cy, 0, n.cx, n.cy, r * 3);
            grd.addColorStop(0, n.color + '44');
            grd.addColorStop(1, 'transparent');
            ctx.beginPath();
            ctx.arc(n.cx, n.cy, r * 3, 0, Math.PI * 2);
            ctx.fillStyle = grd;
            ctx.fill();

            // Circle
            ctx.beginPath();
            ctx.arc(n.cx, n.cy, r, 0, Math.PI * 2);
            ctx.fillStyle = n.color;
            ctx.fill();

            ctx.beginPath();
            ctx.arc(n.cx, n.cy, r * 0.45, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(255,255,255,0.5)';
            ctx.fill();

            // Label
            ctx.font = `500 11px Inter, sans-serif`;
            ctx.fillStyle = isDark ? 'rgba(240,240,255,0.65)' : 'rgba(26,26,46,0.65)';
            ctx.textAlign = 'center';
            ctx.fillText(n.label, n.cx, n.cy + r + 14);
        });

        requestAnimationFrame(draw);
    }

    resize();
    draw();
    const ro = new ResizeObserver(resize);
    ro.observe(canvas);
}


/* ===========================
   SCROLL REVEAL
=========================== */
function initScrollReveal() {
    const els = document.querySelectorAll('.reveal');
    if (!els.length) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
    els.forEach(el => io.observe(el));
}


/* ===========================
   STATS COUNTER
=========================== */
function initStatsCounter() {
    const stats = document.querySelectorAll('[data-count]');
    if (!stats.length) return;

    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                animateCount(e.target);
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.5 });

    stats.forEach(el => io.observe(el));
}

function animateCount(el) {
    const target = parseInt(el.getAttribute('data-count'));
    if (isNaN(target)) return;
    const duration = 1600;
    const start = performance.now();
    function update(now) {
        const progress = Math.min((now - start) / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(ease * target);
        if (progress < 1) requestAnimationFrame(update);
        else el.textContent = target;
    }
    requestAnimationFrame(update);
}


/* ===========================
   LIVE SEARCH
=========================== */
function initSearch() {
    const wrap = document.querySelector('.nav-search-wrap');
    const input = document.querySelector('.nav-search');
    const dropdown = document.querySelector('.search-results');
    if (!input || !dropdown) return;

    let debounce;

    input.addEventListener('input', () => {
        clearTimeout(debounce);
        const q = input.value.trim();
        if (q.length < 2) { dropdown.classList.remove('active'); return; }
        debounce = setTimeout(() => performSearch(q, dropdown), 220);
    });

    input.addEventListener('focus', () => {
        if (input.value.trim().length >= 2) dropdown.classList.add('active');
    });

    document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) dropdown.classList.remove('active');
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            dropdown.classList.remove('active');
            input.blur();
        }
    });
}

function performSearch(query, dropdown) {
    // Client-side search through visible cards
    const cards = document.querySelectorAll('.blog-card');
    const results = [];

    cards.forEach(card => {
        const title = card.querySelector('.blog-card-title')?.textContent || '';
        const excerpt = card.querySelector('.blog-card-excerpt')?.textContent || '';
        const author = card.querySelector('.author-name')?.textContent || '';
        const q = query.toLowerCase();
        if (title.toLowerCase().includes(q) || excerpt.toLowerCase().includes(q) || author.toLowerCase().includes(q)) {
            const link = card.querySelector('a');
            results.push({ title: title.trim(), href: link?.href || '#', author: author.trim() });
        }
    });

    if (results.length === 0) {
        dropdown.innerHTML = `<div class="search-no-results">No articles found for "<strong>${escapeHtml(query)}</strong>"</div>`;
    } else {
        dropdown.innerHTML = results.slice(0, 6).map(r => `
            <a href="${r.href}" class="search-result-item">
                <div class="search-result-icon">📄</div>
                <div>
                    <div class="search-result-title">${escapeHtml(r.title)}</div>
                    <div class="search-result-meta">by ${escapeHtml(r.author)}</div>
                </div>
            </a>
        `).join('');
    }

    dropdown.classList.add('active');
}


/* ===========================
   EDITOR TABS
=========================== */
function initEditorTabs() {
    const tabs = document.querySelectorAll('.editor-tab');
    const writePane = document.getElementById('write-pane');
    const previewPane = document.getElementById('preview-pane');
    if (!tabs.length) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            if (this.dataset.tab === 'write') {
                writePane?.classList.add('active');
                previewPane?.classList.remove('active');
            } else {
                writePane?.classList.remove('active');
                previewPane?.classList.add('active');
                updateMarkdownPreview();
            }
        });
    });

    // Toolbar buttons
    document.querySelectorAll('.toolbar-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const ta = document.getElementById('content');
            if (!ta) return;
            const action = btn.dataset.action;
            const start = ta.selectionStart;
            const end = ta.selectionEnd;
            const sel = ta.value.slice(start, end);
            let replacement = sel;
            let cursorOffset = 0;

            switch (action) {
                case 'bold': replacement = `**${sel || 'bold text'}**`; cursorOffset = sel ? 0 : -2; break;
                case 'italic': replacement = `*${sel || 'italic text'}*`; cursorOffset = sel ? 0 : -1; break;
                case 'code': replacement = `\`${sel || 'code'}\``; cursorOffset = sel ? 0 : -1; break;
                case 'h2': replacement = `\n## ${sel || 'Heading'}`; break;
                case 'link': replacement = `[${sel || 'link text'}](url)`; break;
                case 'list': replacement = `\n- ${sel || 'List item'}`; break;
                case 'quote': replacement = `\n> ${sel || 'Quote'}`; break;
            }

            ta.setRangeText(replacement, start, end, 'end');
            if (cursorOffset) ta.setSelectionRange(ta.selectionStart + cursorOffset, ta.selectionStart + cursorOffset);
            ta.focus();
        });
    });
}


/* ===========================
   MARKDOWN PREVIEW
=========================== */
function initMarkdownPreview() {
    const ta = document.getElementById('content');
    const preview = document.getElementById('preview-content');
    if (!ta || !preview) return;
    let t;
    ta.addEventListener('input', () => {
        clearTimeout(t);
        t = setTimeout(updateMarkdownPreview, 280);
    });
    if (ta.value.trim()) updateMarkdownPreview();
}

function updateMarkdownPreview() {
    const ta = document.getElementById('content');
    const preview = document.getElementById('preview-content');
    if (!ta || !preview) return;
    const md = ta.value.trim();
    preview.innerHTML = md ? convertMarkdownToHtml(md) : '<em style="color:var(--text-muted)">Preview will appear here...</em>';
}

function convertMarkdownToHtml(text) {
    let h = escapeHtml(text);
    h = h.replace(/^### (.+)$/gm, '<h3>$1</h3>');
    h = h.replace(/^## (.+)$/gm, '<h2>$1</h2>');
    h = h.replace(/^# (.+)$/gm, '<h1>$1</h1>');
    h = h.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    h = h.replace(/\*(.+?)\*/g, '<em>$1</em>');
    h = h.replace(/`(.+?)`/g, '<code>$1</code>');
    h = h.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');
    h = h.replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>');
    h = h.replace(/^- (.+)$/gm, '<li>$1</li>');
    h = h.replace(/(<li>.*<\/li>\n?)+/gs, '<ul>$&</ul>');
    h = h.replace(/^\d+\. (.+)$/gm, '<li>$1</li>');
    const paragraphs = h.split(/\n\n+/);
    h = paragraphs.map(p => {
        p = p.trim();
        if (!p) return '';
        if (/^<(h[1-6]|ul|ol|pre|blockquote)/.test(p)) return p;
        return '<p>' + p.replace(/\n/g, '<br>') + '</p>';
    }).join('');
    h = h.replace(/<p>\s*<\/p>/g, '');
    return h;
}

function escapeHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}


/* ===========================
   FORM ENHANCEMENTS
=========================== */
function initFormEnhancements() {
    // Password strength
    const pwInput = document.querySelector('input[name="password"]');
    const confirmPw = document.querySelector('input[name="confirm_password"]');
    if (pwInput && confirmPw) {
        const bar = document.querySelector('.strength-fill');
        const text = document.querySelector('.strength-text');

        pwInput.addEventListener('input', function () {
            const s = getPasswordStrength(this.value);
            if (bar) { bar.className = 'strength-fill ' + s; }
            if (text) { text.className = 'strength-text ' + s; text.textContent = this.value.length === 0 ? '' : 'Strength: ' + s; }
        });

        confirmPw.addEventListener('input', function () {
            if (this.value && this.value !== pwInput.value) {
                this.style.borderColor = 'var(--danger)';
            } else {
                this.style.borderColor = '';
            }
        });
    }

    // Show/hide password
    document.querySelectorAll('.input-eye').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.closest('.input-wrap').querySelector('input');
            if (!input) return;
            if (input.type === 'password') { input.type = 'text'; this.textContent = '🙈'; }
            else { input.type = 'password'; this.textContent = '👁️'; }
        });
    });

    // Unsaved changes warning
    const blogForm = document.getElementById('blog-form');
    if (blogForm) {
        let changed = false;
        blogForm.addEventListener('input', () => changed = true);
        blogForm.addEventListener('submit', () => changed = false);
        window.addEventListener('beforeunload', e => {
            if (changed) { e.preventDefault(); e.returnValue = ''; }
        });
    }

    // Title char counter
    const titleInput = document.querySelector('input[name="title"]');
    if (titleInput && titleInput.maxLength > 0) {
        const counter = document.createElement('small');
        counter.className = 'form-hint';
        counter.style.cssText = 'float:right;transition:color 0.2s';
        const update = () => {
            const r = titleInput.maxLength - titleInput.value.length;
            counter.textContent = `${r} characters remaining`;
            counter.style.color = r < 30 ? 'var(--danger)' : '';
        };
        titleInput.addEventListener('input', update);
        titleInput.parentNode.appendChild(counter);
        update();
    }

    // Featured image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                imagePreview.style.display = 'none';
                imagePreview.src = '';
                return;
            }

            if (!file.type.startsWith('image/')) {
                window.TechFlow?.showToast('⚠️ Please choose an image file', 'error');
                this.value = '';
                imagePreview.style.display = 'none';
                imagePreview.src = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                window.TechFlow?.showToast('⚠️ Image must be smaller than 5MB', 'error');
                this.value = '';
                imagePreview.style.display = 'none';
                imagePreview.src = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                // A new image was chosen, so it will replace the current one
                const removeCheckbox = document.getElementById('remove-image-checkbox');
                if (removeCheckbox) removeCheckbox.checked = false;
            };
            reader.readAsDataURL(file);
        });
    }

    // If "remove current image" is checked, clear any newly-chosen file
    const removeImageCheckbox = document.getElementById('remove-image-checkbox');
    if (removeImageCheckbox && imageInput) {
        removeImageCheckbox.addEventListener('change', function () {
            if (this.checked) {
                imageInput.value = '';
                if (imagePreview) {
                    imagePreview.style.display = 'none';
                    imagePreview.src = '';
                }
            }
        });
    }
}

function getPasswordStrength(pw) {
    if (pw.length < 6) return 'weak';
    const hasLetter = /[a-zA-Z]/.test(pw);
    const hasNum = /[0-9]/.test(pw);
    const hasSpecial = /[^a-zA-Z0-9]/.test(pw);
    if (pw.length >= 10 && hasLetter && hasNum && hasSpecial) return 'strong';
    if (pw.length >= 6 && hasLetter && hasNum) return 'medium';
    return 'weak';
}


/* ===========================
   LIKE & BOOKMARK BUTTONS
=========================== */
function initActionButtons() {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.action-btn');
        if (!btn) return;

        if (btn.classList.contains('like-btn')) {
            const isLiked = btn.classList.toggle('liked');
            const countEl = btn.querySelector('.action-btn-count');
            if (countEl) {
                let count = parseInt(countEl.textContent) || 0;
                countEl.textContent = isLiked ? count + 1 : Math.max(0, count - 1);
            }
            animatePop(btn);
            showToast(isLiked ? '❤️ Article liked!' : 'Like removed', isLiked ? 'success' : 'info');
        }

        if (btn.classList.contains('bookmark-btn')) {
            const isBookmarked = btn.classList.toggle('bookmarked');
            animatePop(btn);
            showToast(isBookmarked ? '🔖 Bookmarked!' : 'Bookmark removed', 'success');
        }
    });
}

function animatePop(el) {
    el.style.transform = 'scale(1.35)';
    setTimeout(() => el.style.transform = '', 250);
}


/* ===========================
   COMMENTS
=========================== */
function initComments() {
    const form = document.querySelector('.comment-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const ta = form.querySelector('textarea');
        const text = ta.value.trim();
        if (!text) return;

        const list = document.querySelector('.comments-list');
        const countEl = document.querySelector('.comments-count');

        const comment = createCommentEl('You', text);
        list?.prepend(comment);
        ta.value = '';

        if (countEl) {
            const c = parseInt(countEl.textContent) || 0;
            countEl.textContent = c + 1;
        }

        showToast('💬 Comment added!', 'success');
    });
}

function createCommentEl(author, text) {
    const el = document.createElement('div');
    el.className = 'comment';
    const initials = author.slice(0, 2).toUpperCase();
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    el.innerHTML = `
        <div class="comment-avatar">${initials}</div>
        <div>
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.3rem;">
                <span class="comment-author">${escapeHtml(author)}</span>
                <span class="comment-date">Just now · ${dateStr}</span>
            </div>
            <div class="comment-text">${escapeHtml(text)}</div>
        </div>
    `;
    return el;
}


/* ===========================
   WRITING MODAL
=========================== */
function initWritingModal() {
    const openBtns = document.querySelectorAll('[data-open-modal="write"]');
    const overlay = document.getElementById('writing-modal-overlay');
    const closeBtn = document.querySelector('.modal-close');
    if (!overlay) return;

    openBtns.forEach(btn => {
        btn.addEventListener('click', () => overlay.classList.add('active'));
    });

    closeBtn?.addEventListener('click', () => overlay.classList.remove('active'));
    overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.classList.remove('active');
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') overlay.classList.remove('active');
    });
}


/* ===========================
   TOAST NOTIFICATIONS
=========================== */
function initToasts() {
    if (!document.getElementById('toast-container')) {
        const c = document.createElement('div');
        c.id = 'toast-container';
        document.body.appendChild(c);
    }
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span class="toast-icon">${icons[type] || '•'}</span><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}


// Expose globally
window.TechFlow = { showToast, animateCount };
