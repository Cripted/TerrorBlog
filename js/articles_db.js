/**
 * articles_db.js — Terror Digital
 * Carga artículos desde la API PHP y los inyecta en los HTMLs
 */

// ── Detectar ruta base a la API ───────────────────────────
function getApiBase() {
    const path = window.location.pathname;
    // Si estamos dentro de /pages/ la API está un nivel arriba
    if (path.includes('/pages/')) return '../php/';
    return 'php/';
}
const API_BASE = getApiBase();

// ── Detectar si es la home ────────────────────────────────
function esHome() {
    const path = window.location.pathname;
    return path.endsWith('index.html') || path.endsWith('/TerrorBlog/') || path === '/';
}

// ── Detectar si es página de artículo ────────────────────
function esArticulo() {
    return window.location.pathname.includes('articulo.html');
}

// ── Leer ?id= de la URL ───────────────────────────────────
function getSlugFromURL() {
    return new URLSearchParams(window.location.search).get('id');
}

// ── Formatear fecha ───────────────────────────────────────
function formatearFecha(fecha) {
    if (!fecha) return '—';
    const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                   'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const d = new Date(fecha);
    if (isNaN(d)) return fecha;
    return `${d.getDate()} de ${meses[d.getMonth()]}, ${d.getFullYear()}`;
}

// ── Poner texto en un elemento por id (seguro) ───────────
function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

// ─────────────────────────────────────────────────────────
// PÁGINA DE ARTÍCULO INDIVIDUAL
// ─────────────────────────────────────────────────────────
async function loadArticle() {
    const slug = getSlugFromURL();

    if (!slug) {
        setText('article-heading', 'Artículo no encontrado');
        const body = document.getElementById('article-body');
        if (body) body.innerHTML = '<p>No se especificó ningún artículo.</p>';
        return;
    }

    try {
        const res = await fetch(`${API_BASE}api_articulos.php?action=get&slug=${encodeURIComponent(slug)}`);
        const art = await res.json();

        if (art.error) {
            setText('article-heading', 'Artículo no encontrado');
            const body = document.getElementById('article-body');
            if (body) body.innerHTML = `<p>${art.error}</p>`;
            return;
        }

        // Título de la pestaña
        document.title = `${art.titulo} — TERROR DIGITAL`;

        // Metadatos del artículo
        setText('article-title',    art.titulo);
        setText('article-category', art.categoria || 'Artículo');
        setText('article-heading',  art.titulo);
        setText('article-author',   `Por ${art.autor || 'Redacción'}`);
        setText('article-date',     formatearFecha(art.fecha_publicacion));
        setText('views-count',      Number(art.vistas || 0).toLocaleString());

        // Imagen destacada
        const imgEl = document.getElementById('article-image');
        if (imgEl) {
            imgEl.innerHTML = art.imagen_destacada
                ? `<img src="../uploads/${art.imagen_destacada}" alt="${art.titulo}"
                        style="width:100%;height:100%;object-fit:cover;">`
                : `<div class="image-placeholder">${art.categoria_icono || '📰'}</div>`;
        }

        // Contenido HTML
        const bodyEl = document.getElementById('article-body');
        if (bodyEl) bodyEl.innerHTML = art.contenido || '';

        // Tags
        const tagsEl = document.getElementById('article-tags');
        if (tagsEl && Array.isArray(art.tags) && art.tags.length) {
            tagsEl.innerHTML = art.tags.map(t => `<span class="tag">${t}</span>`).join('');
        }

        // Calificación (si hay un .rating-score en el contenido)
        if (art.calificacion) {
            const ratingEl = document.querySelector('.rating-box .rating-score');
            if (ratingEl) ratingEl.textContent = art.calificacion;
        }

        // Artículos relacionados
        if (art.id) loadRelacionados(art.id);

    } catch (err) {
        console.error('Error cargando artículo:', err);
        const bodyEl = document.getElementById('article-body');
        if (bodyEl) bodyEl.innerHTML = '<p>Error al cargar el artículo. Intenta más tarde.</p>';
    }
}

