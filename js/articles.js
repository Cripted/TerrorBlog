// Base de datos de artículos
const articlesDatabase = {
    'silent-hill-2': {
        category: 'Review',
        title: 'Silent Hill 2 Remake: El Terror Psicológico Renace',
        author: 'Admin Terror',
        date: '10 de Febrero, 2026',
        image: '👻',
        tags: ['Silent Hill 2', 'Remake', 'Bloober Team', 'Review', 'Terror Psicológico'],
        content: `
            <p class="lead">Bloober Team ha logrado lo imposible: recrear la atmósfera opresiva y perturbadora del clásico de Konami mientras introduce mejoras significativas que elevan la experiencia sin traicionar la visión original.</p>

            <h2>Un Remake Respetuoso</h2>
            <p>Silent Hill 2 es considerado por muchos como el pináculo del survival horror psicológico. La presión sobre Bloober Team era inmensa, especialmente considerando sus trabajos anteriores que, aunque competentes, nunca alcanzaron las alturas del material original de Team Silent.</p>

            <p>Afortunadamente, el estudio polaco ha demostrado que aprendió de sus proyectos anteriores. Cada rincón de Silent Hill ha sido meticulosamente recreado, desde la niebla omnipresente hasta los interiores claustrofóbicos que definen la experiencia.</p>

            <h2>Mejoras Técnicas y Visuales</h2>
            <p>La tecnología moderna permite que Silent Hill 2 luzca absolutamente espectacular. El sistema de iluminación dinámica crea sombras inquietantes que bailan en las paredes mientras explores los edificios abandonados. La niebla, ese elemento icónico de la serie, ahora es volumétrica y reacciona a tu linterna de formas que agregan profundidad visual.</p>

            <p>Los modelos de personajes han sido completamente reconstruidos. James Sunderland ahora muestra expresiones faciales sutiles que comunican su deterioro mental progresivo. Las criaturas, especialmente Pyramid Head, son más aterradoras que nunca gracias al nivel de detalle que la nueva generación permite.</p>

            <h2>Diseño de Audio Magistral</h2>
            <p>Akira Yamaoka supervisó personalmente la remasterización del soundtrack, y se nota. Las melodías melancólicas que definieron el original están aquí, pero con una fidelidad que permite apreciar cada nota. El diseño de sonido ambiental es particularmente impresionante: cada crujido, cada respiración distante, cada radio estática contribuye a la tensión constante.</p>

            <h2>Jugabilidad Modernizada</h2>
            <p>El combate ha sido refinado sin perder la torpeza intencional que hace que cada encuentro sea tenso. James no es un soldado; es un hombre común en una situación extraordinaria, y el sistema de combate refleja esto perfectamente. La cámara sobre el hombro permite una conexión más íntima con el protagonista.</p>

            <p>Los puzzles mantienen su complejidad pero con pistas más claras. Bloober Team entendió que la frustración por puzzles oscuros no es lo mismo que el terror psicológico que Silent Hill intenta evocar.</p>

            <h2>La Historia y su Impacto</h2>
            <p>La narrativa permanece intacta, y es tan devastadora como siempre. Para quienes no conocen Silent Hill 2, prepárense para una de las historias más profundas y emocionalmente complejas en videojuegos. Sin spoilers, la exploración de la culpa, el duelo y el castigo autoimpuesto sigue siendo relevante y conmovedora.</p>

            <p>Los múltiples finales están presentes, cada uno con sus propios requisitos sutiles. El juego te observa, juzga tus acciones, y eventualmente te presenta la conclusión que mereces.</p>

            <h2>Algunas Pequeñas Objeciones</h2>
            <p>No todo es perfecto. Algunos puristas pueden objetar ciertos cambios en el diseño de escenarios o la reinterpretación de ciertas escenas. Personalmente, encuentro que estos cambios son mínimos y, en su mayoría, beneficiosos.</p>

            <p>El rendimiento técnico es generalmente sólido, aunque experimenté algunas caídas de framerate en áreas particularmente densas en niebla. Nada que rompa la experiencia, pero notable.</p>

            <h2>Veredicto Final</h2>
            <p>Silent Hill 2 Remake es un triunfo. Bloober Team no solo respetó el material original, sino que lo elevó con mejoras técnicas que hacen que esta sea la versión definitiva para experimentar esta obra maestra. Ya seas un fan de toda la vida o un recién llegado, este remake merece tu atención.</p>

            <p>La ciudad silenciosa vuelve a llamar, y esta vez, los horrores son más reales que nunca.</p>

            <div class="rating-box">
                <div class="rating-score">9.5</div>
                <div class="rating-label">SOBRESALIENTE</div>
            </div>
        `
    },
    'alan-wake-3': {
        category: 'Preview',
        title: 'Alan Wake 3: Primeras Impresiones',
        author: 'Maria Santos',
        date: '8 de Febrero, 2026',
        image: '🎮',
        tags: ['Alan Wake', 'Remedy', 'Preview', 'Tercera Persona'],
        content: `
            <p class="lead">Remedy Entertainment nos adelanta lo que viene en la tercera entrega de la saga. La oscuridad es más profunda y los enemigos más despiadados que nunca.</p>

            <h2>El Regreso a Bright Falls</h2>
            <p>Después del éxito rotundo de Alan Wake 2, Remedy no pierde tiempo. Alan Wake 3 nos lleva de regreso a Bright Falls, pero el pueblo que conocíamos ha cambiado drásticamente. La Dark Presence ha crecido en poder, y la línea entre realidad y ficción es más borrosa que nunca.</p>

            <h2>Nuevas Mecánicas de Juego</h2>
            <p>El combate con luz sigue siendo central, pero ahora tenemos más herramientas. Las bengalas son solo el comienzo: reflectores móviles, trampas de luz, y una nueva linterna modificada que consume batería a cambio de explosiones de luz devastadoras.</p>

            <h2>Narrativa Expandida</h2>
            <p>Sam Lake promete que esta será la entrada más ambiciosa narrativamente. El universo Remedy continúa expandiéndose, con conexiones más explícitas a Control y referencias a Quantum Break que los fans apreciarán.</p>

            <h2>Lo Que Hemos Visto</h2>
            <p>En nuestra demo de dos horas, exploramos una sección del bosque circundante de Bright Falls. La atmósfera es increíblemente tensa, con la oscuridad acechando en cada esquina. Los Taken son más variados y peligrosos, requiriendo estrategia y no solo disparar luz indiscriminadamente.</p>

            <p>La demo culminó en un enfrentamiento boss contra una entidad que solo puedo describir como una pesadilla manifestada. Fue intenso, aterrador, y absolutamente brillante.</p>

            <h2>Expectativas</h2>
            <p>Si esta preview es indicativa del producto final, Alan Wake 3 podría ser el mejor juego de Remedy hasta la fecha. El lanzamiento está programado para otoño de 2026, y la espera será difícil.</p>
        `
    },
    'still-wakes': {
        category: 'Review',
        title: 'Still Wakes the Deep: Terror en Alta Mar',
        author: 'Maria Santos',
        date: '6 de Febrero, 2026',
        image: '🔪',
        tags: ['Still Wakes the Deep', 'Terror', 'Review', 'Primera Persona'],
        content: `
            <p class="lead">Una plataforma petrolera en los años 70 se convierte en tu peor pesadilla en este thriller de supervivencia que te dejará sin aliento.</p>

            <h2>Aislamiento Total</h2>
            <p>Still Wakes the Deep aprovecha magistralmente el escenario de una plataforma petrolera en medio del Mar del Norte. Estás completamente aislado, rodeado de agua helada y oscuridad, mientras algo terrible comienza a despertar en las profundidades de la instalación.</p>

            <h2>Atmósfera Opresiva</h2>
            <p>El diseño de sonido es excepcional. El constante rugido del océano, el crujir del metal, los gritos distantes de tus compañeros... todo contribuye a una sensación de terror constante. No hay música de fondo; el ambiente es tu banda sonora.</p>

            <h2>Una Historia Humana</h2>
            <p>Lo que eleva este juego es su enfoque en los personajes. Conoces a tu tripulación, entiendes sus motivaciones, y cuando las cosas van mal, realmente importa. El protagonista está bien escrito y actuado, con un acento escocés auténtico que añade personalidad.</p>

            <h2>Supervivencia Sin Combate</h2>
            <p>No hay armas. Tu única opción es esconderte, correr, y usar el entorno a tu favor. Esta decisión de diseño hace que cada encuentro sea absolutamente aterrador. Cuando escuchas esos pasos acercándose, tu corazón se acelera porque sabes que no puedes luchar.</p>

            <h2>Veredicto</h2>
            <p>Still Wakes the Deep es una experiencia de terror intensa que se queda contigo mucho después de los créditos. Si te gusta el terror atmosférico y no te importa la falta de combate, este es imprescindible.</p>

            <div class="rating-box">
                <div class="rating-score">8.5</div>
                <div class="rating-label">EXCELENTE</div>
            </div>
        `
    },
    'dead-space-guide': {
        category: 'Guía',
        title: 'Dead Space Remake: Guía de Supervivencia',
        author: 'Carlos Mendez',
        date: '5 de Febrero, 2026',
        image: '🧟',
        tags: ['Dead Space', 'Guía', 'Tips', 'Supervivencia'],
        content: `
            <p class="lead">Consejos esenciales para sobrevivir a bordo del USG Ishimura. Desde gestión de munición hasta los mejores nodos de mejora.</p>

            <h2>Regla Básica: Apunta a las Extremidades</h2>
            <p>El consejo más importante: siempre dispara a las extremidades de los Necromorphs. Disparos a la cabeza raramente funcionan. Cercena las piernas para ralentizarlos, luego elimina los brazos. La Plasma Cutter es tu mejor amiga para esto.</p>

            <h2>Gestión de Recursos</h2>
            <p>La munición es escasa, especialmente en dificultades altas. Algunos tips:</p>
            <ul>
                <li>Usa la Kinesis para lanzar objetos afilados en lugar de gastar munición</li>
                <li>Las hojas de sierra giratorias son armas devastadoras cuando se lanzan</li>
                <li>Pisotea cadáveres para obtener recursos adicionales</li>
                <li>Vende armas que no uses para comprar mejoras</li>
            </ul>

            <h2>Mejores Armas</h2>
            <p><strong>Plasma Cutter:</strong> Versátil y eficiente. Rota entre modo vertical y horizontal según la situación.</p>
            <p><strong>Line Gun:</strong> Excelente para grupos. La mina secundaria es perfecta para emboscadas.</p>
            <p><strong>Contact Beam:</strong> Para jefes y enemigos grandes. Consume mucha energía pero vale la pena.</p>

            <h2>Uso de Nodos de Potencia</h2>
            <p>Prioriza mejorar tu traje y stasis primero. Un traje mejorado significa más inventario y salud. Stasis mejorado te salva la vida en situaciones difíciles.</p>

            <h2>Capítulos Difíciles</h2>
            <p><strong>Capítulo 5:</strong> Guarda mucha munición de Contact Beam para el jefe.</p>
            <p><strong>Capítulo 10:</strong> La sección de asteroides es complicada. Tómate tu tiempo y no te apresures.</p>

            <h2>Secretos y Coleccionables</h2>
            <p>Usa el localizador para encontrar todos los registros de audio y texto. Algunos revelan códigos para almacenes secretos con equipo valioso.</p>

            <h2>Modo Imposible</h2>
            <p>Para los valientes que intentan el modo imposible: una sola vida, sin guardar. Mis consejos:</p>
            <ul>
                <li>Juega conservadoramente. Cada decisión cuenta</li>
                <li>Memoriza las ubicaciones de enemigos</li>
                <li>Maximiza tu traje lo antes posible</li>
                <li>Usa Stasis generosamente; es tu mejor herramienta de supervivencia</li>
            </ul>

            <p>¡Buena suerte a bordo del Ishimura, y recuerda: el espacio no perdona errores!</p>
        `
    }
};

