// Base de datos de discusiones por juego
const discussionsData = {
    'silent-hill-2': [
        {
            id: 'sh2-1',
            title: '¿Cuál es el final verdadero?',
            content: 'He completado Silent Hill 2 tres veces y cada vez obtengo un final diferente. ¿Alguien sabe exactamente qué acciones determinan cada desenlace? El final "In Water" me dejó devastado...',
            author: 'HorrorFan88',
            replies: 0,
            time: 'hace 2 horas'
        },
        {
            id: 'sh2-2',
            title: 'Easter eggs que quizás te perdiste',
            content: 'Compilación de todos los easter eggs y referencias que he encontrado en el remake. Algunos son homenajes al original y otros son completamente nuevos. ¡Hay uno increíble en el hospital Brookhaven!',
            author: 'PyramidHead',
            replies: 0,
            time: 'hace 4 horas'
        },
        {
            id: 'sh2-3',
            title: 'Comparativa gráfica: Original vs Remake',
            content: 'He creado una comparación lado a lado de las escenas más icónicas. La tecnología ha avanzado muchísimo pero Bloober Team respetó la dirección artística original. ¿Qué opinan ustedes?',
            author: 'SilentObserver',
            replies: 0,
            time: 'hace 1 día'
        },
        {
            id: 'sh2-4',
            title: 'Teorías sobre el simbolismo en Silent Hill',
            content: 'Después de analizar cada detalle, creo que he descubierto nuevas capas de simbolismo que el remake añade. El uso de colores y la iluminación no son aleatorios. ¿Alguien más notó el patrón en las habitaciones del hotel?',
            author: 'MaryShepherd',
            replies: 0,
            time: 'hace 2 días'
        }
    ],
    'resident-evil-village': [
        {
            id: 'rev-1',
            title: 'Lady Dimitrescu: Análisis del personaje',
            content: 'Más allá del meme, Lady Dimitrescu es un villano complejo con una historia fascinante. Exploremos su trasfondo y conexión con Mother Miranda.',
            author: 'VillageExplorer',
            replies: 0,
            time: 'hace 3 horas'
        },
        {
            id: 'rev-2',
            title: 'Mejores armas para dificultad máxima',
            content: '¿Qué build recomiendan para Village of Shadows? Estoy atascado en la pelea contra Heisenberg.',
            author: 'REVeteran',
            replies: 0,
            time: 'hace 6 horas'
        },
        {
            id: 'rev-3',
            title: 'El DLC Shadows of Rose es increíble',
            content: 'Acabo de terminar el DLC y quedé impresionado. La historia de Rose está muy bien desarrollada y los nuevos poderes son geniales.',
            author: 'RoseFan2026',
            replies: 0,
            time: 'hace 1 día'
        },
        {
            id: 'rev-4',
            title: 'Secretos del castillo Dimitrescu',
            content: 'He encontrado varias áreas secretas en el castillo que muchos jugadores se pierden. ¿Alguien encontró la habitación oculta en el ático?',
            author: 'CastleExplorer',
            replies: 0,
            time: 'hace 2 días'
        }
    ],
    'phasmophobia': [
        {
            id: 'phas-1',
            title: 'Guía de identificación de fantasmas',
            content: 'Lista actualizada de todas las evidencias y comportamientos únicos de cada tipo de fantasma. ¡Con el nuevo modo pesadilla es esencial!',
            author: 'GhostHunter101',
            replies: 0,
            time: 'hace 1 hora'
        },
        {
            id: 'phas-2',
            title: '¿Alguien más experimenta bugs en el modo pesadilla?',
            content: 'En varias partidas el micrófono deja de funcionar y el fantasma no responde a la ouija. ¿Soluciones?',
            author: 'BugReporter',
            replies: 0,
            time: 'hace 4 horas'
        },
        {
            id: 'phas-3',
            title: 'Momentos más aterradores en Phasmophobia',
            content: 'Compartan sus experiencias más terroríficas. El otro día un Oni me persiguió durante 3 minutos seguidos...',
            author: 'ScaredPlayer',
            replies: 0,
            time: 'hace 8 horas'
        },
        {
            id: 'phas-4',
            title: 'Mejores estrategias para modo profesional',
            content: 'Tips y trucos para sobrevivir en profesional. La gestión de cordura es clave, aquí comparto mi método.',
            author: 'ProGhostHunter',
            replies: 0,
            time: 'hace 1 día'
        }
    ],
    'alan-wake-3': [
        {
            id: 'aw3-1',
            title: 'Conexiones con Control y Quantum Break',
            content: 'Remedy está construyendo un universo compartido increíble. ¿Qué referencias han encontrado en el tercer juego?',
            author: 'RemedyFanatic',
            replies: 0,
            time: 'hace 2 horas'
        },
        {
            id: 'aw3-2',
            title: 'La narrativa meta es genial',
            content: 'La forma en que el juego mezcla realidad y ficción es magistral. Sam Lake es un genio del storytelling.',
            author: 'WriterInTheDark',
            replies: 0,
            time: 'hace 5 horas'
        },
        {
            id: 'aw3-3',
            title: 'Análisis del episodio final',
            content: '¿Qué significó ese final? Tengo varias teorías sobre lo que realmente pasó con Alan. SPOILERS ADELANTE.',
            author: 'DarkPresence',
            replies: 0,
            time: 'hace 12 horas'
        },
        {
            id: 'aw3-4',
            title: 'Manuscritos escondidos - Ubicaciones',
            content: 'Guía completa de todos los manuscritos perdidos. Algunos revelan secretos importantes de la trama.',
            author: 'ManuscriptCollector',
            replies: 0,
            time: 'hace 1 día'
        }
    ],
    'dead-space': [
        {
            id: 'ds-1',
            title: 'Modo imposible: ¿Vale la pena?',
            content: 'Estoy considerando hacer una run en imposible pero escuché que es brutalmente difícil. ¿Tips para sobrevivir?',
            author: 'IsaacClarke',
            replies: 0,
            time: 'hace 3 horas'
        },
        {
            id: 'ds-2',
            title: 'Mejoras vs Original: Análisis completo',
            content: 'El remake no solo se ve mejor, las mecánicas de juego también fueron pulidas. El sistema de desmembramiento mejoró muchísimo.',
            author: 'EngineerGamer',
            replies: 0,
            time: 'hace 1 día'
        },
        {
            id: 'ds-3',
            title: 'Builds óptimos para cada capítulo',
            content: 'Comparto mi estrategia de mejoras para cada sección del juego. La gestión de recursos es crítica.',
            author: 'IshimuraSurvivor',
            replies: 0,
            time: 'hace 1 día'
        },
        {
            id: 'ds-4',
            title: 'Los necromorphos más aterradores',
            content: 'Ranking de los enemigos que más pánico me causan. El Divider sigue siendo mi pesadilla.',
            author: 'NecromorphHater',
            replies: 0,
            time: 'hace 2 días'
        }
    ],
    'outlast-trials': [
        {
            id: 'ot-1',
            title: 'Mejores estrategias cooperativas',
            content: 'Para los que juegan en equipo, ¿qué roles asignan? Necesitamos optimizar nuestras runs.',
            author: 'TeamPlayer',
            replies: 0,
            time: 'hace 4 horas'
        },
        {
            id: 'ot-2',
            title: 'Los enemigos más difíciles del juego',
            content: 'Ranking de los adversarios más complicados. El Doctor definitivamente está en mi top 3.',
            author: 'TrialsVeteran',
            replies: 0,
            time: 'hace 7 horas'
        },
        {
            id: 'ot-3',
            title: 'Guía de todos los laboratorios',
            content: 'Walkthrough completo de cada programa MK-Ultra. Incluye ubicaciones de documentos secretos.',
            author: 'ProgramExpert',
            replies: 0,
            time: 'hace 1 día'
        },
        {
            id: 'ot-4',
            title: 'Mejores habilidades para solo',
            content: 'Si prefieres jugar en solitario, estas son las habilidades esenciales para sobrevivir.',
            author: 'LoneWolf',
            replies: 0,
            time: 'hace 2 días'
        }
    ]
};

