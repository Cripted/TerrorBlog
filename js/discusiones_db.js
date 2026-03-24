/**
 * discusiones_db.js — Terror Digital
 * Gestiona el foro de discusiones: carga threads, comentarios y formularios
 */

// ── API base (siempre estamos en /pages/) ─────────────────
const DISC_API = '../php/';
let currentThreadId = null;

// ── Formatear fecha relativa o absoluta ───────────────────
function formatDiscFecha(fecha) {
    if (!fecha) return '—';
    const d     = new Date(fecha);
    const ahora = new Date();
    const diff  = Math.floor((ahora - d) / 1000);

    if (diff < 60)     return 'hace unos segundos';
    if (diff < 3600)   return `hace ${Math.floor(diff / 60)} minutos`;
    if (diff < 86400)  return `hace ${Math.floor(diff / 3600)} horas`;
    if (diff < 604800) return `hace ${Math.floor(diff / 86400)} días`;

    const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                   'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    return `${d.getDate()} de ${meses[d.getMonth()]}, ${d.getFullYear()}`;
}

// ─────────────────────────────────────────────────────────
// CARGAR DISCUSIONES DEL JUEGO SELECCIONADO
// ─────────────────────────────────────────────────────────
async function loadDiscussions() {
    const gameSelect = document.getElementById('game-select');
    const container  = document.getElementById('threads-container');
    if (!gameSelect || !container) return;

    const juego = gameSelect.value;
    container.innerHTML = '<p style="text-align:center;color:#666;padding:2rem;">Cargando discusiones...</p>';

    try {
        const res     = await fetch(`${DISC_API}api_discusiones.php?action=list&juego=${encodeURIComponent(juego)}`);
        const threads = await res.json();

        if (threads.error) {
            container.innerHTML = `<p style="text-align:center;color:#f66;padding:2rem;">${threads.error}</p>`;
            return;
        }

        if (!threads.length) {
            container.innerHTML = '<p style="text-align:center;color:#666;padding:2rem;">No hay discusiones todavía. ¡Sé el primero en iniciar una!</p>';
            return;
        }

        container.innerHTML = '';

        threads.forEach(t => {
            const views  = t.vistas >= 1000 ? (t.vistas / 1000).toFixed(1) + 'K' : t.vistas;
            const liked  = localStorage.getItem(`liked_${t.id}`) === 'true';

            const div = document.createElement('div');
            div.className = 'thread';
            div.innerHTML = `
                <div class="thread-header">
                    <h4 class="thread-title">${t.titulo}</h4>
                    <div class="thread-meta">
                        <span>💬 ${t.total_comentarios || 0} respuestas</span>
                        <span>👁️ ${views} vistas</span>
                    </div>
                </div>
                <p class="thread-content">${t.contenido}</p>
                <p class="meta">
                    Por <span class="thread-author">${t.autor_nombre}</span>
                    | ${formatDiscFecha(t.fecha_creacion)}
                </p>
                <div class="thread-actions">
                    <button class="like-btn ${liked ? 'liked' : ''}"
                            onclick="handleLike(${t.id}, event)">
                        <span class="like-icon">${liked ? '❤️' : '🤍'}</span>
                        <span class="like-count">${t.likes || 0}</span> Me gusta
                    </button>
                    <button class="comment-btn" onclick="openCommentsModal(${t.id})">
                        💬 Ver comentarios (${t.total_comentarios || 0})
                    </button>
                </div>
            `;
            container.appendChild(div);
        });

    } catch (e) {
        console.error('Error cargando discusiones:', e);
        container.innerHTML = '<p style="text-align:center;color:#f66;padding:2rem;">Error al cargar discusiones.</p>';
    }
}

// ─────────────────────────────────────────────────────────
// DAR LIKE
// ─────────────────────────────────────────────────────────
async function handleLike(threadId, event) {
    event.stopPropagation();
    const btn     = event.currentTarget;
    const likeKey = `liked_${threadId}`;

    // Ya dio like → solo actualizar ícono visualmente
    if (localStorage.getItem(likeKey) === 'true') {
        btn.querySelector('.like-icon').textContent = '❤️';
        return;
    }

    try {
        const fd = new FormData();
        fd.append('action',       'like');
        fd.append('discusion_id', threadId);

        const res    = await fetch(`${DISC_API}api_discusiones.php`, { method: 'POST', body: fd });
        const result = await res.json();

        if (result.success) {
            btn.querySelector('.like-icon').textContent  = '❤️';
            btn.querySelector('.like-count').textContent = result.likes;
            btn.classList.add('liked');
            localStorage.setItem(likeKey, 'true');
        }
    } catch (e) {
        console.error('Error al dar like:', e);
    }
}

