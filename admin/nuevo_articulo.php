<?php
require_once '../config/auth.php';
$auth->requireLogin();
$user = $auth->getCurrentUser();

$editId  = isset($_GET['id']) ? (int)$_GET['id'] : null;
$art     = null;
$artTags = [];
$errors  = [];

// Cargar artículo a editar
if ($editId) {
    $s = $conn->prepare("SELECT * FROM articulos WHERE id=?");
    $s->bind_param("i",$editId); $s->execute();
    $art = $s->get_result()->fetch_assoc(); $s->close();
    if (!$art) { setFlashMessage('error','Artículo no encontrado'); redirect(SITE_URL.'/admin/articulos.php'); }
    if (!$auth->hasRole('editor') && $art['autor_id']!=$user['id']) { setFlashMessage('error','Sin permiso'); redirect(SITE_URL.'/admin/articulos.php'); }
    $t = $conn->prepare("SELECT tag_id FROM articulo_tags WHERE articulo_id=?");
    $t->bind_param("i",$editId); $t->execute();
    $artTags = array_column($t->get_result()->fetch_all(MYSQLI_ASSOC),'tag_id'); $t->close();
}

// Guardar
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $titulo    = sanitize($_POST['titulo']      ?? '');
    $extracto  = sanitize($_POST['extracto']    ?? '');
    $contenido = $_POST['contenido'] ?? '';
    $catId     = (int)($_POST['categoria_id']   ?? 0);
    $juegoId   = !empty($_POST['juego_id'])      ? (int)$_POST['juego_id']    : null;
    $calif     = !empty($_POST['calificacion'])  ? (float)$_POST['calificacion'] : null;
    $dest      = isset($_POST['destacado'])      ? 1 : 0;
    $pub       = isset($_POST['publicado'])      ? 1 : 0;
    $tags      = $_POST['tags'] ?? [];
    $slug      = generateSlug($titulo);

    if (!$titulo)  $errors[]='El título es requerido';
    if (!$contenido) $errors[]='El contenido es requerido';
    if (!$catId)   $errors[]='La categoría es requerida';

    // Imagen
    $imagen = $art['imagen_destacada'] ?? null;
    if (!empty($_FILES['imagen']['name'])) {
        $up = uploadImage($_FILES['imagen'],'art');
        if ($up['success']) $imagen = $up['filename'];
        else $errors[] = $up['message'];
    }

    if (!$errors) {
        if ($editId) {
            $s = $conn->prepare("UPDATE articulos SET titulo=?,slug=?,extracto=?,contenido=?,imagen_destacada=?,categoria_id=?,juego_id=?,calificacion=?,destacado=?,publicado=?,fecha_publicacion=IF(?=1 AND fecha_publicacion IS NULL,NOW(),fecha_publicacion) WHERE id=?");
            $s->bind_param("sssssiiidiii",$titulo,$slug,$extracto,$contenido,$imagen,$catId,$juegoId,$calif,$dest,$pub,$pub,$editId);
            $s->execute(); $s->close();
            $artId = $editId;
        } else {
            $s = $conn->prepare("INSERT INTO articulos (titulo,slug,extracto,contenido,imagen_destacada,autor_id,categoria_id,juego_id,calificacion,destacado,publicado,fecha_publicacion) VALUES (?,?,?,?,?,?,?,?,?,?,?,IF(?=1,NOW(),NULL))");
            $s->bind_param("sssssiiidiii",$titulo,$slug,$extracto,$contenido,$imagen,$user['id'],$catId,$juegoId,$calif,$dest,$pub,$pub);
            $s->execute(); $artId=$conn->insert_id; $s->close();
        }
        // Tags
        $d=$conn->prepare("DELETE FROM articulo_tags WHERE articulo_id=?");
        $d->bind_param("i",$artId); $d->execute(); $d->close();
        foreach ($tags as $tid) {
            $i=$conn->prepare("INSERT IGNORE INTO articulo_tags (articulo_id,tag_id) VALUES (?,?)");
            $i->bind_param("ii",$artId,$tid); $i->execute(); $i->close();
        }
        setFlashMessage('success', $editId?'Artículo actualizado':'Artículo creado correctamente');
        redirect(SITE_URL.'/admin/articulos.php');
    }
}

