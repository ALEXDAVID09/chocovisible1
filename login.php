<?php
session_start();

// Configuración de la base de datos
$host = 'localhost:3309';
$dbname = 'codechoco_denuncias';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Función para registrar actividades de auditoría
function registrarActividad($pdo, $admin_id, $accion, $descripcion, $tabla_afectada = null, $registro_id = null) {
    try {
        $ip_address = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : 
                     (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
        
        $query = "INSERT INTO admin_actividades (admin_id, accion, descripcion, tabla_afectada, registro_id, ip_address, fecha) 
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$admin_id, $accion, $descripcion, $tabla_afectada, $registro_id, $ip_address]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Error al registrar actividad: " . $e->getMessage());
        return false;
    }
}

// Configuración de seguridad
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
    header('Location: admin.php');
    exit();
}

// Configuración de intentos de login
$max_attempts = 5;
$lockout_time = 300; // 5 minutos

// Inicializar contador de intentos si no existe
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

$error_message = '';
$success_message = '';
$is_locked_out = false;
$show_register = false;

// Verificar si el usuario está bloqueado temporalmente
if ($_SESSION['login_attempts'] >= $max_attempts) {
    $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
    
    if ($time_since_last_attempt < $lockout_time) {
        $remaining_time = $lockout_time - $time_since_last_attempt;
        $error_message = "Demasiados intentos fallidos. Por favor espere ".ceil($remaining_time/60)." minutos antes de intentar nuevamente.";
        $is_locked_out = true;
    } else {
        $_SESSION['login_attempts'] = 0;
    }
}

// Función para actualizar intentos de login en BD
function updateLoginAttempts($pdo, $username, $success = false) {
    try {
        if ($success) {
            $stmt = $pdo->prepare("UPDATE administradores SET intentos_login = 0, bloqueado_hasta = NULL, ultimo_acceso = NOW() WHERE username = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE administradores SET intentos_login = intentos_login + 1, bloqueado_hasta = IF(intentos_login >= 4, DATE_ADD(NOW(), INTERVAL 5 MINUTE), NULL) WHERE username = ?");
        }
        $stmt->execute([$username]);
    } catch(PDOException $e) {
        error_log("Error updating login attempts: " . $e->getMessage());
    }
}

