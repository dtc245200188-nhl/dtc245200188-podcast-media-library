/**
 * PodcastHub - Audio Player, Favorites & UI Interactions
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

    // ---- User Menu Dropdown ----
    const userTrigger = document.getElementById('user-menu-trigger');
    const userDropdown = document.getElementById('user-dropdown');
    if (userTrigger && userDropdown) {
        userTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
        document.addEventListener('click', () => {
            userDropdown.classList.remove('show');
        });
    }

    // ---- In-Page Episode Player (if on episode.php) ----
    initAudioPlayer();

    // ---- Global Sticky Bottom Audio Player ----
    initStickyPlayer();

    // ---- Favorites / Bookmarking System ----
    initFavorites();

    // ---- Scroll Animations ----
    initScrollAnimations();

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
 * =========================================================
 * GLOBAL STICKY BOTTOM AUDIO PLAYER
 * =========================================================
 */
let currentPlayingSlug = null;

function initStickyPlayer() {
    const stickyPlayer = document.getElementById('sticky-player');
    const globalAudio = document.getElementById('global-audio-element');
    if (!stickyPlayer || !globalAudio) return;

    const playToggleBtn = document.getElementById('sticky-play-toggle');
    const iconPlay = playToggleBtn ? playToggleBtn.querySelector('.sticky-icon-play') : null;
    const iconPause = playToggleBtn ? playToggleBtn.querySelector('.sticky-icon-pause') : null;
    const skipBack = document.getElementById('sticky-skip-back');
    const skipForward = document.getElementById('sticky-skip-forward');
    const progressContainer = document.getElementById('sticky-progress-bar-container');
    const progressBar = document.getElementById('sticky-progress-bar');
    const timeCurrent = document.getElementById('sticky-time-current');
    const timeDuration = document.getElementById('sticky-time-duration');
    const volumeSlider = document.getElementById('sticky-volume-slider');
    const volumeIcon = document.getElementById('sticky-volume-icon');
    const closeBtn = document.getElementById('sticky-close-btn');
    const titleEl = document.getElementById('sticky-title');
    const catEl = document.getElementById('sticky-cat');
    const coverImg = document.getElementById('sticky-cover');
    const coverPlaceholder = document.getElementById('sticky-placeholder');
    const stickyFavBtn = document.getElementById('sticky-fav-btn');

    // Handle clicks on any [data-play-trigger] elements
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-play-trigger]');
        if (!trigger) return;

        // If inside an episode card play button, prevent navigating if has audio file
        const audioSrc = trigger.getAttribute('data-audio');
        if (audioSrc && audioSrc.trim() !== '') {
            e.preventDefault();
            e.stopPropagation();

            const title = trigger.getAttribute('data-title') || 'Tập Podcast';
            const slug = trigger.getAttribute('data-slug') || '';
            const cat = trigger.getAttribute('data-cat') || 'Podcast';
            const cover = trigger.getAttribute('data-cover') || '';

            playStickyTrack({ audioSrc, title, slug, cat, cover });
        }
    });

    function playStickyTrack(track) {
        currentPlayingSlug = track.slug;

        // Update UI
        if (titleEl) {
            titleEl.textContent = track.title;
            titleEl.href = track.slug ? '/episode.php?slug=' + encodeURIComponent(track.slug) : '#';
        }
        if (catEl) catEl.textContent = track.cat;
        
        if (coverImg && coverPlaceholder) {
            if (track.cover && track.cover.trim() !== '') {
                coverImg.src = track.cover;
                coverImg.style.display = 'block';
                coverPlaceholder.style.display = 'none';
            } else {
                coverImg.style.display = 'none';
                coverPlaceholder.style.display = 'flex';
            }
        }

        // Update Sticky Favorite Button
        if (stickyFavBtn) {
            stickyFavBtn.setAttribute('data-slug', track.slug);
            stickyFavBtn.setAttribute('data-title', track.title);
            updateFavoriteButtonState(stickyFavBtn, track.slug);
        }

        // Show sticky bar
        stickyPlayer.style.display = 'block';

        // Set audio source & play
        if (globalAudio.src !== window.location.origin + track.audioSrc && !globalAudio.src.endsWith(track.audioSrc)) {
            globalAudio.src = track.audioSrc;
            globalAudio.load();
        }

        globalAudio.play().then(() => {
            updatePlayIcon(true);
        }).catch(err => {
            console.log('Audio autoplay prevented:', err);
            updatePlayIcon(false);
        });
    }

    function updatePlayIcon(isPlaying) {
        if (iconPlay && iconPause) {
            iconPlay.style.display = isPlaying ? 'none' : 'block';
            iconPause.style.display = isPlaying ? 'block' : 'none';
        }
    }

    // Play/Pause toggle
    if (playToggleBtn) {
        playToggleBtn.addEventListener('click', () => {
            if (globalAudio.paused) {
                globalAudio.play().then(() => updatePlayIcon(true)).catch(() => {});
            } else {
                globalAudio.pause();
                updatePlayIcon(false);
            }
        });
    }

    globalAudio.addEventListener('play', () => updatePlayIcon(true));
    globalAudio.addEventListener('pause', () => updatePlayIcon(false));
    globalAudio.addEventListener('ended', () => {
        updatePlayIcon(false);
        if (progressBar) progressBar.style.width = '0%';
    });

    // Skip Buttons
    if (skipBack) {
        skipBack.addEventListener('click', () => {
            globalAudio.currentTime = Math.max(0, globalAudio.currentTime - 10);
        });
    }
    if (skipForward) {
        skipForward.addEventListener('click', () => {
            globalAudio.currentTime = Math.min(globalAudio.duration || 0, globalAudio.currentTime + 10);
        });
    }

    // Time & Progress Update
    globalAudio.addEventListener('timeupdate', () => {
        if (globalAudio.duration && !isNaN(globalAudio.duration)) {
            const percent = (globalAudio.currentTime / globalAudio.duration) * 100;
            if (progressBar) progressBar.style.width = percent + '%';
            if (timeCurrent) timeCurrent.textContent = formatTime(globalAudio.currentTime);
        }
    });

    globalAudio.addEventListener('loadedmetadata', () => {
        if (timeDuration && !isNaN(globalAudio.duration)) {
            timeDuration.textContent = formatTime(globalAudio.duration);
        }
    });

    // Click on progress bar to seek
    if (progressContainer) {
        progressContainer.addEventListener('click', (e) => {
            if (!globalAudio.duration || isNaN(globalAudio.duration)) return;
            const rect = progressContainer.getBoundingClientRect();
            const clickPos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
            globalAudio.currentTime = clickPos * globalAudio.duration;
        });
    }

    // Volume Slider
    if (volumeSlider) {
        volumeSlider.addEventListener('input', (e) => {
            const vol = e.target.value / 100;
            globalAudio.volume = vol;
        });
    }

    // Close / Hide Player
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            globalAudio.pause();
            updatePlayIcon(false);
            stickyPlayer.style.display = 'none';
        });
    }
}

