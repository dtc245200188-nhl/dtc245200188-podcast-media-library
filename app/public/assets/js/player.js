/**
 * PodcastHub - Audio Player & UI Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    // ---- Header Scroll Effect ----
    const header = document.getElementById('site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    // ---- Mobile Menu Toggle ----
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const mainNav = document.getElementById('main-nav');
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', () => {
            mainNav.style.display = mainNav.style.display === 'flex' ? 'none' : 'flex';
            mainNav.style.position = 'absolute';
            mainNav.style.top = '100%';
            mainNav.style.left = '0';
            mainNav.style.right = '0';
            mainNav.style.background = 'var(--bg-secondary)';
            mainNav.style.flexDirection = 'column';
            mainNav.style.padding = '16px';
            mainNav.style.borderBottom = '1px solid var(--border-color)';
        });
    }

    // ---- Dropdown Menu ----
    const dropdownToggle = document.getElementById('categories-dropdown');
    const dropdownMenu = document.getElementById('categories-menu');
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        document.addEventListener('click', () => {
            dropdownMenu.classList.remove('show');
        });
    }

    // ---- Audio Player ----
    initAudioPlayer();

    // ---- Scroll Animations ----
    initScrollAnimations();

    // ---- Confirm Delete ----
    document.querySelectorAll('.btn-danger[onclick]').forEach(btn => {
        // Confirmation handled inline
    });

    // ---- File Input Preview ----
    document.querySelectorAll('.file-input-wrapper input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const wrapper = this.closest('.file-input-wrapper');
            const label = wrapper.querySelector('span');
            if (this.files.length > 0) {
                label.textContent = this.files[0].name;
                wrapper.style.borderColor = 'var(--primary)';
            }
        });
    });
});

/**
 * Initialize the custom audio player
 */
function initAudioPlayer() {
    const audioElement = document.getElementById('audio-element');
    const playBtn = document.getElementById('play-btn');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const currentTimeEl = document.getElementById('current-time');
    const durationEl = document.getElementById('duration-time');
    const volumeSlider = document.getElementById('volume-slider');

    if (!audioElement || !playBtn) return;

    let isPlaying = false;

    // Play/Pause
    playBtn.addEventListener('click', () => {
        if (isPlaying) {
            audioElement.pause();
        } else {
            audioElement.play().catch(err => {
                console.log('Playback failed:', err);
            });
        }
    });

    audioElement.addEventListener('play', () => {
        isPlaying = true;
        playBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>`;
    });

    audioElement.addEventListener('pause', () => {
        isPlaying = false;
        playBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>`;
    });

    // Progress bar
    audioElement.addEventListener('timeupdate', () => {
        if (audioElement.duration) {
            const percent = (audioElement.currentTime / audioElement.duration) * 100;
            if (progressBar) progressBar.style.width = percent + '%';
            if (currentTimeEl) currentTimeEl.textContent = formatTime(audioElement.currentTime);
        }
    });

    audioElement.addEventListener('loadedmetadata', () => {
        if (durationEl) durationEl.textContent = formatTime(audioElement.duration);
    });

    // Click to seek
    if (progressContainer) {
        progressContainer.addEventListener('click', (e) => {
            const rect = progressContainer.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            audioElement.currentTime = percent * audioElement.duration;
        });
    }

    // Volume
    if (volumeSlider) {
        volumeSlider.addEventListener('input', (e) => {
            audioElement.volume = e.target.value / 100;
        });
    }
}

/**
 * Format seconds to MM:SS
 */
function formatTime(seconds) {
    if (isNaN(seconds)) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

/**
 * Initialize scroll-triggered animations
 */
function initScrollAnimations() {
    const elements = document.querySelectorAll('.scroll-animate');
    if (elements.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    elements.forEach(el => observer.observe(el));
}

/**
 * Confirm delete action
 */
function confirmDelete(message) {
    return confirm(message || 'Bạn có chắc chắn muốn xóa?');
}