$cats   = $conn->query("SELECT * FROM categorias WHERE activo=1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$juegos = $conn->query("SELECT * FROM juegos WHERE activo=1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$allTags= $conn->query("SELECT * FROM tags ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$flash  = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editId?'Editar':'Nuevo' ?> Artículo - Admin | Terror Digital</title>
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
            <h1><?= $editId?'✏️ Editar':'➕ Nuevo' ?> Artículo</h1>
            <a href="articulos.php" class="btn-sm">← Volver</a>
        </div>

        <?php if ($errors): ?>
        <div class="alert alert-error"><?php foreach($errors as $e) echo "<p>• ".htmlspecialchars($e)."</p>"; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-row">
                <div class="form-col-main">
                    <div class="form-group">
                        <label>Título *</label>
                        <input type="text" name="titulo" required value="<?= htmlspecialchars($art['titulo']??'') ?>">
                    </div>
                    <div class="form-group">
                        <label>Extracto</label>
                        <textarea name="extracto" rows="3"><?= htmlspecialchars($art['extracto']??'') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Contenido * <small style="color:#555">(HTML permitido)</small></label>
                        <textarea name="contenido" rows="22" class="content-editor"><?= htmlspecialchars($art['contenido']??'') ?></textarea>
                    </div>
                </div>
                <div class="form-col-side">
                    <div class="form-box">
                        <h3>Publicación</h3>
                        <label class="checkbox-label"><input type="checkbox" name="publicado" <?= ($art['publicado']??0)?'checked':'' ?>> Publicar</label>
                        <label class="checkbox-label"><input type="checkbox" name="destacado" <?= ($art['destacado']??0)?'checked':'' ?>> ⭐ Destacado</label>
                        <div style="margin-top:1rem;"><button type="submit" class="btn-primary" style="width:100%"><?= $editId?'💾 Guardar':'✅ Crear' ?></button></div>
                    </div>
                    <div class="form-box">
                        <h3>Categoría *</h3>
                        <select name="categoria_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($cats as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($art['categoria_id']??0)==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-box">
                        <h3>Juego relacionado</h3>
                        <select name="juego_id">
                            <option value="">Ninguno</option>
                            <?php foreach($juegos as $j): ?>
                            <option value="<?= $j['id'] ?>" <?= ($art['juego_id']??0)==$j['id']?'selected':'' ?>><?= htmlspecialchars($j['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-box">
                        <h3>Calificación (0–10)</h3>
                        <input type="number" name="calificacion" min="0" max="10" step="0.1" value="<?= $art['calificacion']??'' ?>" placeholder="Ej: 9.5">
                    </div>
                    <div class="form-box">
                        <h3>Imagen destacada</h3>
                        <?php if (!empty($art['imagen_destacada'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($art['imagen_destacada']) ?>" style="width:100%;margin-bottom:.5rem;border:1px solid #333;" alt="Imagen actual">
                        <p style="font-size:.8rem;color:#666;margin-bottom:.5rem;">Imagen actual. Sube otra para reemplazarla.</p>
                        <?php endif; ?>
                        <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.gif,.webp">
                        <small class="hint-txt">JPG, PNG, GIF, WebP. Máx 5 MB.</small>
                    </div>
                    <div class="form-box">
                        <h3>Tags</h3>
                        <?php foreach($allTags as $t): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="tags[]" value="<?= $t['id'] ?>" <?= in_array($t['id'],$artTags)?'checked':'' ?>>
                            <?= htmlspecialchars($t['nombre']) ?>
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