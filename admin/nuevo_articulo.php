<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Artículo - Admin Terror Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Nosifer&family=Crimson+Text:wght@400;600;700&family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="grain"></div>

<?php
require_once '../config/auth.php';
$auth->requireRole('editor');
$user = $auth->getCurrentUser();
$db   = getDB();

$errorMsg   = '';
$successMsg = '';

// ── Procesar formulario ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo       = sanitize($_POST['titulo']       ?? '');
    $extracto     = sanitize($_POST['extracto']     ?? '');
    $contenido    = $_POST['contenido']             ?? '';   // HTML del editor
    $categoria_id = intval($_POST['categoria_id']   ?? 0);
    $juego_id     = intval($_POST['juego_id']       ?? 0);
    $calificacion = floatval($_POST['calificacion'] ?? 0);
    $publicado    = isset($_POST['publicado']) ? 1 : 0;
    $destacado    = isset($_POST['destacado']) ? 1 : 0;
    $tags_raw     = sanitize($_POST['tags']         ?? '');
    $slug         = generateSlug($titulo);
    $autor_id     = $user['id'];

    if (empty($titulo) || empty($contenido) || !$categoria_id) {
        $errorMsg = 'Título, contenido y categoría son obligatorios.';
    } else {
        // Verificar slug único
        $check = $db->prepare("SELECT id FROM articulos WHERE slug = ?");
        $check->execute([$slug]);
        if ($check->fetch()) $slug .= '-' . time();

        // Subir imagen si viene
        $imagen_destacada = null;
        if (!empty($_FILES['imagen']['name'])) {
            $up = uploadImage($_FILES['imagen'], 'art');
            if ($up['success']) {
                $imagen_destacada = $up['filename'];
            } else {
                $errorMsg = $up['message'];
            }
        }

        if (!$errorMsg) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO articulos
                        (titulo, slug, extracto, contenido, imagen_destacada,
                         categoria_id, juego_id, autor_id, calificacion,
                         publicado, destacado, fecha_publicacion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                        " . ($publicado ? 'NOW()' : 'NULL') . ")
                ");
                $stmt->execute([
                    $titulo, $slug, $extracto, $contenido, $imagen_destacada,
                    $categoria_id, $juego_id ?: null, $autor_id, $calificacion,
                    $publicado, $destacado
                ]);
                $newId = $db->lastInsertId();

                // Tags
                if ($tags_raw) {
                    $tags = array_map('trim', explode(',', $tags_raw));
                    foreach ($tags as $tagNombre) {
                        if (!$tagNombre) continue;
                        // Insertar o ignorar tag
                        $db->prepare("INSERT IGNORE INTO tags (nombre, slug) VALUES (?, ?)")
                           ->execute([$tagNombre, generateSlug($tagNombre)]);
                        $tagId = $db->query("SELECT id FROM tags WHERE slug = '" . generateSlug($tagNombre) . "'")->fetchColumn();
                        if ($tagId) {
                            $db->prepare("INSERT IGNORE INTO articulo_tags (articulo_id, tag_id) VALUES (?, ?)")
                               ->execute([$newId, $tagId]);
                        }
                    }
                }

                setFlashMessage('success', "Artículo \"$titulo\" " . ($publicado ? 'publicado' : 'guardado como borrador') . " correctamente.");
                redirect(SITE_URL . '/admin/articulos.php');

            } catch (PDOException $e) {
                $errorMsg = 'Error al guardar: ' . $e->getMessage();
            }
        }
    }
}

// ── Cargar categorías y juegos ────────────────────────────────────────────────
$categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre")->fetchAll();
$juegos     = $db->query("SELECT id, nombre FROM juegos WHERE activo = TRUE ORDER BY nombre")->fetchAll();
?>

