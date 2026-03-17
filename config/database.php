<?php
/**
 * Configuración de Base de Datos - Terror Digital
 * 
 * IMPORTANTE: Cambiar estos valores según tu configuración de XAMPP
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'terror_digital');
define('DB_USER', 'root');
define('DB_PASS', ''); // En XAMPP por defecto está vacío

// Configuración del sitio
define('SITE_URL', 'http://localhost/TerrorBlog');
define('SITE_NAME', 'Terror Digital');
define('SITE_DESCRIPTION', 'Blog de Videojuegos de Horror');

// Configuración de sesiones
define('SESSION_NAME', 'terror_digital_session');
define('SESSION_LIFETIME', 7200); // 2 horas en segundos

// Configuración de uploads
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes
define('ALLOWED_IMAGES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Timezone
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (cambiar a false en producción)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * Clase de Conexión a Base de Datos
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevenir clonación
    private function __clone() {}
    
    // Prevenir unserialize
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Función helper para obtener la conexión
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Función para sanitizar inputs
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Función para generar slug
 */
function generateSlug($text) {
    // Convertir a minúsculas
    $text = mb_strtolower($text, 'UTF-8');
    
    // Reemplazar acentos
    $replacements = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ñ' => 'n', 'ü' => 'u'
    ];
    $text = strtr($text, $replacements);
    
    // Eliminar caracteres especiales
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    
    // Reemplazar espacios y guiones múltiples
    $text = preg_replace('/[\s-]+/', '-', $text);
    
    // Eliminar guiones del inicio y final
    return trim($text, '-');
}

/**
 * Función para formatear fechas
 */
function formatDate($date, $format = 'd \d\e F, Y') {
    $meses = [
        'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
        'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
        'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
        'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
    ];
    
    $fecha = date($format, strtotime($date));
    return strtr($fecha, $meses);
}

/**
 * Función para redireccionar
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Función para mostrar mensajes flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Función para subir imágenes
 */
function uploadImage($file, $prefix = 'img') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error al subir el archivo'];
    }
    
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileName = $file['name'];
    
    // Verificar tamaño
    if ($fileSize > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'El archivo es demasiado grande'];
    }
    
    // Verificar extensión
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExt, ALLOWED_IMAGES)) {
        return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
    }
    
    // Generar nombre único
    $newFileName = $prefix . '_' . uniqid() . '_' . time() . '.' . $fileExt;
    $uploadPath = UPLOAD_DIR . $newFileName;
    
    // Crear directorio si no existe
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }
    
    // Mover archivo
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return [
            'success' => true,
            'filename' => $newFileName,
            'url' => UPLOAD_URL . $newFileName
        ];
    }
    
    return ['success' => false, 'message' => 'Error al mover el archivo'];
}