<?php
require_once '../config/auth.php';
$auth->requireRole('admin');
$user = $auth->getCurrentUser();
$db   = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nuevo_juego'])) {
        $nom  = sanitize($_POST['nombre'] ?? '');
        $slug = generateSlug($nom);
        if ($nom) {
            $dev  = sanitize($_POST['desarrollador'] ?? '');
            $desc = sanitize($_POST['descripcion']   ?? '');
            $anio = !empty($_POST['anio_lanzamiento']) ? (int)$_POST['anio_lanzamiento'] : null;
            $cal  = !empty($_POST['calificacion'])     ? (float)$_POST['calificacion']   : null;
            $db->prepare("INSERT INTO juegos (nombre, slug, descripcion, desarrollador, anio_lanzamiento, calificacion) VALUES (?, ?, ?, ?, ?, ?)")
               ->execute([$nom, $slug, $desc, $dev, $anio, $cal]);
            setFlashMessage('success', "Juego \"$nom\" creado");
        } else {
            setFlashMessage('error', 'Nombre requerido');
        }
    } elseif (isset($_POST['toggle_juego'])) {
        $db->prepare("UPDATE juegos SET activo = NOT activo WHERE id = ?")->execute([(int)$_POST['toggle_juego']]);
        setFlashMessage('success', 'Estado actualizado');
    }
    redirect(SITE_URL . '/admin/juegos.php');
}

$juegos = $db->query("
    SELECT j.*,
           (SELECT COUNT(*) FROM discusiones d  WHERE d.juego_id  = j.id) AS total_disc,
           (SELECT COUNT(*) FROM articulos a    WHERE a.juego_id  = j.id) AS total_arts
    FROM juegos j
    ORDER BY j.nombre
")->fetchAll();

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juegos - Admin | Terror Digital</title>
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
            <h1>🎮 Juegos</h1>
        </div>

        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
        <?php endif; ?>

        <div class="admin-section">
            <h2>Agregar juego</h2>
            <form method="POST" class="admin-form">
                <input type="hidden" name="nuevo_juego" value="1">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" name="nombre" required placeholder="Silent Hill 2">
                    </div>
                    <div class="form-group">
                        <label>Desarrollador</label>
                        <input type="text" name="desarrollador" placeholder="Bloober Team">
                    </div>
                    <div class="form-group">
                        <label>Año</label>
                        <input type="number" name="anio_lanzamiento" min="1990" max="2030" placeholder="2024">
                    </div>
                    <div class="form-group">
                        <label>Calificación (0-10)</label>
                        <input type="number" name="calificacion" min="0" max="10" step="0.1" placeholder="9.5">
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="2" placeholder="Breve descripción del juego..."></textarea>
                </div>
                <button type="submit" class="btn-sm">➕ Crear juego</button>
            </form>
        </div>

        <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Desarrollador</th>
                    <th>Año</th>
                    <th>Cal.</th>
                    <th>Artículos</th>
                    <th>Discusiones</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($juegos as $j): ?>
            <tr>
                <td><strong><?= htmlspecialchars($j['nombre']) ?></strong></td>
                <td><code style="color:#888;font-size:.78rem;"><?= htmlspecialchars($j['slug']) ?></code></td>
                <td><?= htmlspecialchars($j['desarrollador'] ?? '—') ?></td>
                <td><?= $j['anio_lanzamiento'] ?? '—' ?></td>
                <td><?= $j['calificacion'] ? number_format($j['calificacion'], 1) : '—' ?></td>
                <td><?= $j['total_arts'] ?></td>
                <td><?= $j['total_disc'] ?></td>
                <td>
                    <span class="badge <?= $j['activo'] ? 'badge-green' : 'badge-gray' ?>">
                        <?= $j['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="toggle_juego" value="<?= $j['id'] ?>">
                        <button type="submit" class="btn-xs">
                            <?= $j['activo'] ? '🚫 Desactivar' : '✅ Activar' ?>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($juegos)): ?>
            <tr><td colspan="9" style="text-align:center;color:#666;padding:2rem;">No hay juegos registrados todavía.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </main>
</div>
</body>
</html>