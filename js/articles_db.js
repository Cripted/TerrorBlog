/**
 * Sistema de artículos conectado con la base de datos
 * Consume las APIs PHP en lugar de datos estáticos
 */

const API_BASE = '../php/';

// Obtener parámetro de la URL
function getArticleId() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}

// Función para formatear fechas
function formatearFecha(fecha) {
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    
    const date = new Date(fecha);
    const dia = date.getDate();
    const mes = meses[date.getMonth()];
    const anio = date.getFullYear();
    
    return `${dia} de ${mes}, ${anio}`;
}

// Cargar artículo desde la base de datos
async function loadArticle() {
    const articleSlug = getArticleId();
    
    if (!articleSlug) {
        document.getElementById('article-heading').textContent = 'Artículo no encontrado';
        document.getElementById('article-body').innerHTML = '<p>Lo sentimos, el artículo que buscas no existe o ha sido movido.</p>';
        return;
    }
    
    try {
        // Obtener artículo de la API
        const response = await fetch(`${API_BASE}api_articulos.php?action=get&slug=${articleSlug}`);
        const article = await response.json();
        
        if (article.error) {
            document.getElementById('article-heading').textContent = 'Artículo no encontrado';
            document.getElementById('article-body').innerHTML = `<p>${article.error}</p>`;
            return;
        }
        
        // Actualizar título de la página
        document.title = `${article.titulo} - TERROR DIGITAL`;
        
        // Actualizar contenido
        document.getElementById('article-title').textContent = article.titulo;
        document.getElementById('article-category').textContent = article.categoria || 'Artículo';
        document.getElementById('article-heading').textContent = article.titulo;
        document.getElementById('article-author').textContent = `Por ${article.autor}`;
        document.getElementById('article-date').textContent = formatearFecha(article.fecha_publicacion);
        document.getElementById('views-count').textContent = article.vistas.toLocaleString();
        
        // Imagen destacada
        const imageElement = document.getElementById('article-image');
        if (article.imagen_destacada) {
            imageElement.innerHTML = `<img src="../uploads/${article.imagen_destacada}" alt="${article.titulo}" style="width: 100%; height: 100%; object-fit: cover;">`;
        } else {
            imageElement.innerHTML = `<div class="image-placeholder">${article.categoria_icono || '📰'}</div>`;
        }
        
        // Contenido del artículo
        document.getElementById('article-body').innerHTML = article.contenido;
        
        // Tags
        const tagsContainer = document.getElementById('article-tags');
        if (article.tags && article.tags.length > 0) {
            tagsContainer.innerHTML = article.tags.map(tag => `<span class="tag">${tag}</span>`).join('');
        }
        
        // Cargar artículos relacionados
        loadRelatedArticles(article.id);
        
    } catch (error) {
        console.error('Error al cargar el artículo:', error);
        document.getElementById('article-body').innerHTML = '<p>Error al cargar el artículo. Por favor, intenta de nuevo más tarde.</p>';
    }
}

// Cargar artículos relacionados
async function loadRelatedArticles(articleId) {
    try {
        const response = await fetch(`${API_BASE}api_articulos.php?action=relacionados&articulo_id=${articleId}`);
        const relacionados = await response.json();
        
        if (relacionados && relacionados.length > 0) {
            const container = document.querySelector('.related-articles .articles-grid');
            
            container.innerHTML = relacionados.map(art => `
                <article class="article-card">
                    <div class="article-thumbnail">${art.categoria_icono || '📰'}</div>
                    <div class="article-body">
                        <span class="category-tag">${art.categoria}</span>
                        <h3>${art.titulo}</h3>
                        <p class="meta">${formatearFecha(art.fecha_publicacion)}</p>
                        <p class="excerpt">${art.extracto || ''}</p>
                        <a href="articulo.html?id=${art.slug}" class="read-more">Leer más →</a>
                    </div>
                </article>
            `).join('');
        }
    } catch (error) {
        console.error('Error al cargar artículos relacionados:', error);
    }
}

// Compartir artículo
function shareArticle(platform) {
    const url = window.location.href;
    const title = document.getElementById('article-heading').textContent;
    
    switch(platform) {
        case 'twitter':
            window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`, '_blank');
            break;
        case 'facebook':
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
            break;
        case 'reddit':
            window.open(`https://reddit.com/submit?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`, '_blank');
            break;
        case 'copy':
            navigator.clipboard.writeText(url).then(() => {
                alert('¡Link copiado al portapapeles!');
            });
            break;
    }
}

// Cargar artículos en páginas de listado (index, noticias, reviews, guias)
async function loadArticlesList(categoria = null, limit = 9, containerId = 'articles-container') {
    try {
        let url = `${API_BASE}api_articulos.php?action=list&limit=${limit}`;
        if (categoria) {
            url += `&categoria=${categoria}`;
        }
        
        const response = await fetch(url);
        const articulos = await response.json();
        
        const container = document.getElementById(containerId) || document.querySelector('.articles-grid');
        
        if (!container) return;
        
        container.innerHTML = articulos.map(art => `
            <article class="article-card">
                <div class="article-thumbnail">${art.categoria_icono || '📰'}</div>
                <div class="article-body">
                    <span class="category-tag">${art.categoria}</span>
                    <h3>${art.titulo}</h3>
                    <p class="meta">${formatearFecha(art.fecha_publicacion)}</p>
                    <p class="excerpt">${art.extracto || ''}</p>
                    <a href="pages/articulo.html?id=${art.slug}" class="read-more">Leer más →</a>
                </div>
            </article>
        `).join('');
        
    } catch (error) {
        console.error('Error al cargar artículos:', error);
    }
}

// Cargar artículo destacado
async function loadFeaturedArticle() {
    try {
        const response = await fetch(`${API_BASE}api_articulos.php?action=destacados&limit=1`);
        const articulos = await response.json();
        
        if (articulos && articulos.length > 0) {
            const art = articulos[0];
            const featuredContainer = document.querySelector('.featured-article .featured-content');
            
            if (featuredContainer) {
                featuredContainer.innerHTML = `
                    <span class="category-tag">Destacado</span>
                    <h2>${art.titulo}</h2>
                    <p class="meta">Por ${art.autor} | ${formatearFecha(art.fecha_publicacion)} | ${art.vistas || 0} vistas</p>
                    <p class="excerpt">${art.extracto || ''}</p>
                    <a href="pages/articulo.html?id=${art.slug}" class="read-more">Leer análisis completo →</a>
                `;
            }
        }
    } catch (error) {
        console.error('Error al cargar artículo destacado:', error);
    }
}

// Cargar al iniciar (solo en página de artículo)
if (document.getElementById('article-body')) {
    document.addEventListener('DOMContentLoaded', loadArticle);
}

// Para index.html
if (window.location.pathname.includes('index.html') || window.location.pathname.endsWith('/')) {
    document.addEventListener('DOMContentLoaded', () => {
        loadFeaturedArticle();
        loadArticlesList(null, 6, 'articles-container');
    });
}