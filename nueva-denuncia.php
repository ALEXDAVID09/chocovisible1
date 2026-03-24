<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ChocoVisible — Realiza tu denuncia ciudadana de forma segura y confidencial.">
    <title>Nueva Denuncia · ChocoVisible</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/chocovisibleee.png">
    <style>
        /* ══════════════════════════════
           TOKENS
        ══════════════════════════════ */
        :root {
            --cv-green:        #1A6636;
            --cv-green-mid:    #248C4A;
            --cv-green-light:  #32B060;
            --cv-blue:         #0C3460;
            --cv-blue-mid:     #134E9B;
            --cv-blue-light:   #1A73D6;
            --cv-gold:         #E8A020;
            --cv-gold-light:   #F5C842;
            --cv-white:        #FFFFFF;
            --cv-off:          #F6F8FA;
            --cv-border:       #E2E8F0;
            --cv-text:         #111827;
            --cv-text-2:       #374151;
            --cv-text-muted:   #6B7280;
            --cv-danger:       #DC2626;
            --cv-danger-bg:    #FEF2F2;
            --cv-success-bg:   #ECFDF5;
            --cv-warn-bg:      #FFFBEB;
            --cv-info-bg:      #EFF6FF;
            --cv-radius:       14px;
            --cv-radius-sm:    8px;
            --cv-shadow:       0 4px 24px rgba(0,0,0,.07);
            --cv-shadow-lg:    0 16px 48px rgba(12,52,96,.14);
        }

        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--cv-text);
            background: var(--cv-off);
            min-height: 100vh;
        }

        /* ══════════════════════════════
           NAVBAR (idéntico al index)
        ══════════════════════════════ */
        .cv-navbar {
            position: fixed; top:0; left:0; right:0; z-index:1000;
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--cv-border);
        }
        .cv-navbar.scrolled { box-shadow: 0 2px 20px rgba(0,0,0,.1); }
        .cv-navbar .container {
            display: flex; align-items: center;
            height: 68px; gap: 32px;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; flex-shrink: 0;
        }
        .nav-logo img { height: 40px; width: 40px; object-fit: contain; }
        .nav-logo-text { font-size: 20px; font-weight: 700; line-height:1; letter-spacing:-.4px; }
        .nav-logo-text .choco   { color: var(--cv-green); }
        .nav-logo-text .visible { color: var(--cv-blue); }
        .nav-links {
            display: flex; align-items: center; gap: 4px;
            margin-left: auto; list-style: none;
        }
        .nav-links .nav-link {
            font-size:14.5px; font-weight:500; color: var(--cv-text-2);
            padding:8px 14px; border-radius: var(--cv-radius-sm);
            text-decoration:none; transition: color .2s, background .2s;
            white-space:nowrap;
        }
        .nav-links .nav-link:hover  { color: var(--cv-green); background: rgba(26,102,54,.07); }
        .nav-links .nav-link.active { color: var(--cv-green); font-weight:600; }
        .nav-toggle {
            display:none; background:none;
            border: 1.5px solid var(--cv-border);
            border-radius:8px; padding:7px 9px;
            cursor:pointer; margin-left:auto; flex-shrink:0;
            color: var(--cv-text-2); font-size:16px;
            transition:border-color .2s;
        }
        .nav-toggle:hover { border-color: var(--cv-green); color: var(--cv-green); }
        .nav-drawer {
            display:none; flex-direction:column; gap:4px;
            background: var(--cv-white);
            border-top: 1px solid var(--cv-border);
            padding:16px 20px 20px;
        }
        .nav-drawer .nav-link {
            font-size:15px; font-weight:500; color: var(--cv-text-2);
            padding:11px 14px; border-radius: var(--cv-radius-sm);
            text-decoration:none; display:flex; align-items:center; gap:8px;
            transition:background .2s, color .2s;
        }
        .nav-drawer .nav-link:hover { background: var(--cv-off); color: var(--cv-green); }
        @media (max-width:991px) {
            .nav-links { display:none; }
            .nav-toggle { display:flex; align-items:center; }
            .nav-drawer.open { display:flex; }
        }

        /* ══════════════════════════════
           HERO COMPACTO
        ══════════════════════════════ */
        .page-hero {
            padding: 100px 0 0;
            background: linear-gradient(150deg, var(--cv-blue) 0%, var(--cv-blue-mid) 40%, var(--cv-green) 100%);
            position: relative; overflow: hidden;
        }
        .page-hero::before {
            content:'';
            position:absolute; inset:0;
            background-image: radial-gradient(circle, rgba(255,255,255,.07) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .page-hero-inner {
            position:relative; z-index:2;
            padding: 40px 0 56px;
            text-align: center;
        }
        .page-hero h1 {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 800; color: #fff;
            letter-spacing: -.4px; margin-bottom: 12px;
        }
        .page-hero p {
            font-size: clamp(.95rem, 2vw, 1.1rem);
            color: rgba(255,255,255,.78); margin-bottom: 20px;
        }
        .hero-pills {
            display: flex; flex-wrap:wrap; gap: 10px;
            justify-content: center;
        }
        .hero-pill {
            display:inline-flex; align-items:center; gap:6px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.9);
            padding: 6px 14px; border-radius:50px;
            font-size:13px; font-weight:500;
            backdrop-filter: blur(4px);
        }
        .hero-pill i { color: var(--cv-gold-light); font-size:11px; }
        .hero-wave {
            line-height:0; margin-bottom:-1px;
            position:relative; z-index:2;
        }
        .hero-wave svg { display:block; width:100%; }

        /* ══════════════════════════════
           LAYOUT PRINCIPAL
        ══════════════════════════════ */
        .page-body { padding: 36px 0 60px; }

        /* ── Sidebar de progreso ── */
        .progress-sidebar {
            position: sticky; top: 88px;
        }
        .progress-card {
            background: var(--cv-white);
            border: 1px solid var(--cv-border);
            border-radius: var(--cv-radius);
            padding: 24px 20px;
            box-shadow: var(--cv-shadow);
        }
        .progress-card h6 {
            font-size:13px; font-weight:700;
            color: var(--cv-text-muted);
            text-transform:uppercase; letter-spacing:.5px;
            margin-bottom:20px;
        }

        .progress-bar-wrap {
            height: 4px; background: var(--cv-border);
            border-radius: 4px; margin-bottom: 20px; overflow:hidden;
        }
        .progress-bar-fill {
            height:100%;
            background: linear-gradient(90deg, var(--cv-green), var(--cv-blue-mid));
            border-radius:4px;
            transition: width .4s ease;
        }

        .step-list { list-style:none; display:flex; flex-direction:column; gap:4px; }
        .step-item {
            display:flex; align-items:center; gap:12px;
            padding: 10px 12px; border-radius: var(--cv-radius-sm);
            transition: background .2s; cursor:default;
        }
        .step-item.active  { background: rgba(26,102,54,.07); }
        .step-item.done    { background: rgba(26,102,54,.04); }

        .step-dot {
            width:28px; height:28px; flex-shrink:0;
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:12px; font-weight:700;
            background: var(--cv-border); color: var(--cv-text-muted);
            transition: all .3s;
        }
        .step-item.active .step-dot {
            background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
            color:#fff;
            box-shadow: 0 3px 10px rgba(26,102,54,.3);
        }
        .step-item.done .step-dot {
            background: var(--cv-green-light); color:#fff;
        }
        .step-label {
            font-size:13.5px; font-weight:500; color: var(--cv-text-muted);
        }
        .step-item.active .step-label { color: var(--cv-green); font-weight:600; }
        .step-item.done  .step-label  { color: var(--cv-text-2); }

        /* ── Tarjeta de ayuda rápida ── */
        .quick-help {
            background: linear-gradient(135deg, rgba(26,102,54,.07), rgba(19,78,155,.07));
            border: 1px solid rgba(26,102,54,.15);
            border-radius: var(--cv-radius);
            padding: 18px 16px;
            margin-top: 16px;
        }
        .quick-help h6 {
            font-size:13px; font-weight:700; color: var(--cv-green);
            margin-bottom:12px;
        }
        .quick-help p { font-size:12.5px; color: var(--cv-text-muted); margin:0; line-height:1.6; }

        /* ══════════════════════════════
           FORM CONTAINER
        ══════════════════════════════ */
        .form-wrapper {
            background: transparent;
            display:flex; flex-direction:column; gap:20px;
        }

        /* ── Sección del formulario ── */
        .form-section {
            background: var(--cv-white);
            border: 1px solid var(--cv-border);
            border-radius: var(--cv-radius);
            overflow:hidden;
            box-shadow: var(--cv-shadow);
            transition: box-shadow .25s;
        }
        .form-section:hover { box-shadow: var(--cv-shadow-lg); }

        .section-header {
            display:flex; align-items:center; gap:14px;
            padding: 20px 24px;
            border-bottom: 1px solid var(--cv-border);
            background: linear-gradient(135deg, #fafbfc, #f6f8fa);
        }
        .section-icon {
            width:44px; height:44px; flex-shrink:0;
            border-radius: var(--cv-radius-sm);
            background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:16px;
            box-shadow: 0 3px 10px rgba(26,102,54,.25);
        }
        .section-header h4 {
            font-size:16px; font-weight:700; color: var(--cv-text);
            margin-bottom:2px;
        }
        .section-header p { font-size:13px; color: var(--cv-text-muted); margin:0; }

        .section-body { padding: 24px; }

        /* ── Inputs ── */
        .form-label {
            font-size:13px; font-weight:600; color: var(--cv-text);
            margin-bottom:6px; display:flex; align-items:center; gap:5px;
        }
        .form-label i { color: var(--cv-green-mid); font-size:11px; }
        .required { color: var(--cv-danger); }

        .form-control, .form-select {
            border: 1.5px solid var(--cv-border);
            border-radius: var(--cv-radius-sm);
            padding: 11px 14px;
            font-size: 14.5px; font-family:'Inter',sans-serif;
            background: var(--cv-off);
            color: var(--cv-text);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .form-control::placeholder, .form-select::placeholder { color:#A0AEC0; }
        .form-control:hover, .form-select:hover { border-color:#B0C4DE; background:#fff; }
        .form-control:focus, .form-select:focus {
            outline:none;
            border-color: var(--cv-green-mid);
            box-shadow: 0 0 0 3px rgba(36,140,74,.12);
            background:#fff;
        }
        .form-control.is-invalid { border-color: var(--cv-danger); }
        .form-control.is-valid   { border-color: var(--cv-green-light); }
        .form-select.is-invalid  { border-color: var(--cv-danger); }
        .form-select.is-valid    { border-color: var(--cv-green-light); }
        .invalid-feedback { font-size:12px; color: var(--cv-danger); margin-top:4px; }
        .form-text { font-size:12px; color: var(--cv-text-muted); margin-top:5px; }

        textarea.form-control { resize:vertical; min-height:130px; }

        /* Contador de caracteres */
        .char-counter {
            display:flex; justify-content:space-between; align-items:center;
            margin-top:5px;
        }
        .char-counter span { font-size:12px; }
        #charCount { font-weight:600; }
        #charCount.ok     { color: var(--cv-green-mid); }
        #charCount.warn   { color: var(--cv-gold); }
        #charCount.bad    { color: var(--cv-danger); }

        /* ── Alerts ── */
        .cv-alert {
            display:flex; align-items:flex-start; gap:10px;
            padding:13px 16px; border-radius: var(--cv-radius-sm);
            font-size:13.5px; line-height:1.55; margin-bottom:20px;
            border-left:3px solid;
        }
        .cv-alert i { margin-top:1px; flex-shrink:0; font-size:14px; }
        .cv-alert-info    { background: var(--cv-info-bg);    border-color: var(--cv-blue-light); color:#1E40AF; }
        .cv-alert-warning { background: var(--cv-warn-bg);    border-color: var(--cv-gold);       color:#92400E; }
        .cv-alert-success { background: var(--cv-success-bg); border-color: var(--cv-green-mid);  color:#065F46; }
        .cv-alert-danger  { background: var(--cv-danger-bg);  border-color: var(--cv-danger);     color:#991B1B; }

        /* ── Zona de archivos ── */
        .file-drop-zone {
            border: 2px dashed var(--cv-border);
            border-radius: var(--cv-radius);
            padding: 36px 24px;
            text-align:center;
            background: var(--cv-off);
            cursor:pointer;
            transition: border-color .25s, background .25s, transform .2s;
        }
        .file-drop-zone:hover,
        .file-drop-zone.dragover {
            border-color: var(--cv-green-mid);
            background: rgba(26,102,54,.04);
            transform: scale(1.01);
        }
        .file-drop-zone .drop-icon {
            font-size:2.4rem; color: var(--cv-text-muted);
            margin-bottom:12px;
        }
        .file-drop-zone h5 { font-size:15px; font-weight:600; color: var(--cv-text-2); margin-bottom:6px; }
        .file-drop-zone p  { font-size:13px; color: var(--cv-text-muted); margin-bottom:16px; }

        .btn-select-file {
            display:inline-flex; align-items:center; gap:7px;
            border: 1.5px solid var(--cv-blue-mid);
            color: var(--cv-blue-mid);
            background:transparent;
            padding:9px 20px; border-radius:50px;
            font-size:13.5px; font-weight:600;
            cursor:pointer; transition: background .2s, color .2s;
        }
        .btn-select-file:hover { background: var(--cv-blue-mid); color:#fff; }

        .file-preview-grid {
            display:grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap:12px; margin-top:16px;
        }
        .file-card {
            background: var(--cv-white);
            border: 1px solid var(--cv-border);
            border-radius: var(--cv-radius-sm);
            overflow:hidden;
            transition: box-shadow .2s;
        }
        .file-card:hover { box-shadow: var(--cv-shadow); }
        .file-card-thumb {
            width:100%; height:80px; object-fit:cover;
        }
        .file-card-icon {
            height:80px; display:flex; align-items:center; justify-content:center;
            background: var(--cv-off); font-size:2rem; color: var(--cv-text-muted);
        }
        .file-card-info { padding:8px 10px; }
        .file-card-name { font-size:11.5px; font-weight:600; color: var(--cv-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .file-card-size { font-size:11px; color: var(--cv-text-muted); }
        .file-card-remove {
            width:100%; border:none; border-top:1px solid var(--cv-border);
            background:transparent; color: var(--cv-danger);
            font-size:12px; font-weight:500;
            padding:6px; cursor:pointer;
            transition: background .2s;
            display:flex; align-items:center; justify-content:center; gap:4px;
        }
        .file-card-remove:hover { background: var(--cv-danger-bg); }

        /* ── Botón obtener ubicación ── */
        .btn-location {
            display:inline-flex; align-items:center; gap:8px;
            border: 1.5px solid var(--cv-green-mid);
            color: var(--cv-green);
            background:transparent;
            padding:11px 22px; border-radius:50px;
            font-size:14px; font-weight:600;
            cursor:pointer; transition: all .2s;
        }
        .btn-location:hover { background: var(--cv-green); color:#fff; border-color: var(--cv-green); }
        .btn-location.success { background: var(--cv-green); color:#fff; border-color: var(--cv-green); }
        .btn-location.error   { background: var(--cv-danger); color:#fff; border-color: var(--cv-danger); }

        /* ── Form check personalizado ── */
        .cv-check {
            display:flex; align-items:flex-start; gap:10px;
            padding:14px 16px;
            background: var(--cv-off);
            border: 1.5px solid var(--cv-border);
            border-radius: var(--cv-radius-sm);
            cursor:pointer;
            transition: border-color .2s, background .2s;
        }
        .cv-check:hover { border-color: var(--cv-green-mid); background:#fff; }
        .cv-check input[type=checkbox] {
            width:18px; height:18px; margin-top:1px; flex-shrink:0;
            accent-color: var(--cv-green);
            cursor:pointer;
        }
        .cv-check label { font-size:14px; color: var(--cv-text-2); cursor:pointer; line-height:1.5; }

        /* ── Botones de acción ── */
        .action-row {
            display:flex; flex-wrap:wrap; gap:12px;
            justify-content:center; margin-top:4px;
        }
        .btn-back {
            display:inline-flex; align-items:center; gap:7px;
            border: 1.5px solid var(--cv-border);
            color: var(--cv-text-muted); background:transparent;
            padding:13px 22px; border-radius:50px;
            font-size:14.5px; font-weight:600;
            text-decoration:none; cursor:pointer;
            transition: border-color .2s, color .2s;
        }
        .btn-back:hover { border-color: var(--cv-text-muted); color: var(--cv-text); }

        .btn-preview {
            display:inline-flex; align-items:center; gap:7px;
            border: 1.5px solid var(--cv-blue-mid);
            color: var(--cv-blue-mid); background:transparent;
            padding:13px 22px; border-radius:50px;
            font-size:14.5px; font-weight:600;
            cursor:pointer;
            transition: background .2s, color .2s;
        }
        .btn-preview:hover { background: var(--cv-blue-mid); color:#fff; }

        .btn-submit {
            display:inline-flex; align-items:center; gap:8px;
            background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
            color:#fff; border:none;
            padding:14px 32px; border-radius:50px;
            font-size:15px; font-weight:700;
            cursor:pointer;
            box-shadow: 0 4px 16px rgba(26,102,54,.3);
            transition: opacity .2s, transform .15s;
        }
        .btn-submit:hover:not(:disabled) { opacity:.88; transform:translateY(-1px); }
        .btn-submit:disabled { opacity:.55; cursor:not-allowed; }

        /* ══════════════════════════════
           MODAL ESTILOS
        ══════════════════════════════ */
        .modal-content {
            border:none; border-radius: var(--cv-radius);
            overflow:hidden; box-shadow: var(--cv-shadow-lg);
        }
        .modal-header-cv {
            background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
            padding:20px 24px;
            display:flex; align-items:center; justify-content:space-between;
        }
        .modal-header-cv h5 {
            font-size:16px; font-weight:700; color:#fff; margin:0;
            display:flex; align-items:center; gap:8px;
        }
        .modal-header-cv .btn-close {
            filter:invert(1) brightness(2); opacity:.8;
        }
        .modal-body { padding:24px; }
        .modal-footer {
            padding:16px 24px;
            border-top:1px solid var(--cv-border);
            background: var(--cv-off);
            display:flex; gap:10px; justify-content:flex-end;
        }

        .preview-section {
            margin-bottom:20px;
        }
        .preview-section h6 {
            font-size:13px; font-weight:700;
            text-transform:uppercase; letter-spacing:.5px;
            color: var(--cv-text-muted);
            margin-bottom:12px;
            display:flex; align-items:center; gap:6px;
        }
        .preview-table { width:100%; font-size:13.5px; }
        .preview-table td { padding:6px 0; vertical-align:top; }
        .preview-table td:first-child { color: var(--cv-text-muted); width:38%; padding-right:12px; }
        .preview-table td:last-child { font-weight:500; color: var(--cv-text); }

        /* Modal ayuda */
        .modal-header-help {
            background: linear-gradient(135deg, var(--cv-green-mid), var(--cv-green));
            padding:20px 24px;
            display:flex; align-items:center; justify-content:space-between;
        }
        .modal-header-help h5 {
            font-size:16px; font-weight:700; color:#fff; margin:0;
            display:flex; align-items:center; gap:8px;
        }
        .help-item {
            display:flex; align-items:flex-start; gap:10px;
            padding:10px 0; border-bottom:1px solid var(--cv-border);
        }
        .help-item:last-child { border-bottom:none; }
        .help-item i { font-size:16px; margin-top:1px; flex-shrink:0; }
        .help-item p { font-size:13.5px; color: var(--cv-text-2); margin:0; line-height:1.5; }
        .help-item strong { display:block; font-size:12px; color: var(--cv-text-muted); text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }

        /* ── Botón flotante ayuda ── */
        .fab-help {
            position:fixed; bottom:28px; right:28px; z-index:900;
            width:54px; height:54px; border-radius:50%;
            background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
            border:none; color:#fff; font-size:18px;
            box-shadow: 0 6px 20px rgba(26,102,54,.35);
            cursor:pointer; transition: transform .2s, box-shadow .2s;
            display:flex; align-items:center; justify-content:center;
        }
        .fab-help:hover { transform:scale(1.1); box-shadow: 0 8px 28px rgba(26,102,54,.45); }

        /* ══════════════════════════════
           TOAST NOTIFICACIONES
        ══════════════════════════════ */
        .cv-toast {
            position:fixed; top:84px; right:20px; z-index:9999;
            min-width:300px; max-width:380px;
            background: var(--cv-white);
            border-radius: var(--cv-radius-sm);
            box-shadow: 0 8px 32px rgba(0,0,0,.14);
            border-left:4px solid;
            padding:14px 16px;
            display:flex; align-items:flex-start; gap:10px;
            animation: toastIn .3s ease;
            font-size:13.5px;
        }
        @keyframes toastIn {
            from { opacity:0; transform:translateX(20px); }
            to   { opacity:1; transform:translateX(0); }
        }
        .cv-toast.success { border-color: var(--cv-green-mid); }
        .cv-toast.error   { border-color: var(--cv-danger); }
        .cv-toast.warning { border-color: var(--cv-gold); }
        .cv-toast.info    { border-color: var(--cv-blue-light); }
        .cv-toast i { font-size:16px; margin-top:1px; flex-shrink:0; }
        .cv-toast.success i { color: var(--cv-green-mid); }
        .cv-toast.error   i { color: var(--cv-danger); }
        .cv-toast.warning i { color: var(--cv-gold); }
        .cv-toast.info    i { color: var(--cv-blue-light); }
        .cv-toast .toast-close {
            margin-left:auto; background:none; border:none;
            font-size:14px; color: var(--cv-text-muted);
            cursor:pointer; padding:0 0 0 8px;
        }

        /* ══════════════════════════════
           SPINNER
        ══════════════════════════════ */
        @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
        .spinning { animation: spin .8s linear infinite; display:inline-block; }

        /* ══════════════════════════════
           RESPONSIVE
        ══════════════════════════════ */
        @media (max-width:768px) {
            .section-body { padding:18px 16px; }
            .action-row { flex-direction:column; }
            .action-row > * { width:100%; justify-content:center; }
        }
    </style>
</head>
<body>

    <!-- ══ NAVBAR ══ -->
    <nav class="cv-navbar" id="navbar">
        <div class="container">
            <a href="index.php" class="nav-logo">
                <img src="assets/images/chocovisibleee.png" alt="ChocoVisible">
                <span class="nav-logo-text">
                    <span class="choco">Choco</span><span class="visible">Visible</span>
                </span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Inicio</a></li>
                <li><a href="nueva-denuncia.php" class="nav-link active">Nueva Denuncia</a></li>
                <li><a href="consultar.php" class="nav-link">Consultar Estado</a></li>
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
        <div class="container page-hero-inner">
            <h1><i class="fas fa-shield-alt me-2" style="color:var(--cv-gold-light);"></i>Nueva Denuncia</h1>
            <p>Tu voz importa. Reporta incidentes de manera segura y confidencial.</p>
            <div class="hero-pills">
                <span class="hero-pill"><i class="fas fa-lock"></i> 100% Confidencial</span>
                <span class="hero-pill"><i class="fas fa-eye-slash"></i> Anónimo opcional</span>
                <span class="hero-pill"><i class="fas fa-shield-alt"></i> Datos protegidos</span>
            </div>
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
            <div class="row g-4">

                <!-- ── Sidebar progreso ── -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="progress-sidebar">
                        <div class="progress-card">
                            <h6>Progreso</h6>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" id="progressFill" style="width:20%"></div>
                            </div>
                            <ul class="step-list" id="stepList">
                                <li class="step-item active" data-step="1">
                                    <div class="step-dot" id="dot1">1</div>
                                    <span class="step-label">Información del incidente</span>
                                </li>
                                <li class="step-item" data-step="2">
                                    <div class="step-dot" id="dot2">2</div>
                                    <span class="step-label">Ubicación</span>
                                </li>
                                <li class="step-item" data-step="3">
                                    <div class="step-dot" id="dot3">3</div>
                                    <span class="step-label">Evidencias</span>
                                </li>
                                <li class="step-item" data-step="4">
                                    <div class="step-dot" id="dot4">4</div>
                                    <span class="step-label">Información de contacto</span>
                                </li>
                                <li class="step-item" data-step="5">
                                    <div class="step-dot" id="dot5">5</div>
                                    <span class="step-label">Confirmación y envío</span>
                                </li>
                            </ul>
                        </div>
                        <div class="quick-help">
                            <h6><i class="fas fa-life-ring me-2"></i>¿Necesitas ayuda?</h6>
                            <p>Emergencias: <strong>123</strong><br>
                               Policía: <strong>112</strong><br>
                               Antiextorsión: <strong>165</strong></p>
                            <button class="btn-location mt-3 w-100" style="border-radius:var(--cv-radius-sm);" data-bs-toggle="modal" data-bs-target="#helpModal">
                                <i class="fas fa-headset"></i> Ver más ayuda
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ── Formulario principal ── -->
                <div class="col-lg-9">
                    <form action="procesar-denuncia.php" method="POST" enctype="multipart/form-data" id="denunciaForm">
                        <div class="form-wrapper">

                            <!-- ─ Sección 1: Información del incidente ─ -->
                            <div class="form-section" data-step="1">
                                <div class="section-header">
                                    <div class="section-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                    <div>
                                        <h4>Información del Incidente</h4>
                                        <p>Describe qué sucedió y cuándo ocurrió</p>
                                    </div>
                                </div>
                                <div class="section-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="tipo" class="form-label">
                                                <i class="fas fa-tag"></i>
                                                Tipo de denuncia <span class="required">*</span>
                                            </label>
                                            <select class="form-select" id="tipo" name="tipo" required>
                                                <option value="">Selecciona el tipo de incidente</option>
                                                <option value="acoso">🚫 Acoso o Intimidación</option>
                                                <option value="seguridad">🛡️ Problema de Seguridad</option>
                                                <option value="etico">⚖️ Problema Ético</option>
                                                <option value="discriminacion">🤝 Discriminación</option>
                                                <option value="corrupcion">💼 Corrupción</option>
                                                <option value="laboral">💔 Problema Laboral</option>
                                                <option value="ambiental">🌿 Problema Ambiental</option>
                                                <option value="servicios">💧 Servicios Públicos</option>
                                                <option value="otro">📋 Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="fecha" class="form-label">
                                                <i class="fas fa-calendar-alt"></i>
                                                Fecha del incidente
                                            </label>
                                            <input type="date" class="form-control" id="fecha" name="fecha"
                                                   max="<?php echo date('Y-m-d'); ?>">
                                            <div class="form-text">Fecha aproximada si no recuerdas exactamente</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">
                                            <i class="fas fa-align-left"></i>
                                            Descripción detallada <span class="required">*</span>
                                        </label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="6"
                                                  placeholder="Describe detalladamente lo que ocurrió... ¿Qué pasó? ¿Quién estuvo involucrado? ¿Dónde sucedió? ¿Hay testigos?"
                                                  required></textarea>
                                        <div class="char-counter">
                                            <span class="form-text"><i class="fas fa-edit me-1"></i>Mínimo 20 caracteres. Sé específico.</span>
                                            <span id="charCount" class="bad">0 caracteres</span>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="urgencia" class="form-label">
                                                <i class="fas fa-tachometer-alt"></i>
                                                Nivel de urgencia
                                            </label>
                                            <select class="form-select" id="urgencia" name="urgencia">
                                                <option value="baja">🟢 Baja — No requiere acción inmediata</option>
                                                <option value="media" selected>🟡 Media — Requiere atención</option>
                                                <option value="alta">🔴 Alta — Requiere acción urgente</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="categoria_afectada" class="form-label">
                                                <i class="fas fa-layer-group"></i>
                                                Área afectada
                                            </label>
                                            <select class="form-select" id="categoria_afectada" name="categoria_afectada">
                                                <option value="">— Selecciona —</option>
                                                <option value="salud">Salud</option>
                                                <option value="educacion">Educación</option>
                                                <option value="trabajo">Trabajo</option>
                                                <option value="medio_ambiente">Medio Ambiente</option>
                                                <option value="seguridad">Seguridad Pública</option>
                                                <option value="infraestructura">Infraestructura</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ─ Sección 2: Ubicación ─ -->
                            <div class="form-section" data-step="2">
                                <div class="section-header">
                                    <div class="section-icon"><i class="fas fa-map-marker-alt"></i></div>
                                    <div>
                                        <h4>Ubicación del Incidente</h4>
                                        <p>Ayúdanos a ubicar dónde ocurrió</p>
                                    </div>
                                </div>
                                <div class="section-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="direccion" class="form-label">
                                                <i class="fas fa-road"></i>
                                                Dirección o lugar
                                            </label>
                                            <input type="text" class="form-control" id="direccion" name="direccion"
                                                   placeholder="Ej: Calle 25 #12-34, Oficina, parque...">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="municipio" class="form-label">
                                                <i class="fas fa-city"></i>
                                                Municipio
                                            </label>
                                            <select class="form-select" id="municipio" name="municipio">
                                                <option value="quibdo" selected>Quibdó</option>
                                                <option value="istmina">Istmina</option>
                                                <option value="condoto">Condoto</option>
                                                <option value="nuqui">Nuquí</option>
                                                <option value="otro_choco">Otro municipio del Chocó</option>
                                                <option value="otro">Otro departamento</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="latitud" class="form-label">
                                                <i class="fas fa-globe"></i>
                                                Latitud
                                            </label>
                                            <input type="number" step="any" class="form-control" id="latitud"
                                                   name="latitud" placeholder="5.6918" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="longitud" class="form-label">
                                                <i class="fas fa-globe"></i>
                                                Longitud
                                            </label>
                                            <input type="number" step="any" class="form-control" id="longitud"
                                                   name="longitud" placeholder="-76.6669" readonly>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn-location" id="getLocationBtn">
                                            <i class="fas fa-crosshairs"></i> Obtener ubicación actual
                                        </button>
                                        <p class="form-text mt-2">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Tu ubicación se usará solo para la investigación y permanecerá confidencial.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- ─ Sección 3: Evidencias ─ -->
                            <div class="form-section" data-step="3">
                                <div class="section-header">
                                    <div class="section-icon"><i class="fas fa-camera"></i></div>
                                    <div>
                                        <h4>Evidencias</h4>
                                        <p>Adjunta documentos, fotos o archivos de respaldo</p>
                                    </div>
                                </div>
                                <div class="section-body">
                                    <div class="file-drop-zone" id="fileDropZone">
                                        <div class="drop-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                        <h5>Arrastra archivos aquí</h5>
                                        <p>Imágenes, PDFs, documentos Word · Máx. 5 archivos de 10 MB c/u</p>
                                        <input type="file" class="d-none" id="evidencias" name="evidencias[]"
                                               multiple accept="image/*,.pdf,.doc,.docx,.txt">
                                        <button type="button" class="btn-select-file" onclick="document.getElementById('evidencias').click()">
                                            <i class="fas fa-plus"></i> Seleccionar archivos
                                        </button>
                                    </div>
                                    <div class="file-preview-grid" id="filePreviewGrid"></div>
                                </div>
                            </div>

                            <!-- ─ Sección 4: Contacto ─ -->
                            <div class="form-section" data-step="4">
                                <div class="section-header">
                                    <div class="section-icon"><i class="fas fa-user-circle"></i></div>
                                    <div>
                                        <h4>Información de Contacto</h4>
                                        <p>Opcional y completamente confidencial</p>
                                    </div>
                                </div>
                                <div class="section-body">
                                    <div class="cv-alert cv-alert-info mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        <div>
                                            <strong>¿Por qué pedimos esta información?</strong><br>
                                            Solo para contactarte si necesitamos aclarar detalles. Tu identidad permanecerá protegida en todo momento.
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="nombre" class="form-label">
                                                <i class="fas fa-user"></i> Nombre completo
                                            </label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                   placeholder="Tu nombre (opcional)">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="contacto" class="form-label">
                                                <i class="fas fa-phone"></i> Teléfono
                                            </label>
                                            <input type="tel" class="form-control" id="contacto" name="contacto"
                                                   placeholder="+57 300 123 4567 (opcional)">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope"></i> Correo electrónico
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               placeholder="tu@correo.com (opcional)">
                                    </div>
                                    <label class="cv-check">
                                        <input type="checkbox" id="anonimo" name="anonimo">
                                        <span><i class="fas fa-user-secret me-1" style="color:var(--cv-green);"></i>
                                        Prefiero mantener mi denuncia completamente anónima (se deshabilitarán los campos de contacto)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- ─ Sección 5: Confirmación ─ -->
                            <div class="form-section" data-step="5">
                                <div class="section-header">
                                    <div class="section-icon"><i class="fas fa-check-double"></i></div>
                                    <div>
                                        <h4>Confirmación y Envío</h4>
                                        <p>Revisa y acepta los términos antes de enviar</p>
                                    </div>
                                </div>
                                <div class="section-body">
                                    <div class="cv-alert cv-alert-warning mb-4">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <div>
                                            <strong>Importante:</strong> Una vez enviada la denuncia, recibirás un <strong>código único de seguimiento</strong> para consultar su estado en cualquier momento.
                                        </div>
                                    </div>
                                    <label class="cv-check mb-4">
                                        <input type="checkbox" id="terminos" name="terminos" required>
                                        <span>
                                            <strong>Declaro que:</strong><br>
                                            · La información proporcionada es veraz y completa.<br>
                                            · Autorizo el procesamiento de mis datos para la investigación.<br>
                                            · Entiendo que proporcionar información falsa puede tener consecuencias legales.
                                            <span class="required"> *</span>
                                        </span>
                                    </label>
                                    <div class="action-row">
                                        <a href="index.php" class="btn-back">
                                            <i class="fas fa-arrow-left"></i> Volver al inicio
                                        </a>
                                        <button type="button" class="btn-preview" id="previewBtn">
                                            <i class="fas fa-eye"></i> Vista previa
                                        </button>
                                        <button type="submit" class="btn-submit" id="submitBtn">
                                            <i class="fas fa-paper-plane"></i> Enviar denuncia
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /form-wrapper -->
                    </form>
                </div><!-- /col-lg-9 -->
            </div>
        </div>
    </div>

    <!-- ══ MODAL: VISTA PREVIA ══ -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header-cv">
                    <h5 id="previewModalLabel"><i class="fas fa-eye"></i> Vista previa de la denuncia</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn-back" data-bs-dismiss="modal">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button type="button" class="btn-submit" id="confirmSubmit">
                        <i class="fas fa-check"></i> Confirmar y enviar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: AYUDA ══ -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header-help">
                    <h5><i class="fas fa-life-ring"></i> ¿Necesitas ayuda?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="help-item">
                        <i class="fas fa-phone-alt" style="color:var(--cv-green);"></i>
                        <div>
                            <strong>Líneas de emergencia</strong>
                            <p>Emergencias: <strong>123</strong> · Policía Nacional: <strong>112</strong> · Antiextorsión: <strong>165</strong></p>
                        </div>
                    </div>
                    <div class="help-item">
                        <i class="fas fa-shield-alt" style="color:var(--cv-blue-mid);"></i>
                        <div>
                            <strong>Tu seguridad es primero</strong>
                            <p>Tus datos están protegidos. Puedes reportar de forma anónima. No compartiremos tu identidad sin tu consentimiento.</p>
                        </div>
                    </div>
                    <div class="help-item">
                        <i class="fas fa-envelope" style="color:var(--cv-gold);"></i>
                        <div>
                            <strong>Contacto directo</strong>
                            <p>Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="10737f7e647173647f5073787f737f66796379727c753e737f">[email&#160;protected]</a><br>WhatsApp: +57 314 123 4567</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-submit" data-bs-dismiss="modal">
                        <i class="fas fa-check"></i> Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ FAB AYUDA ══ -->
    <button class="fab-help" data-bs-toggle="modal" data-bs-target="#helpModal" title="¿Necesitas ayuda?">
        <i class="fas fa-question"></i>
    </button>

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {

        /* ── Navbar ────────────────────────────────────────── */
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });

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

        /* ── Progreso lateral ──────────────────────────────── */
        let currentStep = 1;
        const totalSteps = 5;

        function updateProgress(step) {
            currentStep = step;
            const pct = (step / totalSteps) * 100;
            document.getElementById('progressFill').style.width = pct + '%';

            for (let i = 1; i <= totalSteps; i++) {
                const li  = document.querySelector(`#stepList [data-step="${i}"]`);
                const dot = document.getElementById(`dot${i}`);
                li.classList.remove('active','done');
                if (i < step) {
                    li.classList.add('done');
                    dot.innerHTML = '<i class="fas fa-check" style="font-size:10px;"></i>';
                } else if (i === step) {
                    li.classList.add('active');
                    dot.textContent = i;
                } else {
                    dot.textContent = i;
                }
            }
        }

        // Observer de secciones
        const sections = document.querySelectorAll('.form-section[data-step]');
        const stepObserver = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    updateProgress(parseInt(e.target.dataset.step));
                }
            });
        }, { threshold: 0.4 });
        sections.forEach(s => stepObserver.observe(s));
        updateProgress(1);

        /* ── Contador de caracteres ────────────────────────── */
        const desc     = document.getElementById('descripcion');
        const counter  = document.getElementById('charCount');
        desc.addEventListener('input', function () {
            const n = this.value.length;
            counter.textContent = n + ' caracteres';
            counter.className = n < 20 ? 'bad' : n < 50 ? 'warn' : 'ok';
        });

        /* ── Validación en tiempo real ─────────────────────── */
        function validateField(field) {
            const wrap = field.closest('.mb-3, .row, .col-md-6, .section-body');
            if (!wrap) return true;

            const prev = field.parentNode.querySelector('.invalid-feedback');
            if (prev) prev.remove();
            field.classList.remove('is-invalid','is-valid');

            let ok = true, msg = '';

            if (field.id === 'descripcion' && field.value.length > 0 && field.value.length < 20) {
                ok = false; msg = 'Mínimo 20 caracteres.';
            }
            if (field.id === 'email' && field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                ok = false; msg = 'Ingresa un email válido.';
            }
            if (field.id === 'contacto' && field.value && !/^[\+]?[\d\s\-\(\)]{7,}$/.test(field.value)) {
                ok = false; msg = 'Ingresa un teléfono válido.';
            }
            if (field.required && !field.value.trim()) {
                ok = false; msg = 'Este campo es obligatorio.';
            }

            if (field.value.trim()) {
                field.classList.add(ok ? 'is-valid' : 'is-invalid');
                if (!ok) {
                    const fb = document.createElement('div');
                    fb.className = 'invalid-feedback';
                    fb.textContent = msg;
                    field.parentNode.appendChild(fb);
                }
            }
            return ok;
        }

        function validateForm() {
            let ok = true;
            document.querySelectorAll('[required]').forEach(f => { if (!validateField(f)) ok = false; });
            if (!ok) {
                showToast('Corrige los errores antes de continuar.', 'error');
                const first = document.querySelector('.is-invalid');
                if (first) { first.scrollIntoView({ behavior:'smooth', block:'center' }); first.focus(); }
            }
            return ok;
        }

        document.getElementById('denunciaForm').addEventListener('input', e => validateField(e.target));
        document.getElementById('denunciaForm').addEventListener('submit', function (e) {
            e.preventDefault();
            if (validateForm()) showPreview();
        });

        /* ── Carga de archivos ─────────────────────────────── */
        const fileInput = document.getElementById('evidencias');
        const dropZone  = document.getElementById('fileDropZone');
        const grid      = document.getElementById('filePreviewGrid');
        let selectedFiles = [];

        dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', e => { e.preventDefault(); dropZone.classList.remove('dragover'); });
        dropZone.addEventListener('drop', e => {
            e.preventDefault(); dropZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });
        fileInput.addEventListener('change', e => handleFiles(e.target.files));

        function handleFiles(files) {
            const arr = Array.from(files);
            if (arr.length > 5) { showToast('Máximo 5 archivos permitidos.', 'warning'); return; }

            selectedFiles = [];
            grid.innerHTML = '';

            arr.forEach((file, idx) => {
                if (file.size > 10 * 1024 * 1024) {
                    showToast(`"${file.name}" supera los 10 MB.`, 'warning'); return;
                }
                selectedFiles.push(file);

                const card = document.createElement('div');
                card.className = 'file-card';
                card.dataset.index = idx;

                const topHtml = file.type.startsWith('image/')
                    ? `<img src="${URL.createObjectURL(file)}" class="file-card-thumb" alt="${file.name}">`
                    : `<div class="file-card-icon"><i class="${getFileIcon(file.type)}"></i></div>`;

                card.innerHTML = `
                    ${topHtml}
                    <div class="file-card-info">
                        <div class="file-card-name" title="${file.name}">${file.name}</div>
                        <div class="file-card-size">${formatSize(file.size)}</div>
                    </div>
                    <button type="button" class="file-card-remove" onclick="removeFile(${idx})">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                `;
                grid.appendChild(card);
            });

            if (arr.length > 0) {
                dropZone.querySelector('h5').textContent = arr.length + ' archivo(s) seleccionado(s)';
                dropZone.querySelector('p').textContent = 'Arrastra más o haz clic para agregar.';
            }
        }

        window.removeFile = function (idx) {
            const card = grid.querySelector(`[data-index="${idx}"]`);
            if (card) card.remove();
            selectedFiles.splice(idx, 1);
            if (selectedFiles.length === 0) {
                dropZone.querySelector('h5').textContent = 'Arrastra archivos aquí';
                dropZone.querySelector('p').textContent = 'Imágenes, PDFs, documentos Word · Máx. 5 archivos de 10 MB c/u';
            }
            showToast('Archivo eliminado.', 'info');
        };

        /* ── Geolocalización ───────────────────────────────── */
        const locBtn = document.getElementById('getLocationBtn');
        locBtn.addEventListener('click', function () {
            if (!navigator.geolocation) { showToast('Tu navegador no soporta geolocalización.', 'error'); return; }

            this.innerHTML = '<i class="fas fa-spinner spinning"></i> Obteniendo ubicación…';
            this.disabled = true;

            navigator.geolocation.getCurrentPosition(
                pos => {
                    document.getElementById('latitud').value  = pos.coords.latitude.toFixed(6);
                    document.getElementById('longitud').value = pos.coords.longitude.toFixed(6);
                    this.innerHTML = '<i class="fas fa-check"></i> Ubicación obtenida';
                    this.classList.add('success');
                    showToast('Ubicación obtenida correctamente.', 'success');
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-crosshairs"></i> Actualizar ubicación';
                        this.classList.remove('success');
                        this.disabled = false;
                    }, 3000);
                },
                err => {
                    const msgs = { 1:'Permiso denegado.', 2:'Información no disponible.', 3:'Tiempo de espera agotado.' };
                    showToast(msgs[err.code] || 'Error al obtener la ubicación.', 'error');
                    this.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error — reintentar';
                    this.classList.add('error');
                    this.disabled = false;
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-crosshairs"></i> Obtener ubicación actual';
                        this.classList.remove('error');
                    }, 3000);
                },
                { enableHighAccuracy:true, timeout:10000, maximumAge:60000 }
            );
        });

        /* ── Checkbox anónimo ──────────────────────────────── */
        document.getElementById('anonimo').addEventListener('change', function () {
            ['nombre','email','contacto'].forEach(id => {
                const f = document.getElementById(id);
                if (this.checked) {
                    f.value = ''; f.disabled = true;
                    f.placeholder = 'Deshabilitado — denuncia anónima';
                } else {
                    f.disabled = false;
                    const ph = { nombre:'Tu nombre (opcional)', email:'tu@correo.com (opcional)', contacto:'+57 300 123 4567 (opcional)' };
                    f.placeholder = ph[id];
                }
            });
        });

        /* ── Vista previa ──────────────────────────────────── */
        function sel(id) {
            const el = document.getElementById(id);
            if (!el) return '—';
            if (el.tagName === 'SELECT') return el.options[el.selectedIndex]?.text || '—';
            return el.value || '—';
        }

        function showPreview() {
            const html = `
                <div class="preview-section">
                    <h6><i class="fas fa-exclamation-triangle" style="color:var(--cv-gold);"></i> Información del incidente</h6>
                    <table class="preview-table">
                        <tr><td>Tipo</td><td>${sel('tipo')}</td></tr>
                        <tr><td>Fecha</td><td>${sel('fecha') || 'No especificada'}</td></tr>
                        <tr><td>Urgencia</td><td>${sel('urgencia')}</td></tr>
                        <tr><td>Área afectada</td><td>${sel('categoria_afectada') || 'No especificada'}</td></tr>
                        <tr><td>Descripción</td><td>${sel('descripcion')}</td></tr>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="preview-section">
                            <h6><i class="fas fa-map-marker-alt" style="color:var(--cv-green);"></i> Ubicación</h6>
                            <table class="preview-table">
                                <tr><td>Dirección</td><td>${sel('direccion') || 'No especificada'}</td></tr>
                                <tr><td>Municipio</td><td>${sel('municipio')}</td></tr>
                                <tr><td>Coordenadas</td><td>${sel('latitud')}, ${sel('longitud')}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="preview-section">
                            <h6><i class="fas fa-user" style="color:var(--cv-blue-mid);"></i> Contacto</h6>
                            <table class="preview-table">
                                <tr><td>Nombre</td><td>${sel('nombre') || 'Anónimo'}</td></tr>
                                <tr><td>Email</td><td>${sel('email') || 'No proporcionado'}</td></tr>
                                <tr><td>Teléfono</td><td>${sel('contacto') || 'No proporcionado'}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('previewContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }

        document.getElementById('previewBtn').addEventListener('click', () => {
            if (validateForm()) showPreview();
        });

        document.getElementById('confirmSubmit').addEventListener('click', function () {
            bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fas fa-spinner spinning"></i> Enviando…';
            btn.disabled = true;
            document.getElementById('denunciaForm').submit();
        });

        /* ── Toast notifications ───────────────────────────── */
        const icons = { success:'fa-check-circle', error:'fa-exclamation-circle', warning:'fa-exclamation-triangle', info:'fa-info-circle' };

        window.showToast = function (msg, type = 'info') {
            const t = document.createElement('div');
            t.className = `cv-toast ${type}`;
            t.innerHTML = `
                <i class="fas ${icons[type]}"></i>
                <span>${msg}</span>
                <button class="toast-close" onclick="this.parentNode.remove()"><i class="fas fa-times"></i></button>
            `;
            document.body.appendChild(t);
            setTimeout(() => { if (t.parentNode) t.remove(); }, 5000);
        };

        /* ── Helpers ───────────────────────────────────────── */
        function getFileIcon(mime) {
            if (mime.includes('pdf'))   return 'fas fa-file-pdf';
            if (mime.includes('word'))  return 'fas fa-file-word';
            if (mime.includes('image')) return 'fas fa-file-image';
            return 'fas fa-file-alt';
        }
        function formatSize(b) {
            if (b === 0) return '0 B';
            const s = ['B','KB','MB','GB'], i = Math.floor(Math.log(b)/Math.log(1024));
            return (b / Math.pow(1024, i)).toFixed(1) + ' ' + s[i];
        }

    })();
    </script>

    <!-- ══════════════════════════════════════════════════════
         WIDGET DE VOZ — ChocoVisible
         Botón fijo en bottom:28px left:28px
    ══════════════════════════════════════════════════════ -->
    <style>
    /* ── Variables voz (heredan tokens cv-*) ── */
    #vozToggleBtn {
        position: fixed; bottom: 28px; left: 28px; z-index: 1100;
        width: 62px; height: 62px; border-radius: 50%; border: none; cursor: pointer;
        background: linear-gradient(135deg, var(--cv-blue-mid), var(--cv-green));
        box-shadow: 0 6px 24px rgba(19,78,155,.35);
        display: flex; align-items: center; justify-content: center;
        flex-direction: column; gap: 2px;
        transition: transform .25s, box-shadow .25s;
    }
    #vozToggleBtn:hover { transform: scale(1.1); box-shadow: 0 10px 36px rgba(19,78,155,.45); }
    #vozToggleBtn svg   { width: 26px; height: 26px; fill: none; stroke: #fff; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

    #vozBadge {
        position: absolute; top: -4px; left: -4px;
        background: var(--cv-gold-light); color: var(--cv-blue);
        font-size: .58rem; font-weight: 800;
        border-radius: 20px; padding: 2px 6px; white-space: nowrap;
        letter-spacing: .3px; border: 1.5px solid var(--cv-white);
        animation: vozBadgePulse 2s infinite;
    }
    @keyframes vozBadgePulse {
        0%,100% { box-shadow: 0 0 0 0 rgba(245,200,66,.55); }
        50%      { box-shadow: 0 0 0 6px rgba(245,200,66,0); }
    }

    /* ── Panel ── */
    #vozPanel {
        position: fixed; bottom: 106px; left: 28px; z-index: 1099;
        width: min(360px, calc(100vw - 48px));
        border-radius: 18px;
        background: var(--cv-white);
        border: 1px solid var(--cv-border);
        box-shadow: 0 12px 48px rgba(12,52,96,.2), 0 2px 8px rgba(0,0,0,.07);
        overflow: hidden;
        transform: scale(.92) translateY(20px);
        opacity: 0; pointer-events: none;
        transition: transform .28s cubic-bezier(.34,1.56,.64,1), opacity .22s ease;
    }
    #vozPanel.open { transform: scale(1) translateY(0); opacity: 1; pointer-events: all; }

    /* Header */
    #vozHeader {
        background: linear-gradient(135deg, var(--cv-blue-mid), var(--cv-green));
        color: #fff; padding: 14px 16px;
        display: flex; align-items: center; gap: 10px;
    }
    #vozHeaderIcon {
        width: 42px; height: 42px; border-radius: 50%;
        background: rgba(255,255,255,.18);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
        border: 1.5px solid rgba(255,255,255,.25);
    }
    #vozHeaderInfo { flex: 1; }
    #vozHeaderInfo strong { display: block; font-size: .93rem; font-weight: 700; }
    #vozHeaderInfo small  { font-size: .72rem; opacity: .85; }
    #vozCloseBtn {
        background: none; border: none; color: #fff; font-size: 1.2rem;
        cursor: pointer; padding: 6px; border-radius: 8px; transition: background .2s;
    }
    #vozCloseBtn:hover { background: rgba(255,255,255,.15); }

    /* Sin soporte */
    #vozNoSupport {
        display: none; background: var(--cv-warn-bg);
        border-left: 4px solid var(--cv-gold);
        padding: 10px 14px; font-size: .8rem; color: #92400E;
        margin: 10px 14px; border-radius: var(--cv-radius-sm);
    }

    /* Selector idioma */
    #vozLangSelector { display: flex; gap: 6px; padding: 12px 14px 0; flex-wrap: wrap; }
    .voz-lang-btn {
        flex: 1; min-width: 76px; padding: 7px 4px;
        border-radius: var(--cv-radius-sm); border: 1.5px solid var(--cv-border);
        background: var(--cv-off); color: var(--cv-text-2);
        font-size: .74rem; font-weight: 600; cursor: pointer;
        transition: all .2s; text-align: center;
    }
    .voz-lang-btn:hover { border-color: var(--cv-green-mid); color: var(--cv-green); }
    .voz-lang-btn.active { background: var(--cv-green); border-color: var(--cv-green); color: #fff; }

    /* Selector campo destino */
    #vozTargetSelector { padding: 10px 14px 0; }
    #vozTargetSelector label { font-size: .74rem; font-weight: 600; color: var(--cv-muted); display: block; margin-bottom: 4px; }
    #vozTargetSelect {
        width: 100%; border: 1.5px solid var(--cv-border); border-radius: var(--cv-radius-sm);
        padding: 8px 10px; font-size: .8rem; color: var(--cv-text);
        background: var(--cv-off); outline: none; transition: border-color .2s;
        font-family: 'Inter', sans-serif;
    }
    #vozTargetSelect:focus { border-color: var(--cv-green-mid); background: #fff; }

    /* Display texto */
    #vozDisplay {
        margin: 12px 14px 0; border-radius: var(--cv-radius-sm);
        border: 1.5px solid var(--cv-border); background: var(--cv-off);
        min-height: 88px; padding: 12px; font-size: .85rem;
        color: var(--cv-text-2); line-height: 1.6; position: relative;
        transition: border-color .3s, background .3s;
    }
    #vozDisplay.escuchando { border-color: #EF4444; background: #FEF2F2; }
    #vozDisplay.listo      { border-color: var(--cv-green-mid); background: var(--cv-success-bg); }
    #vozPlaceholder   { color: var(--cv-muted); font-style: italic; font-size: .82rem; }
    #vozTexto         { display: none; }
    #vozTextoInterim  { color: var(--cv-muted); font-style: italic; }

    /* Onda de audio */
    #vozWave { display: none; align-items: center; justify-content: center; gap: 3px; margin: 6px 0 2px; }
    #vozWave.show { display: flex; }
    #vozWave span {
        display: inline-block; width: 4px; border-radius: 4px;
        background: #EF4444; animation: vozWaveAnim .6s ease-in-out infinite alternate;
    }
    #vozWave span:nth-child(1) { height: 8px;  animation-delay: 0s; }
    #vozWave span:nth-child(2) { height: 18px; animation-delay: .1s; }
    #vozWave span:nth-child(3) { height: 26px; animation-delay: .2s; }
    #vozWave span:nth-child(4) { height: 18px; animation-delay: .3s; }
    #vozWave span:nth-child(5) { height: 8px;  animation-delay: .4s; }
    @keyframes vozWaveAnim { from{transform:scaleY(1)} to{transform:scaleY(1.6)} }

    /* Estado */
    #vozStatus {
        text-align: center; font-size: .74rem; padding: 6px 14px 0;
        font-weight: 600; min-height: 22px; color: var(--cv-muted);
    }
    #vozStatus.grabando { color: #DC2626; }
    #vozStatus.listo    { color: var(--cv-green-mid); }
    #vozStatus.error    { color: var(--cv-gold); }

    /* Controles */
    #vozControls { display: flex; gap: 8px; padding: 12px 14px 14px; }

    #vozStartBtn {
        flex: 1; height: 44px; border-radius: var(--cv-radius-sm); border: none;
        cursor: pointer; font-size: .85rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        background: linear-gradient(135deg, var(--cv-blue-mid), var(--cv-green));
        color: #fff; box-shadow: 0 3px 10px rgba(19,78,155,.25);
        transition: opacity .2s, transform .15s;
    }
    #vozStartBtn:hover { opacity: .88; transform: translateY(-1px); }
    #vozStartBtn.grabando {
        background: linear-gradient(135deg, #DC2626, #991B1B);
        animation: vozPulseBtn 1.2s infinite;
    }
    @keyframes vozPulseBtn {
        0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.4); }
        50%      { box-shadow: 0 0 0 8px rgba(220,38,38,0); }
    }

    #vozApplyBtn {
        height: 44px; padding: 0 14px; border-radius: var(--cv-radius-sm);
        border: 1.5px solid var(--cv-green-mid); background: var(--cv-white);
        color: var(--cv-green); font-size: .82rem; font-weight: 700;
        cursor: pointer; display: flex; align-items: center; gap: 5px;
        transition: all .2s; white-space: nowrap;
    }
    #vozApplyBtn:hover   { background: var(--cv-green); color: #fff; border-color: var(--cv-green); }
    #vozApplyBtn:disabled{ opacity: .4; cursor: default; }

    #vozClearBtn {
        height: 44px; width: 44px; border-radius: var(--cv-radius-sm);
        border: 1.5px solid var(--cv-border); background: var(--cv-off);
        color: var(--cv-muted); font-size: 1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all .2s;
    }
    #vozClearBtn:hover { border-color: #EF4444; color: #DC2626; }

    /* Tip */
    #vozTip {
        font-size: .67rem; color: var(--cv-muted);
        text-align: center; padding: 0 14px 10px;
    }

    /* Tooltip del botón voz */
    #vozTooltip {
        position: fixed; bottom: 100px; left: 28px; z-index: 1098;
        background: var(--cv-text); color: #fff;
        font-size: 12px; font-weight: 500;
        padding: 6px 12px; border-radius: var(--cv-radius-sm);
        white-space: nowrap; pointer-events: none;
        opacity: 0; transform: translateY(4px);
        transition: opacity .2s, transform .2s;
    }
    #vozTooltip.show { opacity: 1; transform: translateY(0); }

    @media (max-width: 480px) {
        #vozPanel, #aiPanel { left: 12px; width: calc(100vw - 24px); }
        #vozToggleBtn  { left: 16px;  bottom: 20px; }
        #aiToggleBtn   { left: 90px;  bottom: 20px; }
        #vozTooltip    { left: 16px; }
        #aiTooltip     { left: 90px; }
    }
    </style>

    <!-- Tooltip voz -->
    <div id="vozTooltip">🎤 Dictado por voz</div>

    <!-- Botón flotante voz -->
    <button id="vozToggleBtn" title="Dictar denuncia por voz" aria-label="Abrir dictado por voz">
        <span id="vozBadge">VOZ</span>
        <svg viewBox="0 0 24 24">
            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
            <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
            <line x1="12" y1="19" x2="12" y2="23"/>
            <line x1="8"  y1="23" x2="16" y2="23"/>
        </svg>
    </button>

    <!-- Panel de voz -->
    <div id="vozPanel" role="dialog" aria-label="Dictado por voz">

        <div id="vozHeader">
            <div id="vozHeaderIcon">🎤</div>
            <div id="vozHeaderInfo">
                <strong>Dictado por Voz</strong>
                <small>Habla y tu denuncia se escribe sola</small>
            </div>
            <button id="vozCloseBtn" title="Cerrar" aria-label="Cerrar">✕</button>
        </div>

        <div id="vozNoSupport">
            ⚠️ Tu navegador no soporta dictado por voz. Usa <strong>Google Chrome</strong> o <strong>Microsoft Edge</strong>.
        </div>

        <div id="vozLangSelector">
            <button class="voz-lang-btn active" data-lang="es-CO">🇨🇴 Español</button>
            <button class="voz-lang-btn" data-lang="es-ES">🇪🇸 Castellano</button>
            <button class="voz-lang-btn" data-lang="en-US">🇺🇸 English</button>
            <button class="voz-lang-btn" data-lang="fr-FR">🇫🇷 Français</button>
        </div>

        <div id="vozTargetSelector">
            <label>¿Dónde quieres escribir?</label>
            <select id="vozTargetSelect">
                <option value="descripcion">📝 Descripción del incidente</option>
                <option value="nombre">👤 Tu nombre</option>
                <option value="direccion">📍 Dirección del lugar</option>
                <option value="contacto">📞 Teléfono de contacto</option>
            </select>
        </div>

        <div id="vozDisplay">
            <span id="vozPlaceholder">Presiona "Iniciar" y comienza a hablar...</span>
            <span id="vozTexto"></span>
            <span id="vozTextoInterim"></span>
        </div>

        <div id="vozWave">
            <span></span><span></span><span></span><span></span><span></span>
        </div>

        <div id="vozStatus">Listo para escuchar</div>

        <div id="vozControls">
            <button id="vozStartBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                </svg>
                Iniciar dictado
            </button>
            <button id="vozApplyBtn" disabled title="Aplicar texto al formulario">✅ Aplicar</button>
            <button id="vozClearBtn" title="Limpiar texto">🗑️</button>
        </div>

        <div id="vozTip">🌐 Funciona mejor en Chrome · Requiere micrófono</div>
    </div>

    <script>
    (function () {
        const toggleBtn   = document.getElementById('vozToggleBtn');
        const panel       = document.getElementById('vozPanel');
        const closeBtn    = document.getElementById('vozCloseBtn');
        const startBtn    = document.getElementById('vozStartBtn');
        const applyBtn    = document.getElementById('vozApplyBtn');
        const clearBtn    = document.getElementById('vozClearBtn');
        const display     = document.getElementById('vozDisplay');
        const placeholder = document.getElementById('vozPlaceholder');
        const textoEl     = document.getElementById('vozTexto');
        const interimEl   = document.getElementById('vozTextoInterim');
        const statusEl    = document.getElementById('vozStatus');
        const waveEl      = document.getElementById('vozWave');
        const noSupport   = document.getElementById('vozNoSupport');
        const targetSel   = document.getElementById('vozTargetSelect');
        const langBtns    = document.querySelectorAll('.voz-lang-btn');
        const tooltip     = document.getElementById('vozTooltip');

        let recognition = null;
        let grabando    = false;
        let textoFinal  = '';
        let langActual  = 'es-CO';

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            noSupport.style.display = 'block';
            startBtn.disabled = true;
            setStatus('Tu navegador no soporta dictado por voz', 'error');
        }

        /* ── Tooltip ── */
        toggleBtn.addEventListener('mouseenter', () => tooltip.classList.add('show'));
        toggleBtn.addEventListener('mouseleave', () => tooltip.classList.remove('show'));

        /* ── Abrir / cerrar ── */
        toggleBtn.addEventListener('click', () => {
            tooltip.classList.remove('show');
            panel.classList.toggle('open');
            if (panel.classList.contains('open')) {
                document.getElementById('vozBadge').style.display = 'none';
            }
        });
        closeBtn.addEventListener('click', () => {
            panel.classList.remove('open');
            if (grabando) detener();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && panel.classList.contains('open')) {
                panel.classList.remove('open');
                if (grabando) detener();
            }
        });

        /* ── Selector de idioma ── */
        langBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                langBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                langActual = btn.dataset.lang;
                if (grabando) { detener(); setTimeout(iniciar, 300); }
            });
        });

        /* ── Iniciar / Detener ── */
        startBtn.addEventListener('click', () => { grabando ? detener() : iniciar(); });

        function iniciar() {
            if (!SpeechRecognition) return;
            recognition = new SpeechRecognition();
            recognition.lang            = langActual;
            recognition.continuous      = true;
            recognition.interimResults  = true;
            recognition.maxAlternatives = 1;

            recognition.onstart = () => {
                grabando = true;
                startBtn.classList.add('grabando');
                startBtn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                        <rect x="6" y="6" width="12" height="12" rx="2"/>
                    </svg>
                    Detener
                `;
                waveEl.classList.add('show');
                display.classList.add('escuchando');
                display.classList.remove('listo');
                setStatus('🔴 Escuchando... habla ahora', 'grabando');
                placeholder.style.display = 'none';
                textoEl.style.display     = 'inline';
            };

            recognition.onresult = (e) => {
                let interim = '', final = '';
                for (let i = e.resultIndex; i < e.results.length; i++) {
                    const t = e.results[i][0].transcript;
                    if (e.results[i].isFinal) final += t + ' ';
                    else interim += t;
                }
                if (final) {
                    textoFinal += final;
                    textoEl.textContent = textoFinal;
                    applyBtn.disabled = false;
                }
                interimEl.textContent = interim;
            };

            recognition.onerror = (e) => {
                const errores = {
                    'no-speech'    : '⚠️ No detecté voz. Intenta de nuevo.',
                    'audio-capture': '⚠️ No se puede acceder al micrófono.',
                    'not-allowed'  : '🔒 Permiso de micrófono denegado. Habilítalo en tu navegador.',
                    'network'      : '🌐 Error de red. Revisa tu conexión.',
                    'aborted'      : '',
                };
                const msg = errores[e.error] || ('Error: ' + e.error);
                if (msg) setStatus(msg, 'error');
                detener(false);
            };

            recognition.onend = () => {
                if (grabando) {
                    try { recognition.start(); } catch(err) { detener(); }
                }
            };

            try { recognition.start(); }
            catch(err) { setStatus('Error al iniciar el micrófono', 'error'); }
        }

        function detener(limpiarInterim = true) {
            grabando = false;
            if (recognition) { try { recognition.stop(); } catch(e){} }

            startBtn.classList.remove('grabando');
            startBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                </svg>
                Iniciar dictado
            `;
            waveEl.classList.remove('show');
            if (limpiarInterim) interimEl.textContent = '';

            if (textoFinal.trim()) {
                display.classList.remove('escuchando');
                display.classList.add('listo');
                setStatus('✅ Dictado listo — presiona "Aplicar"', 'listo');
            } else {
                display.classList.remove('escuchando', 'listo');
                setStatus('Listo para escuchar', '');
                placeholder.style.display = 'inline';
                textoEl.style.display     = 'none';
            }
        }

        /* ── Aplicar al formulario ── */
        applyBtn.addEventListener('click', () => {
            const campo = document.getElementById(targetSel.value);
            if (!campo || !textoFinal.trim()) return;

            const textoLimpio = textoFinal.trim();
            if (campo.tagName === 'TEXTAREA') {
                const sep = campo.value.trim() ? ' ' : '';
                campo.value += sep + textoLimpio;
            } else {
                campo.value = textoLimpio;
            }

            campo.dispatchEvent(new Event('input', { bubbles: true }));
            campo.scrollIntoView({ behavior: 'smooth', block: 'center' });
            campo.focus();

            if (typeof window.showToast === 'function') {
                window.showToast('🎤 Texto dictado aplicado al formulario', 'success');
            }

            applyBtn.textContent = '✔ Aplicado';
            applyBtn.style.cssText = 'background:var(--cv-green);color:#fff;border-color:var(--cv-green);';
            setTimeout(() => {
                applyBtn.textContent   = '✅ Aplicar';
                applyBtn.style.cssText = '';
            }, 2000);

            setTimeout(() => panel.classList.remove('open'), 800);
        });

        /* ── Limpiar ── */
        clearBtn.addEventListener('click', () => {
            textoFinal = '';
            textoEl.textContent     = '';
            interimEl.textContent   = '';
            textoEl.style.display   = 'none';
            placeholder.style.display = 'inline';
            display.classList.remove('escuchando', 'listo');
            applyBtn.disabled = true;
            setStatus('Listo para escuchar', '');
            if (grabando) detener();
        });

        function setStatus(msg, tipo) {
            statusEl.textContent = msg;
            statusEl.className   = tipo || '';
        }
    })();
    </script>

    <!-- ══════════════════════════════════════════════════════
         ASISTENTE IA — ASIS (ChocoVisible)
         Integrado desde asistente-ia.php con tokens cv-*
    ══════════════════════════════════════════════════════ -->
    <style>
    /* ── Botón flotante IA — se mueve a la derecha del de voz ── */
    #aiToggleBtn {
        position: fixed; bottom: 28px; left: 104px; z-index: 1100;
        width: 62px; height: 62px; border-radius: 50%; border: none; cursor: pointer;
        background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
        box-shadow: 0 6px 24px rgba(26,102,54,.38);
        display: flex; align-items: center; justify-content: center;
        transition: transform .25s, box-shadow .25s;
    }
    #aiToggleBtn:hover { transform: scale(1.1); box-shadow: 0 10px 36px rgba(26,102,54,.48); }
    #aiToggleBtn svg  { width: 26px; height: 26px; fill: none; stroke: #fff; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

    #aiBadge {
        position: absolute; top: -4px; right: -4px;
        background: var(--cv-gold-light); color: var(--cv-blue);
        font-size: .62rem; font-weight: 800;
        border-radius: 20px; padding: 2px 7px; white-space: nowrap;
        animation: aiBadgePulse 2s infinite;
        border: 1.5px solid var(--cv-white);
    }
    @keyframes aiBadgePulse {
        0%,100% { box-shadow: 0 0 0 0 rgba(245,200,66,.55); }
        50%      { box-shadow: 0 0 0 7px rgba(245,200,66,0); }
    }

    /* ── Panel ── */
    #aiPanel {
        position: fixed; bottom: 106px; left: 104px; z-index: 1099;
        width: min(400px, calc(100vw - 48px));
        border-radius: 18px;
        background: var(--cv-white);
        box-shadow: 0 12px 48px rgba(12,52,96,.22), 0 2px 8px rgba(0,0,0,.08);
        display: flex; flex-direction: column;
        max-height: min(570px, calc(100vh - 150px));
        overflow: hidden;
        border: 1px solid var(--cv-border);
        transform: scale(.92) translateY(20px);
        opacity: 0; pointer-events: none;
        transition: transform .28s cubic-bezier(.34,1.56,.64,1), opacity .22s ease;
    }
    #aiPanel.open { transform: scale(1) translateY(0); opacity: 1; pointer-events: all; }

    /* ── Header ── */
    #aiHeader {
        background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
        color: #fff; padding: 14px 16px;
        display: flex; align-items: center; gap: 10px; flex-shrink: 0;
    }
    #aiAvatar {
        width: 42px; height: 42px; border-radius: 50%;
        background: rgba(255,255,255,.18);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
        border: 1.5px solid rgba(255,255,255,.25);
    }
    #aiHeaderInfo { flex: 1; }
    #aiHeaderInfo strong { display: block; font-size: .93rem; font-weight: 700; letter-spacing: .2px; }
    #aiHeaderInfo small  { font-size: .72rem; opacity: .85; display: flex; align-items: center; gap: 4px; }
    #aiOnlineDot {
        width: 7px; height: 7px; background: #4ade80; border-radius: 50%;
        display: inline-block; animation: blink 1.6s infinite; flex-shrink: 0;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
    #aiCloseBtn {
        background: none; border: none; color: #fff; font-size: 1.2rem;
        cursor: pointer; line-height: 1; padding: 6px; border-radius: 8px;
        transition: background .2s;
    }
    #aiCloseBtn:hover { background: rgba(255,255,255,.15); }

    /* ── Mensajes ── */
    #aiMessages {
        flex: 1; overflow-y: auto; padding: 14px 14px 0;
        display: flex; flex-direction: column; gap: 10px;
        scrollbar-width: thin; scrollbar-color: var(--cv-green-mid) transparent;
    }
    #aiMessages::-webkit-scrollbar { width: 4px; }
    #aiMessages::-webkit-scrollbar-thumb { background: var(--cv-green-mid); border-radius: 4px; }

    .ai-msg { max-width: 88%; display: flex; flex-direction: column; gap: 3px; }
    .ai-msg.bot  { align-self: flex-start; }
    .ai-msg.user { align-self: flex-end; align-items: flex-end; }

    .ai-bubble {
        padding: 9px 13px; border-radius: 14px;
        font-size: .84rem; line-height: 1.55; word-break: break-word;
    }
    .ai-msg.bot  .ai-bubble { background: var(--cv-off); color: var(--cv-text); border-bottom-left-radius: 4px; border: 1px solid var(--cv-border); }
    .ai-msg.user .ai-bubble { background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid)); color: #fff; border-bottom-right-radius: 4px; }
    .ai-time { font-size: .64rem; color: var(--cv-text-muted); padding: 0 4px; }

    /* ── Typing ── */
    #aiTyping { display: none; align-self: flex-start; padding: 0 4px 2px; }
    #aiTyping.show { display: flex; }
    .ai-dots {
        display: flex; gap: 4px;
        background: var(--cv-off); padding: 10px 14px;
        border-radius: 14px; border-bottom-left-radius: 4px;
        border: 1px solid var(--cv-border);
    }
    .ai-dots span {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--cv-green-mid); animation: dotBounce .9s infinite;
    }
    .ai-dots span:nth-child(2) { animation-delay: .15s; }
    .ai-dots span:nth-child(3) { animation-delay: .30s; }
    @keyframes dotBounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-7px)} }

    /* ── Sugerencias ── */
    #aiSuggestions { padding: 8px 14px 0; display: flex; flex-wrap: wrap; gap: 6px; flex-shrink: 0; }
    .ai-suggestion {
        background: var(--cv-off); border: 1.5px solid var(--cv-border);
        color: var(--cv-green); font-size: .74rem; font-weight: 600;
        padding: 5px 10px; border-radius: 20px; cursor: pointer;
        transition: all .18s; white-space: nowrap;
    }
    .ai-suggestion:hover { background: var(--cv-green); color: #fff; border-color: var(--cv-green); }

    /* ── Tarjeta de datos estructurados ── */
    .ai-data-card {
        background: linear-gradient(135deg, var(--cv-success-bg), #EFF6FF);
        border: 1.5px solid #86EFAC; border-radius: var(--cv-radius-sm);
        padding: 11px 13px; margin-top: 6px; font-size: .8rem;
    }
    .ai-data-card strong { color: var(--cv-green); font-size: .85rem; display: block; margin-bottom: 5px; }
    .ai-data-card p { margin: 3px 0; color: var(--cv-text-2); line-height: 1.45; }
    .ai-apply-btn {
        margin-top: 9px; width: 100%;
        background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
        color: #fff; border: none; border-radius: var(--cv-radius-sm);
        padding: 8px; font-size: .8rem; font-weight: 700; cursor: pointer;
        transition: opacity .2s; display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .ai-apply-btn:hover { opacity: .87; }

    /* ── Input area ── */
    #aiInputArea {
        padding: 10px 12px 12px; border-top: 1px solid var(--cv-border);
        display: flex; gap: 8px; align-items: flex-end; flex-shrink: 0;
        background: var(--cv-white);
    }
    #aiInput {
        flex: 1; border: 1.5px solid var(--cv-border); border-radius: var(--cv-radius-sm);
        padding: 9px 12px; font-size: .84rem; resize: none;
        max-height: 96px; min-height: 38px; outline: none;
        font-family: 'Inter', sans-serif; color: var(--cv-text);
        background: var(--cv-off);
        transition: border-color .2s, background .2s;
    }
    #aiInput:focus { border-color: var(--cv-green-mid); background: #fff; }
    #aiInput::placeholder { color: var(--cv-text-muted); }

    #aiSendBtn {
        width: 40px; height: 40px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
        border: none; border-radius: var(--cv-radius-sm); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 3px 10px rgba(26,102,54,.28);
        transition: transform .18s, opacity .2s;
    }
    #aiSendBtn:hover   { transform: scale(1.08); }
    #aiSendBtn:disabled{ opacity: .5; cursor: default; transform: none; }
    #aiSendBtn svg { width: 17px; height: 17px; fill: none; stroke: #fff; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; }

    #aiDisclaimer {
        font-size: .65rem; color: var(--cv-text-muted);
        text-align: center; padding: 4px 12px 8px; flex-shrink: 0;
        background: var(--cv-white);
    }

    /* ── Tooltip del botón ── */
    #aiTooltip {
        position: fixed; bottom: 100px; left: 104px; z-index: 1098;
        background: var(--cv-text); color: #fff;
        font-size: 12px; font-weight: 500;
        padding: 6px 12px; border-radius: var(--cv-radius-sm);
        white-space: nowrap; pointer-events: none;
        opacity: 0; transform: translateX(0) translateY(4px);
        transition: opacity .2s, transform .2s;
    }
    #aiTooltip.show { opacity: 1; transform: translateX(0) translateY(0); }

    @media (max-width: 480px) {
        #aiPanel { left: 12px; bottom: 100px; width: calc(100vw - 24px); }
        #aiToggleBtn { left: 16px; bottom: 20px; }
        #aiTooltip { left: 16px; }
    }
    </style>

    <!-- Tooltip del botón -->
    <div id="aiTooltip">🤖 Asis — Asistente IA</div>

    <!-- Botón flotante -->
    <button id="aiToggleBtn" title="Asistente IA para tu denuncia" aria-label="Abrir asistente IA">
        <span id="aiBadge">IA</span>
        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </button>

    <!-- Panel del asistente -->
    <div id="aiPanel" role="dialog" aria-label="Asistente IA Asis">

        <div id="aiHeader">
            <div id="aiAvatar">🤖</div>
            <div id="aiHeaderInfo">
                <strong>Asis — Asistente IA</strong>
                <small><span id="aiOnlineDot"></span>En línea · ChocoVisible</small>
            </div>
            <button id="aiCloseBtn" title="Cerrar asistente" aria-label="Cerrar">✕</button>
        </div>

        <div id="aiMessages">
            <div class="ai-msg bot">
                <div class="ai-bubble">
                    👋 Hola, soy <strong>Asis</strong>, tu asistente de IA para redactar denuncias.<br><br>
                    Cuéntame qué pasó con tus propias palabras y yo te ayudo a organizar la información para el formulario. ¿Qué incidente quieres reportar?
                </div>
                <span class="ai-time" id="aiWelcomeTime"></span>
            </div>
            <div id="aiTyping">
                <div class="ai-dots"><span></span><span></span><span></span></div>
            </div>
        </div>

        <div id="aiSuggestions">
            <button class="ai-suggestion" data-text="Quiero reportar un caso de acoso laboral">🚫 Acoso laboral</button>
            <button class="ai-suggestion" data-text="Quiero denunciar un problema ambiental">🌿 Problema ambiental</button>
            <button class="ai-suggestion" data-text="Tengo información sobre un acto de corrupción">💼 Corrupción</button>
            <button class="ai-suggestion" data-text="Quiero reportar un problema de seguridad">🛡️ Seguridad</button>
        </div>

        <div id="aiInputArea">
            <textarea id="aiInput" placeholder="Escribe aquí lo que pasó..." rows="1" aria-label="Mensaje al asistente"></textarea>
            <button id="aiSendBtn" title="Enviar mensaje" aria-label="Enviar">
                <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </div>
        <div id="aiDisclaimer">🔒 Conversación segura · No almacenamos tus mensajes</div>
    </div>

    <script>
    /* ══════════════════════════════════════════════
       ASIS — Lógica del asistente IA
    ══════════════════════════════════════════════ */
    (function(){
        const historial = [];
        let esperando   = false;

        const btn      = document.getElementById('aiToggleBtn');
        const panel    = document.getElementById('aiPanel');
        const closeBtn = document.getElementById('aiCloseBtn');
        const msgs     = document.getElementById('aiMessages');
        const typing   = document.getElementById('aiTyping');
        const input    = document.getElementById('aiInput');
        const sendBtn  = document.getElementById('aiSendBtn');
        const suggs    = document.getElementById('aiSuggestions');
        const tooltip  = document.getElementById('aiTooltip');

        document.getElementById('aiWelcomeTime').textContent = horaFmt();

        /* ── Tooltip hover ── */
        btn.addEventListener('mouseenter', () => tooltip.classList.add('show'));
        btn.addEventListener('mouseleave', () => tooltip.classList.remove('show'));

        /* ── Abrir / cerrar panel ── */
        btn.addEventListener('click', () => {
            tooltip.classList.remove('show');
            panel.classList.toggle('open');
            if (panel.classList.contains('open')) {
                document.getElementById('aiBadge').style.display = 'none';
                input.focus();
                scrollBottom();
            }
        });
        closeBtn.addEventListener('click', () => panel.classList.remove('open'));

        /* Cerrar con Escape */
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && panel.classList.contains('open')) panel.classList.remove('open');
        });

        /* ── Sugerencias ── */
        suggs.querySelectorAll('.ai-suggestion').forEach(s => {
            s.addEventListener('click', () => {
                input.value = s.dataset.text;
                enviar();
            });
        });

        /* ── Enviar ── */
        sendBtn.addEventListener('click', enviar);
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviar(); }
        });
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 96) + 'px';
        });

        async function enviar() {
            const texto = input.value.trim();
            if (!texto || esperando) return;

            agregarMensaje('user', texto);
            historial.push({ role: 'user', content: texto });
            input.value = '';
            input.style.height = 'auto';
            suggs.style.display = 'none';

            esperando = true;
            sendBtn.disabled = true;
            mostrarTyping(true);

            try {
                const res = await fetch('asistente-ia.php', {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ mensaje: texto, historial: historial.slice(0, -1) })
                });
                const data = await res.json();
                mostrarTyping(false);

                if (data.error) {
                    agregarMensaje('bot', '⚠️ ' + data.error);
                } else {
                    agregarMensaje('bot', data.respuesta, data.datos);
                    historial.push({
                        role: 'assistant',
                        content: data.respuesta + (data.datos ? JSON.stringify(data.datos) : '')
                    });
                }
            } catch (e) {
                mostrarTyping(false);
                agregarMensaje('bot', '⚠️ No pude conectarme con el asistente. Por favor intenta de nuevo.');
            }

            esperando = false;
            sendBtn.disabled = false;
            input.focus();
        }

        function agregarMensaje(rol, texto, datos = null) {
            const wrap   = document.createElement('div');
            wrap.className = 'ai-msg ' + rol;

            const bubble = document.createElement('div');
            bubble.className = 'ai-bubble';
            bubble.innerHTML = fmtTexto(texto);
            wrap.appendChild(bubble);

            /* Tarjeta de datos estructurados */
            if (datos && rol === 'bot') {
                const card = document.createElement('div');
                card.className = 'ai-data-card';
                const tipos = {
                    acoso:'🚫 Acoso o Intimidación', seguridad:'🛡️ Seguridad',
                    etico:'⚖️ Ético',               discriminacion:'🤝 Discriminación',
                    corrupcion:'💼 Corrupción',      laboral:'💔 Laboral',
                    ambiental:'🌿 Ambiental',        servicios:'💧 Servicios Públicos',
                    otro:'📋 Otro'
                };
                const urgs = { baja:'🟢 Baja', media:'🟡 Media', alta:'🔴 Alta' };
                const desc = datos.descripcion || '';
                card.innerHTML = `
                    <strong>✅ Denuncia lista para el formulario</strong>
                    <p><b>Tipo:</b> ${tipos[datos.tipo] || datos.tipo}</p>
                    <p><b>Urgencia:</b> ${urgs[datos.urgencia] || datos.urgencia}</p>
                    <p><b>Descripción:</b> ${desc.substring(0,120)}${desc.length > 120 ? '…' : ''}</p>
                    <button class="ai-apply-btn" onclick="aplicarAlFormulario(${JSON.stringify(datos).replace(/"/g,'&quot;')})">
                        <i class="fas fa-bolt"></i> Rellenar formulario automáticamente
                    </button>`;
                wrap.appendChild(card);
            }

            const time = document.createElement('span');
            time.className  = 'ai-time';
            time.textContent = horaFmt();
            wrap.appendChild(time);

            msgs.insertBefore(wrap, typing);
            scrollBottom();
        }

        function mostrarTyping(show) {
            typing.classList.toggle('show', show);
            scrollBottom();
        }

        function scrollBottom() {
            requestAnimationFrame(() => { msgs.scrollTop = msgs.scrollHeight; });
        }

        function horaFmt() {
            return new Date().toLocaleTimeString('es-CO', { hour:'2-digit', minute:'2-digit' });
        }

        function fmtTexto(txt) {
            return txt
                .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                .replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>')
                .replace(/\n/g,'<br>');
        }
    })();

    /* ══ Aplicar datos al formulario (global) ══ */
    function aplicarAlFormulario(datos) {
        /* Tipo */
        const sel = document.getElementById('tipo');
        if (sel) {
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value === datos.tipo) { sel.selectedIndex = i; break; }
            }
        }
        /* Descripción */
        const desc = document.getElementById('descripcion');
        if (desc) {
            desc.value = datos.descripcion;
            desc.dispatchEvent(new Event('input'));
        }
        /* Urgencia */
        const urg = document.getElementById('urgencia');
        if (urg) {
            for (let i = 0; i < urg.options.length; i++) {
                if (urg.options[i].value === datos.urgencia) { urg.selectedIndex = i; break; }
            }
        }

        /* Cerrar panel y scroll al formulario */
        document.getElementById('aiPanel').classList.remove('open');
        const sec1 = document.querySelector('[data-step="1"]');
        if (sec1) sec1.scrollIntoView({ behavior: 'smooth', block: 'start' });

        /* Notificación */
        if (typeof window.showToast === 'function') {
            window.showToast('✅ Formulario rellenado con la ayuda de Asis IA', 'success');
        }
    }
    </script>
    
</body>
</html>