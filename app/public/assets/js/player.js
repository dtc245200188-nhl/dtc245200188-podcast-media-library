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
            mainNav.classList.toggle('show-mobile');
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
    const skipBackBtn = document.getElementById('skip-back');
    const skipForwardBtn = document.getElementById('skip-forward');

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
        const iconPlay = playBtn.querySelector('.icon-play');
        const iconPause = playBtn.querySelector('.icon-pause');
        if (iconPlay) iconPlay.style.display = 'none';
        if (iconPause) iconPause.style.display = 'block';
    });

    audioElement.addEventListener('pause', () => {
        isPlaying = false;
        const iconPlay = playBtn.querySelector('.icon-play');
        const iconPause = playBtn.querySelector('.icon-pause');
        if (iconPlay) iconPlay.style.display = 'block';
        if (iconPause) iconPause.style.display = 'none';
    });

    // Skip Buttons
    if (skipBackBtn) {
        skipBackBtn.addEventListener('click', () => {
            audioElement.currentTime = Math.max(0, audioElement.currentTime - 10);
        });
    }

    if (skipForwardBtn) {
        skipForwardBtn.addEventListener('click', () => {
            audioElement.currentTime = Math.min(audioElement.duration || 0, audioElement.currentTime + 10);
        });
    }

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
