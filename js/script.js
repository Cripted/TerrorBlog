document.addEventListener('DOMContentLoaded', function () {

    // Smooth scroll en enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.getElementById(this.getAttribute('href').substring(1));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // Hover en tarjetas (respaldo para navegadores sin CSS :hover en transform)
    document.querySelectorAll('.article-card, .news-item').forEach(card => {
        card.addEventListener('mouseenter', function () { this.style.transform = 'translateY(-5px)'; });
        card.addEventListener('mouseleave', function () { this.style.transform = 'translateY(0)'; });
    });
});

// Parpadeo aleatorio del logo
setInterval(() => {
    const logo = document.querySelector('.logo');
    if (logo && Math.random() > 0.95) {
        logo.style.opacity = '0.5';
        setTimeout(() => { logo.style.opacity = '1'; }, 100);
    }
}, 3000);