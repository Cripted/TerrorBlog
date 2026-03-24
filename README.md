# 🩸 TERROR DIGITAL — Guía de Instalación

## Requisitos
- XAMPP con PHP 7.4+ y MySQL 5.7+
- Navegador moderno

---

## Paso 1 — Colocar el proyecto

Copia la carpeta `TerrorBlog` dentro de:
```
C:/xampp/htdocs/TerrorBlog/
```

---

## Paso 2 — Crear la base de datos

1. Abre **phpMyAdmin**: http://localhost/phpmyadmin
2. Crea una base de datos llamada **`terror_digital`**  
   (cotejamiento: `utf8mb4_unicode_ci`)
3. Selecciónala → pestaña **Importar** → sube `database.sql` → **Continuar**

---

## Paso 3 — Verificar configuración

Abre `config/database.php` y confirma los datos de tu XAMPP:

```php
$servername = "localhost";
$username   = "root";
$password   = "";              // Vacío por defecto en XAMPP
$dbname     = "terror_digital";
```

Si tu MySQL tiene contraseña, escríbela en `$password`.

---

## Paso 4 — Generar contraseña del admin

Accede a:
```
http://localhost/TerrorBlog/reset_admin.php
```
Verás el mensaje ✅ y ya puedes iniciar sesión.  
**⚠️ Elimina `reset_admin.php` del servidor después de usarlo.**

---

## Paso 5 — Acceder

| URL | Descripción |
|-----|-------------|
| http://localhost/TerrorBlog/ | Sitio público |
| http://localhost/TerrorBlog/admin/login.php | Panel admin |

**Credenciales por defecto:**
- Usuario: `admin`
- Contraseña: `admin123`

---

## Estructura del proyecto

```
TerrorBlog/
├── config/
│   ├── database.php        ← Conexión MySQLi + helpers
│   └── auth.php            ← Login, sesiones, roles
├── admin/
│   ├── login.php           ← Formulario de acceso
│   ├── logout.php          ← Cerrar sesión
│   ├── index.php           ← Dashboard
│   ├── articulos.php       ← Listar artículos
│   ├── nuevo_articulo.php  ← Crear artículo
│   ├── editar_articulo.php ← Editar artículo (mismo archivo)
│   ├── discusiones.php     ← Moderar discusiones
│   ├── usuarios.php        ← Gestionar usuarios  (solo admin)
│   ├── juegos.php          ← Gestionar juegos    (solo admin)
│   ├── perfil.php          ← Editar perfil propio
│   └── partials/
│       └── sidebar.php     ← Menú lateral reutilizable
├── php/
│   ├── api_articulos.php   ← API REST artículos
│   ├── api_discusiones.php ← API REST discusiones
│   └── api_general.php     ← API REST juegos y estadísticas
├── css/
│   ├── styles.css          ← Estilos sitio público
│   └── admin.css           ← Estilos panel admin
├── js/
│   ├── script.js           ← JS general
│   ├── articles_db.js      ← Carga artículos desde API
│   └── discusiones_db.js   ← Carga discusiones desde API
├── pages/
│   ├── articulo.html
│   ├── noticias.html
│   ├── reviews.html
│   ├── guias.html
│   ├── discusiones.html
│   └── comunidad.html
├── uploads/                ← Se crea automáticamente
├── index.html
└── database.sql            ← Esquema + datos de ejemplo

## Roles de usuario

| Rol | Permisos |
|-----|----------|
| **Autor** | Crear y editar sus propios artículos |
| **Editor** | Editar todos los artículos, moderar discusiones |
| **Admin** | Todo lo anterior + gestionar usuarios y juegos |

---

## Tecnología usada

- **PHP + MySQLi** con prepared statements (seguro contra SQL injection)
- `password_hash()` / `password_verify()` para contraseñas
- Sesiones PHP con nombre personalizado
- APIs REST en JSON consumidas desde JavaScript con `fetch()`