// ── Artículos relacionados ────────────────────────────────
async function loadRelacionados(articuloId) {
    try {
        const res  = await fetch(`${API_BASE}api_articulos.php?action=relacionados&articulo_id=${articuloId}`);
        const arts = await res.json();

        if (!arts || arts.error || arts.length === 0) return;

        // El HTML usa id="related-grid" o la clase .related-articles .articles-grid
        const container = document.getElementById('related-grid')
                       || document.querySelector('.related-articles .articles-grid');
        if (!container) return;

        container.innerHTML = arts.map(a => `
            <article class="article-card">
                <div class="article-thumbnail">${a.categoria_icono || '📰'}</div>
                <div class="article-body">
                    <span class="category-tag">${a.categoria || ''}</span>
                    <h3>${a.titulo}</h3>
                    <p class="meta">${formatearFecha(a.fecha_publicacion)}</p>
                    <p class="excerpt">${a.extracto || ''}</p>
                    <a href="articulo.html?id=${a.slug}" class="read-more">Leer más →</a>
                </div>
            </article>
        `).join('');
    } catch (e) {
        console.error('Error cargando relacionados:', e);
    }
}

// ─────────────────────────────────────────────────────────
// LISTADOS (noticias, reviews, guías)
// ─────────────────────────────────────────────────────────
async function loadArticlesList(categoria = null, limit = 9, containerId = 'articles-container') {
    const container = document.getElementById(containerId)
                   || document.querySelector('.articles-grid');
    if (!container) return;

    // Mostrar spinner mientras carga
    container.innerHTML = '<p style="text-align:center;color:#666;grid-column:1/-1;padding:3rem;">Cargando artículos...</p>';

    try {
        let url = `${API_BASE}api_articulos.php?action=list&limit=${limit}`;
        if (categoria) url += `&categoria=${encodeURIComponent(categoria)}`;

        const res  = await fetch(url);
        const arts = await res.json();

        // Si la API no devuelve datos, dejar el contenido estático del HTML
        if (!arts || arts.error || arts.length === 0) {
            container.innerHTML = '';   // limpiar spinner, el HTML estático ya no existe
            return;
        }

        // Si estamos en /pages/ el enlace es directo, si es raíz necesita pages/
        const prefix = window.location.pathname.includes('/pages/') ? '' : 'pages/';

        container.innerHTML = arts.map(a => `
            <article class="article-card">
                <div class="article-thumbnail">${a.categoria_icono || '📰'}</div>
                <div class="article-body">
                    <span class="category-tag">${a.categoria || ''}</span>
                    <h3>${a.titulo}</h3>
                    <p class="meta">${formatearFecha(a.fecha_publicacion)}</p>
                    <p class="excerpt">${a.extracto || ''}</p>
                    <a href="${prefix}articulo.html?id=${a.slug}" class="read-more">Leer más →</a>
                </div>
            </article>
        `).join('');

    } catch (e) {
        console.error('Error cargando lista:', e);
        // Si falla la API dejamos el contenido HTML estático intacto (no borramos)
        container.innerHTML = '';
    }
}

// ─────────────────────────────────────────────────────────
// HOME — artículo destacado + listado reciente
// ─────────────────────────────────────────────────────────
async function loadFeaturedArticle() {
    try {
        const res  = await fetch(`${API_BASE}api_articulos.php?action=destacados&limit=1`);
        const arts = await res.json();

        if (!arts || arts.error || arts.length === 0) return;

        const art = arts[0];
        const el  = document.querySelector('.featured-article .featured-content');
        if (!el) return;

        el.innerHTML = `
            <span class="category-tag">Destacado</span>
            <h2>${art.titulo}</h2>
            <p class="meta">
                Por ${art.autor || 'Redacción'} |
                ${formatearFecha(art.fecha_publicacion)} |
                ${Number(art.vistas || 0).toLocaleString()} vistas
            </p>
            <p class="excerpt">${art.extracto || ''}</p>
            <a href="pages/articulo.html?id=${art.slug}" class="read-more">Leer análisis completo →</a>
        `;
    } catch (e) {
        console.error('Error cargando destacado:', e);
    }
}

// ─────────────────────────────────────────────────────────
// COMPARTIR
// ─────────────────────────────────────────────────────────
function shareArticle(platform) {
    const url   = window.location.href;
    const title = document.getElementById('article-heading')?.textContent || 'Terror Digital';

    const links = {
        twitter:  `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`,
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
        reddit:   `https://reddit.com/submit?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`,
    };

    if (links[platform]) {
        window.open(links[platform], '_blank');
    } else if (platform === 'copy') {
        navigator.clipboard.writeText(url)
            .then(() => alert('¡Link copiado al portapapeles!'))
            .catch(() => prompt('Copia este link:', url));
    }
}

// ─────────────────────────────────────────────────────────
// INICIALIZACIÓN AUTOMÁTICA
// ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (esArticulo()) {
        loadArticle();
        return;
    }
    if (esHome()) {
        loadFeaturedArticle();
        loadArticlesList(null, 6, 'articles-container');
    }
});