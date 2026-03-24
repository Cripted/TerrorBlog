<?php
require_once '../config/auth.php';
$auth->requireLogin();
$user = $auth->getCurrentUser();

$editId   = isset($_GET['id']) ? (int)$_GET['id'] : null;
$articulo = null;
$artTags  = [];
$errors   = [];

// ── Cargar artículo para editar ──────────────────────────
if ($editId) {
    $stmt = $conn->prepare("SELECT * FROM articulos WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $articulo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$articulo) {
        setFlashMessage('error', 'Artículo no encontrado');
        redirect(SITE_URL . '/admin/articulos.php');
    }
    if (!$auth->hasRole('editor') && $articulo['autor_id'] != $user['id']) {
        setFlashMessage('error', 'Sin permiso para editar este artículo');
        redirect(SITE_URL . '/admin/articulos.php');
    }

    // Tags actuales
    $tStmt = $conn->prepare("SELECT tag_id FROM articulo_tags WHERE articulo_id = ?");
    $tStmt->bind_param("i", $editId);
    $tStmt->execute();
    $artTags = array_column($tStmt->get_result()->fetch_all(MYSQLI_ASSOC), 'tag_id');
    $tStmt->close();
}

// ── Procesar formulario ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = sanitize($_POST['titulo']      ?? '');
    $extracto    = sanitize($_POST['extracto']    ?? '');
    $contenido   = $_POST['contenido'] ?? '';   // HTML permitido
    $categoriaId = (int)($_POST['categoria_id']  ?? 0);
    $juegoId     = !empty($_POST['juego_id'])     ? (int)$_POST['juego_id'] : null;
    $calificacion= !empty($_POST['calificacion']) ? (float)$_POST['calificacion'] : null;
    $destacado   = isset($_POST['destacado'])     ? 1 : 0;
    $publicado   = isset($_POST['publicado'])     ? 1 : 0;
    $tags        = $_POST['tags'] ?? [];
    $slug        = generateSlug($titulo);

    if (!$titulo)      $errors[] = 'El título es requerido';
    if (!$contenido)   $errors[] = 'El contenido es requerido';
    if (!$categoriaId) $errors[] = 'La categoría es requerida';

    // Imagen
    $imagen = $articulo['imagen_destacada'] ?? null;
    if (!empty($_FILES['imagen']['name'])) {
        $upload = uploadImage($_FILES['imagen'], 'art');
        if ($upload['success']) $imagen = $upload['filename'];
        else $errors[] = $upload['message'];
    }

    if (empty($errors)) {
        // juego_id y calificacion pueden ser NULL — MySQLi necesita pasar null directamente
        $juegoIdVal    = $juegoId    ?: null;
        $calificVal    = $calificacion ? (float)$calificacion : null;

        if ($editId) {
            // UPDATE
            $stmt = $conn->prepare("
                UPDATE articulos SET
                    titulo = ?, slug = ?, extracto = ?, contenido = ?,
                    imagen_destacada = ?, categoria_id = ?, juego_id = ?,
                    calificacion = ?, destacado = ?, publicado = ?,
                    fecha_publicacion = IF(? = 1 AND fecha_publicacion IS NULL, NOW(), fecha_publicacion)
                WHERE id = ?
            ");
            // s=string, i=int, d=double
            $stmt->bind_param(
                "sssssiiidiii",
                $titulo, $slug, $extracto, $contenido,
                $imagen, $categoriaId, $juegoIdVal,
                $calificVal, $destacado, $publicado,
                $publicado, $editId
            );
            $stmt->execute();
            $stmt->close();
            $artId = $editId;
        } else {
            // INSERT
            $stmt = $conn->prepare("
                INSERT INTO articulos
                    (titulo, slug, extracto, contenido, imagen_destacada,
                     autor_id, categoria_id, juego_id, calificacion,
                     destacado, publicado, fecha_publicacion)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, IF(? = 1, NOW(), NULL))
            ");
            $stmt->bind_param(
                "sssssiiidiii",
                $titulo, $slug, $extracto, $contenido, $imagen,
                $user['id'], $categoriaId, $juegoIdVal,
                $calificVal, $destacado, $publicado, $publicado
            );
            $stmt->execute();
            $artId = $conn->insert_id;
            $stmt->close();
        }

        // Tags: borrar y reinsertar
        $del = $conn->prepare("DELETE FROM articulo_tags WHERE articulo_id = ?");
        $del->bind_param("i", $artId);
        $del->execute();
        $del->close();

        foreach ($tags as $tagId) {
            $ins = $conn->prepare("INSERT IGNORE INTO articulo_tags (articulo_id, tag_id) VALUES (?, ?)");
            $ins->bind_param("ii", $artId, $tagId);
            $ins->execute();
            $ins->close();
        }

        setFlashMessage('success', $editId ? 'Artículo actualizado' : 'Artículo creado correctamente');
        redirect(SITE_URL . '/admin/articulos.php');
    }
}