<div class="admin-wrapper">

    <div class="admin-topbar">
        <a href="index.php" class="logo-small">TERROR DIGITAL</a>
        <div class="user-info">
            <span>👤 <?= htmlspecialchars($user['nombre_completo'] ?? $user['username']) ?></span>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        </div>
    </div>

    <nav class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="articulos.php">Artículos</a>
        <a href="nuevo_articulo.php" class="active">+ Nuevo artículo</a>
        <a href="discusiones.php">Discusiones</a>
        <?php if ($auth->hasRole('admin')): ?>
        <a href="usuarios.php">Usuarios</a>
        <a href="juegos.php">Juegos</a>
        <?php endif; ?>
        <a href="../index.html" target="_blank">Ver sitio</a>
    </nav>

    <main class="admin-main">

        <?php if ($errorMsg):   echo '<div class="alert alert-error">'   . htmlspecialchars($errorMsg)   . '</div>'; endif; ?>
        <?php if ($successMsg): echo '<div class="alert alert-success">' . htmlspecialchars($successMsg) . '</div>'; endif; ?>

        <h2 style="font-family:'Creepster',cursive;color:var(--blood-red);font-size:2rem;margin-bottom:1.5rem;letter-spacing:.15rem;">
            ✏️ Nuevo Artículo
        </h2>

        <form method="POST" enctype="multipart/form-data">

            <!-- Fila: Título y Categoría -->
            <div class="admin-panel">
                <h3>Información General</h3>

                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" required
                           placeholder="Escribe el título del artículo"
                           value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria_id">Categoría *</label>
                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">Selecciona una categoría</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= (($_POST['categoria_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="juego_id">Juego relacionado</label>
                        <select id="juego_id" name="juego_id">
                            <option value="">Sin juego específico</option>
                            <?php foreach ($juegos as $juego): ?>
                            <option value="<?= $juego['id'] ?>"
                                <?= (($_POST['juego_id'] ?? '') == $juego['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($juego['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="extracto">Extracto (resumen corto)</label>
                    <textarea name="extracto" id="extracto" rows="3"
                              placeholder="Resumen breve que aparece en las tarjetas de artículos..."><?= htmlspecialchars($_POST['extracto'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Imagen destacada -->
            <div class="admin-panel">
                <h3>Imagen Destacada</h3>
                <div class="upload-zone" id="upload-zone" onclick="document.getElementById('imagen').click()">
                    <div class="upload-icon">🖼️</div>
                    <p>Haz clic para seleccionar una imagen</p>
                    <p>JPG, PNG, GIF, WEBP — Máx. 5 MB</p>
                </div>
                <input type="file" id="imagen" name="imagen"
                       accept=".jpg,.jpeg,.png,.gif,.webp"
                       style="display:none" onchange="previewImage(this)">
                <img id="img-preview" class="img-preview" alt="Vista previa">
                <p id="img-name" style="color:#999;font-size:.85rem;margin-top:.3rem;"></p>
            </div>

            <!-- Contenido -->
            <div class="admin-panel">
                <h3>Contenido del Artículo</h3>

                <!-- Barra de herramientas HTML básico -->
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.8rem;">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="wrapTag('h2')"><b>H2</b></button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="wrapTag('p')">¶ Párrafo</button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="wrapTag('strong')"><b>B</b></button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="wrapTag('em')"><em>I</em></button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="wrapTag('ul')">Lista</button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="insertBlockquote()">❝ Cita</button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="insertRatingBox()">⭐ Rating</button>
                </div>

                <textarea class="content-editor" id="contenido" name="contenido"
                          placeholder="Escribe el contenido HTML del artículo aquí...
Puedes usar etiquetas como <h2>, <p>, <strong>, <em>, <ul><li>...</li></ul>
"><?= htmlspecialchars($_POST['contenido'] ?? '') ?></textarea>

                <!-- Vista previa del contenido -->
                <div style="margin-top:1rem;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="togglePreview()">👁️ Vista previa</button>
                </div>
                <div id="content-preview"
                     style="display:none;margin-top:1rem;padding:1.5rem;background:rgba(10,10,10,.5);border:1px solid var(--fog-gray);color:#ccc;line-height:1.9;font-family:'Crimson Text',serif;font-size:1.1rem;">
                </div>
            </div>

            <!-- Opciones -->
            <div class="admin-panel">
                <h3>Opciones</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="calificacion">Calificación (0–10)</label>
                        <input type="number" id="calificacion" name="calificacion"
                               min="0" max="10" step="0.1"
                               value="<?= htmlspecialchars($_POST['calificacion'] ?? '0') ?>">
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags (separados por comas)</label>
                        <input type="text" id="tags" name="tags"
                               placeholder="horror, survival, ps5, review..."
                               value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>">
                    </div>
                </div>

                <div style="display:flex;gap:2rem;align-items:center;flex-wrap:wrap;margin-top:.5rem;">
                    <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;color:var(--ghost-white);">
                        <input type="checkbox" name="publicado" value="1"
                               <?= isset($_POST['publicado']) ? 'checked' : '' ?>>
                        Publicar inmediatamente
                    </label>
                    <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;color:var(--ghost-white);">
                        <input type="checkbox" name="destacado" value="1"
                               <?= isset($_POST['destacado']) ? 'checked' : '' ?>>
                        Marcar como destacado
                    </label>
                </div>
            </div>

            <!-- Botones de acción -->
            <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:3rem;">
                <button type="submit" class="btn btn-primary" style="padding:1rem 2.5rem;font-size:1rem;">
                    💾 Guardar artículo
                </button>
                <a href="articulos.php" class="btn btn-secondary" style="padding:1rem 2rem;">
                    Cancelar
                </a>
            </div>

        </form>
    </main>
</div>

<script>
// ── Vista previa de imagen ────────────────────────────────────────────────────
function previewImage(input) {
    const preview = document.getElementById('img-preview');
    const nameEl  = document.getElementById('img-name');
    const zone    = document.getElementById('upload-zone');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
        nameEl.textContent = `📎 ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        zone.style.borderColor = 'var(--pale-green)';
    }
}

// Drag & drop en upload zone
const zone = document.getElementById('upload-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor='var(--accent-crimson)'; });
zone.addEventListener('dragleave', () => zone.style.borderColor = '');
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.style.borderColor = '';
    const dt = e.dataTransfer;
    if (dt.files.length) {
        document.getElementById('imagen').files = dt.files;
        previewImage(document.getElementById('imagen'));
    }
});

// ── Editor HTML básico ────────────────────────────────────────────────────────
const editor = document.getElementById('contenido');

function wrapTag(tag) {
    const start = editor.selectionStart;
    const end   = editor.selectionEnd;
    const sel   = editor.value.substring(start, end) || 'Texto aquí';
    const open  = `<${tag}>`;
    const close = `</${tag}>`;
    editor.value = editor.value.substring(0, start) + open + sel + close + editor.value.substring(end);
    editor.focus();
}

function insertBlockquote() {
    const pos = editor.selectionStart;
    const snippet = '\n<blockquote>\n    <p>Cita importante aquí</p>\n</blockquote>\n';
    editor.value = editor.value.substring(0, pos) + snippet + editor.value.substring(pos);
    editor.focus();
}

function insertRatingBox() {
    const pos = editor.selectionStart;
    const snippet = '\n<div class="rating-box">\n    <div class="rating-score">9.5/10</div>\n    <div class="rating-label">Terrorífico</div>\n</div>\n';
    editor.value = editor.value.substring(0, pos) + snippet + editor.value.substring(pos);
    editor.focus();
}

// ── Vista previa de contenido ─────────────────────────────────────────────────
function togglePreview() {
    const prev = document.getElementById('content-preview');
    if (prev.style.display === 'none') {
        prev.innerHTML = editor.value || '<p style="color:#666">Sin contenido aún.</p>';
        prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }
}
</script>

</body>
</html>