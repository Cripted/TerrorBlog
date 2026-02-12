// Inicialización cuando se carga el DOM
document.addEventListener('DOMContentLoaded', function() {
    // Agregar smooth scroll a los enlaces de navegación internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Efecto de hover en las tarjetas de artículos
    document.querySelectorAll('.article-card, .news-item, .featured-article').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

/**
 * Efecto de parpadeo aleatorio en el logo
 */
setInterval(() => {
    const logo = document.querySelector('.logo');
    if (logo && Math.random() > 0.95) {
        logo.style.opacity = '0.5';
        setTimeout(() => {
            logo.style.opacity = '1';
        }, 100);
    }
}, 3000);

/**
 * Función para incrementar vistas (usada en páginas de artículos)
 */
function incrementViews(articleId) {
    let views = localStorage.getItem(`views_${articleId}`) || 0;
    views = parseInt(views) + 1;
    localStorage.setItem(`views_${articleId}`, views);
    return views;
}

/**
 * Función para obtener vistas
 */
function getViews(articleId) {
    return parseInt(localStorage.getItem(`views_${articleId}`)) || 0;
}

/**
 * Función para dar like
 */
function toggleLike(threadId) {
    const likeKey = `liked_${threadId}`;
    const countKey = `likes_${threadId}`;
    
    const hasLiked = localStorage.getItem(likeKey) === 'true';
    let likeCount = parseInt(localStorage.getItem(countKey)) || 0;
    
    if (hasLiked) {
        likeCount = Math.max(0, likeCount - 1);
        localStorage.setItem(likeKey, 'false');
    } else {
        likeCount += 1;
        localStorage.setItem(likeKey, 'true');
    }
    
    localStorage.setItem(countKey, likeCount);
    
    return {
        liked: !hasLiked,
        count: likeCount
    };
}

/**
 * Función para obtener estado de like
 */
function getLikeStatus(threadId) {
    const likeKey = `liked_${threadId}`;
    const countKey = `likes_${threadId}`;
    
    return {
        liked: localStorage.getItem(likeKey) === 'true',
        count: parseInt(localStorage.getItem(countKey)) || 0
    };
}