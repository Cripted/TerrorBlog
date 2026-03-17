/**
 * Sistema de discusiones conectado con la base de datos
 */

const API_BASE = '../php/';
let currentThreadId = null;

// Formatear fecha
function formatearFecha(fecha) {
    const date = new Date(fecha);
    const ahora = new Date();
    const diff = Math.floor((ahora - date) / 1000); // diferencia en segundos
    
    if (diff < 60) return 'hace unos segundos';
    if (diff < 3600) return `hace ${Math.floor(diff / 60)} minutos`;
    if (diff < 86400) return `hace ${Math.floor(diff / 3600)} horas`;
    if (diff < 604800) return `hace ${Math.floor(diff / 86400)} días`;
    
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    
    return `${date.getDate()} de ${meses[date.getMonth()]}, ${date.getFullYear()}`;
}

// Cargar discusiones del juego seleccionado
async function loadDiscussions() {
    const gameSelect = document.getElementById('game-select');
    const threadsContainer = document.getElementById('threads-container');
    const selectedGame = gameSelect.value;
    
    threadsContainer.innerHTML = '<p style="text-align: center; color: #999;">Cargando...</p>';
    
    try {
        const response = await fetch(`${API_BASE}api_discusiones.php?action=list&juego=${selectedGame}`);
        const threads = await response.json();
        
        if (threads.error) {
            threadsContainer.innerHTML = `<p style="text-align: center; color: #ff6b6b;">${threads.error}</p>`;
            return;
        }
        
        if (threads.length === 0) {
            threadsContainer.innerHTML = '<p style="text-align: center; color: #999;">No hay discusiones todavía. ¡Sé el primero en iniciar una!</p>';
            return;
        }
        
        threadsContainer.innerHTML = '';
        
        threads.forEach(thread => {
            const threadElement = document.createElement('div');
            threadElement.className = 'thread';
            
            const formattedViews = thread.vistas >= 1000 
                ? (thread.vistas / 1000).toFixed(1) + 'K' 
                : thread.vistas;
            
            threadElement.innerHTML = `
                <div class="thread-header">
                    <h4 class="thread-title">${thread.titulo}</h4>
                    <div class="thread-meta">
                        <span>💬 ${thread.total_comentarios || 0} respuestas</span>
                        <span>👁️ ${formattedViews} vistas</span>
                    </div>
                </div>
                <p class="thread-content">${thread.contenido}</p>
                <p class="meta">
                    Por <span class="thread-author">${thread.autor_nombre}</span> | ${formatearFecha(thread.fecha_creacion)}
                </p>
                <div class="thread-actions">
                    <button class="like-btn" onclick="handleLike(${thread.id}, event)">
                        <span class="like-icon">🤍</span>
                        <span class="like-count">${thread.likes || 0}</span> Me gusta
                    </button>
                    <button class="comment-btn" onclick="openCommentsModal(${thread.id})">
                        💬 Ver comentarios (${thread.total_comentarios || 0})
                    </button>
                </div>
            `;
            
            threadsContainer.appendChild(threadElement);
        });
        
    } catch (error) {
        console.error('Error al cargar discusiones:', error);
        threadsContainer.innerHTML = '<p style="text-align: center; color: #ff6b6b;">Error al cargar discusiones</p>';
    }
}

// Manejar like
async function handleLike(threadId, event) {
    event.stopPropagation();
    const button = event.currentTarget;
    
    // Verificar si ya dio like (usando localStorage)
    const likeKey = `liked_${threadId}`;
    const hasLiked = localStorage.getItem(likeKey) === 'true';
    
    if (hasLiked) {
        alert('Ya has dado like a esta discusión');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'like');
        formData.append('discusion_id', threadId);
        
        const response = await fetch(`${API_BASE}api_discusiones.php`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            const icon = button.querySelector('.like-icon');
            const count = button.querySelector('.like-count');
            
            icon.textContent = '❤️';
            count.textContent = result.likes;
            button.classList.add('liked');
            
            // Guardar en localStorage
            localStorage.setItem(likeKey, 'true');
        }
    } catch (error) {
        console.error('Error al dar like:', error);
    }
}