let currentThreadId = null;

function incrementViews(threadId) {
    let views = parseInt(localStorage.getItem(`views_${threadId}`)) || Math.floor(Math.random() * 3000) + 500;
    views += 1;
    localStorage.setItem(`views_${threadId}`, views);
    return views;
}

function getViews(threadId) {
    let views = parseInt(localStorage.getItem(`views_${threadId}`));
    if (!views) {
        views = Math.floor(Math.random() * 3000) + 500;
        localStorage.setItem(`views_${threadId}`, views);
    }
    return views;
}

function toggleLike(threadId) {
    const likeKey = `liked_${threadId}`;
    const countKey = `likes_${threadId}`;
    
    const hasLiked = localStorage.getItem(likeKey) === 'true';
    let likeCount = parseInt(localStorage.getItem(countKey)) || Math.floor(Math.random() * 100) + 10;
    
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

function getLikeStatus(threadId) {
    const likeKey = `liked_${threadId}`;
    const countKey = `likes_${threadId}`;
    
    let count = parseInt(localStorage.getItem(countKey));
    if (!count) {
        count = Math.floor(Math.random() * 100) + 10;
        localStorage.setItem(countKey, count);
    }
    
    return {
        liked: localStorage.getItem(likeKey) === 'true',
        count: count
    };
}

function getComments(threadId) {
    const commentsKey = `comments_${threadId}`;
    const comments = localStorage.getItem(commentsKey);
    return comments ? JSON.parse(comments) : [];
}

function saveComment(threadId, username, text) {
    const commentsKey = `comments_${threadId}`;
    const comments = getComments(threadId);
    
    const newComment = {
        id: Date.now(),
        username: username,
        text: text,
        time: new Date().toLocaleString('es-ES', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })
    };
    
    comments.unshift(newComment);
    localStorage.setItem(commentsKey, JSON.stringify(comments));
    
    return comments;
}

