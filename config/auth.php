<?php
require_once __DIR__ . '/database.php';

class Auth {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        _sess(); // arrancar sesión con nombre correcto
    }

    public function login($username, $password, $remember = false) {
        $stmt = $this->conn->prepare(
            "SELECT id,username,email,password,nombre_completo,rol,avatar
             FROM usuarios WHERE (username=? OR email=?) AND activo=1 LIMIT 1"
        );
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) return ['success'=>false,'message'=>'Usuario no encontrado'];
        if (!password_verify($password, $user['password']))
            return ['success'=>false,'message'=>'Contraseña incorrecta'];

        session_regenerate_id(true);
        $_SESSION['uid']    = $user['id'];
        $_SESSION['uname']  = $user['username'];
        $_SESSION['email']  = $user['email'];
        $_SESSION['nombre'] = $user['nombre_completo'];
        $_SESSION['rol']    = $user['rol'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['ok']     = true;

        $u = $this->conn->prepare("UPDATE usuarios SET ultimo_acceso=NOW() WHERE id=?");
        $u->bind_param("i", $user['id']); $u->execute(); $u->close();

        if ($remember) setcookie('remember_token', bin2hex(random_bytes(32)), time()+86400*30, '/');

        return ['success'=>true,'message'=>'Sesión iniciada'];
    }

    public function logout() {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) setcookie(session_name(),'',time()-3600,'/');
        if (isset($_COOKIE['remember_token'])) setcookie('remember_token','',time()-3600,'/');
        session_destroy();
    }

    public function isLoggedIn() {
        return !empty($_SESSION['ok']) && $_SESSION['ok'] === true;
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) return null;
        return [
            'id'              => $_SESSION['uid'],
            'username'        => $_SESSION['uname'],
            'email'           => $_SESSION['email'],
            'nombre_completo' => $_SESSION['nombre'],
            'rol'             => $_SESSION['rol'],
            'avatar'          => $_SESSION['avatar'],
        ];
    }

    public function hasRole($role) {
        if (!$this->isLoggedIn()) return false;
        $n = ['autor'=>1,'editor'=>2,'admin'=>3];
        return ($n[$_SESSION['rol']] ?? 0) >= ($n[$role] ?? 99);
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            setFlashMessage('error','Debes iniciar sesión');
            redirect(SITE_URL.'/admin/login.php');
        }
    }

    public function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            setFlashMessage('error','Sin permisos');
            redirect(SITE_URL.'/admin/index.php');
        }
    }

    public function register($username, $email, $password, $nombre, $rol='autor') {
        $c = $this->conn->prepare("SELECT id FROM usuarios WHERE username=? OR email=?");
        $c->bind_param("ss",$username,$email); $c->execute(); $c->store_result();
        if ($c->num_rows>0) { $c->close(); return ['success'=>false,'message'=>'Usuario o email ya existe']; }
        $c->close();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $s = $this->conn->prepare("INSERT INTO usuarios (username,email,password,nombre_completo,rol) VALUES (?,?,?,?,?)");
        $s->bind_param("sssss",$username,$email,$hash,$nombre,$rol);
        $ok = $s->execute(); $s->close();
        return $ok ? ['success'=>true,'message'=>'Usuario creado'] : ['success'=>false,'message'=>'Error al crear'];
    }

    public function changePassword($userId, $current, $new) {
        $s = $this->conn->prepare("SELECT password FROM usuarios WHERE id=?");
        $s->bind_param("i",$userId); $s->execute();
        $row = $s->get_result()->fetch_assoc(); $s->close();
        if (!$row || !password_verify($current,$row['password']))
            return ['success'=>false,'message'=>'Contraseña actual incorrecta'];
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $u = $this->conn->prepare("UPDATE usuarios SET password=? WHERE id=?");
        $u->bind_param("si",$hash,$userId); $ok=$u->execute(); $u->close();
        return $ok ? ['success'=>true,'message'=>'Contraseña actualizada'] : ['success'=>false,'message'=>'Error'];
    }

    public function updateProfile($userId, $data) {
        $fields=[]; $types=''; $vals=[];
        foreach (['nombre_completo','email','avatar'] as $f) {
            if (isset($data[$f])) { $fields[]="$f=?"; $types.='s'; $vals[]=$data[$f]; }
        }
        if (!$fields) return ['success'=>false,'message'=>'Nada que actualizar'];
        $types.='i'; $vals[]=$userId;
        $s = $this->conn->prepare("UPDATE usuarios SET ".implode(',',$fields)." WHERE id=?");
        $s->bind_param($types,...$vals); $ok=$s->execute(); $s->close();
        foreach (['nombre_completo'=>'nombre','email'=>'email','avatar'=>'avatar'] as $f=>$k)
            if (isset($data[$f])) $_SESSION[$k]=$data[$f];
        return $ok ? ['success'=>true,'message'=>'Perfil actualizado'] : ['success'=>false,'message'=>'Error'];
    }
}

$auth = new Auth($conn);