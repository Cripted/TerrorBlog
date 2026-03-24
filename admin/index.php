<?php
require_once '../config/auth.php';
$auth->requireLogin();
$user = $auth->getCurrentUser();

$ta = $conn->query("SELECT COUNT(*) FROM articulos WHERE publicado=1")->fetch_row()[0];
$tb = $conn->query("SELECT COUNT(*) FROM articulos WHERE publicado=0")->fetch_row()[0];
$td = $conn->query("SELECT COUNT(*) FROM discusiones WHERE aprobado=1")->fetch_row()[0];
$tu = $conn->query("SELECT COUNT(*) FROM usuarios WHERE activo=1")->fetch_row()[0];

$arts = $conn->query("SELECT a.id,a.titulo,a.slug,a.publicado,a.vistas,a.fecha_publicacion,c.nombre AS categoria,u.nombre_completo AS autor FROM articulos a LEFT JOIN categorias c ON a.categoria_id=c.id LEFT JOIN usuarios u ON a.autor_id=u.id ORDER BY a.creado_en DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$discs = $conn->query("SELECT d.id,d.titulo,d.autor_nombre,d.likes,d.fecha_creacion,j.nombre AS juego FROM discusiones d JOIN juegos j ON d.juego_id=j.id ORDER BY d.fecha_creacion DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin | Terror Digital</title>
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
            <div><h1>Panel de Control</h1><p>Bienvenido, <strong><?= htmlspecialchars($user['nombre_completo']) ?></strong> — <?= date('d/m/Y H:i') ?></p></div>
            <a href="nuevo_articulo.php" class="btn-primary">➕ Nuevo artículo</a>
        </div>
        <?php if ($flash): ?><div class="alert alert-<?= $flash['type']==='error'?'error':'success' ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <div class="stats-grid">
            <div class="stat-box"><div class="stat-icon">📰</div><div class="stat-info"><span class="stat-num"><?= $ta ?></span><span class="stat-lbl">Publicados</span></div></div>
            <div class="stat-box"><div class="stat-icon">📝</div><div class="stat-info"><span class="stat-num"><?= $tb ?></span><span class="stat-lbl">Borradores</span></div></div>
            <div class="stat-box"><div class="stat-icon">💬</div><div class="stat-info"><span class="stat-num"><?= $td ?></span><span class="stat-lbl">Discusiones</span></div></div>
            <div class="stat-box"><div class="stat-icon">👥</div><div class="stat-info"><span class="stat-num"><?= $tu ?></span><span class="stat-lbl">Usuarios</span></div></div>
        </div>
        <div class="admin-section">
            <div class="section-head"><h2>Últimos artículos</h2><a href="articulos.php" class="btn-sm">Ver todos</a></div>
            <table class="admin-table">
                <thead><tr><th>Título</th><th>Categoría</th><th>Autor</th><th>Vistas</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($arts as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['titulo']) ?></td>
                    <td><?= htmlspecialchars($a['categoria']??'—') ?></td>
                    <td><?= htmlspecialchars($a['autor']??'—') ?></td>
                    <td><?= number_format($a['vistas']) ?></td>
                    <td><span class="badge <?= $a['publicado']?'badge-green':'badge-gray' ?>"><?= $a['publicado']?'Publicado':'Borrador' ?></span></td>
                    <td><a href="editar_articulo.php?id=<?= $a['id'] ?>" class="btn-xs">✏️ Editar</a> <a href="../pages/articulo.html?id=<?= $a['slug'] ?>" target="_blank" class="btn-xs">👁️ Ver</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$arts): ?><tr><td colspan="6" style="text-align:center;color:#666;padding:1.5rem;">Sin artículos aún. <a href="nuevo_articulo.php">Crea el primero</a></td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="admin-section">
            <div class="section-head"><h2>Últimas discusiones</h2><a href="discusiones.php" class="btn-sm">Ver todas</a></div>
            <table class="admin-table">
                <thead><tr><th>Título</th><th>Autor</th><th>Juego</th><th>Likes</th><th>Fecha</th></tr></thead>
                <tbody>
                <?php foreach ($discs as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['titulo']) ?></td>
                    <td><?= htmlspecialchars($d['autor_nombre']) ?></td>
                    <td><?= htmlspecialchars($d['juego']) ?></td>
                    <td>❤️ <?= $d['likes'] ?></td>
                    <td><?= date('d/m/Y',strtotime($d['fecha_creacion'])) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>