// Procesar registro de nuevo administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register']) && !$is_locked_out) {
    $reg_username = trim($_POST['reg_username']);
    $reg_email = trim($_POST['reg_email']);
    $reg_password = $_POST['reg_password'];
    $reg_confirm_password = $_POST['reg_confirm_password'];
    $reg_nombre_completo = trim($_POST['reg_nombre_completo']);
    $reg_telefono = trim($_POST['reg_telefono']);
    
    if (empty($reg_username) || empty($reg_email) || empty($reg_password) || empty($reg_nombre_completo)) {
        $error_message = 'Por favor complete todos los campos obligatorios.';
    } elseif ($reg_password !== $reg_confirm_password) {
        $error_message = 'Las contraseñas no coinciden.';
    } elseif (strlen($reg_password) < 8) {
        $error_message = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM administradores WHERE username = ? OR email = ?");
            $stmt->execute([$reg_username, $reg_email]);
            
            if ($stmt->rowCount() > 0) {
                $error_message = 'El usuario o email ya existe.';
            } else {
                $password_hash = password_hash($reg_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO administradores (username, email, password_hash, nombre_completo, telefono, rol, estado) VALUES (?, ?, ?, ?, ?, 'Admin', 'Activo')");
                $stmt->execute([$reg_username, $reg_email, $password_hash, $reg_nombre_completo, $reg_telefono]);
                
                $nuevo_admin_id = $pdo->lastInsertId();
                
                registrarActividad(
                    $pdo,
                    $nuevo_admin_id,
                    'Registro de administrador',
                    "Se registró un nuevo administrador: $reg_nombre_completo (@$reg_username)",
                    'administradores',
                    $nuevo_admin_id
                );
                
                $success_message = 'Administrador registrado exitosamente. Ahora puede iniciar sesión.';
                $show_register = false;
            }
        } catch(PDOException $e) {
            $error_message = 'Error al registrar el administrador. Intente nuevamente.';
            error_log("Registration error: " . $e->getMessage());
        }
    }
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']) && !$is_locked_out) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = 'Por favor complete todos los campos.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, nombre_completo, rol, estado, intentos_login, bloqueado_hasta FROM administradores WHERE username = ? AND estado = 'Activo'");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin) {
                if ($admin['bloqueado_hasta'] && strtotime($admin['bloqueado_hasta']) > time()) {
                    $error_message = 'Cuenta temporalmente bloqueada. Intente más tarde.';
                    
                    registrarActividad(
                        $pdo,
                        $admin['id'],
                        'Login bloqueado',
                        "Intento de inicio de sesión mientras la cuenta está bloqueada. Usuario: $username",
                        'administradores',
                        $admin['id']
                    );
                    
                } elseif (password_verify($password, $admin['password_hash'])) {
                    $_SESSION['admin_loggedin'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_nombre'] = $admin['nombre_completo'];
                    $_SESSION['admin_rol'] = $admin['rol'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['login_attempts'] = 0;
                    
                    updateLoginAttempts($pdo, $username, true);
                    
                    registrarActividad(
                        $pdo,
                        $admin['id'],
                        'Login exitoso',
                        "Inicio de sesión exitoso en el panel de administración. Usuario: {$admin['nombre_completo']} (@{$admin['username']})",
                        'administradores',
                        $admin['id']
                    );
                    
                    session_regenerate_id(true);
                    
                    header('Location: admin.php');
                    exit();
                } else {
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_attempt_time'] = time();
                    
                    updateLoginAttempts($pdo, $username, false);
                    
                    registrarActividad(
                        $pdo,
                        $admin['id'],
                        'Login fallido',
                        "Intento de inicio de sesión con contraseña incorrecta. Usuario: $username. Intentos: " . $_SESSION['login_attempts'],
                        'administradores',
                        $admin['id']
                    );
                    
                    $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
                    
                    if ($remaining_attempts > 0) {
                        $error_message = 'Credenciales incorrectas. Le quedan '.$remaining_attempts.' intentos.';
                    } else {
                        $error_message = "Demasiados intentos fallidos. Por favor espere ".ceil($lockout_time/60)." minutos antes de intentar nuevamente.";
                    }
                }
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                
                $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
                
                if ($remaining_attempts > 0) {
                    $error_message = 'Credenciales incorrectas. Le quedan '.$remaining_attempts.' intentos.';
                } else {
                    $error_message = "Demasiados intentos fallidos. Por favor espere ".ceil($lockout_time/60)." minutos antes de intentar nuevamente.";
                }
            }
        } catch(PDOException $e) {
            $error_message = 'Error de conexión. Intente nuevamente.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Toggle para mostrar formulario de registro
if (isset($_GET['register'])) {
    $show_register = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChocoVisible — Panel de Administración</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/chocovisibleee.png">
    <style>
        :root {
            --cv-green:        #1E6B3C;
            --cv-green-mid:    #2D8A52;
            --cv-green-light:  #3DAD66;
            --cv-blue:         #0D3B6E;
            --cv-blue-mid:     #1557A0;
            --cv-blue-light:   #1E7FC5;
            --cv-white:        #FFFFFF;
            --cv-off-white:    #F5F7FA;
            --cv-border:       #E2E8F0;
            --cv-text:         #1A2332;
            --cv-text-muted:   #64748B;
            --cv-error:        #DC2626;
            --cv-error-bg:     #FEF2F2;
            --cv-success:      #059669;
            --cv-success-bg:   #ECFDF5;
            --cv-shadow-sm:    0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
            --cv-shadow-lg:    0 20px 60px rgba(13,59,110,.18), 0 8px 24px rgba(0,0,0,.08);
            --cv-radius:       14px;
            --cv-radius-sm:    8px;
        }

        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background-color: #0a1628;
            background-image:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(30,107,60,.55) 0%, transparent 60%),
                radial-gradient(ellipse 70% 50% at 80% 90%, rgba(13,59,110,.65) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 60% 30%, rgba(21,87,160,.3) 0%, transparent 50%);
        }

        /* ── Partículas de fondo ── */
        .bg-particles {
            position: fixed; inset: 0; pointer-events: none; overflow: hidden; z-index: 0;
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: .12;
            animation: float linear infinite;
        }
        @keyframes float {
            0%   { transform: translateY(110vh) rotate(0deg); opacity: 0; }
            10%  { opacity: .15; }
            90%  { opacity: .12; }
            100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
        }

        /* ── Tarjeta principal ── */
        .login-card {
            position: relative; z-index: 1;
            background: var(--cv-white);
            border-radius: 20px;
            box-shadow: var(--cv-shadow-lg);
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.12);
        }

        /* ── Cabecera ── */
        .login-header {
            padding: 44px 36px 36px;
            text-align: center;
            background: linear-gradient(160deg, var(--cv-blue) 0%, var(--cv-blue-mid) 40%, var(--cv-green) 100%);
            position: relative;
            overflow: hidden;
        }
        .login-header::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 28px;
            background: var(--cv-white);
            clip-path: ellipse(55% 100% at 50% 100%);
        }

        .logo-wrap {
            position: relative; z-index: 2;
            margin-bottom: 18px;
        }
        .logo-wrap img {
            width: 96px; height: 96px;
            object-fit: contain;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
            padding: 10px;
            border: 2.5px solid rgba(255,255,255,.25);
            box-shadow: 0 4px 20px rgba(0,0,0,.2);
        }

        .header-badge {
            position: relative; z-index: 2;
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.22);
            color: rgba(255,255,255,.92);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px; font-weight: 500;
            letter-spacing: .3px;
            margin-bottom: 14px;
            backdrop-filter: blur(4px);
        }

        .login-header h1 {
            position: relative; z-index: 2;
            font-size: 30px; font-weight: 700;
            color: var(--cv-white);
            letter-spacing: -.5px;
            margin-bottom: 6px;
        }
        .login-header h1 span { color: #6EE7A0; }

        .login-header p {
            position: relative; z-index: 2;
            font-size: 13.5px; font-weight: 400;
            color: rgba(255,255,255,.75);
            margin-bottom: 10px;
        }

        /* ── Cuerpo ── */
        .login-body { padding: 40px 36px 36px; }

        /* ── Alertas ── */
        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 13px 16px;
            border-radius: var(--cv-radius-sm);
            margin-bottom: 22px;
            font-size: 13.5px; line-height: 1.5;
            border: none;
            animation: fadeDown .3s ease;
        }
        @keyframes fadeDown {
            from { opacity:0; transform: translateY(-6px); }
            to   { opacity:1; transform: translateY(0); }
        }
        .alert-danger  { background: var(--cv-error-bg);   color: var(--cv-error); }
        .alert-success { background: var(--cv-success-bg); color: var(--cv-success); }
        .alert i { margin-top: 1px; flex-shrink: 0; }

        /* ── Formulario ── */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: flex; align-items: center; gap: 6px;
            margin-bottom: 7px;
            font-size: 13px; font-weight: 600;
            color: var(--cv-text);
        }
        .form-label i { color: var(--cv-green-mid); font-size: 12px; }

        .input-wrapper { position: relative; }

        .form-control {
            width: 100%;
            padding: 13px 16px 13px 44px;
            border: 1.5px solid var(--cv-border);
            border-radius: var(--cv-radius-sm);
            font-size: 15px; font-family: 'Inter', sans-serif;
            background: var(--cv-off-white);
            color: var(--cv-text);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .form-control::placeholder { color: #A0AEC0; }
        .form-control:hover { border-color: #B0C4DE; background: #fff; }
        .form-control:focus {
            outline: none;
            border-color: var(--cv-green-mid);
            box-shadow: 0 0 0 3.5px rgba(45,138,82,.13);
            background: #fff;
        }
        .form-control.error { border-color: var(--cv-error); }
        .form-control.error:focus { box-shadow: 0 0 0 3.5px rgba(220,38,38,.12); }
        .form-control.success { border-color: var(--cv-success); }

        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--cv-text-muted); font-size: 15px; pointer-events: none;
            transition: color .2s;
        }
        .form-control:focus ~ .input-icon { color: var(--cv-green-mid); }

        .form-text { font-size: 11.5px; color: var(--cv-text-muted); margin-top: 5px; }

        /* Botón mostrar contraseña */
        .toggle-password {
            position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--cv-text-muted); font-size: 14px; padding: 4px;
            transition: color .2s;
        }
        .toggle-password:hover { color: var(--cv-green-mid); }
        .password-field { padding-right: 44px !important; }

        /* ── Botones ── */
        .btn-primary-cv {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--cv-green) 0%, var(--cv-green-mid) 50%, var(--cv-blue-mid) 100%);
            color: #fff;
            border: none;
            border-radius: var(--cv-radius-sm);
            font-size: 15px; font-weight: 600;
            cursor: pointer;
            transition: opacity .2s, transform .15s, box-shadow .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 14px;
            letter-spacing: .2px;
            box-shadow: 0 4px 14px rgba(30,107,60,.3);
        }
        .btn-primary-cv:hover:not(:disabled) {
            opacity: .93;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30,107,60,.38);
        }
        .btn-primary-cv:active:not(:disabled) { transform: translateY(0); }
        .btn-primary-cv:disabled { opacity: .55; cursor: not-allowed; box-shadow: none; }

        /* ── Toggle link ── */
        .toggle-link {
            text-align: center;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--cv-border);
        }
        .toggle-link a {
            color: var(--cv-green-mid);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            display: inline-flex; align-items: center; gap: 5px;
            transition: color .2s;
        }
        .toggle-link a:hover { color: var(--cv-green); text-decoration: underline; }

        /* ── Back link ── */
        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            padding: 14px 36px;
            background: var(--cv-off-white);
            color: var(--cv-text-muted);
            text-decoration: none;
            font-size: 13px; font-weight: 500;
            border-top: 1px solid var(--cv-border);
            transition: background .2s, color .2s;
        }
        .back-link:hover { background: #EDF2F7; color: var(--cv-blue); }
        .back-link i { font-size: 12px; }

        /* ── Divider ── */
        .form-divider {
            display: flex; align-items: center; gap: 10px;
            margin: 22px 0;
            font-size: 12px; color: var(--cv-text-muted);
        }
        .form-divider::before, .form-divider::after {
            content: ''; flex: 1;
            height: 1px; background: var(--cv-border);
        }

        /* ── Grid dos columnas ── */
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        /* ── Animaciones ── */
        .slide-in { animation: slideIn .3s cubic-bezier(.25,.46,.45,.94); }
        @keyframes slideIn {
            from { opacity:0; transform: translateX(16px); }
            to   { opacity:1; transform: translateX(0); }
        }

        /* ── Seguridad badge ── */
        .security-info {
            display: flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, #EFF6FF, #F0FDF4);
            border: 1px solid #BFDBFE;
            border-radius: var(--cv-radius-sm);
            padding: 10px 14px;
            margin-bottom: 22px;
            font-size: 12px; color: #1E40AF;
        }
        .security-info i { color: #2D8A52; flex-shrink: 0; }

        /* ── Indicador de fortaleza de contraseña ── */
        .strength-bar { display: flex; gap: 4px; margin-top: 6px; }
        .strength-bar span {
            flex: 1; height: 3px; border-radius: 3px;
            background: var(--cv-border); transition: background .3s;
        }
        .strength-bar.weak span:nth-child(1) { background: var(--cv-error); }
        .strength-bar.fair span:nth-child(-n+2) { background: #F59E0B; }
        .strength-bar.good span:nth-child(-n+3) { background: #3B82F6; }
        .strength-bar.strong span { background: var(--cv-success); }

        /* ── Footer de tarjeta ── */
        .card-footer-info {
            text-align: center;
            font-size: 11px; color: var(--cv-text-muted);
            padding: 10px 36px 16px;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .login-header { padding: 36px 24px 32px; }
            .login-body   { padding: 32px 24px 28px; }
            .back-link    { padding: 14px 24px; }
            .row-2        { grid-template-columns: 1fr; }
            .login-header h1 { font-size: 26px; }
        }
    </style>
</head>
<body>

    <!-- Partículas de fondo -->
    <div class="bg-particles" id="particles"></div>

    <div class="login-card">

        <!-- CABECERA -->
        <div class="login-header">
            <div class="logo-wrap">
                <img src="assets/images/chocovisibleee.png" alt="ChocoVisible Logo">
            </div>
            <div class="header-badge">
                <i class="fas fa-map-marker-alt"></i>
                Quibdó, Chocó · Colombia
            </div>
            <h1>Choco<span>Visible</span></h1>
            <p>Panel de Administración · Denuncia Ciudadana</p>
        </div>

        <!-- CUERPO -->
        <div class="login-body">

            <!-- Alertas -->
            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <?php if (!$show_register): ?>
                <!-- ══ FORMULARIO DE LOGIN ══ -->
                <div class="form-container slide-in">

                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <span>Conexión segura · Acceso exclusivo para administradores autorizados</span>
                    </div>

                    <form method="POST" action="" autocomplete="on" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="form-group">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Usuario
                            </label>
                            <div class="input-wrapper">
                                <input type="text"
                                       class="form-control"
                                       id="username"
                                       name="username"
                                       placeholder="Ingrese su usuario"
                                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '' ?>"
                                       autocomplete="username"
                                       required
                                       <?= $is_locked_out ? 'disabled' : '' ?>>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Contraseña
                            </label>
                            <div class="input-wrapper">
                                <input type="password"
                                       class="form-control password-field"
                                       id="password"
                                       name="password"
                                       placeholder="Ingrese su contraseña"
                                       autocomplete="current-password"
                                       required
                                       <?= $is_locked_out ? 'disabled' : '' ?>>
                                <i class="fas fa-lock input-icon"></i>
                                <button type="button" class="toggle-password" id="togglePwd" aria-label="Mostrar contraseña">
                                    <i class="fas fa-eye" id="togglePwdIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn-primary-cv" <?= $is_locked_out ? 'disabled' : '' ?>>
                            <i class="fas fa-sign-in-alt"></i>
                            Iniciar Sesión
                        </button>
                    </form>

                    <div class="toggle-link">
                        <a href="?register=1">
                            <i class="fas fa-user-plus"></i>
                            ¿No tienes cuenta? Regístrate aquí
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- ══ FORMULARIO DE REGISTRO ══ -->
                <div class="form-container slide-in">
                    <form method="POST" action="" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="form-group">
                            <label for="reg_nombre_completo" class="form-label">
                                <i class="fas fa-id-card"></i> Nombre completo *
                            </label>
                            <div class="input-wrapper">
                                <input type="text"
                                       class="form-control"
                                       id="reg_nombre_completo"
                                       name="reg_nombre_completo"
                                       placeholder="Ingrese su nombre completo"
                                       value="<?= isset($_POST['reg_nombre_completo']) ? htmlspecialchars($_POST['reg_nombre_completo'], ENT_QUOTES, 'UTF-8') : '' ?>"
                                       required>
                                <i class="fas fa-id-card input-icon"></i>
                            </div>
                        </div>

                        <div class="row-2">
                            <div class="form-group">
                                <label for="reg_username" class="form-label">
                                    <i class="fas fa-user"></i> Usuario *
                                </label>
                                <div class="input-wrapper">
                                    <input type="text"
                                           class="form-control"
                                           id="reg_username"
                                           name="reg_username"
                                           placeholder="usuario"
                                           value="<?= isset($_POST['reg_username']) ? htmlspecialchars($_POST['reg_username'], ENT_QUOTES, 'UTF-8') : '' ?>"
                                           required>
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reg_telefono" class="form-label">
                                    <i class="fas fa-phone"></i> Teléfono
                                </label>
                                <div class="input-wrapper">
                                    <input type="tel"
                                           class="form-control"
                                           id="reg_telefono"
                                           name="reg_telefono"
                                           placeholder="+57 300 000 0000"
                                           value="<?= isset($_POST['reg_telefono']) ? htmlspecialchars($_POST['reg_telefono'], ENT_QUOTES, 'UTF-8') : '' ?>">
                                    <i class="fas fa-phone input-icon"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reg_email" class="form-label">
                                <i class="fas fa-envelope"></i> Correo electrónico *
                            </label>
                            <div class="input-wrapper">
                                <input type="email"
                                       class="form-control"
                                       id="reg_email"
                                       name="reg_email"
                                       placeholder="correo@ejemplo.com"
                                       value="<?= isset($_POST['reg_email']) ? htmlspecialchars($_POST['reg_email'], ENT_QUOTES, 'UTF-8') : '' ?>"
                                       required>
                                <i class="fas fa-envelope input-icon"></i>
                            </div>
                        </div>

                        <div class="row-2">
                            <div class="form-group">
                                <label for="reg_password" class="form-label">
                                    <i class="fas fa-lock"></i> Contraseña *
                                </label>
                                <div class="input-wrapper">
                                    <input type="password"
                                           class="form-control password-field"
                                           id="reg_password"
                                           name="reg_password"
                                           placeholder="••••••••"
                                           required>
                                    <i class="fas fa-lock input-icon"></i>
                                    <button type="button" class="toggle-password" id="toggleRegPwd">
                                        <i class="fas fa-eye" id="toggleRegPwdIcon"></i>
                                    </button>
                                </div>
                                <div class="strength-bar" id="strengthBar">
                                    <span></span><span></span><span></span><span></span>
                                </div>
                                <div class="form-text">Mínimo 8 caracteres</div>
                            </div>
                            <div class="form-group">
                                <label for="reg_confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirmar *
                                </label>
                                <div class="input-wrapper">
                                    <input type="password"
                                           class="form-control password-field"
                                           id="reg_confirm_password"
                                           name="reg_confirm_password"
                                           placeholder="••••••••"
                                           required>
                                    <i class="fas fa-lock input-icon"></i>
                                    <button type="button" class="toggle-password" id="toggleConf">
                                        <i class="fas fa-eye" id="toggleConfIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn-primary-cv">
                            <i class="fas fa-user-plus"></i>
                            Registrar Administrador
                        </button>
                    </form>

                    <div class="toggle-link">
                        <a href="login.php">
                            <i class="fas fa-sign-in-alt"></i>
                            ¿Ya tienes cuenta? Inicia sesión
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- VOLVER AL SITIO -->
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Volver al sitio principal
        </a>

        <div class="card-footer-info">
            © <?= date('Y') ?> ChocoVisible · Denuncia Ciudadana · Quibdó, Chocó
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Partículas de fondo ──────────────────────────────
        const container = document.getElementById('particles');
        const colors = ['#1E6B3C','#2D8A52','#0D3B6E','#1557A0','#6EE7A0'];
        for (let i = 0; i < 18; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 40 + 10;
            p.style.cssText = `
                width:${size}px; height:${size}px;
                left:${Math.random()*100}%;
                background:${colors[Math.floor(Math.random()*colors.length)]};
                animation-duration:${Math.random()*20+12}s;
                animation-delay:${Math.random()*12}s;
            `;
            container.appendChild(p);
        }

        // ── Toggle visibilidad de contraseñas ────────────────
        function bindToggle(btnId, iconId, inputId) {
            const btn  = document.getElementById(btnId);
            const icon = document.getElementById(iconId);
            const inp  = document.getElementById(inputId);
            if (!btn) return;
            btn.addEventListener('click', () => {
                const show = inp.type === 'password';
                inp.type = show ? 'text' : 'password';
                icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        }
        bindToggle('togglePwd',    'togglePwdIcon',    'password');
        bindToggle('toggleRegPwd', 'toggleRegPwdIcon', 'reg_password');
        bindToggle('toggleConf',   'toggleConfIcon',   'reg_confirm_password');

        // ── Fortaleza de contraseña ───────────────────────────
        const regPwd  = document.getElementById('reg_password');
        const confPwd = document.getElementById('reg_confirm_password');
        const bar     = document.getElementById('strengthBar');

        function passwordStrength(pwd) {
            let score = 0;
            if (pwd.length >= 8)  score++;
            if (pwd.length >= 12) score++;
            if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) score++;
            if (/[0-9]/.test(pwd)) score++;
            if (/[^A-Za-z0-9]/.test(pwd)) score++;
            if (score <= 1) return 'weak';
            if (score === 2) return 'fair';
            if (score === 3) return 'good';
            return 'strong';
        }

        if (regPwd && bar) {
            regPwd.addEventListener('input', () => {
                bar.className = 'strength-bar ' + (regPwd.value ? passwordStrength(regPwd.value) : '');
            });
        }

        // ── Validación visual de confirmación ────────────────
        if (regPwd && confPwd) {
            function validateConfirm() {
                if (!confPwd.value) { confPwd.classList.remove('error','success'); return; }
                const match = regPwd.value === confPwd.value;
                confPwd.classList.toggle('error',   !match);
                confPwd.classList.toggle('success',  match);
            }
            regPwd.addEventListener('input',  validateConfirm);
            confPwd.addEventListener('input', validateConfirm);
        }

        // ── Longitud mínima contraseña ────────────────────────
        if (regPwd) {
            regPwd.addEventListener('input', () => {
                regPwd.classList.toggle('error', regPwd.value.length > 0 && regPwd.value.length < 8);
            });
        }
    });
    </script>
</body>
</html>