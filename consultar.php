<?php
require_once 'config.php';

$denuncia      = null;
$actualizaciones = [];
$fotos         = [];
$error         = null;

// Procesar búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['codigo'])) {
    try {
        $pdo    = conectarDB();
        $codigo = limpiarInput($_POST['codigo']);

        $stmt = $pdo->prepare("SELECT * FROM denuncias WHERE codigo_seguimiento = ?");
        $stmt->execute([$codigo]);
        $denuncia = $stmt->fetch();

        if ($denuncia) {
            $stmt_act = $pdo->prepare("SELECT * FROM denuncia_actualizaciones WHERE denuncia_id = ? ORDER BY fecha DESC");
            $stmt_act->execute([$denuncia['id']]);
            $actualizaciones = $stmt_act->fetchAll();

            $stmt_fotos = $pdo->prepare("SELECT * FROM denuncia_fotos WHERE denuncia_id = ? ORDER BY fecha_subida");
            $stmt_fotos->execute([$denuncia['id']]);
            $fotos = $stmt_fotos->fetchAll();
        } else {
            $error = "No se encontró ninguna denuncia con ese código de seguimiento.";
        }
    } catch (Exception $e) {
        $error = "Error del sistema al procesar la consulta. Por favor, intente nuevamente.";
    }
}

function getEstadoColor($estado) {
    if (empty($estado)) $estado = 'pendiente';
    switch (strtolower($estado)) {
        case 'pendiente':  return 'pendiente';
        case 'en_proceso': return 'proceso';
        case 'resuelto':   return 'resuelto';
        case 'cerrado':    return 'cerrado';
        default:           return 'pendiente';
    }
}

function getEstadoIcono($estado) {
    if (empty($estado)) $estado = 'pendiente';
    switch (strtolower($estado)) {
        case 'pendiente':  return 'clock';
        case 'en_proceso': return 'gear';
        case 'resuelto':   return 'check-circle';
        case 'cerrado':    return 'times-circle';
        default:           return 'clock';
    }
}

function getEstadoTexto($estado) {
    if (empty($estado)) $estado = 'pendiente';
    switch (strtolower($estado)) {
        case 'pendiente':  return 'Pendiente';
        case 'en_proceso': return 'En Proceso';
        case 'resuelto':   return 'Resuelto';
        case 'cerrado':    return 'Cerrado';
        default:           return 'Pendiente';
    }
}

function formatearTipo($tipo) {
    if (empty($tipo)) return 'No especificado';
    $tipos = [
        'acoso'         => 'Acoso o Intimidación',
        'seguridad'     => 'Problema de Seguridad',
        'etico'         => 'Problema Ético',
        'discriminacion'=> 'Discriminación',
        'corrupcion'    => 'Corrupción',
        'laboral'       => 'Problema Laboral',
        'ambiental'     => 'Problema Ambiental',
        'servicios'     => 'Servicios Públicos',
        'otro'          => 'Otro',
    ];
    return $tipos[strtolower(trim($tipo))] ?? ucfirst($tipo);
}

