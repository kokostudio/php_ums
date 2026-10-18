<?php
/**
 * YouTube-Style Top Preloader Progress Bar Component
 * Provides a sleek, animated top progress bar during page navigation and AJAX transitions.
 */
?>
<!-- Top Loading Progress Bar (YouTube Style) -->
<div id="yt-progress-bar" class="yt-progress-bar" style="width: 0%; opacity: 0;">
    <div class="yt-progress-bar-peg"></div>
</div>

<style>
    /* ── YouTube-Style Top Progress Bar ── */
    #yt-progress-bar {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        width: 0%;
        opacity: 0;
        background: linear-gradient(90deg, #ef4444 0%, #ff2d55 50%, #f43f5e 100%);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.7), 0 0 4px rgba(239, 68, 68, 0.9);
        z-index: 99999999;
        pointer-events: none;
        transition: width 200ms cubic-bezier(0.1, 0.9, 0.2, 1), opacity 250ms ease;
    }
    #yt-progress-bar.yt-active {
        opacity: 1 !important;
    }
    #yt-progress-bar .yt-progress-bar-peg {
        position: absolute;
        right: 0;
        top: 0;
        width: 100px;
        height: 100%;
        box-shadow: 0 0 14px #ef4444, 0 0 6px #ef4444;
        opacity: 1;
        transform: rotate(3deg) translateY(-2px);
    }
</style>

<script>
/**
 * TopProgressBar - High-Performance YouTube-Style Navigation Progress Bar
 */
window.TopProgressBar = (function() {
    let progress = 0;
    let timer = null;
    let active = false;
    let finishTimeout = null;
    let safetyTimeout = null;

    function getBar() {
        return document.getElementById('yt-progress-bar');
    }

    function set(percent) {
        const bar = getBar();
        if (!bar) return;
        progress = Math.min(Math.max(percent, 0), 100);
        bar.style.width = progress + '%';
        if (progress > 0 && progress < 100) {
            bar.classList.add('yt-active');
            bar.style.opacity = '1';
            active = true;
        }
    }

    function inc(amount) {
        if (progress >= 95) return;
        if (typeof amount !== 'number') {
            if (progress < 25) amount = 15;
            else if (progress < 50) amount = 8;
            else if (progress < 70) amount = 5;
            else if (progress < 85) amount = 2.5;
            else amount = 1;
        }
        set(progress + amount);
    }

    function start(initial) {
        if (finishTimeout) {
            clearTimeout(finishTimeout);
            finishTimeout = null;
        }
        if (safetyTimeout) {
            clearTimeout(safetyTimeout);
        }

        const bar = getBar();
        if (!bar) return;

        active = true;
        bar.style.transition = 'width 200ms cubic-bezier(0.1, 0.9, 0.2, 1), opacity 200ms ease';
        set(typeof initial === 'number' ? initial : 20);

        if (timer) clearInterval(timer);
        timer = setInterval(function() {
            if (progress < 95) {
                inc();
            }
        }, 200);

        // Auto-complete safety fallback if page doesn't unload within 10s
        safetyTimeout = setTimeout(function() {
            if (active) done();
        }, 10000);
    }

    function done() {
        const bar = getBar();
        if (!bar) return;

        if (timer) {
            clearInterval(timer);
            timer = null;
        }
        if (safetyTimeout) {
            clearTimeout(safetyTimeout);
            safetyTimeout = null;
        }

        if (!active && progress === 0) return;

        set(100);

        finishTimeout = setTimeout(function() {
            bar.style.opacity = '0';
            finishTimeout = setTimeout(function() {
                bar.style.transition = 'none';
                bar.style.width = '0%';
                bar.classList.remove('yt-active');
                progress = 0;
                active = false;
            }, 250);
        }, 150);
    }

    return {
        start: start,
        set: set,
        inc: inc,
        done: done,
        status: function() { return progress; },
        isActive: function() { return active; }
    };
})();

