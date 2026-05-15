/**
 * ============================================
 * MODERN UI - JavaScript Engine
 * Glassmorphism, Particles, Transitions
 * ============================================
 */

(function () {
    'use strict';

    // ─── Particle System ───
    class ParticleSystem {
        constructor(canvasId) {
            this.canvas = document.getElementById(canvasId);
            if (!this.canvas) return;

            this.ctx = this.canvas.getContext('2d');
            this.particles = [];
            this.animFrame = null;
            this.mouse = { x: null, y: null, radius: 150 };

            this.colors = [
                'rgba(102, 126, 234, 0.6)',  // #667eea
                'rgba(118, 75, 162, 0.5)',    // #764ba2
                'rgba(139, 92, 246, 0.4)',    // violet
                'rgba(59, 130, 246, 0.5)',    // blue
                'rgba(6, 182, 212, 0.4)',     // cyan
                'rgba(168, 85, 247, 0.3)',    // purple
            ];

            this.resize();
            this.init();
            this.bindEvents();
            this.animate();
        }

        resize() {
            this.width = this.canvas.width = window.innerWidth;
            this.height = this.canvas.height = window.innerHeight;
        }

        init() {
            const numParticles = Math.min(Math.floor((this.width * this.height) / 15000), 80);
            this.particles = [];
            for (let i = 0; i < numParticles; i++) {
                this.particles.push(this.createParticle());
            }
        }

        createParticle() {
            const size = Math.random() * 3 + 1;
            return {
                x: Math.random() * this.width,
                y: Math.random() * this.height,
                size: size,
                speedX: (Math.random() - 0.5) * 0.4,
                speedY: (Math.random() - 0.5) * 0.4,
                color: this.colors[Math.floor(Math.random() * this.colors.length)],
                opacity: Math.random() * 0.5 + 0.2,
                pulse: Math.random() * Math.PI * 2,
                pulseSpeed: Math.random() * 0.02 + 0.005,
            };
        }

        bindEvents() {
            window.addEventListener('resize', () => {
                this.resize();
                this.init();
            });
            window.addEventListener('mousemove', (e) => {
                this.mouse.x = e.clientX;
                this.mouse.y = e.clientY;
            });
            window.addEventListener('mouseout', () => {
                this.mouse.x = null;
                this.mouse.y = null;
            });
        }

        animate() {
            if (!this.ctx) return;
            this.ctx.clearRect(0, 0, this.width, this.height);

            // Update and draw particles
            for (let i = 0; i < this.particles.length; i++) {
                const p = this.particles[i];

                // Update pulse
                p.pulse += p.pulseSpeed;
                const currentOpacity = p.opacity * (0.7 + 0.3 * Math.sin(p.pulse));

                // Mouse interaction
                if (this.mouse.x !== null) {
                    const dx = this.mouse.x - p.x;
                    const dy = this.mouse.y - p.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < this.mouse.radius) {
                        const force = (this.mouse.radius - dist) / this.mouse.radius;
                        p.x -= dx * force * 0.02;
                        p.y -= dy * force * 0.02;
                    }
                }

                // Move
                p.x += p.speedX;
                p.y += p.speedY;

                // Wrap around edges
                if (p.x < -10) p.x = this.width + 10;
                if (p.x > this.width + 10) p.x = -10;
                if (p.y < -10) p.y = this.height + 10;
                if (p.y > this.height + 10) p.y = -10;

                // Draw particle
                this.ctx.beginPath();
                this.ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                this.ctx.fillStyle = p.color.replace('0.6', currentOpacity.toFixed(2))
                    .replace('0.5', currentOpacity.toFixed(2))
                    .replace('0.4', currentOpacity.toFixed(2))
                    .replace('0.3', currentOpacity.toFixed(2));
                this.ctx.fill();

                // Glow effect
                this.ctx.beginPath();
                this.ctx.arc(p.x, p.y, p.size * 3, 0, Math.PI * 2);
                this.ctx.fillStyle = p.color.replace(/[\d.]+\)$/, (currentOpacity * 0.15) + ')');
                this.ctx.fill();
            }

            // Draw connections
            for (let i = 0; i < this.particles.length; i++) {
                for (let j = i + 1; j < this.particles.length; j++) {
                    const dx = this.particles[i].x - this.particles[j].x;
                    const dy = this.particles[i].y - this.particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120) {
                        const opacity = (1 - dist / 120) * 0.15;
                        this.ctx.beginPath();
                        this.ctx.moveTo(this.particles[i].x, this.particles[i].y);
                        this.ctx.lineTo(this.particles[j].x, this.particles[j].y);
                        this.ctx.strokeStyle = `rgba(102, 126, 234, ${opacity})`;
                        this.ctx.lineWidth = 0.5;
                        this.ctx.stroke();
                    }
                }
            }

            this.animFrame = requestAnimationFrame(() => this.animate());
        }

        destroy() {
            if (this.animFrame) {
                cancelAnimationFrame(this.animFrame);
            }
        }
    }

    // ─── Skeleton Loader ───
    function showSkeleton(container) {
        const skeletonHTML = `
        <div class="skeleton-wrapper">
            <div class="skeleton-header">
                <div class="skeleton skeleton-title"></div>
                <div class="skeleton skeleton-text" style="width:40%"></div>
            </div>
            <div class="row stagger-children">
                <div class="col-md-3">
                    <div class="glass-card">
                        <div class="glass-card-body">
                            <div class="skeleton skeleton-text"></div>
                            <div class="skeleton skeleton-text" style="width:60%"></div>
                            <div class="skeleton skeleton-row"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card">
                        <div class="glass-card-body">
                            <div class="skeleton skeleton-text"></div>
                            <div class="skeleton skeleton-text" style="width:60%"></div>
                            <div class="skeleton skeleton-row"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card">
                        <div class="glass-card-body">
                            <div class="skeleton skeleton-text"></div>
                            <div class="skeleton skeleton-text" style="width:60%"></div>
                            <div class="skeleton skeleton-row"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="glass-card">
                        <div class="glass-card-body">
                            <div class="skeleton skeleton-text"></div>
                            <div class="skeleton skeleton-text" style="width:60%"></div>
                            <div class="skeleton skeleton-row"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="glass-card mt-4">
                <div class="glass-card-body">
                    <div class="skeleton skeleton-text"></div>
                    ${Array.from({length: 5}, () =>
                        '<div class="skeleton skeleton-row"></div>'
                    ).join('')}
                </div>
            </div>
        </div>`;
        container.innerHTML = skeletonHTML;
    }

    // ─── Ripple Effect ───
    function createRipple(event) {
        const button = event.currentTarget;
        if (button.classList.contains('no-ripple')) return;

        const ripple = document.createElement('span');
        ripple.classList.add('ripple');

        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (event.clientX - rect.left - size / 2) + 'px';
        ripple.style.top = (event.clientY - rect.top - size / 2) + 'px';

        button.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    function initRipple() {
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.btn-glass, .btn-primary, .btn-secondary, .btn-success, .btn-danger, .btn-warning, .btn-outline-primary, .btn');
            if (button) {
                createRipple({ currentTarget: button, clientX: e.clientX, clientY: e.clientY });
            }
        });
    }

    // ─── Modern Toast System ───
    class ToastManager {
        constructor() {
            this.container = null;
            this.init();
        }

        init() {
            this.container = document.getElementById('toast-stack');
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.id = 'toast-stack';
                document.body.appendChild(this.container);
            }
        }

        show(options) {
            const { title, message, type = 'default', duration = 4000 } = options;

            const icons = {
                success: '&#xE876;',
                warning: '&#xE002;',
                danger: '&#xE5CD;',
                info: '&#xE88F;',
                default: '&#xE88F;'
            };

            const isLight = document.body.classList.contains('light-theme');
            const toast = document.createElement('div');
            toast.className = `toast-item toast-${type}${isLight ? ' light-theme' : ''}`;
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="material-icons" style="font-size:18px">${icons[type] || icons.default}</i>
                </div>
                <div class="toast-content">
                    ${title ? `<p class="toast-title">${title}</p>` : ''}
                    <p class="toast-message">${message || ''}</p>
                </div>
                <button class="toast-close" onclick="this.parentElement.classList.add('exiting'); setTimeout(() => this.parentElement.remove(), 250);">&times;</button>
            `;

            this.container.appendChild(toast);

            if (duration > 0) {
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.classList.add('exiting');
                        setTimeout(() => toast.remove(), 250);
                    }
                }, duration);
            }
        }

        success(message, title) { this.show({ message, title: title || 'Berhasil', type: 'success' }); }
        warning(message, title) { this.show({ message, title: title || 'Peringatan', type: 'warning' }); }
        danger(message, title) { this.show({ message, title: title || 'Galat', type: 'danger' }); }
        info(message, title) { this.show({ message, title: title || 'Info', type: 'info' }); }
    }

    // Global toast instance
    window.__toast = new ToastManager();

    // ─── Modern Tooltip ───
    class TooltipManager {
        constructor() {
            this.activeTooltip = null;
            this.init();
        }

        init() {
            document.addEventListener('mouseover', (e) => {
                const el = e.target.closest('[data-tooltip]');
                if (!el) return;

                const text = el.getAttribute('data-tooltip');
                if (!text) return;

                // Remove existing
                if (this.activeTooltip) {
                    this.activeTooltip.remove();
                }

                const tooltip = document.createElement('div');
                tooltip.className = 'modern-tooltip';
                tooltip.textContent = text;
                document.body.appendChild(tooltip);

                const rect = el.getBoundingClientRect();
                tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + 'px';
                tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

                // Adjust if off screen
                const ttRect = tooltip.getBoundingClientRect();
                if (ttRect.left < 10) tooltip.style.left = '10px';
                if (ttRect.right > window.innerWidth - 10) {
                    tooltip.style.left = window.innerWidth - ttRect.width - 10 + 'px';
                }

                this.activeTooltip = tooltip;
            });

            document.addEventListener('mouseout', (e) => {
                if (e.target.closest('[data-tooltip]') && this.activeTooltip) {
                    this.activeTooltip.remove();
                    this.activeTooltip = null;
                }
            });
        }
    }

    // ─── Mobile Sidebar (global, in case JS also needs) ───
    function closeMobileMenuGlobal() {
        const overlay = document.getElementById('mobileOverlay');
        const sidebar = document.getElementById('mobileSidebar');
        if (overlay) overlay.classList.remove('active');
        if (sidebar) sidebar.classList.remove('open');
        document.body.style.overflow = '';
        document.body.classList.remove('mobile-menu-open');
    }

    // Listen for custom event from layout.php
    document.addEventListener('closeMobileMenu', closeMobileMenuGlobal);

    // ─── Navbar Scroll Animation ───
    function initNavbarScroll() {
        const navbar = document.querySelector('.hk-navbar');
        if (!navbar) return;

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 50) {
                navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.4)';
            } else {
                navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.2)';
            }
        }, { passive: true });
    }

    // ─── Active Nav Link ───
    function setActiveNavLink(pageName) {
        document.querySelectorAll('.hk-navbar .nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-page') === pageName) {
                link.classList.add('active');
            }
        });
    }

    // ─── Content Animations on Appear ───
    function animateContent(container) {
        if (!container) return;

        const elements = container.querySelectorAll('.glass-card, .glass-alert, .card, .alert, .stat-card, .page-header, .hk-pg-header');
        elements.forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(16px)';
            el.style.transition = `opacity 0.4s ease-out ${i * 60}ms, transform 0.4s ease-out ${i * 60}ms`;
            el.style.willChange = 'opacity, transform';

            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 50);
        });

        // Also animate rows
        const rows = container.querySelectorAll('.row');
        rows.forEach((row, i) => {
            if (!row.closest('.glass-card') && !row.closest('.card')) {
                row.style.opacity = '0';
                row.style.transform = 'translateY(12px)';
                row.style.transition = `opacity 0.35s ease-out ${i * 40}ms, transform 0.35s ease-out ${i * 40}ms`;
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 50);
            }
        });
    }

    // ─── Enhanced Page Loader ───
    function showPageLoader() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('active');
        }
    }

    function hidePageLoader() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.remove('active');
        }
    }

    // ─── Init All ───
    let particleSystem = null;

    function initModernUI() {
        // Init particle system
        particleSystem = new ParticleSystem('particle-canvas');

        // Init ripple
        initRipple();

        // Init tooltip
        new TooltipManager();

        // Init navbar scroll
        initNavbarScroll();

        // Animate first content
        const app = document.getElementById('app');
        if (app) {
            animateContent(app);
        }
    }

    // ─── Public API ───
    window.ModernUI = {
        init: initModernUI,
        showSkeleton,
        animateContent,
        showPageLoader,
        hidePageLoader,
        setActiveNavLink,
        toast: window.__toast,

        // Convenience wrappers
        toastSuccess: (msg, title) => window.__toast.success(msg, title),
        toastWarning: (msg, title) => window.__toast.warning(msg, title),
        toastDanger: (msg, title) => window.__toast.danger(msg, title),
        toastInfo: (msg, title) => window.__toast.info(msg, title),

        // Particle control
        pauseParticles: () => { if (particleSystem) particleSystem.destroy(); },
        resumeParticles: () => { if (!particleSystem) particleSystem = new ParticleSystem('particle-canvas'); },
    };

    // Auto-init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModernUI);
    } else {
        initModernUI();
    }

})();