// Datos para los selects
$categorias = $conn->query("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$juegos     = $conn->query("SELECT * FROM juegos     WHERE activo = 1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$todosTags  = $conn->query("SELECT * FROM tags ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editId ? 'Editar' : 'Nuevo' ?> Artículo - Admin | Terror Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600;700&family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="grain"></div>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <h1><?= $editId ? '✏️ Editar Artículo' : '➕ Nuevo Artículo' ?></h1>
            <a href="articulos.php" class="btn-sm">← Volver</a>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?><p>• <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-row">

                <!-- Columna principal -->
                <div class="form-col-main">
                    <div class="form-group">
                        <label>Título *</label>
                        <input type="text" name="titulo" required
                               value="<?= htmlspecialchars($articulo['titulo'] ?? $_POST['titulo'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Extracto / Resumen</label>
                        <textarea name="extracto" rows="3"><?= htmlspecialchars($articulo['extracto'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Contenido * <small style="color:#555">(HTML permitido: &lt;p&gt; &lt;h2&gt; &lt;ul&gt; &lt;li&gt; &lt;strong&gt; etc.)</small></label>
                        <textarea name="contenido" rows="22" class="content-editor"><?= htmlspecialchars($articulo['contenido'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Columna lateral -->
                <div class="form-col-side">

                    <div class="form-box">
                        <h3>Publicación</h3>
                        <label class="checkbox-label">
                            <input type="checkbox" name="publicado" <?= ($articulo['publicado'] ?? false) ? 'checked' : '' ?>>
                            Publicar artículo
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="destacado" <?= ($articulo['destacado'] ?? false) ? 'checked' : '' ?>>
                            ⭐ Artículo destacado
                        </label>
                        <div style="margin-top:1rem;">
                            <button type="submit" class="btn-primary" style="width:100%">
                                <?= $editId ? '💾 Guardar cambios' : '✅ Crear artículo' ?>
                            </button>
                        </div>
                    </div>

                    <div class="form-box">
                        <h3>Categoría *</h3>
                        <select name="categoria_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($articulo['categoria_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-box">
                        <h3>Juego relacionado</h3>
                        <select name="juego_id">
                            <option value="">Ninguno</option>
                            <?php foreach ($juegos as $j): ?>
                            <option value="<?= $j['id'] ?>"
                                <?= ($articulo['juego_id'] ?? 0) == $j['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-box">
                        <h3>Calificación (0–10)</h3>
                        <input type="number" name="calificacion" min="0" max="10" step="0.1"
                               value="<?= $articulo['calificacion'] ?? '' ?>" placeholder="Ej: 9.5">
                    </div>

                    <div class="form-box">
                        <h3>Imagen destacada</h3>
                        <?php if (!empty($articulo['imagen_destacada'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($articulo['imagen_destacada']) ?>"
                             style="width:100%;margin-bottom:.5rem;border:1px solid #333;" alt="Imagen actual">
                        <?php endif; ?>
                        <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.gif,.webp">
                        <small class="hint-txt">JPG, PNG, GIF, WebP. Máx 5 MB.</small>
                    </div>

                    <div class="form-box">
                        <h3>Tags</h3>
                        <?php foreach ($todosTags as $tag): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>"
                                <?= in_array($tag['id'], $artTags) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($tag['nombre']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        </form>
    </main>
</div>
</body>
</html>