// Automatic Page Navigation & Load Handlers
(function() {
    // 1. Initial Page Load or Navigation Transition
    const wasNavigating = (function() {
        try {
            const val = sessionStorage.getItem('yt_navigating');
            sessionStorage.removeItem('yt_navigating');
            return val === '1';
        } catch(e) {
            return false;
        }
    })();

    if (wasNavigating) {
        // Fast finish on incoming page
        TopProgressBar.set(65);
    } else {
        TopProgressBar.start(25);
    }

    function finishLoad() {
        TopProgressBar.done();
    }

    if (document.readyState === 'complete') {
        finishLoad();
    } else {
        window.addEventListener('load', finishLoad);
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (TopProgressBar.isActive()) {
                    TopProgressBar.done();
                }
            }, 300);
        });
    }

    // 2. Back-Forward Cache (bfcache) Restoration
    window.addEventListener('pageshow', function(e) {
        try { sessionStorage.removeItem('yt_navigating'); } catch(err) {}
        TopProgressBar.done();
    });

    // 3. Link Click Interception (Page Navigation)
    document.addEventListener('click', function(e) {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
            return;
        }

        const anchor = e.target.closest('a');
        if (!anchor) return;

        // Skip non-standard targets and excluded links
        if (anchor.target && anchor.target !== '_self') return;
        if (anchor.hasAttribute('download')) return;
        if (anchor.getAttribute('data-no-progress') !== null) return;

        const href = anchor.getAttribute('href');
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return;
        }

        try {
            const targetUrl = new URL(anchor.href, window.location.href);
            if (targetUrl.origin !== window.location.origin) return;

            // Same page hash link
            if (targetUrl.pathname === window.location.pathname && 
                targetUrl.search === window.location.search && 
                targetUrl.hash) {
                return;
            }

            // Exclude common file downloads
            const ext = targetUrl.pathname.split('.').pop().toLowerCase();
            const downloadExts = ['pdf', 'zip', 'rar', 'tar', 'gz', 'xlsx', 'xls', 'csv', 'docx', 'doc', 'png', 'jpg', 'jpeg', 'svg', 'mp4', 'mp3'];
            if (downloadExts.includes(ext)) {
                return;
            }

            try { sessionStorage.setItem('yt_navigating', '1'); } catch(err) {}
            TopProgressBar.start();
        } catch (err) {}
    }, false);

    // 4. Form Submission Interception
    document.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;
        const form = e.target;
        if (form.target && form.target !== '_self') return;
        if (form.getAttribute('data-no-progress') !== null) return;

        try { sessionStorage.setItem('yt_navigating', '1'); } catch(err) {}
        TopProgressBar.start();
    }, false);

    // 5. Browser BeforeUnload Backup
    window.addEventListener('beforeunload', function() {
        try { sessionStorage.setItem('yt_navigating', '1'); } catch(err) {}
        TopProgressBar.start();
    });

    // 6. Axios Interceptor for AJAX Operations
    function setupAxiosInterceptors() {
        if (!window.axios || window._ytAxiosHooked) return;
        window._ytAxiosHooked = true;

        let activeAxios = 0;
        axios.interceptors.request.use(function(config) {
            const url = config.url || '';
            // Ignore polling notifications and live quick search
            if (!url.includes('/api/notifications') && !url.includes('/api/search') && !config.noProgressBar) {
                config._ytProgress = true;
                if (activeAxios === 0) {
                    TopProgressBar.start();
                }
                activeAxios++;
            }
            return config;
        }, function(error) {
            return Promise.reject(error);
        });

        axios.interceptors.response.use(function(response) {
            if (response.config && response.config._ytProgress) {
                activeAxios = Math.max(0, activeAxios - 1);
                if (activeAxios === 0) {
                    TopProgressBar.done();
                }
            }
            return response;
        }, function(error) {
            if (error.config && error.config._ytProgress) {
                activeAxios = Math.max(0, activeAxios - 1);
                if (activeAxios === 0) {
                    TopProgressBar.done();
                }
            }
            return Promise.reject(error);
        });
    }

    if (window.axios) {
        setupAxiosInterceptors();
    } else {
        document.addEventListener('DOMContentLoaded', setupAxiosInterceptors);
    }
})();
</script>
