import './bootstrap';

const dashboardAwal = document.querySelector('[data-dashboard-awal]');
const explorePage = document.querySelector('[data-explore-page]');
const dashboardPages = [...document.querySelectorAll('.cz-dashboard-page')];
const themeStorageKey = 'cityzen-theme';

dashboardPages.forEach((page) => {
    const themeToggle = page.querySelector('[data-theme-toggle]');
    const themeLabel = page.querySelector('[data-theme-label]');
    const userMenu = page.querySelector('[data-user-menu]');
    const userMenuToggle = page.querySelector('[data-user-menu-toggle]');
    let toast = page.querySelector('[data-dashboard-toast]');
    let toastTimer;

    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'cz-dash-toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.dataset.dashboardToast = '';
        document.body.append(toast);
    }

    const showPageToast = (message) => {
        if (!toast || !message) return;

        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 1800);
    };

    const setTheme = (theme) => {
        const isDark = theme === 'dark';
        page.dataset.theme = isDark ? 'dark' : 'light';

        if (themeToggle) {
            themeToggle.setAttribute('aria-pressed', String(isDark));
        }

        if (themeLabel) {
            themeLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
        }
    };

    const storedTheme = window.localStorage?.getItem(themeStorageKey);
    setTheme(storedTheme === 'dark' ? 'dark' : 'light');

    themeToggle?.addEventListener('click', () => {
        const nextTheme = page.dataset.theme === 'dark' ? 'light' : 'dark';
        setTheme(nextTheme);
        window.localStorage?.setItem(themeStorageKey, nextTheme);
        showPageToast(nextTheme === 'dark' ? 'Dark mode aktif.' : 'Light mode aktif.');
    });

    page.querySelectorAll('[data-action-toast]').forEach((control) => {
        control.addEventListener('click', () => showPageToast(control.dataset.actionToast));
    });

    userMenuToggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        const isOpen = userMenu?.classList.toggle('is-open') ?? false;
        userMenuToggle.setAttribute('aria-expanded', String(isOpen));
    });

    document.addEventListener('click', (event) => {
        if (!userMenu || userMenu.contains(event.target)) return;

        userMenu.classList.remove('is-open');
        userMenuToggle?.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        userMenu?.classList.remove('is-open');
        userMenuToggle?.setAttribute('aria-expanded', 'false');
    });
});

if (dashboardAwal) {
    const searchInput = dashboardAwal.querySelector('[data-dashboard-search]');
    const posts = [...dashboardAwal.querySelectorAll('[data-feed-post]')];
    const trendButtons = [...dashboardAwal.querySelectorAll('[data-trending-place]')];
    const toast = dashboardAwal.querySelector('[data-dashboard-toast]');
    const navLinks = [...dashboardAwal.querySelectorAll('.cz-dash-nav-link[href^="#"]')];
    let toastTimer;

    const showToast = (message) => {
        if (!toast) return;

        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 1800);
    };

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

    const formatCompactNumber = (number) => {
        if (number >= 1000) {
            return `${Number((number / 1000).toFixed(1)).toString()}k`;
        }

        return String(number);
    };

    dashboardAwal.querySelectorAll('[data-async-interaction]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            const count = form.querySelector('[data-count]');
            const activeClass = button?.hasAttribute('data-like-post') ? 'is-liked' : 'is-active';

            button?.setAttribute('disabled', 'disabled');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        Accept: 'application/json',
                        'X-CityZen-Async': '1',
                    },
                });

                if (!response.ok) {
                    throw new Error('Interaction failed');
                }

                const payload = await response.json();

                button?.classList.toggle(activeClass, Boolean(payload.active));
                button?.setAttribute('aria-pressed', String(Boolean(payload.active)));

                if (count && typeof payload.count === 'number') {
                    count.textContent = formatCompactNumber(payload.count);
                }

                showToast(payload.message || 'Interaction updated.');
            } catch (error) {
                showToast('Aksi belum berhasil. Coba ulang sebentar lagi.');
            } finally {
                button?.removeAttribute('disabled');
            }
        });
    });

    dashboardAwal.querySelectorAll('[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (window.confirm('Hapus post ini dari feed CityZen?')) return;

            event.preventDefault();
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
    const tabs = explorePage.querySelector('.cz-explore-tabs');
    const items = [...explorePage.querySelectorAll('[data-explore-item]')];
    const chips = [...explorePage.querySelectorAll('[data-explore-chip]')];
    const toast = explorePage.querySelector('[data-dashboard-toast]');
    let toastTimer;
    let isDraggingTabs = false;
    let dragStartX = 0;
    let dragStartScroll = 0;

    const showToast = (message) => {
        if (!toast) return;

        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 1800);
    };

    const filterExplore = () => {
        const term = searchInput?.value.trim().toLowerCase() || '';

        items.forEach((item) => {
            item.classList.toggle('is-hidden', term.length > 0 && !item.dataset.title.includes(term));
        });
    };

    searchInput?.addEventListener('input', filterExplore);

    tabs?.addEventListener(
        'wheel',
        (event) => {
            if (Math.abs(event.deltaY) <= Math.abs(event.deltaX)) return;

            event.preventDefault();
            tabs.scrollLeft += event.deltaY;
        },
        { passive: false },
    );

    tabs?.addEventListener('pointerdown', (event) => {
        if (event.button !== 0) return;

        isDraggingTabs = true;
        dragStartX = event.clientX;
        dragStartScroll = tabs.scrollLeft;
        tabs.classList.add('is-dragging');
        tabs.setPointerCapture(event.pointerId);
    });

    tabs?.addEventListener('pointermove', (event) => {
        if (!isDraggingTabs) return;

        tabs.scrollLeft = dragStartScroll - (event.clientX - dragStartX);
    });

    const stopDraggingTabs = (event) => {
        if (!isDraggingTabs) return;

        isDraggingTabs = false;
        tabs?.classList.remove('is-dragging');

        if (event.pointerId && tabs?.hasPointerCapture(event.pointerId)) {
            tabs.releasePointerCapture(event.pointerId);
        }
    };

    tabs?.addEventListener('pointerup', stopDraggingTabs);
    tabs?.addEventListener('pointercancel', stopDraggingTabs);
    tabs?.addEventListener('pointerleave', stopDraggingTabs);

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
