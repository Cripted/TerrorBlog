<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel de Administración | TERROR DIGITAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Nosifer&family=Crimson+Text:wght@400;600;700&family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .login-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 3rem;
            background: rgba(10, 10, 10, 0.9);
            border: 2px solid var(--blood-red);
            box-shadow: 0 0 50px rgba(139, 0, 0, 0.5);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            font-family: 'Creepster', cursive;
            font-size: 2.5rem;
            color: var(--blood-red);
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: var(--pale-green);
            text-transform: uppercase;
            letter-spacing: 0.2rem;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--ghost-white);
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem;
            background: var(--deep-black);
            border: 1px solid var(--fog-gray);
            color: var(--ghost-white);
            font-family: 'Crimson Text', serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--blood-red);
            box-shadow: 0 0 10px rgba(139, 0, 0, 0.3);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .remember-me input[type="checkbox"] {
            width: auto;
        }
        
        .remember-me label {
            margin: 0;
            font-weight: normal;
            cursor: pointer;
        }
        
        .btn-login {
            width: 100%;
            background: var(--blood-red);
            color: var(--ghost-white);
            padding: 1rem;
            border: none;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Rubik', sans-serif;
            font-weight: 600;
        }
        
        .btn-login:hover {
            background: var(--accent-crimson);
            box-shadow: 0 0 20px rgba(220, 20, 60, 0.5);
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        .back-link a {
            color: var(--pale-green);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: var(--accent-crimson);
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .alert-error {
            border-color: var(--accent-crimson);
            color: #ffcccc;
        }
        
        .alert-success {
            border-color: var(--pale-green);
            color: #ccffcc;
        }
    </style>
</head>
<body>
    <div class="grain"></div>
    
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1>Panel Admin</h1>
                <p>Terror Digital</p>
            </div>
            
            <?php
            require_once '../config/auth.php';
            
            // Si ya está logueado, redirigir al panel
            if ($auth->isLoggedIn()) {
                redirect(SITE_URL . '/admin/index.php');
            }
            
            // Procesar formulario
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = sanitize($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                $remember = isset($_POST['remember']);
                
                if (empty($username) || empty($password)) {
                    echo '<div class="alert alert-error">Por favor completa todos los campos</div>';
                } else {
                    $result = $auth->login($username, $password, $remember);
                    
                    if ($result['success']) {
                        redirect(SITE_URL . '/admin/index.php');
                    } else {
                        echo '<div class="alert alert-error">' . $result['message'] . '</div>';
                    }
                }
            }
            
            // Mostrar mensaje flash si existe
            $flash = getFlashMessage();
            if ($flash) {
                $alertClass = $flash['type'] === 'error' ? 'alert-error' : 'alert-success';
                echo '<div class="alert ' . $alertClass . '">' . $flash['message'] . '</div>';
            }
            ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Usuario o Email:</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Recordar sesión (30 días)</label>
                </div>
                
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
            
            <div class="back-link">
                <a href="../index.html">← Volver al sitio principal</a>
            </div>
            
            <div class="back-link" style="margin-top: 1rem; font-size: 0.85rem; color: #666;">
                <p>Usuario por defecto: <strong>admin</strong></p>
                <p>Contraseña por defecto: <strong>admin123</strong></p>
                <p style="color: var(--accent-crimson); margin-top: 0.5rem;">¡Cambia la contraseña después del primer login!</p>
            </div>
        </div>
    </div>
</body>
</html>