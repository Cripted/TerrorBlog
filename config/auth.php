<?php
/**
 * Sistema de Autenticación - Terror Digital
 */

require_once __DIR__ . 'database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
        $this->initSession();
    }
    
    /**
     * Inicializar sesión
     */
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params(SESSION_LIFETIME);
            session_start();
        }
    }
    
    /**
     * Registrar nuevo usuario
     */
    public function register($username, $email, $password, $nombreCompleto) {
        try {
            // Validar que el usuario no exista
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'El usuario o email ya existe'];
            }
            
            // Hash de la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar usuario
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (username, email, password, nombre_completo, rol) 
                VALUES (?, ?, ?, ?, 'autor')
            ");
            
            $stmt->execute([$username, $email, $hashedPassword, $nombreCompleto]);
            
            return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
        }
    }
    
    /**
     * Iniciar sesión
     */
    public function login($username, $password, $remember = false) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, password, nombre_completo, rol, avatar 
                FROM usuarios 
                WHERE (username = ? OR email = ?) AND activo = TRUE
            ");
            
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }
            
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Contraseña incorrecta'];
            }
            
            // Crear sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nombre_completo'] = $user['nombre_completo'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['avatar'] = $user['avatar'];
            $_SESSION['logged_in'] = true;
            
            // Actualizar último acceso
            $updateStmt = $this->db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Cookie de recordar sesión
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 días
            }
            
            return ['success' => true, 'message' => 'Sesión iniciada correctamente', 'user' => $user];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al iniciar sesión: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        session_destroy();
        
        return ['success' => true, 'message' => 'Sesión cerrada'];
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Obtener usuario actual
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'nombre_completo' => $_SESSION['nombre_completo'],
            'rol' => $_SESSION['rol'],
            'avatar' => $_SESSION['avatar']
        ];
    }
    
    /**
     * Verificar rol del usuario
     */
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $roles = ['autor' => 1, 'editor' => 2, 'admin' => 3];
        $userRole = $_SESSION['rol'];
        
        return isset($roles[$userRole]) && $roles[$userRole] >= $roles[$role];
    }
    
    /**
     * Requiere autenticación (middleware)
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            setFlashMessage('error', 'Debes iniciar sesión para acceder a esta página');
            redirect(SITE_URL . '/admin/login.php');
        }
    }
    
    /**
     * Requiere rol específico (middleware)
     */
    public function requireRole($role) {
        $this->requireLogin();
        
        if (!$this->hasRole($role)) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta página');
            redirect(SITE_URL . '/admin/index.php');
        }
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Verificar contraseña actual
            $stmt = $this->db->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!password_verify($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'La contraseña actual es incorrecta'];
            }
            
            // Actualizar contraseña
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $this->db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $updateStmt->execute([$hashedPassword, $userId]);
            
            return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al cambiar contraseña: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar perfil
     */
    public function updateProfile($userId, $data) {
        try {
            $fields = [];
            $values = [];
            
            if (isset($data['nombre_completo'])) {
                $fields[] = 'nombre_completo = ?';
                $values[] = $data['nombre_completo'];
            }
            
            if (isset($data['email'])) {
                $fields[] = 'email = ?';
                $values[] = $data['email'];
            }
            
            if (isset($data['avatar'])) {
                $fields[] = 'avatar = ?';
                $values[] = $data['avatar'];
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'No hay datos para actualizar'];
            }
            
            $values[] = $userId;
            $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            // Actualizar sesión
            if (isset($data['nombre_completo'])) {
                $_SESSION['nombre_completo'] = $data['nombre_completo'];
            }
            if (isset($data['email'])) {
                $_SESSION['email'] = $data['email'];
            }
            if (isset($data['avatar'])) {
                $_SESSION['avatar'] = $data['avatar'];
            }
            
            return ['success' => true, 'message' => 'Perfil actualizado correctamente'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al actualizar perfil: ' . $e->getMessage()];
        }
    }
}

// Instancia global de Auth
$auth = new Auth();