<?php
require_once '../config/auth.php';
$auth->requireRole('admin');
$user = $auth->getCurrentUser();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['nuevo_usuario'])) {
        $r = $auth->register(sanitize($_POST['username']??''), sanitize($_POST['email']??''), $_POST['password']??'', sanitize($_POST['nombre_completo']??''), in_array($_POST['rol']??'',['autor','editor','admin'])?$_POST['rol']:'autor');
        setFlashMessage($r['success']?'success':'error', $r['message']);
    } elseif (isset($_POST['toggle_id'])) {
        $s=$conn->prepare("UPDATE usuarios SET activo=NOT activo WHERE id=?"); $s->bind_param("i",(int)$_POST['toggle_id']); $s->execute(); $s->close();
        setFlashMessage('success','Estado actualizado');
    } elseif (isset($_POST['change_rol'])) {
        $rol=in_array($_POST['nuevo_rol']??'',['autor','editor','admin'])?$_POST['nuevo_rol']:'autor';
        $s=$conn->prepare("UPDATE usuarios SET rol=? WHERE id=?"); $s->bind_param("si",$rol,(int)$_POST['change_rol']); $s->execute(); $s->close();
        setFlashMessage('success','Rol actualizado');
    }
    redirect(SITE_URL.'/admin/usuarios.php');
}
$users=$conn->query("SELECT * FROM usuarios ORDER BY creado_en DESC")->fetch_all(MYSQLI_ASSOC);
$flash=getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Admin | Terror Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600;700&family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css"><link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="grain"></div>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header"><h1>👥 Usuarios</h1></div>
        <?php if ($flash): ?><div class="alert alert-<?= $flash['type']==='error'?'error':'success' ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <div class="admin-section">
            <h2>Crear usuario</h2>
            <form method="POST" class="admin-form-inline">
                <input type="hidden" name="nuevo_usuario" value="1">
                <input type="text"     name="username"        placeholder="Usuario *"  required>
                <input type="email"    name="email"           placeholder="Email *"    required>
                <input type="text"     name="nombre_completo" placeholder="Nombre *"   required>
                <input type="password" name="password"        placeholder="Contraseña *" required>
                <select name="rol"><option value="autor">Autor</option><option value="editor">Editor</option><option value="admin">Admin</option></select>
                <button type="submit" class="btn-sm">➕ Crear</button>
            </form>
        </div>
        <table class="admin-table">
            <thead><tr><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Último acceso</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach($users as $u): ?>
            <tr>
                <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <?php if ($u['id']!=$user['id']): ?>
                    <form method="POST" style="display:inline"><input type="hidden" name="change_rol" value="<?= $u['id'] ?>"><select name="nuevo_rol" onchange="this.form.submit()"><?php foreach(['autor','editor','admin'] as $r): ?><option value="<?= $r ?>" <?= $u['rol']===$r?'selected':'' ?>><?= ucfirst($r) ?></option><?php endforeach; ?></select></form>
                    <?php else: ?><span class="badge badge-gold"><?= ucfirst($u['rol']) ?> (tú)</span><?php endif; ?>
                </td>
                <td><span class="badge <?= $u['activo']?'badge-green':'badge-gray' ?>"><?= $u['activo']?'Activo':'Inactivo' ?></span></td>
                <td><?= $u['ultimo_acceso']?date('d/m/Y H:i',strtotime($u['ultimo_acceso'])):'Nunca' ?></td>
                <td>
                    <?php if ($u['id']!=$user['id']): ?>
                    <form method="POST" style="display:inline"><input type="hidden" name="toggle_id" value="<?= $u['id'] ?>"><button type="submit" class="btn-xs"><?= $u['activo']?'🚫 Desactivar':'✅ Activar' ?></button></form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>