# 👻 TERROR DIGITAL - Blog de Videojuegos de Horror

![Version](https://img.shields.io/badge/version-1.0.0-red.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![HTML](https://img.shields.io/badge/HTML-5-orange.svg)
![CSS](https://img.shields.io/badge/CSS-3-blue.svg)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-yellow.svg)

**TERROR DIGITAL** es un blog web completo dedicado a los videojuegos de terror. Proyecto escolar diseñado con una estética oscura y terrorífica, incluyendo noticias, reviews, guías, sistema de discusiones interactivo y sección de comunidad.

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Instalación](#-instalación)
- [Uso](#-uso)
- [Páginas Disponibles](#-páginas-disponibles)
- [Funcionalidades Interactivas](#-funcionalidades-interactivas)
- [Tecnologías Utilizadas](#-tecnologías-utilizadas)
- [Personalización](#-personalización)
- [Créditos](#-créditos)
- [Licencia](#-licencia)

---

## ✨ Características

### 🎨 Diseño
- **Tema oscuro y terrorífico** con paleta de colores personalizada
- **Animaciones CSS** (efectos de parpadeo, glitch, grain)
- **Diseño responsivo** que se adapta a diferentes dispositivos
- **Fuentes temáticas** (Nosifer, Creepster, Crimson Text, Rubik)
- **Efectos visuales** como niebla, sombras y gradientes atmosféricos

### 🔧 Funcionalidades
- ✅ **Sistema de navegación completo** entre todas las páginas
- ✅ **Base de datos de artículos** con contenido predefinido
- ✅ **Sistema de discusiones interactivo** con 6 juegos diferentes
- ✅ **Sistema de likes y comentarios** con persistencia en LocalStorage
- ✅ **Contador de vistas** para artículos y discusiones
- ✅ **Modal de comentarios** para cada hilo de discusión
- ✅ **Compartir artículos** en redes sociales (Twitter, Facebook, Reddit)

### 📱 Páginas Incluidas
1. **Inicio** - Artículo destacado y últimas noticias
2. **Noticias** - 9 noticias del mundo del terror gaming
3. **Reviews** - Análisis detallados de juegos de terror
4. **Guías** - Tutoriales y consejos de supervivencia
5. **Discusiones** - Foro interactivo por juego
6. **Comunidad** - Estadísticas y top contribuidores
7. **Artículo** - Plantilla dinámica para contenido individual

---

## 📁 Estructura del Proyecto

```
TerrorBlog/
│
├── index.html                 # Página principal
├── README.md                  # Documentación del proyecto
│
├── css/
│   └── styles.css            # Estilos completos del sitio
│
├── js/
│   ├── script.js             # Funciones principales
│   ├── articles.js           # Base de datos de artículos
│   └── discusiones.js        # Sistema de discusiones
│
└── pages/
    ├── noticias.html         # Página de noticias
    ├── reviews.html          # Página de reviews
    ├── guias.html            # Página de guías
    ├── discusiones.html      # Foro de discusiones
    ├── comunidad.html        # Página de comunidad
    └── articulo.html         # Plantilla de artículo individual
```

---

## 🚀 Instalación

### Opción 1: Descarga Directa
1. Descarga todos los archivos del proyecto
2. Mantén la estructura de carpetas intacta
3. Abre `index.html` en tu navegador web

### Opción 2: Clonar Repositorio
```bash
# Si tienes el proyecto en un repositorio
git clone [URL-del-repositorio]
cd TerrorBlog
```

### Requisitos
- **Navegador web moderno** (Chrome, Firefox, Edge, Safari)
- **Conexión a internet** (para cargar las fuentes de Google Fonts)
- No requiere servidor web ni instalación adicional

---

## 💻 Uso

### Navegación Básica
1. Abre `index.html` en tu navegador
2. Usa el menú de navegación para explorar las diferentes secciones
3. Haz clic en "Leer más →" para ver artículos completos
4. Interactúa con el sistema de discusiones dando likes y comentarios

### Sistema de Discusiones
```javascript
// Selecciona un juego del dropdown
// Explora los hilos de discusión
// Haz clic en "Ver comentarios" para participar
// Publica tu propia discusión usando el formulario
```

### Artículos Disponibles
El blog incluye contenido predefinido para:
- **Silent Hill 2 Remake** (Review completo)
- **Alan Wake 3** (Preview)
- **Still Wakes the Deep** (Review)
- **Dead Space Remake** (Guía de supervivencia)

---

## 📄 Páginas Disponibles

### 1. **Página Principal (index.html)**
- Artículo destacado de Silent Hill 2
- Sidebar con breaking news
- Grid de últimas noticias (6 artículos)
- Navegación completa

### 2. **Noticias (noticias.html)**
Incluye 9 noticias sobre:
- DLC de Resident Evil Village
- Modo pesadilla de Phasmophobia
- Nuevo juego de Frictional Games
- The Mortuary Assistant
- Five Nights at Freddy's
- The Dark Pictures
- Lethal Company
- Rumores de Silent Hill 3 Remake
- Horror Game Festival

### 3. **Reviews (reviews.html)**
Reviews completos con calificaciones de:
- Silent Hill 2 Remake (9.5/10)
- Still Wakes the Deep (8.5/10)
- Outlast Trials (8.0/10)
- Dead Space Remake (9.0/10)
- Alan Wake 2 (9.5/10)
- Resident Evil 4 Remake (9.0/10)
- Amnesia: The Bunker (8.5/10)
- Lethal Company (8.0/10)
- Phasmophobia (8.5/10)

### 4. **Guías (guias.html)**
Guías estratégicas para:
- Dead Space: Supervivencia completa
- Silent Hill 2: Todos los finales
- Resident Evil Village: Ubicación de tesoros
- Phasmophobia: Identificación de fantasmas
- Alan Wake 2: Manuscritos perdidos
- Outlast Trials: Builds cooperativos
- Amnesia: The Bunker: Supervivencia
- Lethal Company: Bestiary completo
- Consejos generales de survival horror

### 5. **Discusiones (discusiones.html)**
Sistema interactivo con 6 juegos:
- **Silent Hill 2 Remake** (4 hilos)
- **Resident Evil Village** (4 hilos)
- **Phasmophobia** (4 hilos)
- **Alan Wake 3** (4 hilos)
- **Dead Space** (4 hilos)
- **Outlast Trials** (4 hilos)

Cada hilo incluye:
- Sistema de likes (❤️)
- Contador de comentarios
- Contador de vistas
- Modal para agregar comentarios

### 6. **Comunidad (comunidad.html)**
- 3 tarjetas de llamado a la acción (Discord, Eventos, Contribuciones)
- Top 5 contribuidores del mes
- 4 estadísticas de la comunidad (miembros, artículos, discusiones, comentarios)
- Sección de contenido creado por usuarios

### 7. **Artículo Individual (articulo.html)**
Plantilla dinámica que muestra:
- Título y categoría
- Metadata (autor, fecha, vistas)
- Imagen destacada
- Contenido completo del artículo
- Sistema de calificación (rating box)
- Tags relacionados
- Botones para compartir en redes sociales
- 3 artículos relacionados

---

## 🎮 Funcionalidades Interactivas

### LocalStorage
El sitio utiliza LocalStorage para persistir datos:

```javascript
// Vistas de artículos
localStorage.getItem(`views_${articleId}`)

// Likes en discusiones
localStorage.getItem(`liked_${threadId}`)
localStorage.getItem(`likes_${threadId}`)

// Comentarios
localStorage.getItem(`comments_${threadId}`)
```

### Sistema de Likes
```javascript
// Toggle like en un hilo
function toggleLike(threadId) {
    // Alterna entre liked/unliked
    // Actualiza el contador
    // Guarda en LocalStorage
}
```

### Sistema de Comentarios
```javascript
// Agregar comentario
function addComment(event) {
    // Valida campos
    // Crea objeto de comentario con timestamp
    // Guarda en LocalStorage
    // Actualiza la UI
}
```

### Carga Dinámica de Artículos
```javascript
// Obtiene ID del artículo de la URL
function getArticleId() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}

// Carga el contenido del artículo
function loadArticle() {
    // Busca en articlesDatabase
    // Renderiza contenido
    // Incrementa vistas
}
```

---

## 🛠️ Tecnologías Utilizadas

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos y animaciones
  - Custom Properties (CSS Variables)
  - Flexbox & Grid Layout
  - Animaciones y transiciones
  - Media queries para responsividad
- **JavaScript (ES6)** - Interactividad
  - LocalStorage API
  - DOM Manipulation
  - Event Handlers
  - Template Literals

### Fuentes
- [Google Fonts](https://fonts.google.com/)
  - **Nosifer** - Logo principal
  - **Creepster** - Títulos de sección
  - **Crimson Text** - Contenido
  - **Rubik** - UI elements

### Íconos
- Emojis Unicode para iconografía

---

## 🎨 Personalización

### Colores del Tema
Edita las variables CSS en `styles.css`:

```css
:root {
    --blood-red: #8B0000;        /* Rojo sangre */
    --deep-black: #0a0a0a;       /* Negro profundo */
    --ghost-white: #f5f5f5;      /* Blanco fantasmal */
    --shadow-purple: #1a0033;    /* Púrpura oscuro */
    --fog-gray: #2a2a2a;         /* Gris niebla */
    --accent-crimson: #dc143c;   /* Carmesí */
    --pale-green: #00ff41;       /* Verde pálido */
}
```

### Agregar Nuevos Artículos
Edita `js/articles.js`:

```javascript
const articlesDatabase = {
    'nuevo-articulo': {
        category: 'Review',
        title: 'Título del Artículo',
        author: 'Tu Nombre',
        date: 'Fecha',
        image: '🎮', // Emoji para la imagen
        tags: ['Tag1', 'Tag2', 'Tag3'],
        content: `
            <p class="lead">Introducción...</p>
            <h2>Subtítulo</h2>
            <p>Contenido...</p>
        `
    }
};
```

### Agregar Nuevos Juegos a Discusiones
Edita `js/discusiones.js`:

```javascript
const discussionsData = {
    'nuevo-juego': [
        {
            id: 'ng-1',
            title: 'Título del Hilo',
            content: 'Contenido...',
            author: 'Usuario',
            replies: 0,
            time: 'hace X tiempo'
        }
    ]
};
```

Y actualiza el select en `discusiones.html`:

```html
<select id="game-select" onchange="loadDiscussions()">
    <option value="nuevo-juego">Nombre del Juego</option>
</select>
```

---

## 🎯 Características Avanzadas

### Animaciones CSS
- **Grain Effect** - Efecto de grano cinematográfico
- **Flicker Animation** - Parpadeo del logo
- **Glitch Effect** - Efecto glitch en hover
- **Fade In Animations** - Aparición gradual de elementos

### Efectos Visuales
- **Gradientes atmosféricos** en backgrounds
- **Sombras de texto** con glow effect
- **Bordes animados** en hover
- **Transiciones suaves** en todos los elementos interactivos

### Optimización
- **Código modular** separado en archivos lógicos
- **Reutilización de funciones** JavaScript
- **Carga condicional** de contenido
- **Smooth scrolling** para mejor UX

---

## 📊 Estadísticas del Proyecto

- **Total de páginas:** 7
- **Archivos HTML:** 7
- **Archivos CSS:** 1 (con 1000+ líneas)
- **Archivos JavaScript:** 3
- **Artículos predefinidos:** 4
- **Hilos de discusión:** 24 (6 juegos × 4 hilos)
- **Líneas de código totales:** ~3000+

---

## 🐛 Solución de Problemas

### Las fuentes no cargan
**Problema:** Las fuentes de Google no se muestran
**Solución:** Verifica tu conexión a internet. Las fuentes se cargan desde CDN.

### LocalStorage no funciona
**Problema:** Los likes/comentarios no se guardan
**Solución:** 
- Verifica que tu navegador permita LocalStorage
- No uses el sitio en modo incógnito
- Borra la caché del navegador si hay conflictos

### Los enlaces no funcionan
**Problema:** Los botones "Leer más" no redirigen
**Solución:** Verifica que la estructura de carpetas esté intacta

### Artículos no cargan
**Problema:** La página de artículo muestra "Artículo no encontrado"
**Solución:** Verifica que el ID en la URL coincida con algún artículo en `articles.js`

---

## 🔮 Futuras Mejoras

- [ ] Sistema de búsqueda de artículos
- [ ] Filtros por categoría y tags
- [ ] Sistema de calificación de artículos
- [ ] Modo claro/oscuro toggle
- [ ] Integración con API real de juegos
- [ ] Sistema de usuarios con registro
- [ ] Newsletter subscription
- [ ] Galería de screenshots
- [ ] Videos embebidos
- [ ] Backend con base de datos real

---

## 📝 Notas del Desarrollador

Este proyecto fue creado como un trabajo escolar para demostrar:
- Conocimientos de **HTML5 semántico**
- Uso avanzado de **CSS3** (animaciones, grid, flexbox)
- **JavaScript moderno** (ES6+)
- **Diseño responsivo**
- **UI/UX** temática
- **Persistencia de datos** con LocalStorage
- **Arquitectura de proyecto** web

---

## 👨‍💻 Créditos

**Diseño y Desarrollo:** Proyecto Escolar
**Tema:** Videojuegos de Horror
**Inspiración:** Sitios como IGN, GameSpot, Kotaku
**Fuentes:** Google Fonts
**Año:** 2026

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - siéntete libre de usar, modificar y distribuir el código.

```
MIT License

Copyright (c) 2026 Terror Digital

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 🎃 ¡Que lo disfrutes!

Si tienes preguntas, sugerencias o encuentras algún bug, no dudes en reportarlo.

**El miedo es solo el principio...** 👻

---

<div align="center">

### 🕷️ TERROR DIGITAL 🕷️
*El Horror Nunca Duerme*

Made with 💀 and ☕ for horror gaming enthusiasts

</div>