function loadDiscussions() {
    const gameSelect = document.getElementById('game-select');
    const threadsContainer = document.getElementById('threads-container');
    const selectedGame = gameSelect.value;
    const threads = discussionsData[selectedGame];

    threadsContainer.innerHTML = '';

    threads.forEach(thread => {
        const views = getViews(thread.id);
        const likeStatus = getLikeStatus(thread.id);
        const comments = getComments(thread.id);
        
        const threadElement = document.createElement('div');
        threadElement.className = 'thread';
        
        const formattedViews = views >= 1000 
            ? (views / 1000).toFixed(1) + 'K' 
            : views;

        threadElement.innerHTML = `
            <div class="thread-header">
                <h4 class="thread-title">${thread.title}</h4>
                <div class="thread-meta">
                    <span>💬 ${comments.length} respuestas</span>
                    <span>👁️ ${formattedViews} vistas</span>
                </div>
            </div>
            <p class="thread-content">${thread.content}</p>
            <p class="meta">
                Por <span class="thread-author">${thread.author}</span> | Última actividad: ${thread.time}
            </p>
            <div class="thread-actions">
                <button class="like-btn ${likeStatus.liked ? 'liked' : ''}" onclick="handleLike('${thread.id}', event)">
                    <span class="like-icon">${likeStatus.liked ? '❤️' : '🤍'}</span>
                    <span class="like-count">${likeStatus.count}</span> Me gusta
                </button>
                <button class="comment-btn" onclick="openCommentsModal('${thread.id}', '${selectedGame}')">
                    💬 Ver comentarios (${comments.length})
                </button>
            </div>
        `;
        
        threadsContainer.appendChild(threadElement);
    });
}

function handleLike(threadId, event) {
    event.stopPropagation();
    const button = event.currentTarget;
    const result = toggleLike(threadId);
    
    const icon = button.querySelector('.like-icon');
    const count = button.querySelector('.like-count');
    
    icon.textContent = result.liked ? '❤️' : '🤍';
    count.textContent = result.count;
    
    if (result.liked) {
        button.classList.add('liked');
    } else {
        button.classList.remove('liked');
    }
}

function openCommentsModal(threadId, gameId) {
    currentThreadId = threadId;
    
    incrementViews(threadId);
    
    const threads = discussionsData[gameId];
    const thread = threads.find(t => t.id === threadId);
    
    if (!thread) return;
    
    document.getElementById('modal-thread-title').textContent = thread.title;
    document.getElementById('modal-thread-content').innerHTML = `
        <p class="thread-content">${thread.content}</p>
        <p class="meta">Por <span class="thread-author">${thread.author}</span> | ${thread.time}</p>
    `;
    
    loadComments(threadId);
    
    document.getElementById('comments-modal').style.display = 'block';
}

function closeCommentsModal() {
    document.getElementById('comments-modal').style.display = 'none';
    currentThreadId = null;
    loadDiscussions();
}

function loadComments(threadId) {
    const comments = getComments(threadId);
    const commentsList = document.getElementById('comments-list');
    const commentCount = document.getElementById('comment-count');
    
    commentCount.textContent = comments.length;
    
    if (comments.length === 0) {
        commentsList.innerHTML = '<p class="no-comments">No hay comentarios todavía. ¡Sé el primero en comentar!</p>';
        return;
    }
    
    commentsList.innerHTML = comments.map(comment => `
        <div class="comment">
            <div class="comment-header">
                <strong class="thread-author">${comment.username}</strong>
                <span class="comment-time">${comment.time}</span>
            </div>
            <p class="comment-text">${comment.text}</p>
        </div>
    `).join('');
}

function addComment(event) {
    event.preventDefault();
    
    if (!currentThreadId) return;
    
    const username = document.getElementById('comment-username').value;
    const text = document.getElementById('comment-text').value;
    
    if (!username || !text) return;
    
    saveComment(currentThreadId, username, text);
    loadComments(currentThreadId);
    
    document.getElementById('comment-form').reset();
    
    document.getElementById('comments-list').scrollIntoView({ behavior: 'smooth' });
}

function submitDiscussion(event) {
    event.preventDefault();
    
    const title = document.getElementById('thread-title').value;
    const username = document.getElementById('username').value;
    const comment = document.getElementById('comment').value;
    const gameSelect = document.getElementById('game-select');
    const selectedGameText = gameSelect.options[gameSelect.selectedIndex].text;
    
    alert(`¡Gracias por tu contribución, ${username}!\n\nTu tema "${title}" ha sido publicado en las discusiones de ${selectedGameText}.\n\nLa comunidad de Terror Digital agradece tu participación.`);
    
    event.target.reset();
    
    const gameId = gameSelect.value;
    const newThreadId = `${gameId}-new-${Date.now()}`;
    
    const newThread = {
        id: newThreadId,
        title: title,
        content: comment,
        author: username,
        replies: 0,
        time: 'hace unos segundos'
    };
    
    discussionsData[gameId].unshift(newThread);
    
    loadDiscussions();
    
    const threadsContainer = document.getElementById('threads-container');
    threadsContainer.firstElementChild.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

window.onclick = function(event) {
    const modal = document.getElementById('comments-modal');
    if (event.target === modal) {
        closeCommentsModal();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadDiscussions();
});