// Abrir modal de comentarios
async function openCommentsModal(threadId) {
    currentThreadId = threadId;
    
    try {
        // Obtener discusión
        const threadResponse = await fetch(`${API_BASE}api_discusiones.php?action=get&id=${threadId}`);
        const thread = await threadResponse.json();
        
        if (thread.error) {
            alert(thread.error);
            return;
        }
        
        document.getElementById('modal-thread-title').textContent = thread.titulo;
        document.getElementById('modal-thread-content').innerHTML = `
            <p class="thread-content">${thread.contenido}</p>
            <p class="meta">Por <span class="thread-author">${thread.autor_nombre}</span> | ${formatearFecha(thread.fecha_creacion)}</p>
        `;
        
        // Cargar comentarios
        await loadComments(threadId);
        
        // Mostrar modal
        document.getElementById('comments-modal').style.display = 'block';
        
    } catch (error) {
        console.error('Error al abrir modal:', error);
    }
}

// Cerrar modal
function closeCommentsModal() {
    document.getElementById('comments-modal').style.display = 'none';
    currentThreadId = null;
    loadDiscussions(); // Recargar para actualizar contadores
}

// Cargar comentarios
async function loadComments(threadId) {
    const commentsList = document.getElementById('comments-list');
    const commentCount = document.getElementById('comment-count');
    
    try {
        const response = await fetch(`${API_BASE}api_discusiones.php?action=comentarios&discusion_id=${threadId}`);
        const comments = await response.json();
        
        commentCount.textContent = comments.length;
        
        if (comments.length === 0) {
            commentsList.innerHTML = '<p class="no-comments">No hay comentarios todavía. ¡Sé el primero en comentar!</p>';
            return;
        }
        
        commentsList.innerHTML = comments.map(comment => `
            <div class="comment">
                <div class="comment-header">
                    <strong class="thread-author">${comment.autor_nombre}</strong>
                    <span class="comment-time">${formatearFecha(comment.fecha_creacion)}</span>
                </div>
                <p class="comment-text">${comment.contenido}</p>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Error al cargar comentarios:', error);
        commentsList.innerHTML = '<p class="no-comments">Error al cargar comentarios</p>';
    }
}

// Agregar comentario
async function addComment(event) {
    event.preventDefault();
    
    if (!currentThreadId) return;
    
    const username = document.getElementById('comment-username').value.trim();
    const text = document.getElementById('comment-text').value.trim();
    
    if (!username || !text) {
        alert('Por favor completa todos los campos');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'comentar');
        formData.append('discusion_id', currentThreadId);
        formData.append('autor_nombre', username);
        formData.append('contenido', text);
        
        const response = await fetch(`${API_BASE}api_discusiones.php`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('comment-form').reset();
            await loadComments(currentThreadId);
            document.getElementById('comments-list').scrollIntoView({ behavior: 'smooth' });
        } else {
            alert(result.message || 'Error al publicar comentario');
        }
    } catch (error) {
        console.error('Error al agregar comentario:', error);
        alert('Error al publicar comentario');
    }
}

// Enviar nueva discusión
async function submitDiscussion(event) {
    event.preventDefault();
    
    const title = document.getElementById('thread-title').value.trim();
    const username = document.getElementById('username').value.trim();
    const comment = document.getElementById('comment').value.trim();
    const gameSelect = document.getElementById('game-select');
    const selectedGame = gameSelect.value;
    
    if (!title || !username || !comment) {
        alert('Por favor completa todos los campos');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'nueva');
        formData.append('titulo', title);
        formData.append('autor_nombre', username);
        formData.append('contenido', comment);
        formData.append('juego_slug', selectedGame);
        
        const response = await fetch(`${API_BASE}api_discusiones.php`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(`¡Gracias por tu contribución, ${username}!\n\nTu tema "${title}" ha sido publicado exitosamente.`);
            event.target.reset();
            await loadDiscussions();
            
            // Scroll al contenedor de threads
            const threadsContainer = document.getElementById('threads-container');
            threadsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            alert(result.message || 'Error al publicar discusión');
        }
    } catch (error) {
        console.error('Error al enviar discusión:', error);
        alert('Error al publicar discusión');
    }
}

// Cerrar modal al hacer click fuera
window.onclick = function(event) {
    const modal = document.getElementById('comments-modal');
    if (event.target === modal) {
        closeCommentsModal();
    }
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('game-select')) {
        loadDiscussions();
    }
});