<?php
// ── TODO EL PHP VA AQUÍ ARRIBA, ANTES DE CUALQUIER HTML ──
require_once '../config/auth.php';

if ($auth->isLoggedIn()) redirect(SITE_URL . '/admin/index.php');

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = sanitize($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $rem  = isset($_POST['remember']);
    if (!$user || !$pass) {
        $error = 'Por favor completa todos los campos';
    } else {
        $r = $auth->login($user, $pass, $rem);
        if ($r['success']) redirect(SITE_URL . '/admin/index.php');
        else $error = $r['message'];
    }
}
$flash = getFlashMessage();
if ($flash && !$error) {
    $flash['type']==='error' ? $error=$flash['message'] : $success=$flash['message'];
}
$lastUser = sanitize($_POST['username'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Terror Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Crimson+Text:wght@400;600;700&family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body{display:flex;align-items:center;justify-content:center;min-height:100vh;}
        .login-box{width:100%;max-width:460px;padding:3rem;background:rgba(10,10,10,.95);border:2px solid var(--blood-red);box-shadow:0 0 50px rgba(139,0,0,.4);position:relative;z-index:10;}
        .login-logo{text-align:center;margin-bottom:2rem;}
        .login-logo h1{font-family:'Creepster',cursive;font-size:2.2rem;color:var(--blood-red);margin-bottom:.3rem;}
        .login-logo p{color:var(--pale-green);font-family:'Rubik',sans-serif;font-size:.8rem;text-transform:uppercase;letter-spacing:.2rem;}
        .form-group{margin-bottom:1.4rem;}
        .form-group label{display:block;margin-bottom:.4rem;color:#bbb;font-family:'Rubik',sans-serif;font-size:.85rem;font-weight:500;}
        .form-group input{width:100%;padding:.85rem 1rem;background:#050505;border:1px solid #333;color:var(--ghost-white);font-family:'Rubik',sans-serif;font-size:.95rem;box-sizing:border-box;transition:border-color .2s;}
        .form-group input:focus{outline:none;border-color:var(--blood-red);box-shadow:0 0 8px rgba(139,0,0,.3);}
        .remember{display:flex;align-items:center;gap:.5rem;margin-bottom:1.5rem;color:#888;font-family:'Rubik',sans-serif;font-size:.85rem;cursor:pointer;}
        .remember input{width:auto;}
        .btn-login{width:100%;padding:.9rem;background:var(--blood-red);color:var(--ghost-white);border:none;font-family:'Rubik',sans-serif;font-size:1rem;font-weight:700;text-transform:uppercase;letter-spacing:.1rem;cursor:pointer;transition:background .2s;}
        .btn-login:hover{background:var(--accent-crimson);}
        .alert{padding:.85rem 1rem;margin-bottom:1.4rem;border-left:4px solid;font-family:'Rubik',sans-serif;font-size:.88rem;}
        .alert-error{border-color:var(--accent-crimson);background:rgba(220,20,60,.08);color:#ffbbbb;}
        .alert-success{border-color:#0c6;background:rgba(0,200,100,.08);color:#bbffcc;}
        .login-links{display:flex;justify-content:space-between;margin-top:1.5rem;font-family:'Rubik',sans-serif;font-size:.82rem;}
        .login-links a{color:#555;text-decoration:none;} .login-links a:hover{color:var(--pale-green);}
        .hint{margin-top:1.8rem;padding-top:1.2rem;border-top:1px solid #1a1a1a;text-align:center;color:#444;font-family:'Rubik',sans-serif;font-size:.78rem;}
        .hint strong{color:#666;} .hint .warn{color:#8B0000;margin-top:.3rem;display:block;}
    </style>
</head>
<body>
<div class="grain"></div>
<div class="login-box">
    <div class="login-logo">
        <h1>Panel Admin</h1>
        <p>Terror Digital</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Usuario o Email</label>
            <input type="text" name="username" value="<?= htmlspecialchars($lastUser) ?>" required autofocus autocomplete="username">
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required autocomplete="current-password">
        </div>
        <label class="remember">
            <input type="checkbox" name="remember"> Recordar sesión (30 días)
        </label>
        <button type="submit" class="btn-login">Iniciar Sesión</button>
    </form>

    <div class="login-links">
        <a href="../index.html">← Volver al sitio</a>
        <a href="../reset_admin.php">¿Olvidaste la contraseña?</a>
    </div>
    <div class="hint">
        <p>Usuario: <strong>admin</strong> &nbsp;|&nbsp; Contraseña: <strong>admin123</strong></p>
        <span class="warn">¡Cambia la contraseña después del primer acceso!</span>
    </div>
</div>
</body>
</html>