document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(toggle => {
    const chevron = toggle.querySelector('.bi-chevron-down');
    const target = document.querySelector(toggle.getAttribute('data-bs-target'));

    target.addEventListener('show.bs.collapse', () => {
        chevron.style.transition = 'transform 0.3s ease';
        chevron.style.transform = 'rotate(180deg)';
    });

    target.addEventListener('hide.bs.collapse', () => {
        chevron.style.transition = 'transform 0.3s ease';
        chevron.style.transform = 'rotate(0deg)';
    });

    target.addEventListener('show.bs.collapse', () => {
        target.style.opacity = '0';
        target.style.transform = 'translateY(-5px)';
        target.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                target.style.opacity = '1';
                target.style.transform = 'translateY(0)';
            });
        });
    });

    target.addEventListener('hide.bs.collapse', () => {
        target.style.opacity = '0';
        target.style.transform = 'translateY(-5px)';
        target.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    });
});