function getTipoIcono($tipo) {
    if (empty($tipo)) return 'exclamation-circle';
    $iconos = [
        'acoso'         => 'user-times',
        'seguridad'     => 'shield-alt',
        'etico'         => 'balance-scale',
        'discriminacion'=> 'users',
        'corrupcion'    => 'hand-holding-usd',
        'laboral'       => 'briefcase',
        'ambiental'     => 'leaf',
        'servicios'     => 'water',
        'otro'          => 'question-circle',
    ];
    return $iconos[strtolower(trim($tipo))] ?? 'exclamation-circle';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ChocoVisible — Consulta el estado de tu denuncia ciudadana en tiempo real.">
    <title>Consultar Denuncia · ChocoVisible</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/chocovisibleee.png">
    <style>
        /* ══ TOKENS ══════════════════════════════════════════ */
        :root {
            --cv-green:       #1A6636;
            --cv-green-mid:   #248C4A;
            --cv-green-light: #32B060;
            --cv-blue:        #0C3460;
            --cv-blue-mid:    #134E9B;
            --cv-blue-light:  #1A73D6;
            --cv-gold:        #E8A020;
            --cv-gold-light:  #F5C842;
            --cv-white:       #FFFFFF;
            --cv-off:         #F6F8FA;
            --cv-border:      #E2E8F0;
            --cv-text:        #111827;
            --cv-text-2:      #374151;
            --cv-muted:       #6B7280;
            --cv-danger:      #DC2626;
            --cv-radius:      14px;
            --cv-radius-sm:   8px;
            --cv-shadow:      0 4px 24px rgba(0,0,0,.07);
            --cv-shadow-lg:   0 16px 48px rgba(12,52,96,.14);
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { font-family:'Inter',sans-serif; color:var(--cv-text); background:var(--cv-off); min-height:100vh; }

        /* ══ NAVBAR ══════════════════════════════════════════ */
        .cv-navbar {
            position:fixed; top:0; left:0; right:0; z-index:1000;
            background:rgba(255,255,255,.94);
            backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px);
            border-bottom:1px solid var(--cv-border);
        }
        .cv-navbar.scrolled { box-shadow:0 2px 20px rgba(0,0,0,.1); }
        .cv-navbar .container { display:flex; align-items:center; height:68px; gap:32px; }
        .nav-logo { display:flex; align-items:center; gap:10px; text-decoration:none; flex-shrink:0; }
        .nav-logo img { height:40px; width:40px; object-fit:contain; }
        .nav-logo-text { font-size:20px; font-weight:700; line-height:1; letter-spacing:-.4px; }
        .nav-logo-text .choco  { color:var(--cv-green); }
        .nav-logo-text .vis    { color:var(--cv-blue); }
        .nav-links { display:flex; align-items:center; gap:4px; margin-left:auto; list-style:none; }
        .nav-links .nav-link {
            font-size:14.5px; font-weight:500; color:var(--cv-text-2);
            padding:8px 14px; border-radius:var(--cv-radius-sm);
            text-decoration:none; transition:color .2s, background .2s; white-space:nowrap;
        }
        .nav-links .nav-link:hover  { color:var(--cv-green); background:rgba(26,102,54,.07); }
        .nav-links .nav-link.active { color:var(--cv-green); font-weight:600; }
        .nav-toggle {
            display:none; background:none; border:1.5px solid var(--cv-border);
            border-radius:8px; padding:7px 9px; cursor:pointer; margin-left:auto; flex-shrink:0;
            color:var(--cv-text-2); font-size:16px; transition:border-color .2s;
        }
        .nav-toggle:hover { border-color:var(--cv-green); color:var(--cv-green); }
        .nav-drawer {
            display:none; flex-direction:column; gap:4px;
            background:var(--cv-white); border-top:1px solid var(--cv-border); padding:16px 20px 20px;
        }
        .nav-drawer .nav-link {
            font-size:15px; font-weight:500; color:var(--cv-text-2);
            padding:11px 14px; border-radius:var(--cv-radius-sm);
            text-decoration:none; display:flex; align-items:center; gap:8px;
            transition:background .2s, color .2s;
        }
        .nav-drawer .nav-link:hover { background:var(--cv-off); color:var(--cv-green); }
        @media(max-width:991px){
            .nav-links { display:none; }
            .nav-toggle { display:flex; align-items:center; }
            .nav-drawer.open { display:flex; }
        }

        /* ══ HERO ════════════════════════════════════════════ */
        .page-hero {
            padding:100px 0 0;
            background:linear-gradient(150deg, var(--cv-blue) 0%, var(--cv-blue-mid) 40%, var(--cv-green) 100%);
            position:relative; overflow:hidden;
        }
        .page-hero::before {
            content:''; position:absolute; inset:0;
            background-image:radial-gradient(circle, rgba(255,255,255,.07) 1px, transparent 1px);
            background-size:28px 28px;
        }
        .hero-inner {
            position:relative; z-index:2;
            padding:44px 0 60px; text-align:center;
        }
        .hero-inner h1 {
            font-size:clamp(1.8rem,4vw,2.8rem); font-weight:800;
            color:#fff; letter-spacing:-.4px; margin-bottom:12px;
        }
        .hero-inner p {
            font-size:clamp(.95rem,2vw,1.1rem);
            color:rgba(255,255,255,.78); margin-bottom:32px;
        }

        /* ── Caja de búsqueda flotante ── */
        .search-card {
            background:var(--cv-white);
            border-radius:var(--cv-radius);
            padding:28px 32px;
            box-shadow:var(--cv-shadow-lg);
            max-width:680px; margin:0 auto;
        }
        .search-card label {
            font-size:13px; font-weight:600; color:var(--cv-text-2);
            display:flex; align-items:center; gap:6px; margin-bottom:10px;
        }
        .search-card label i { color:var(--cv-green-mid); }
        .search-group {
            display:flex; gap:10px; align-items:stretch;
        }
        .search-input-wrap { position:relative; flex:1; }
        .search-input-wrap i {
            position:absolute; left:14px; top:50%; transform:translateY(-50%);
            color:var(--cv-muted); font-size:15px; pointer-events:none;
        }
        .search-input {
            width:100%; padding:13px 14px 13px 42px;
            border:1.5px solid var(--cv-border); border-radius:var(--cv-radius-sm);
            font-size:15px; font-family:'Inter',sans-serif;
            background:var(--cv-off); color:var(--cv-text);
            letter-spacing:1px; font-weight:600;
            transition:border-color .2s, box-shadow .2s, background .2s;
        }
        .search-input:focus {
            outline:none; border-color:var(--cv-green-mid);
            box-shadow:0 0 0 3px rgba(36,140,74,.12); background:#fff;
        }
        .search-input::placeholder { font-weight:400; letter-spacing:0; color:#A0AEC0; }
        .btn-search {
            display:inline-flex; align-items:center; gap:8px;
            background:linear-gradient(135deg,var(--cv-green),var(--cv-blue-mid));
            color:#fff; border:none;
            padding:13px 24px; border-radius:var(--cv-radius-sm);
            font-size:14.5px; font-weight:700; cursor:pointer; white-space:nowrap;
            box-shadow:0 4px 14px rgba(26,102,54,.3);
            transition:opacity .2s, transform .15s;
        }
        .btn-search:hover { opacity:.88; transform:translateY(-1px); }
        .btn-search:disabled { opacity:.55; cursor:not-allowed; }
        .search-hint { font-size:12px; color:rgba(255,255,255,.55); margin-top:12px; }
        .search-hint a { color:var(--cv-gold-light); text-decoration:none; }

        .hero-wave { line-height:0; margin-bottom:-1px; position:relative; z-index:2; }
        .hero-wave svg { display:block; width:100%; }

        /* ══ CONTENIDO ═══════════════════════════════════════ */
        .page-body { padding:36px 0 64px; }

        /* ── Estado vacío ── */
        .empty-state {
            text-align:center; padding:56px 24px;
            background:var(--cv-white);
            border:1px solid var(--cv-border);
            border-radius:var(--cv-radius);
            box-shadow:var(--cv-shadow);
        }
        .empty-icon {
            width:80px; height:80px; border-radius:50%;
            background:linear-gradient(135deg,rgba(26,102,54,.1),rgba(19,78,155,.1));
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 20px; font-size:2rem; color:var(--cv-green-mid);
        }
        .empty-state h3 { font-size:20px; font-weight:700; color:var(--cv-text); margin-bottom:10px; }
        .empty-state p  { font-size:14.5px; color:var(--cv-muted); margin-bottom:28px; max-width:480px; margin-inline:auto; }

        .code-info-grid {
            display:grid; grid-template-columns:1fr 1fr; gap:12px;
            background:var(--cv-off); border:1px solid var(--cv-border);
            border-radius:var(--cv-radius-sm); padding:18px 20px;
            text-align:left; margin-bottom:28px;
        }
        .code-info-item { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--cv-text-2); }
        .code-info-item i { font-size:12px; width:16px; text-align:center; }
        .code-info-item .ok   { color:var(--cv-green-mid); }
        .code-info-item .info { color:var(--cv-blue-mid); }
        .code-info-item .warn { color:var(--cv-gold); }
        @media(max-width:480px){ .code-info-grid { grid-template-columns:1fr; } }

        /* ── Error ── */
        .error-card {
            background:var(--cv-white); border:1px solid var(--cv-border);
            border-left:4px solid var(--cv-danger);
            border-radius:var(--cv-radius); padding:32px;
            box-shadow:var(--cv-shadow); text-align:center;
        }
        .error-card .err-icon {
            width:64px; height:64px; border-radius:50%;
            background:#FEF2F2; display:flex; align-items:center; justify-content:center;
            margin:0 auto 16px; font-size:1.6rem; color:var(--cv-danger);
        }
        .error-card h4 { font-size:18px; font-weight:700; color:var(--cv-text); margin-bottom:8px; }
        .error-card p  { font-size:14px; color:var(--cv-muted); margin-bottom:20px; }
        .tips-box {
            background:var(--cv-off); border:1px solid var(--cv-border);
            border-radius:var(--cv-radius-sm); padding:16px 18px; text-align:left;
            margin-bottom:20px;
        }
        .tips-box h6 { font-size:13px; font-weight:700; color:var(--cv-text-2); margin-bottom:10px; }
        .tip-item { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--cv-text-2); margin-bottom:6px; }
        .tip-item i { color:var(--cv-green-mid); font-size:11px; }

        /* ── Resultado denuncia ── */
        .result-card {
            background:var(--cv-white); border:1px solid var(--cv-border);
            border-radius:var(--cv-radius); overflow:hidden; box-shadow:var(--cv-shadow);
            margin-bottom:20px; transition:box-shadow .25s;
        }
        .result-card:hover { box-shadow:var(--cv-shadow-lg); }

        .card-head {
            padding:18px 24px;
            background:linear-gradient(135deg,var(--cv-blue),var(--cv-blue-mid) 50%,var(--cv-green) 100%);
            display:flex; align-items:center; gap:10px;
        }
        .card-head h5, .card-head h4 {
            color:#fff; font-size:15px; font-weight:700; margin:0;
            display:flex; align-items:center; gap:8px;
        }
        .card-body-cv { padding:24px; }

        /* Info box dentro de tarjeta */
        .info-box {
            background:var(--cv-off); border:1px solid var(--cv-border);
            border-radius:var(--cv-radius-sm); padding:14px 16px;
            margin-bottom:14px;
        }
        .info-label {
            font-size:11.5px; font-weight:600; color:var(--cv-muted);
            text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;
            display:flex; align-items:center; gap:5px;
        }
        .info-value {
            font-size:15px; font-weight:600; color:var(--cv-text);
        }
        .info-text {
            font-size:14px; color:var(--cv-text-2); line-height:1.65;
        }

        /* Código de seguimiento */
        .tracking-code {
            background:linear-gradient(135deg,var(--cv-blue),var(--cv-green));
            color:#fff; padding:14px 18px;
            border-radius:var(--cv-radius-sm);
            font-family:'Courier New',monospace;
            font-size:1.15rem; font-weight:700; letter-spacing:2px;
            text-align:center; cursor:pointer;
            position:relative; overflow:hidden;
            transition:opacity .2s;
        }
        .tracking-code:hover { opacity:.9; }
        .tracking-code::after {
            content:''; position:absolute; top:0; left:-100%; width:100%; height:100%;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.15),transparent);
            animation:shine 2.5s infinite;
        }
        @keyframes shine { 0%{left:-100%} 60%{left:100%} 100%{left:100%} }
        .copy-hint { font-size:11px; color:rgba(255,255,255,.65); margin-top:5px; text-align:center; letter-spacing:0; font-family:'Inter',sans-serif; }

        /* Badges de estado */
        .estado-badge {
            display:inline-flex; align-items:center; gap:7px;
            padding:8px 16px; border-radius:50px;
            font-size:13.5px; font-weight:700; border:1.5px solid;
        }
        .estado-pendiente { background:#FFFBEB; color:#92400E; border-color:#FDE68A; }
        .estado-proceso   { background:#EFF6FF; color:#1E40AF; border-color:#BFDBFE; }
        .estado-resuelto  { background:#ECFDF5; color:#065F46; border-color:#6EE7B7; }
        .estado-cerrado   { background:#FEF2F2; color:#991B1B; border-color:#FECACA; }

        /* Evidencias */
        .foto-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(100px,1fr)); gap:10px; margin-top:12px; }
        .foto-thumb {
            aspect-ratio:1; border-radius:var(--cv-radius-sm);
            object-fit:cover; cursor:pointer; border:2px solid transparent;
            transition:transform .25s, border-color .25s, box-shadow .25s; width:100%;
        }
        .foto-thumb:hover { transform:scale(1.06); border-color:var(--cv-gold); box-shadow:var(--cv-shadow); }
        .file-thumb {
            aspect-ratio:1; border-radius:var(--cv-radius-sm);
            border:1.5px solid var(--cv-border); background:var(--cv-off);
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            font-size:1.4rem; color:var(--cv-muted); gap:4px; cursor:default;
        }
        .file-thumb span { font-size:10px; font-weight:700; color:var(--cv-muted); letter-spacing:.5px; }

        /* Timeline de actualizaciones */
        .cv-timeline { position:relative; padding-left:28px; }
        .cv-timeline::before {
            content:''; position:absolute; left:10px; top:0; bottom:0;
            width:2px;
            background:linear-gradient(to bottom,var(--cv-green),var(--cv-blue-mid));
            border-radius:2px;
        }
        .tl-item { position:relative; margin-bottom:20px; }
        .tl-item::before {
            content:''; position:absolute; left:-22px; top:14px;
            width:12px; height:12px; border-radius:50%;
            background:var(--cv-gold); border:2.5px solid #fff;
            box-shadow:0 0 0 2.5px var(--cv-green-mid); z-index:1;
        }
        .tl-card {
            background:var(--cv-white); border:1px solid var(--cv-border);
            border-left:3px solid var(--cv-gold);
            border-radius:var(--cv-radius-sm); padding:14px 16px;
            box-shadow:var(--cv-shadow);
        }
        .tl-card-head {
            display:flex; align-items:center; justify-content:space-between;
            margin-bottom:8px; flex-wrap:wrap; gap:6px;
        }
        .tl-responsible { font-size:13px; font-weight:700; color:var(--cv-green); display:flex; align-items:center; gap:6px; }
        .tl-date { font-size:12px; color:var(--cv-muted); display:flex; align-items:center; gap:4px; }
        .tl-desc { font-size:13.5px; color:var(--cv-text-2); line-height:1.6; }
        .tl-time { font-size:11.5px; color:var(--cv-muted); margin-top:6px; display:flex; align-items:center; gap:4px; }

        /* Sin actualizaciones */
        .no-updates { text-align:center; padding:28px 20px; color:var(--cv-muted); }
        .no-updates i { font-size:2rem; margin-bottom:10px; opacity:.5; }
        .no-updates p { font-size:13px; line-height:1.6; }

        /* Coordenadas */
        .coord-badge {
            display:inline-flex; align-items:center; gap:5px;
            background:var(--cv-off); border:1px solid var(--cv-border);
            padding:5px 12px; border-radius:50px;
            font-size:12.5px; font-weight:600; color:var(--cv-text-2);
            margin-right:8px; margin-top:6px;
        }
        .coord-badge i { color:var(--cv-green-mid); font-size:11px; }

        /* Contacto card */
        .contact-item {
            display:flex; align-items:center; gap:10px;
            padding:10px 0; border-bottom:1px solid var(--cv-border);
            font-size:13.5px;
        }
        .contact-item:last-child { border-bottom:none; }
        .contact-item i { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; }
        .contact-item a { color:var(--cv-blue-mid); text-decoration:none; font-weight:500; }
        .contact-item a:hover { text-decoration:underline; }

        /* Botones utilitarios */
        .btn-cv {
            display:inline-flex; align-items:center; gap:7px;
            padding:11px 22px; border-radius:50px;
            font-size:14px; font-weight:600; text-decoration:none; cursor:pointer;
            transition:opacity .2s, transform .15s;
        }
        .btn-cv:hover { opacity:.88; transform:translateY(-1px); }
        .btn-cv-primary {
            background:linear-gradient(135deg,var(--cv-green),var(--cv-blue-mid));
            color:#fff; border:none; box-shadow:0 4px 14px rgba(26,102,54,.28);
        }
        .btn-cv-outline {
            background:transparent; color:var(--cv-text-2);
            border:1.5px solid var(--cv-border);
        }
        .btn-cv-outline:hover { border-color:var(--cv-text-muted); color:var(--cv-text); }

        /* Toast copiar código */
        .copy-toast {
            position:fixed; top:50%; left:50%; transform:translate(-50%,-50%) scale(.8);
            background:linear-gradient(135deg,var(--cv-green),var(--cv-blue-mid));
            color:#fff; padding:10px 22px; border-radius:50px;
            font-size:14px; font-weight:600; z-index:9999;
            opacity:0; pointer-events:none;
            transition:opacity .2s, transform .2s;
        }
        .copy-toast.show { opacity:1; transform:translate(-50%,-50%) scale(1); }

        /* Footer */
        footer {
            background:linear-gradient(150deg,#091a30 0%,#0d2c1a 100%);
            color:rgba(255,255,255,.7); padding:48px 0 24px; font-size:14px;
        }
        .footer-logo { display:flex; align-items:center; gap:10px; text-decoration:none; margin-bottom:12px; }
        .footer-logo img { height:36px; width:36px; object-fit:contain; }
        .footer-logo-text { font-size:19px; font-weight:700; }
        .footer-logo-text .choco  { color:#6EE7A0; }
        .footer-logo-text .vis    { color:#7EB8F7; }
        footer p { color:rgba(255,255,255,.55); line-height:1.7; }
        .footer-links { list-style:none; }
        .footer-links li { margin-bottom:8px; }
        .footer-links a { color:rgba(255,255,255,.5); text-decoration:none; font-size:13.5px; display:flex; align-items:center; gap:7px; transition:color .2s; }
        .footer-links a:hover { color:rgba(255,255,255,.85); }
        .footer-divider { border:none; border-top:1px solid rgba(255,255,255,.1); margin:24px 0 18px; }
        .footer-bottom { display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px; font-size:12px; color:rgba(255,255,255,.35); }
        .footer-admin { color:rgba(255,255,255,.2); text-decoration:none; font-size:11px; transition:color .2s; }
        .footer-admin:hover { color:rgba(255,255,255,.5); }

        /* Reveal */
        .reveal { opacity:0; transform:translateY(24px); transition:opacity .5s ease, transform .5s ease; }
        .reveal.visible { opacity:1; transform:translateY(0); }

        /* Spinner */
        @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
        .spinning { animation:spin .8s linear infinite; display:inline-block; }

        @media(max-width:576px){
            .search-group { flex-direction:column; }
            .card-body-cv { padding:16px; }
        }
    </style>
</head>
<body>

    <!-- ══ NAVBAR ══ -->
    <nav class="cv-navbar" id="navbar">
        <div class="container">
            <a href="index.php" class="nav-logo">
                <img src="assets/images/chocovisibleee.png" alt="ChocoVisible">
                <span class="nav-logo-text"><span class="choco">Choco</span><span class="vis">Visible</span></span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Inicio</a></li>
                <li><a href="nueva-denuncia.php" class="nav-link">Nueva Denuncia</a></li>
                <li><a href="consultar.php" class="nav-link active">Consultar Estado</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle" aria-label="Menú">
                <i class="fas fa-bars" id="navIcon"></i>
            </button>
        </div>
        <div class="nav-drawer" id="navDrawer">
            <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Inicio</a>
            <a href="nueva-denuncia.php" class="nav-link"><i class="fas fa-pen"></i> Nueva Denuncia</a>
            <a href="consultar.php" class="nav-link"><i class="fas fa-search"></i> Consultar Estado</a>
        </div>
    </nav>

    <!-- ══ HERO ══ -->
    <div class="page-hero">
        <div class="container hero-inner">
            <h1><i class="fas fa-search me-2" style="color:var(--cv-gold-light,#F5C842);"></i>Consulta tu Denuncia</h1>
            <p>Transparencia y seguimiento en tiempo real para las denuncias ciudadanas del Chocó.</p>

            <div class="search-card reveal">
                <form method="POST" id="searchForm">
                    <label for="codigo"><i class="fas fa-barcode"></i> Código de seguimiento</label>
                    <div class="search-group">
                        <div class="search-input-wrap">
                            <i class="fas fa-hashtag"></i>
                            <input type="text" class="search-input" id="codigo" name="codigo"
                                   placeholder="Ej: CV-2024-1234"
                                   value="<?php echo isset($_POST['codigo']) ? htmlspecialchars($_POST['codigo']) : ''; ?>"
                                   autocomplete="off" required>
                        </div>
                        <button type="submit" class="btn-search" id="btnSearch">
                            <i class="fas fa-search"></i> Consultar
                        </button>
                    </div>
                </form>
            </div>
            <p class="search-hint">
                <i class="fas fa-info-circle me-1"></i>
                El código te fue entregado al registrar tu denuncia ·
                <a href="nueva-denuncia.php">¿No tienes uno? Crea una denuncia</a>
            </p>
        </div>
        <div class="hero-wave">
            <svg viewBox="0 0 1440 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,24 C480,48 960,0 1440,24 L1440,48 L0,48 Z" fill="#F6F8FA"/>
            </svg>
        </div>
    </div>

    <!-- ══ CONTENIDO ══ -->
    <div class="page-body">
        <div class="container">

            <?php if ($error): ?>
            <!-- ── Error ── -->
            <div class="row justify-content-center reveal">
                <div class="col-lg-7">
                    <div class="error-card">
                        <div class="err-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <h4>Denuncia no encontrada</h4>
                        <p><?php echo htmlspecialchars($error); ?></p>
                        <div class="tips-box">
                            <h6><i class="fas fa-lightbulb me-2" style="color:var(--cv-gold);"></i>Consejos de búsqueda</h6>
                            <div class="tip-item"><i class="fas fa-check ok"></i> Verifica que el código esté completo (Ej: CV-2024-1234)</div>
                            <div class="tip-item"><i class="fas fa-check ok"></i> Asegúrate de incluir los guiones (–)</div>
                            <div class="tip-item"><i class="fas fa-check ok"></i> El código no distingue entre mayúsculas y minúsculas</div>
                        </div>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <button onclick="location.reload()" class="btn-cv btn-cv-outline">
                                <i class="fas fa-redo"></i> Intentar de nuevo
                            </button>
                            <a href="nueva-denuncia.php" class="btn-cv btn-cv-primary">
                                <i class="fas fa-plus"></i> Crear nueva denuncia
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif ($denuncia): ?>
            <!-- ── Resultado ── -->
            <div class="row g-4 reveal">

                <!-- Columna principal -->
                <div class="col-lg-8">

                    <!-- Card principal -->
                    <div class="result-card">
                        <div class="card-head">
                            <h4><i class="fas fa-file-contract"></i> Información de la Denuncia</h4>
                        </div>
                        <div class="card-body-cv">

                            <!-- Código + Estado -->
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label"><i class="fas fa-barcode"></i> Código de seguimiento</div>
                                        <div class="tracking-code" id="codigoDisplay">
                                            <?php echo htmlspecialchars($denuncia['codigo_seguimiento']); ?>
                                        </div>
                                        <p class="copy-hint">Haz clic para copiar</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label"><i class="fas fa-circle-dot"></i> Estado actual</div>
                                        <div class="mt-2">
                                            <span class="estado-badge estado-<?php echo getEstadoColor($denuncia['estado']); ?>">
                                                <i class="fas fa-<?php echo getEstadoIcono($denuncia['estado']); ?>"></i>
                                                <?php echo getEstadoTexto($denuncia['estado']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo + Fecha -->
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label"><i class="fas fa-tag"></i> Tipo de denuncia</div>
                                        <div class="info-value">
                                            <i class="fas fa-<?php echo getTipoIcono($denuncia['tipo']); ?> me-2" style="color:var(--cv-green-mid);"></i>
                                            <?php echo formatearTipo($denuncia['tipo']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label"><i class="fas fa-calendar-alt"></i> Fecha del incidente</div>
                                        <div class="info-value"><?php echo date('d/m/Y', strtotime($denuncia['fecha'])); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="info-box mb-2">
                                <div class="info-label"><i class="fas fa-align-left"></i> Descripción del incidente</div>
                                <div class="info-text mt-1">
                                    <?php echo nl2br(htmlspecialchars($denuncia['descripcion'])); ?>
                                </div>
                            </div>

                            <?php if ($denuncia['latitud'] && $denuncia['longitud']): ?>
                            <!-- Ubicación -->
                            <div class="info-box mb-2">
                                <div class="info-label"><i class="fas fa-map-marker-alt"></i> Ubicación geográfica</div>
                                <div class="mt-1">
                                    <span class="coord-badge">
                                        <i class="fas fa-globe"></i>
                                        Lat: <?php echo number_format($denuncia['latitud'], 6); ?>
                                    </span>
                                    <span class="coord-badge">
                                        <i class="fas fa-compass"></i>
                                        Lng: <?php echo number_format($denuncia['longitud'], 6); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($fotos)): ?>
                            <!-- Evidencias -->
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-camera"></i>
                                    Evidencias adjuntas
                                    <span style="background:var(--cv-green-mid);color:#fff;padding:1px 8px;border-radius:50px;font-size:11px;margin-left:6px;">
                                        <?php echo count($fotos); ?>
                                    </span>
                                </div>
                                <div class="foto-grid">
                                    <?php foreach ($fotos as $index => $foto):
                                        $ext = strtolower(pathinfo($foto['foto_path'], PATHINFO_EXTENSION));
                                        if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
                                            <img src="<?php echo $foto['foto_path']; ?>"
                                                 class="foto-thumb"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#fotoModal"
                                                 data-src="<?php echo $foto['foto_path']; ?>"
                                                 data-index="<?php echo $index + 1; ?>"
                                                 alt="Evidencia <?php echo $index + 1; ?>">
                                        <?php else: ?>
                                            <div class="file-thumb">
                                                <i class="fas fa-file-alt"></i>
                                                <span><?php echo strtoupper($ext); ?></span>
                                            </div>
                                        <?php endif;
                                    endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Información del proceso -->
                    <div class="result-card">
                        <div class="card-head">
                            <h5><i class="fas fa-info-circle"></i> Información del proceso</h5>
                        </div>
                        <div class="card-body-cv">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-box mb-0">
                                        <div class="info-label"><i class="fas fa-calendar-plus"></i> Fecha de registro</div>
                                        <div class="info-value">
                                            <?php echo date('d/m/Y H:i', strtotime($denuncia['fecha'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box mb-0">
                                        <div class="info-label"><i class="fas fa-clock"></i> Última actualización</div>
                                        <div class="info-value">
                                            <?php
                                            $ultima = !empty($actualizaciones)
                                                ? date('d/m/Y H:i', strtotime($actualizaciones[0]['fecha']))
                                                : date('d/m/Y H:i', strtotime($denuncia['fecha_creacion']));
                                            echo $ultima;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna lateral -->
                <div class="col-lg-4">

                    <!-- Timeline de actualizaciones -->
                    <div class="result-card">
                        <div class="card-head">
                            <h5><i class="fas fa-history"></i> Seguimiento y actualizaciones</h5>
                        </div>
                        <div class="card-body-cv">
                            <?php if (!empty($actualizaciones)): ?>
                                <div class="cv-timeline">
                                    <?php foreach ($actualizaciones as $act): ?>
                                    <div class="tl-item">
                                        <div class="tl-card">
                                            <div class="tl-card-head">
                                                <span class="tl-responsible">
                                                    <i class="fas fa-user-tie"></i>
                                                    <?php echo htmlspecialchars($act['responsable']); ?>
                                                </span>
                                                <span class="tl-date">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <?php echo date('d/m/Y', strtotime($act['fecha'])); ?>
                                                </span>
                                            </div>
                                            <p class="tl-desc"><?php echo nl2br(htmlspecialchars($act['descripcion'])); ?></p>
                                            <div class="tl-time">
                                                <i class="fas fa-clock"></i>
                                                <?php echo date('H:i', strtotime($act['fecha'])); ?> hrs
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="no-updates">
                                    <i class="fas fa-clock d-block"></i>
                                    <p>Tu denuncia ha sido registrada exitosamente.<br>Las actualizaciones aparecerán aquí cuando estén disponibles.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="result-card">
                        <div class="card-head">
                            <h5><i class="fas fa-headset"></i> ¿Necesitas ayuda?</h5>
                        </div>
                        <div class="card-body-cv">
                            <div class="contact-item">
                                <i class="fas fa-phone" style="background:rgba(26,102,54,.1);color:var(--cv-green);"></i>
                                <div>
                                    <div style="font-size:12px;color:var(--cv-muted);margin-bottom:2px;">Teléfono</div>
                                    <a href="tel:+5746701234">(4) 670-1234</a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope" style="background:rgba(19,78,155,.1);color:var(--cv-blue-mid);"></i>
                                <div>
                                    <div style="font-size:12px;color:var(--cv-muted);margin-bottom:2px;">Correo electrónico</div>
                                    <a href="mailto:contacto@chocovisible.co">contacto@chocovisible.co</a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock" style="background:rgba(232,160,32,.1);color:var(--cv-gold);"></i>
                                <div>
                                    <div style="font-size:12px;color:var(--cv-muted);margin-bottom:2px;">Horario de atención</div>
                                    <span style="font-size:13.5px;color:var(--cv-text-2);">Lun–Vie · 8:00 AM – 5:00 PM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- ── Estado inicial ── -->
            <div class="row justify-content-center reveal">
                <div class="col-lg-8">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-search"></i></div>
                        <h3>Ingresa tu código de seguimiento</h3>
                        <p>Para consultar el estado de tu denuncia, necesitas el código único que recibiste al momento del registro.</p>

                        <div class="code-info-grid">
                            <div class="code-info-item"><i class="fas fa-check ok"></i> Formato: <code>CV-YYYY-XXXX</code></div>
                            <div class="code-info-item"><i class="fas fa-shield-alt info"></i> Completamente confidencial</div>
                            <div class="code-info-item"><i class="fas fa-check ok"></i> Ejemplo: <code>CV-2024-1234</code></div>
                            <div class="code-info-item"><i class="fas fa-clock info"></i> Válido permanentemente</div>
                            <div class="code-info-item"><i class="fas fa-check ok"></i> Único por denuncia</div>
                            <div class="code-info-item"><i class="fas fa-eye warn"></i> Solo tú puedes consultarlo</div>
                        </div>

                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="nueva-denuncia.php" class="btn-cv btn-cv-primary">
                                <i class="fas fa-plus"></i> Crear nueva denuncia
                            </a>
                            <button onclick="document.getElementById('codigo').focus()" class="btn-cv btn-cv-outline">
                                <i class="fas fa-arrow-up"></i> Buscar arriba
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ══ MODAL FOTO ══ -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border:none;border-radius:var(--cv-radius);overflow:hidden;">
                <div style="background:linear-gradient(135deg,var(--cv-blue),var(--cv-green));padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
                    <h5 style="color:#fff;margin:0;font-size:15px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-image"></i> Evidencia <span id="evidenciaNumero"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImg" src="" class="img-fluid w-100" style="max-height:72vh;object-fit:contain;">
                </div>
                <div class="modal-footer border-0" style="background:var(--cv-off);padding:10px 16px;">
                    <small style="color:var(--cv-muted);font-size:12px;">
                        <i class="fas fa-info-circle me-1"></i> Haz clic fuera de la imagen para cerrar
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ TOAST COPIAR ══ -->
    <div class="copy-toast" id="copyToast">
        <i class="fas fa-check me-2"></i> ¡Código copiado!
    </div>

    <!-- ══ FOOTER ══ -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 col-md-6">
                    <a href="index.php" class="footer-logo">
                        <img src="assets/images/chocovisibleee.png" alt="ChocoVisible">
                        <span class="footer-logo-text"><span class="choco">Choco</span><span class="vis">Visible</span></span>
                    </a>
                    <p>Sistema de denuncia ciudadana para el desarrollo transparente y sostenible del Departamento del Chocó.</p>
                    <div class="d-flex gap-16 mt-3" style="gap:16px;">
                        <a href="#" style="color:rgba(255,255,255,.45);transition:color .2s;" onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.45)'"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" style="color:rgba(255,255,255,.45);transition:color .2s;" onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.45)'"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" style="color:rgba(255,255,255,.45);transition:color .2s;" onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.45)'"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-6">
                    <h6 style="color:rgba(255,255,255,.7);font-size:13px;font-weight:700;margin-bottom:14px;text-transform:uppercase;letter-spacing:.5px;">Enlaces</h6>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-home"></i>Inicio</a></li>
                        <li><a href="nueva-denuncia.php"><i class="fas fa-plus"></i>Nueva Denuncia</a></li>
                        <li><a href="consultar.php"><i class="fas fa-search"></i>Consultar Estado</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-3 col-6">
                    <h6 style="color:rgba(255,255,255,.7);font-size:13px;font-weight:700;margin-bottom:14px;text-transform:uppercase;letter-spacing:.5px;">Contacto</h6>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-map-marker-alt" style="color:#E8A020;"></i>Quibdó, Chocó</a></li>
                        <li><a href="tel:+5746701234"><i class="fas fa-phone" style="color:#E8A020;"></i>(4) 670-1234</a></li>
                        <li><a href="mailto:contacto@chocovisible.co"><i class="fas fa-envelope" style="color:#E8A020;"></i>contacto@chocovisible.co</a></li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="footer-bottom">
                <span>&copy; <?php echo date('Y'); ?> ChocoVisible · Todos los derechos reservados.</span>
                <a href="login.php" class="footer-admin"><i class="fas fa-lock me-1"></i>Administración</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function(){

        /* ── Navbar ─────────────────────────────── */
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive:true });

        const toggle  = document.getElementById('navToggle');
        const drawer  = document.getElementById('navDrawer');
        const navIcon = document.getElementById('navIcon');
        toggle.addEventListener('click', () => {
            const open = drawer.classList.toggle('open');
            navIcon.className = open ? 'fas fa-times' : 'fas fa-bars';
        });
        drawer.querySelectorAll('.nav-link').forEach(a =>
            a.addEventListener('click', () => {
                drawer.classList.remove('open');
                navIcon.className = 'fas fa-bars';
            })
        );

        /* ── Scroll reveal ──────────────────────── */
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('visible'); obs.unobserve(e.target); }});
        }, { threshold:.1 });
        document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

        /* ── Auto-focus input si está vacío ─────── */
        const input = document.getElementById('codigo');
        if (input && !input.value) {
            setTimeout(() => input.focus(), 400);
        }

        /* ── Formateo automático del código ─────── */
        input.addEventListener('input', function(e){
            let v = e.target.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'');
            if (v.length >= 2 && !v.includes('-')) v = v.substring(0,2) + '-' + v.substring(2);
            if (v.length >= 7 && v.split('-').length === 2) {
                const p = v.split('-');
                if (p[1].length >= 4) v = p[0] + '-' + p[1].substring(0,4) + '-' + p[1].substring(4);
            }
            e.target.value = v;
        });

        /* ── Animación botón submit ─────────────── */
        document.getElementById('searchForm').addEventListener('submit', function(){
            const btn = document.getElementById('btnSearch');
            btn.innerHTML = '<i class="fas fa-spinner spinning"></i> Consultando…';
            btn.disabled = true;
            setTimeout(() => { btn.innerHTML = '<i class="fas fa-search"></i> Consultar'; btn.disabled = false; }, 6000);
        });

        /* ── Modal fotos ─────────────────────────── */
        document.querySelectorAll('.foto-thumb').forEach((img, i) => {
            img.addEventListener('click', function(){
                document.getElementById('modalImg').src = this.dataset.src;
                document.getElementById('evidenciaNumero').textContent = `#${this.dataset.index || i+1}`;
            });
        });

        /* ── Copiar código al portapapeles ──────── */
        const codeEl = document.getElementById('codigoDisplay');
        const toast  = document.getElementById('copyToast');
        if (codeEl) {
            codeEl.addEventListener('click', function(){
                const text = this.textContent.trim();
                navigator.clipboard.writeText(text).then(() => {
                    toast.classList.add('show');
                    setTimeout(() => toast.classList.remove('show'), 2000);
                });
                this.style.transform = 'scale(.97)';
                setTimeout(() => this.style.transform = '', 150);
            });
        }

    })();
    </script>
</body>
</html>