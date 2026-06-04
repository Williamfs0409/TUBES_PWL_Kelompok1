import './bootstrap';

const dashboardAwal = document.querySelector('[data-dashboard-awal]');
const explorePage = document.querySelector('[data-explore-page]');

if (dashboardAwal) {
    const searchInput = dashboardAwal.querySelector('[data-dashboard-search]');
    const posts = [...dashboardAwal.querySelectorAll('[data-feed-post]')];
    const trendButtons = [...dashboardAwal.querySelectorAll('[data-trending-place]')];
    const toast = dashboardAwal.querySelector('[data-dashboard-toast]');
    const themeToggle = dashboardAwal.querySelector('[data-theme-toggle]');
    const themeLabel = dashboardAwal.querySelector('[data-theme-label]');
    const navLinks = [...dashboardAwal.querySelectorAll('.cz-dash-nav-link[href^="#"]')];
    let toastTimer;

    const showToast = (message) => {
        if (!toast) return;

        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 1800);
    };

    const setTheme = (theme) => {
        const isDark = theme === 'dark';
        dashboardAwal.dataset.theme = isDark ? 'dark' : 'light';

        if (themeToggle) {
            themeToggle.setAttribute('aria-pressed', String(isDark));
        }

        if (themeLabel) {
            themeLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
        }
    };

    setTheme('light');

    themeToggle?.addEventListener('click', () => {
        const nextTheme = dashboardAwal.dataset.theme === 'dark' ? 'light' : 'dark';
        setTheme(nextTheme);
        showToast(nextTheme === 'dark' ? 'Dark mode aktif.' : 'Light mode aktif.');
    });

    const filterDashboard = () => {
        const term = searchInput?.value.trim().toLowerCase() || '';

        posts.forEach((post) => {
            post.classList.toggle('is-hidden', term.length > 0 && !post.dataset.title.includes(term));
        });

        trendButtons.forEach((button) => {
            button.classList.toggle('is-hidden', term.length > 0 && !button.dataset.title.includes(term));
        });
    };

    searchInput?.addEventListener('input', filterDashboard);

    if (dashboardAwal.dataset.dashboardFlash) {
        showToast(dashboardAwal.dataset.dashboardFlash);
    }

    const scrollToDashboardSection = (hash, updateHistory = true) => {
        const target = dashboardAwal.querySelector(hash);

        if (!target) return;

        navLinks.forEach((link) => {
            link.classList.toggle('is-active', link.getAttribute('href') === hash);
        });

        const top = target.getBoundingClientRect().top + window.scrollY - 12;
        window.scrollTo({ top: Math.max(top, 0), left: 0, behavior: 'smooth' });

        if (updateHistory) {
            history.pushState(null, '', hash);
        }
    };

    navLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            scrollToDashboardSection(link.getAttribute('href'));
        });
    });

    if (window.location.hash) {
        window.setTimeout(() => scrollToDashboardSection(window.location.hash, false), 0);
    }

    dashboardAwal.querySelectorAll('[data-action-toast]').forEach((control) => {
        control.addEventListener('click', () => showToast(control.dataset.actionToast));
    });

    dashboardAwal.querySelectorAll('[data-like-post]').forEach((button) => {
        button.addEventListener('click', () => {
            const isLiked = button.classList.toggle('is-liked');
            button.setAttribute('aria-pressed', String(isLiked));
            showToast(isLiked ? 'Post liked.' : 'Like removed.');
        });
    });

    dashboardAwal.querySelectorAll('[data-bookmark-post]').forEach((button) => {
        button.addEventListener('click', () => {
            const isBookmarked = button.classList.toggle('is-active');
            button.setAttribute('aria-pressed', String(isBookmarked));
            showToast(isBookmarked ? 'Saved to bookmarks.' : 'Bookmark removed.');
        });
    });

    trendButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = button.dataset.trendingPlace || '';
                filterDashboard();
                searchInput.focus();
            }

            showToast(`Showing ${button.dataset.trendingPlace}.`);
        });
    });
}

if (explorePage) {
    const searchInput = explorePage.querySelector('[data-explore-search]');
    const items = [...explorePage.querySelectorAll('[data-explore-item]')];
    const chips = [...explorePage.querySelectorAll('[data-explore-chip]')];
    const toast = explorePage.querySelector('[data-dashboard-toast]');
    const themeToggle = explorePage.querySelector('[data-theme-toggle]');
    const themeLabel = explorePage.querySelector('[data-theme-label]');
    let toastTimer;

    const showToast = (message) => {
        if (!toast) return;

        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 1800);
    };

    const setTheme = (theme) => {
        const isDark = theme === 'dark';
        explorePage.dataset.theme = isDark ? 'dark' : 'light';

        if (themeToggle) {
            themeToggle.setAttribute('aria-pressed', String(isDark));
        }

        if (themeLabel) {
            themeLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
        }
    };

    const filterExplore = () => {
        const term = searchInput?.value.trim().toLowerCase() || '';

        items.forEach((item) => {
            item.classList.toggle('is-hidden', term.length > 0 && !item.dataset.title.includes(term));
        });
    };

    setTheme('light');

    themeToggle?.addEventListener('click', () => {
        const nextTheme = explorePage.dataset.theme === 'dark' ? 'light' : 'dark';
        setTheme(nextTheme);
        showToast(nextTheme === 'dark' ? 'Dark mode aktif.' : 'Light mode aktif.');
    });

    searchInput?.addEventListener('input', filterExplore);

    chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = chip.dataset.exploreChip || '';
                filterExplore();
                searchInput.focus();
            }

            chips.forEach((item) => item.classList.remove('is-active'));
            chip.classList.add('is-active');
        });
    });
}
