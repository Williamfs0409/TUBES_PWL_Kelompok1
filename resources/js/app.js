import './bootstrap';

const publicPage = document.querySelector('[data-public-page]');

if (publicPage) {
    const toast = document.querySelector('#toast');
    let toastTimer;

    const showToast = (message) => {
        if (!toast) return;

        toast.textContent = message;
        toast.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 2400);
    };

    document.querySelector('[data-public-search]')?.addEventListener('click', () => {
        window.location.href = '/dashboard';
    });

    document.querySelector('[data-public-notify]')?.addEventListener('click', () => {
        showToast('CityZen updates will appear here');
    });
}