/**
 * =========================================================
 * IN-PAGE AUDIO PLAYER (episode.php)
 * =========================================================
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

    if (progressContainer) {
        progressContainer.addEventListener('click', (e) => {
            const rect = progressContainer.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            audioElement.currentTime = percent * audioElement.duration;
        });
    }

    if (volumeSlider) {
        volumeSlider.addEventListener('input', (e) => {
            audioElement.volume = e.target.value / 100;
        });
    }
}

/**
 * =========================================================
 * FAVORITES (BOOKMARK) SYSTEM
 * =========================================================
 */
function getFavorites() {
    try {
        const stored = localStorage.getItem('podcasthub_favorites');
        return stored ? JSON.parse(stored) : [];
    } catch (e) {
        return [];
    }
}

function saveFavorites(favs) {
    try {
        localStorage.setItem('podcasthub_favorites', JSON.stringify(favs));
    } catch (e) {}
}

function updateFavoriteButtonState(btn, slug) {
    if (!btn || !slug) return;
    const favs = getFavorites();
    const isFav = favs.includes(slug);
    btn.classList.toggle('active', isFav);
    btn.setAttribute('aria-pressed', isFav ? 'true' : 'false');
    if (isFav) {
        btn.setAttribute('title', 'Đã lưu trong mục yêu thích (Click để bỏ)');
    } else {
        btn.setAttribute('title', 'Thêm vào mục yêu thích');
    }
}

function initFavorites() {
    // Sync initial state of all favorite buttons on page
    const favButtons = document.querySelectorAll('.card-fav-btn, #sticky-fav-btn');
    favButtons.forEach(btn => {
        const slug = btn.getAttribute('data-slug');
        if (slug) updateFavoriteButtonState(btn, slug);
    });

    // Delegate click for favorite buttons
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.card-fav-btn, #sticky-fav-btn');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const slug = btn.getAttribute('data-slug');
        const title = btn.getAttribute('data-title') || 'Tập podcast';
        if (!slug) return;

        let favs = getFavorites();
        let isNowFav = false;

        if (favs.includes(slug)) {
            favs = favs.filter(s => s !== slug);
            isNowFav = false;
            showToast('Đã bỏ yêu thích: ' + title);
        } else {
            favs.push(slug);
            isNowFav = true;
            showToast('❤️ Đã thêm vào yêu thích: ' + title);
        }

        saveFavorites(favs);

        // Update all buttons with this slug on the page
        document.querySelectorAll(`[data-slug="${slug}"]`).forEach(b => {
            if (b.classList.contains('card-fav-btn') || b.id === 'sticky-fav-btn') {
                b.classList.toggle('active', isNowFav);
                b.setAttribute('aria-pressed', isNowFav ? 'true' : 'false');
            }
        });
    });
}

/**
 * =========================================================
 * TOAST NOTIFICATION
 * =========================================================
 */
let toastTimeout = null;
function showToast(message) {
    const toast = document.getElementById('podcast-toast');
    if (!toast) return;

    toast.textContent = message;
    toast.classList.add('show');

    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}

/**
 * =========================================================
 * UTILITY HELPERS
 * =========================================================
 */
function formatTime(seconds) {
    if (isNaN(seconds) || seconds < 0) return '00:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

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

function confirmDelete(message) {
    return confirm(message || 'Bạn có chắc chắn muốn xóa?');
}