// Obtener parámetro de la URL
function getArticleId() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}

// Cargar artículo
function loadArticle() {
    const articleId = getArticleId();
    
    if (!articleId || !articlesDatabase[articleId]) {
        // Si no existe, mostrar artículo por defecto
        document.getElementById('article-heading').textContent = 'Artículo no encontrado';
        document.getElementById('article-body').innerHTML = '<p>Lo sentimos, el artículo que buscas no existe o ha sido movido.</p>';
        return;
    }
    
    const article = articlesDatabase[articleId];
    
    // Incrementar vistas
    const views = incrementViews(articleId);
    
    // Actualizar título de la página
    document.title = `${article.title} - TERROR DIGITAL`;
    
    // Actualizar contenido
    document.getElementById('article-title').textContent = article.title;
    document.getElementById('article-category').textContent = article.category;
    document.getElementById('article-heading').textContent = article.title;
    document.getElementById('article-author').textContent = `Por ${article.author}`;
    document.getElementById('article-date').textContent = article.date;
    document.getElementById('views-count').textContent = views.toLocaleString();
    
    // Imagen
    const imageElement = document.getElementById('article-image');
    imageElement.innerHTML = `<div class="image-placeholder">${article.image}</div>`;
    
    // Contenido
    document.getElementById('article-body').innerHTML = article.content;
    
    // Tags
    const tagsContainer = document.getElementById('article-tags');
    tagsContainer.innerHTML = article.tags.map(tag => `<span class="tag">${tag}</span>`).join('');
}

// Compartir artículo
function shareArticle(platform) {
    const articleId = getArticleId();
    const article = articlesDatabase[articleId];
    const url = window.location.href;
    const text = article ? article.title : 'TERROR DIGITAL';
    
    switch(platform) {
        case 'twitter':
            window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
            break;
        case 'facebook':
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
            break;
        case 'reddit':
            window.open(`https://reddit.com/submit?url=${encodeURIComponent(url)}&title=${encodeURIComponent(text)}`, '_blank');
            break;
        case 'copy':
            navigator.clipboard.writeText(url).then(() => {
                alert('¡Link copiado al portapapeles!');
            });
            break;
    }
}

// Funciones de vistas (copiadas de script.js para independencia)
function incrementViews(articleId) {
    let views = parseInt(localStorage.getItem(`views_${articleId}`)) || Math.floor(Math.random() * 5000) + 1000;
    views += 1;
    localStorage.setItem(`views_${articleId}`, views);
    return views;
}

// Cargar al iniciar
document.addEventListener('DOMContentLoaded', loadArticle);