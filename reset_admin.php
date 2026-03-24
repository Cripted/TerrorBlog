<?php
/**
 * reset_admin.php
 * Genera el hash correcto para la contraseña del admin.
 * Accede a: http://localhost/TerrorBlog/reset_admin.php
 * ¡ELIMINA ESTE ARCHIVO después de usarlo!
 */

require_once 'config/database.php';

$password = 'admin123';
$hash     = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $hash);
$ok = $stmt->execute();
$stmt->close();
$conn->close();

if ($ok) {
    echo "
    <div style='font-family:monospace; max-width:500px; margin:80px auto; padding:2rem;
                background:#111; border:2px solid #8B0000; color:#eee;'>
        <h2 style='color:#8B0000;'>✅ Contraseña actualizada</h2>
        <p>Usuario: <strong>admin</strong></p>
        <p>Contraseña: <strong>admin123</strong></p>
        <hr style='border-color:#333; margin:1rem 0;'>
        <p style='color:#dc143c;'>⚠️ Elimina este archivo ahora:<br>
           <code>C:/xampp/htdocs/TerrorBlog/reset_admin.php</code>
        </p>
        <a href='admin/login.php'
           style='display:inline-block; margin-top:1rem; background:#8B0000;
                  color:#fff; padding:.7rem 1.5rem; text-decoration:none;'>
            → Ir al login
        </a>
    </div>";
} else {
    echo "<p style='color:red; font-family:monospace;'>Error al actualizar: " . $conn->error . "</p>";
}