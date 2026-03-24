<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico - Terror Digital</title>
    <style>
        body { font-family: monospace; background: #111; color: #eee; padding: 2rem; }
        h1   { color: #8B0000; }
        h2   { color: #dc143c; margin-top: 2rem; border-bottom: 1px solid #333; padding-bottom: .5rem; }
        .ok  { color: #0f0; }
        .err { color: #f44; }
        .warn{ color: #fa0; }
        pre  { background: #1a1a1a; padding: 1rem; border-left: 3px solid #333; overflow-x: auto; }
        table{ border-collapse: collapse; width: 100%; }
        td, th { padding: .4rem 1rem; border: 1px solid #333; text-align: left; }
        th   { background: #1a1a1a; color: #aaa; }
    </style>
</head>
<body>
<h1>🩸 Terror Digital — Diagnóstico del sistema</h1>
<p style="color:#666;">Ejecuta este archivo una sola vez y luego bórralo.</p>

<?php

// ── 1. PHP ────────────────────────────────────────────────
echo "<h2>1. PHP</h2>";
echo "<table>";
echo "<tr><th>Versión PHP</th><td>" . phpversion() . "</td></tr>";
echo "<tr><th>MySQLi</th><td>" . (extension_loaded('mysqli') ? '<span class="ok">✅ Habilitado</span>' : '<span class="err">❌ NO disponible</span>') . "</td></tr>";
echo "<tr><th>Session</th><td>" . (extension_loaded('session') ? '<span class="ok">✅ OK</span>' : '<span class="err">❌ Falta</span>') . "</td></tr>";
echo "<tr><th>GD (imágenes)</th><td>" . (extension_loaded('gd') ? '<span class="ok">✅ OK</span>' : '<span class="warn">⚠️ Deshabilitado (subida de imágenes no funcionará)</span>') . "</td></tr>";
echo "<tr><th>Directorio actual</th><td>" . __DIR__ . "</td></tr>";
echo "</table>";

// ── 2. Archivos clave ─────────────────────────────────────
echo "<h2>2. Archivos del proyecto</h2>";
echo "<table>";
$archivos = [
    'config/database.php'    => 'Configuración BD',
    'config/auth.php'        => 'Sistema de autenticación',
    'php/api_articulos.php'  => 'API artículos',
    'php/api_discusiones.php'=> 'API discusiones',
    'php/api_general.php'    => 'API general',
    'admin/login.php'        => 'Login admin',
    'admin/index.php'        => 'Dashboard admin',
    'index.html'             => 'Página principal',
    'pages/articulo.html'    => 'Página de artículo',
    'pages/discusiones.html' => 'Discusiones',
    'css/styles.css'         => 'Estilos',
    'js/articles_db.js'      => 'JS artículos',
    'js/discusiones_db.js'   => 'JS discusiones',
];
foreach ($archivos as $path => $label) {
    $exists = file_exists(__DIR__ . '/' . $path);
    echo "<tr><th>{$label}</th><td><code>{$path}</code></td><td>"
       . ($exists ? '<span class="ok">✅ Existe</span>' : '<span class="err">❌ NO encontrado</span>')
       . "</td></tr>";
}
echo "</table>";

// ── 3. Base de datos ──────────────────────────────────────
echo "<h2>3. Conexión a la base de datos</h2>";

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'terror_digital';

$conn = @new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "<p class='err'>❌ Error de conexión: " . $conn->connect_error . "</p>";
    echo "<p class='warn'>Posibles causas:</p><ul>
        <li>MySQL no está corriendo en XAMPP — verifica que el módulo MySQL esté verde</li>
        <li>La base de datos <strong>terror_digital</strong> no fue creada — ve a phpMyAdmin y créala</li>
        <li>Las credenciales son incorrectas — edita <code>config/database.php</code></li>
    </ul>";
} else {
    echo "<p class='ok'>✅ Conexión exitosa a <strong>{$db}</strong></p>";
    $conn->set_charset('utf8mb4');

    // Verificar tablas
    echo "<h2>4. Tablas de la base de datos</h2><table>";
    $tablas = ['usuarios','categorias','juegos','articulos','articulo_tags','tags','discusiones','comentarios_discusion'];
    foreach ($tablas as $tabla) {
        $res   = $conn->query("SHOW TABLES LIKE '{$tabla}'");
        $exist = $res && $res->num_rows > 0;

        $count = '';
        if ($exist) {
            $r = $conn->query("SELECT COUNT(*) FROM `{$tabla}`");
            $count = $r ? ' (' . $r->fetch_row()[0] . ' registros)' : '';
        }

        echo "<tr><td><code>{$tabla}</code></td><td>"
           . ($exist
               ? "<span class='ok'>✅ OK{$count}</span>"
               : "<span class='err'>❌ No existe — importa database.sql en phpMyAdmin</span>")
           . "</td></tr>";
    }
    echo "</table>";

    // Verificar usuario admin
    echo "<h2>5. Usuario admin</h2>";
    $res = $conn->query("SELECT id, username, rol, activo FROM usuarios WHERE username = 'admin'");
    if ($res && $res->num_rows > 0) {
        $admin = $res->fetch_assoc();
        echo "<p class='ok'>✅ Usuario admin encontrado</p>";
        echo "<table>
            <tr><th>ID</th><td>{$admin['id']}</td></tr>
            <tr><th>Username</th><td>{$admin['username']}</td></tr>
            <tr><th>Rol</th><td>{$admin['rol']}</td></tr>
            <tr><th>Activo</th><td>" . ($admin['activo'] ? '✅ Sí' : '❌ No') . "</td></tr>
        </table>";

        // Verificar que la contraseña funciona
        $res2 = $conn->query("SELECT password FROM usuarios WHERE username = 'admin'");
        $row  = $res2->fetch_assoc();
        $hashOk = password_verify('admin123', $row['password']);
        echo "<p>" . ($hashOk
            ? "<span class='ok'>✅ Contraseña admin123 es correcta</span>"
            : "<span class='err'>❌ Contraseña incorrecta — accede a <a href='reset_admin.php' style='color:#dc143c'>reset_admin.php</a> para resetearla</span>")
        . "</p>";
    } else {
        echo "<p class='err'>❌ No existe usuario admin — importa database.sql y ejecuta reset_admin.php</p>";
        echo "<p><a href='reset_admin.php' style='color:#dc143c'>→ Ejecutar reset_admin.php ahora</a></p>";
    }

    // Verificar datos de ejemplo
    echo "<h2>6. Datos de ejemplo</h2><table>";
    $checks = [
        'Categorías' => "SELECT COUNT(*) FROM categorias",
        'Juegos'     => "SELECT COUNT(*) FROM juegos",
        'Artículos publicados' => "SELECT COUNT(*) FROM articulos WHERE publicado = 1",
        'Discusiones' => "SELECT COUNT(*) FROM discusiones",
    ];
    foreach ($checks as $label => $sql) {
        $r = $conn->query($sql);
        $n = $r ? $r->fetch_row()[0] : 0;
        echo "<tr><td>{$label}</td><td>"
           . ($n > 0
               ? "<span class='ok'>✅ {$n} registros</span>"
               : "<span class='warn'>⚠️ 0 registros — importa database.sql</span>")
           . "</td></tr>";
    }
    echo "</table>";

    $conn->close();
}

// ── 4. Carpeta uploads ────────────────────────────────────
echo "<h2>7. Carpeta de uploads</h2>";
$uploadsDir = __DIR__ . '/uploads/';
if (!file_exists($uploadsDir)) {
    if (mkdir($uploadsDir, 0777, true)) {
        echo "<p class='ok'>✅ Carpeta uploads creada automáticamente</p>";
    } else {
        echo "<p class='err'>❌ No se pudo crear la carpeta uploads — créala manualmente en htdocs/TerrorBlog/uploads/</p>";
    }
} else {
    $writable = is_writable($uploadsDir);
    echo "<p class='" . ($writable ? 'ok' : 'err') . "'>"
       . ($writable ? '✅' : '❌') . " uploads/ "
       . ($writable ? 'existe y tiene permisos de escritura' : 'existe pero NO tiene permisos — chmod 777 uploads/')
       . "</p>";
}

// ── 5. URLs de acceso ─────────────────────────────────────
echo "<h2>8. Links de acceso rápido</h2>";
$base = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
// limpiar doble slash
$base = rtrim(str_replace('//', '/', $base), '/');
$root = str_replace('/diagnostico', '', $base);

echo "<table>
    <tr><td>Sitio público</td><td><a href='{$root}/index.html' style='color:#dc143c'>{$root}/index.html</a></td></tr>
    <tr><td>Panel admin</td><td><a href='{$root}/admin/login.php' style='color:#dc143c'>{$root}/admin/login.php</a></td></tr>
    <tr><td>API artículos</td><td><a href='{$root}/php/api_articulos.php?action=list' style='color:#dc143c'>{$root}/php/api_articulos.php?action=list</a></td></tr>
    <tr><td>API discusiones</td><td><a href='{$root}/php/api_discusiones.php?action=list&juego=silent-hill-2' style='color:#dc143c'>api_discusiones.php?juego=silent-hill-2</a></td></tr>
    <tr><td>Reset admin</td><td><a href='{$root}/reset_admin.php' style='color:#fa0'>{$root}/reset_admin.php</a></td></tr>
</table>";

echo "<p style='margin-top:2rem; color:#555;'>⚠️ Borra este archivo (diagnostico.php) después de usarlo.</p>";
?>
</body>
</html>