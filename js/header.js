/**
 * header.js — Terror Digital
 * Inyecta el header completo en todas las páginas HTML.
 * Muestra "Login" o el nombre del admin según la sesión activa.
 */

(function () {
    const enPages = window.location.pathname.includes('/pages/');
    const raiz    = enPages ? '../' : '';
    const apiUrl  = raiz + 'php/';
    const adminUrl= raiz + 'admin/';

    // Resalta el enlace activo
    function activo(palabra) {
        return window.location.pathname.includes(palabra) ? ' class="nav-active"' : '';
    }

    function buildHeader(adminLabel, adminHref) {
        return `
            <h1 class="logo glitch"><a href="${raiz}index.html">TERROR DIGITAL</a></h1>
            <p class="tagline">El Horror Nunca Duerme</p>
            <nav>
                <a href="${raiz}pages/noticias.html"${activo('noticias')}>Noticias</a>
                <a href="${raiz}pages/reviews.html"${activo('reviews')}>Reviews</a>
                <a href="${raiz}pages/guias.html"${activo('guias')}>Guías</a>
                <a href="${raiz}pages/discusiones.html"${activo('discusiones')}>Discusiones</a>
                <a href="${raiz}pages/comunidad.html"${activo('comunidad')}>Comunidad</a>
                <a href="${adminHref}" class="nav-admin-btn">${adminLabel}</a>
            </nav>
        `;
    }

    function inject(html) {
        const el = document.querySelector('header');
        if (el) el.innerHTML = html;
    }

    async function init() {
        try {
            const res  = await fetch(`${apiUrl}api_session.php`, { cache: 'no-store' });
            const data = await res.json();

            if (data.loggedIn) {
                inject(buildHeader(
                    `👤 ${data.nombre}`,
                    `${adminUrl}index.php`
                ));
            } else {
                inject(buildHeader('🔐 Login', `${adminUrl}login.php`));
            }
        } catch {
            // Sin servidor PHP → fallback estático
            inject(buildHeader('🔐 Login', `${adminUrl}login.php`));
        }
    }

    document.addEventListener('DOMContentLoaded', init);
})();