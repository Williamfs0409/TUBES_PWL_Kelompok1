import './bootstrap';

const dashboardAwal = document.querySelector('[data-dashboard-awal]');

if (dashboardAwal) {
    const searchInput = dashboardAwal.querySelector('[data-dashboard-search]');
    const cards = [...dashboardAwal.querySelectorAll('[data-place-card]')];
    const toast = dashboardAwal.querySelector('[data-dashboard-toast]');
    let toastTimer;

    const showToast = (message) => {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 1800);
    };

    searchInput?.addEventListener('input', () => {
        const term = searchInput.value.trim().toLowerCase();
        cards.forEach((card) => {
            card.classList.toggle('is-hidden', term.length > 0 && !card.dataset.title.includes(term));
        });
    });

    dashboardAwal.querySelectorAll('[data-like-place]').forEach((button) => {
        button.addEventListener('click', () => {
            button.classList.toggle('is-active');
            showToast(button.classList.contains('is-active') ? 'Place liked.' : 'Like removed.');
        });
    });

    dashboardAwal.querySelectorAll('[data-bookmark-place]').forEach((button) => {
        button.addEventListener('click', () => {
            button.classList.toggle('is-active');
            showToast(button.classList.contains('is-active') ? 'Saved to bookmarks.' : 'Bookmark removed.');
        });
    });

    dashboardAwal.querySelectorAll('[data-report-place]').forEach((button) => {
        button.addEventListener('click', () => showToast('Report draft opened.'));
    });

    dashboardAwal.querySelectorAll('[data-report-action]').forEach((button) => {
        button.addEventListener('click', () => showToast(`Prepared report: ${button.dataset.reportAction}.`));
    });

    dashboardAwal.querySelectorAll('[data-trending-place]').forEach((button) => {
        button.addEventListener('click', () => {
            searchInput.value = button.dataset.trendingPlace;
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
            showToast(`Showing ${button.dataset.trendingPlace}.`);
        });
    });
}