// ─────────────────────────────────────────────────────────
// MODAL DE COMENTARIOS
// ─────────────────────────────────────────────────────────
async function openCommentsModal(threadId) {
    currentThreadId = threadId;

    try {
        const res    = await fetch(`${DISC_API}api_discusiones.php?action=get&id=${threadId}`);
        const thread = await res.json();

        if (thread.error) { alert(thread.error); return; }

        document.getElementById('modal-thread-title').textContent = thread.titulo;
        document.getElementById('modal-thread-content').innerHTML = `
            <p class="thread-content">${thread.contenido}</p>
            <p class="meta">
                Por <span class="thread-author">${thread.autor_nombre}</span>
                | ${formatDiscFecha(thread.fecha_creacion)}
            </p>
        `;

        await loadComments(threadId);

        const modal = document.getElementById('comments-modal');
        if (modal) modal.style.display = 'block';

    } catch (e) {
        console.error('Error abriendo modal:', e);
    }
}

function closeCommentsModal() {
    const modal = document.getElementById('comments-modal');
    if (modal) modal.style.display = 'none';
    currentThreadId = null;
    loadDiscussions(); // refrescar contadores
}

// ─────────────────────────────────────────────────────────
// COMENTARIOS
// ─────────────────────────────────────────────────────────
async function loadComments(threadId) {
    const list  = document.getElementById('comments-list');
    const count = document.getElementById('comment-count');
    if (!list) return;

    try {
        const res      = await fetch(`${DISC_API}api_discusiones.php?action=comentarios&discusion_id=${threadId}`);
        const comments = await res.json();

        if (count) count.textContent = Array.isArray(comments) ? comments.length : 0;

        if (!Array.isArray(comments) || !comments.length) {
            list.innerHTML = '<p class="no-comments">No hay comentarios todavía. ¡Sé el primero!</p>';
            return;
        }

        list.innerHTML = comments.map(c => `
            <div class="comment">
                <div class="comment-header">
                    <strong class="thread-author">${c.autor_nombre}</strong>
                    <span class="comment-time">${formatDiscFecha(c.fecha_creacion)}</span>
                </div>
                <p class="comment-text">${c.contenido}</p>
            </div>
        `).join('');

    } catch (e) {
        console.error('Error cargando comentarios:', e);
        list.innerHTML = '<p class="no-comments">Error al cargar comentarios.</p>';
    }
}

async function addComment(event) {
    event.preventDefault();
    if (!currentThreadId) return;

    const username = document.getElementById('comment-username').value.trim();
    const text     = document.getElementById('comment-text').value.trim();

    if (!username || !text) {
        alert('Por favor completa todos los campos');
        return;
    }

    try {
        const fd = new FormData();
        fd.append('action',       'comentar');
        fd.append('discusion_id', currentThreadId);
        fd.append('autor_nombre', username);
        fd.append('contenido',    text);

        const res    = await fetch(`${DISC_API}api_discusiones.php`, { method: 'POST', body: fd });
        const result = await res.json();

        if (result.success) {
            document.getElementById('comment-form').reset();
            await loadComments(currentThreadId);
            document.getElementById('comments-list')?.scrollIntoView({ behavior: 'smooth' });
        } else {
            alert(result.message || 'Error al publicar el comentario');
        }
    } catch (e) {
        console.error('Error al comentar:', e);
        alert('Error al publicar el comentario');
    }
}

// ─────────────────────────────────────────────────────────
// NUEVA DISCUSIÓN
// ─────────────────────────────────────────────────────────
async function submitDiscussion(event) {
    event.preventDefault();

    const titulo      = document.getElementById('thread-title').value.trim();
    const autor       = document.getElementById('username').value.trim();
    const contenido   = document.getElementById('comment').value.trim();
    const juego_slug  = document.getElementById('game-select').value;

    if (!titulo || !autor || !contenido) {
        alert('Por favor completa todos los campos');
        return;
    }

    const btn = event.target.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Publicando...'; }

    try {
        const fd = new FormData();
        fd.append('action',       'nueva');
        fd.append('titulo',       titulo);
        fd.append('autor_nombre', autor);
        fd.append('contenido',    contenido);
        fd.append('juego_slug',   juego_slug);

        const res    = await fetch(`${DISC_API}api_discusiones.php`, { method: 'POST', body: fd });
        const result = await res.json();

        if (result.success) {
            alert(`¡Gracias ${autor}! Tu discusión "${titulo}" fue publicada.`);
            event.target.reset();
            await loadDiscussions();
            document.getElementById('threads-container')?.scrollIntoView({ behavior: 'smooth' });
        } else {
            alert(result.message || 'Error al publicar la discusión');
        }
    } catch (e) {
        console.error('Error enviando discusión:', e);
        alert('Error al publicar la discusión');
    } finally {
        if (btn) { btn.disabled = false; btn.textContent = 'Publicar Discusión'; }
    }
}

// ─────────────────────────────────────────────────────────
// CERRAR MODAL AL HACER CLICK FUERA
// ─────────────────────────────────────────────────────────
window.addEventListener('click', e => {
    const modal = document.getElementById('comments-modal');
    if (modal && e.target === modal) closeCommentsModal();
});

// ─────────────────────────────────────────────────────────
// INIT
// ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('game-select')) {
        loadDiscussions();
    }
});