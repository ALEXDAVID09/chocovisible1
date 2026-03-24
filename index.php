<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ChocoVisible — Sistema seguro de denuncia ciudadana para el Departamento del Chocó, Colombia.">
    <title>ChocoVisible · Denuncia Ciudadana</title>
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
            --cv-radius:       16px;
            --cv-radius-sm:    10px;
            --cv-shadow:       0 4px 24px rgba(0,0,0,.07);
            --cv-shadow-lg:    0 16px 48px rgba(12,52,96,.14);
        }

        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--cv-text);
            background: var(--cv-white);
            overflow-x: hidden;
        }

        /* ══════════════════════════════
           NAVBAR
        ══════════════════════════════ */
        .cv-navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--cv-border);
            padding: 0;
            transition: box-shadow .3s;
        }
        .cv-navbar.scrolled {
            box-shadow: 0 2px 20px rgba(0,0,0,.1);
        }
        .cv-navbar .container {
            display: flex; align-items: center;
            height: 68px; gap: 32px;
        }

        /* Logo */
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; flex-shrink: 0;
        }
        .nav-logo img {
            height: 40px; width: 40px;
            object-fit: contain;
        }
        .nav-logo-text {
            font-size: 20px; font-weight: 700;
            line-height: 1; letter-spacing: -.4px;
        }
        .nav-logo-text .choco  { color: var(--cv-green); }
        .nav-logo-text .visible { color: var(--cv-blue); }

        /* Links */
        .nav-links {
            display: flex; align-items: center; gap: 4px;
            margin-left: auto; list-style: none;
        }
        .nav-links .nav-link {
            font-size: 14.5px; font-weight: 500;
            color: var(--cv-text-2);
            padding: 8px 14px;
            border-radius: var(--cv-radius-sm);
            text-decoration: none;
            transition: color .2s, background .2s;
            white-space: nowrap;
        }
        .nav-links .nav-link:hover {
            color: var(--cv-green);
            background: rgba(26,102,54,.07);
        }
        .nav-links .nav-link.active {
            color: var(--cv-green);
            font-weight: 600;
        }

        /* Botón CTA navbar */
        .nav-cta {
            display: inline-flex; align-items: center; gap: 7px;
            background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
            color: #fff !important;
            padding: 9px 18px;
            border-radius: 50px;
            font-size: 14px; font-weight: 600;
            text-decoration: none;
            transition: opacity .2s, transform .15s;
            white-space: nowrap;
            flex-shrink: 0;
            box-shadow: 0 3px 12px rgba(26,102,54,.3);
        }
        .nav-cta:hover { opacity: .88; transform: translateY(-1px); }

        /* Hamburguesa */
        .nav-toggle {
            display: none;
            background: none; border: 1.5px solid var(--cv-border);
            border-radius: 8px; padding: 7px 9px;
            cursor: pointer; margin-left: auto; flex-shrink: 0;
            color: var(--cv-text-2); font-size: 16px;
            transition: border-color .2s;
        }
        .nav-toggle:hover { border-color: var(--cv-green); color: var(--cv-green); }

        /* Drawer móvil */
        .nav-drawer {
            display: none;
            flex-direction: column;
            gap: 4px;
            background: var(--cv-white);
            border-top: 1px solid var(--cv-border);
            padding: 16px 20px 20px;
        }
        .nav-drawer .nav-link {
            font-size: 15px; font-weight: 500;
            color: var(--cv-text-2);
            padding: 11px 14px;
            border-radius: var(--cv-radius-sm);
            text-decoration: none;
            display: flex; align-items: center; gap: 8px;
            transition: background .2s, color .2s;
        }
        .nav-drawer .nav-link:hover { background: var(--cv-off); color: var(--cv-green); }
        .nav-drawer .nav-cta { margin-top: 8px; justify-content: center; }

        @media (max-width: 991px) {
            .nav-links, .nav-cta { display: none; }
            .nav-toggle { display: flex; align-items: center; }
            .nav-drawer.open { display: flex; }
        }

        /* ══════════════════════════════
           HERO
        ══════════════════════════════ */
        .hero {
            min-height: 100vh;
            padding: 120px 0 80px;
            display: flex; align-items: center;
            position: relative; overflow: hidden;
            background: linear-gradient(150deg, var(--cv-blue) 0%, var(--cv-blue-mid) 35%, var(--cv-green) 100%);
        }

        /* Patrón de puntos */
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,.08) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* Ola inferior */
        .hero-wave {
            position: absolute; bottom: -1px; left: 0; right: 0;
            line-height: 0;
        }
        .hero-wave svg { display: block; width: 100%; }

        /* Orbes decorativos */
        .hero-orb {
            position: absolute; border-radius: 50%; filter: blur(60px); pointer-events: none;
        }
        .hero-orb-1 {
            width: 500px; height: 500px;
            top: -100px; right: -100px;
            background: rgba(50,176,96,.18);
        }
        .hero-orb-2 {
            width: 400px; height: 400px;
            bottom: 60px; left: -80px;
            background: rgba(26,115,214,.18);
        }

        .hero-content { position: relative; z-index: 2; }

        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.9);
            padding: 7px 16px; border-radius: 50px;
            font-size: 13px; font-weight: 500;
            margin-bottom: 28px;
            backdrop-filter: blur(4px);
        }
        .hero-eyebrow i { color: var(--cv-gold-light); font-size: 12px; }

        .hero-title {
            font-size: clamp(2.2rem, 5.5vw, 3.8rem);
            font-weight: 800;
            color: var(--cv-white);
            line-height: 1.15;
            letter-spacing: -.5px;
            margin-bottom: 22px;
        }
        .hero-title .highlight { color: var(--cv-gold-light); }

        .hero-subtitle {
            font-size: clamp(1rem, 2.3vw, 1.2rem);
            color: rgba(255,255,255,.82);
            line-height: 1.7;
            margin-bottom: 40px;
            max-width: 560px;
        }

        .hero-btns {
            display: flex; flex-wrap: wrap; gap: 14px;
            justify-content: center;
        }

        .btn-hero-primary {
            display: inline-flex; align-items: center; gap: 9px;
            background: linear-gradient(135deg, var(--cv-gold), var(--cv-gold-light));
            color: var(--cv-text) !important;
            font-size: 15.5px; font-weight: 700;
            padding: 16px 30px; border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(232,160,32,.35);
            transition: transform .2s, box-shadow .2s;
            animation: pulse 2.5s ease-in-out infinite;
        }
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(232,160,32,.45);
        }

        .btn-hero-secondary {
            display: inline-flex; align-items: center; gap: 9px;
            border: 2px solid rgba(255,255,255,.5);
            color: var(--cv-white) !important;
            font-size: 15.5px; font-weight: 600;
            padding: 14px 28px; border-radius: 50px;
            text-decoration: none;
            backdrop-filter: blur(4px);
            transition: background .2s, border-color .2s, transform .15s;
        }
        .btn-hero-secondary:hover {
            background: rgba(255,255,255,.15);
            border-color: rgba(255,255,255,.8);
            transform: translateY(-2px);
        }

        /* Stats bar */
        .hero-stats {
            display: flex; gap: 32px; flex-wrap: wrap;
            justify-content: center;
            margin-top: 56px;
        }
        .stat-item { text-align: center; }
        .stat-num {
            font-size: 26px; font-weight: 800;
            color: var(--cv-white); line-height: 1;
            margin-bottom: 4px;
        }
        .stat-num span { color: var(--cv-gold-light); }
        .stat-label {
            font-size: 12px; font-weight: 500;
            color: rgba(255,255,255,.65);
            text-transform: uppercase; letter-spacing: .5px;
        }
        .stat-divider {
            width: 1px; background: rgba(255,255,255,.2);
            align-self: stretch; margin: 4px 0;
        }

        /* ══════════════════════════════
           SECCIONES GENÉRICAS
        ══════════════════════════════ */
        .section { padding: clamp(64px, 9vw, 100px) 0; }
        .section-alt { background: var(--cv-off); }

        .section-eyebrow {
            display: inline-flex; align-items: center; gap: 6px;
            background: linear-gradient(135deg, rgba(26,102,54,.1), rgba(19,78,155,.1));
            color: var(--cv-green);
            padding: 5px 14px; border-radius: 50px;
            font-size: 12px; font-weight: 600;
            letter-spacing: .4px; text-transform: uppercase;
            margin-bottom: 14px;
        }

        .section-title {
            font-size: clamp(1.8rem, 3.8vw, 2.6rem);
            font-weight: 800; color: var(--cv-text);
            line-height: 1.2; letter-spacing: -.3px;
            margin-bottom: 14px;
        }

        .section-subtitle {
            font-size: clamp(.95rem, 2vw, 1.05rem);
            color: var(--cv-text-muted);
            line-height: 1.7; max-width: 560px; margin: 0 auto;
        }

        /* ══════════════════════════════
           CÓMO FUNCIONA
        ══════════════════════════════ */
        .process-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px; margin-top: 56px;
        }
        @media (max-width: 768px) { .process-grid { grid-template-columns: 1fr; } }

        .process-card {
            background: var(--cv-white);
            border: 1px solid var(--cv-border);
            border-radius: var(--cv-radius);
            padding: 36px 28px 32px;
            text-align: center;
            position: relative; overflow: hidden;
            transition: transform .3s, box-shadow .3s;
        }
        .process-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--cv-green), var(--cv-gold-light), var(--cv-blue-mid));
        }
        .process-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--cv-shadow-lg);
        }

        .process-num {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
            color: #fff;
            font-size: 20px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 22px;
            box-shadow: 0 4px 14px rgba(26,102,54,.28);
        }

        .process-icon {
            font-size: 2.4rem;
            background: linear-gradient(135deg, var(--cv-blue-mid), var(--cv-blue-light));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 18px;
        }

        .process-card h5 {
            font-size: 17px; font-weight: 700;
            color: var(--cv-text); margin-bottom: 10px;
        }
        .process-card p {
            font-size: 14.5px; color: var(--cv-text-muted); line-height: 1.65;
        }

        /* ══════════════════════════════
           TIPOS DE DENUNCIAS
        ══════════════════════════════ */
        .types-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px; margin-top: 56px;
        }
        @media (max-width: 992px) { .types-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px)  { .types-grid { grid-template-columns: 1fr; } }

        .type-card {
            background: var(--cv-white);
            border: 1.5px solid var(--cv-border);
            border-radius: var(--cv-radius);
            padding: 28px 20px;
            text-align: center;
            transition: border-color .25s, transform .25s, box-shadow .25s;
            cursor: default;
        }
        .type-card:hover {
            border-color: var(--cv-gold);
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(232,160,32,.15);
        }
        .type-icon-wrap {
            width: 60px; height: 60px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px; font-size: 22px;
        }
        .type-card h6 { font-size: 15px; font-weight: 700; margin-bottom: 6px; color: var(--cv-text); }
        .type-card small { font-size: 12.5px; color: var(--cv-text-muted); line-height: 1.5; }

        /* ══════════════════════════════
           CONFIDENCIALIDAD
        ══════════════════════════════ */
        .conf-section {
            background: linear-gradient(150deg, var(--cv-blue) 0%, var(--cv-blue-mid) 40%, var(--cv-green) 100%);
            padding: clamp(64px, 9vw, 100px) 0;
            position: relative; overflow: hidden;
        }
        .conf-section::before {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .conf-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px; margin-top: 52px;
        }
        @media (max-width: 768px) { .conf-grid { grid-template-columns: 1fr; } }

        .conf-card {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: var(--cv-radius);
            padding: 36px 28px;
            text-align: center;
            backdrop-filter: blur(6px);
            transition: background .3s, transform .3s;
            animation: floatY 4s ease-in-out infinite;
        }
        .conf-card:nth-child(2) { animation-delay: .5s; }
        .conf-card:nth-child(3) { animation-delay: 1s;  }
        .conf-card:hover { background: rgba(255,255,255,.17); transform: translateY(-6px); }

        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-8px); }
        }

        .conf-icon {
            font-size: 2.6rem;
            color: var(--cv-gold-light);
            margin-bottom: 18px;
        }
        .conf-card h5 { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 10px; }
        .conf-card p  { font-size: 14px; color: rgba(255,255,255,.72); line-height: 1.65; }

        /* ══════════════════════════════
           CTA BAND
        ══════════════════════════════ */
        .cta-band {
            background: var(--cv-off);
            border-top: 1px solid var(--cv-border);
            border-bottom: 1px solid var(--cv-border);
            padding: 56px 0;
            text-align: center;
        }
        .cta-band h2 {
            font-size: clamp(1.6rem, 3.5vw, 2.2rem);
            font-weight: 800; color: var(--cv-text);
            margin-bottom: 10px;
        }
        .cta-band p { font-size: 15px; color: var(--cv-text-muted); margin-bottom: 28px; }

        .btn-cta {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, var(--cv-green), var(--cv-blue-mid));
            color: #fff !important;
            font-size: 15px; font-weight: 700;
            padding: 15px 32px; border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 5px 18px rgba(26,102,54,.3);
            transition: opacity .2s, transform .15s;
        }
        .btn-cta:hover { opacity: .88; transform: translateY(-2px); }

        .btn-cta-outline {
            display: inline-flex; align-items: center; gap: 8px;
            border: 2px solid var(--cv-green);
            color: var(--cv-green) !important;
            font-size: 15px; font-weight: 600;
            padding: 13px 28px; border-radius: 50px;
            text-decoration: none;
            transition: background .2s, color .2s;
        }
        .btn-cta-outline:hover { background: var(--cv-green); color: #fff !important; }

        /* ══════════════════════════════
           FOOTER
        ══════════════════════════════ */
        footer {
            background: linear-gradient(150deg, #091a30 0%, #0d2c1a 100%);
            color: rgba(255,255,255,.75);
            padding: 52px 0 28px;
            font-size: 14px;
        }

        .footer-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; margin-bottom: 14px;
        }
        .footer-logo img { height: 38px; width: 38px; object-fit: contain; }
        .footer-logo-text { font-size: 20px; font-weight: 700; }
        .footer-logo-text .choco   { color: #6EE7A0; }
        .footer-logo-text .visible { color: #7EB8F7; }

        footer p { line-height: 1.7; color: rgba(255,255,255,.6); }

        .footer-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(232,160,32,.15);
            border: 1px solid rgba(232,160,32,.3);
            color: var(--cv-gold-light);
            padding: 6px 14px; border-radius: 50px;
            font-size: 12.5px; font-weight: 600;
            margin-bottom: 14px;
        }

        .footer-divider {
            border: none; border-top: 1px solid rgba(255,255,255,.1);
            margin: 28px 0 20px;
        }

        .footer-bottom {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
            font-size: 12.5px; color: rgba(255,255,255,.4);
        }

        .footer-admin-link {
            color: rgba(255,255,255,.25);
            text-decoration: none; font-size: 11px;
            transition: color .2s;
        }
        .footer-admin-link:hover { color: rgba(255,255,255,.55); }

        /* ══════════════════════════════
           SCROLL REVEAL
        ══════════════════════════════ */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: .1s; }
        .reveal-delay-2 { transition-delay: .2s; }
        .reveal-delay-3 { transition-delay: .3s; }

        /* ══════════════════════════════
           PULSE
        ══════════════════════════════ */
        @keyframes pulse {
            0%, 100% { box-shadow: 0 6px 20px rgba(232,160,32,.35); }
            50%       { box-shadow: 0 8px 30px rgba(232,160,32,.55); transform: translateY(-1px); }
        }

        /* ══════════════════════════════
           RESPONSIVE misc
        ══════════════════════════════ */
        @media (max-width: 576px) {
            .hero { padding: 100px 0 60px; }
            .hero-stats { gap: 20px; }
            .stat-divider { display: none; }
            .hero-btns { flex-direction: column; }
            .btn-hero-primary, .btn-hero-secondary { width: 100%; justify-content: center; }
            .cta-btns { flex-direction: column; align-items: center; }
        }
    </style>
</head>
<body>

    <!-- ══════════════════════════════
         NAVBAR
    ══════════════════════════════ -->
    <nav class="cv-navbar" id="navbar">
        <div class="container">
            <a href="index.php" class="nav-logo">
                <img src="assets/images/chocovisibleee.png" alt="ChocoVisible">
                <span class="nav-logo-text">
                    <span class="choco">Choco</span><span class="visible">Visible</span>
                </span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link active">Inicio</a></li>
                <li><a href="nueva-denuncia.php" class="nav-link">Nueva Denuncia</a></li>
                <li><a href="consultar.php" class="nav-link">Consultar Estado</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle" aria-label="Menú">
                <i class="fas fa-bars" id="navIcon"></i>
            </button>
        </div>
        <!-- Drawer móvil -->
        <div class="nav-drawer" id="navDrawer">
            <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Inicio</a>
            <a href="nueva-denuncia.php" class="nav-link"><i class="fas fa-pen"></i> Nueva Denuncia</a>
            <a href="consultar.php" class="nav-link"><i class="fas fa-search"></i> Consultar Estado</a>
        </div>
    </nav>

    <!-- ══════════════════════════════
         HERO
    ══════════════════════════════ -->
    <section class="hero" id="inicio">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>

        <div class="container hero-content text-center">
            <div class="hero-eyebrow">
                <i class="fas fa-map-marker-alt"></i>
                Quibdó, Departamento del Chocó · Colombia
            </div>
            <h1 class="hero-title">
                Tu voz protege al<br>
                <span class="highlight">Chocó Visible</span>
            </h1>
            <p class="hero-subtitle mx-auto">
                Plataforma segura y confidencial de denuncia ciudadana. Reporta incidentes, 
                haz seguimiento en tiempo real y construye un Chocó más transparente.
            </p>
            <div class="hero-btns">
                <a href="nueva-denuncia.php" class="btn-hero-primary">
                    <i class="fas fa-plus-circle"></i> Hacer una Denuncia
                </a>
                <a href="consultar.php" class="btn-hero-secondary">
                    <i class="fas fa-search"></i> Consultar Estado
                </a>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-num">100<span>%</span></div>
                    <div class="stat-label">Confidencial</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <div class="stat-num">24<span>/7</span></div>
                    <div class="stat-label">Disponible</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <div class="stat-num">0<span>%</span></div>
                    <div class="stat-label">Datos expuestos</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <div class="stat-num"><span>+</span>5</div>
                    <div class="stat-label">Categorías</div>
                </div>
            </div>
        </div>

        <div class="hero-wave">
            <svg viewBox="0 0 1440 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#FFFFFF"/>
            </svg>
        </div>
    </section>

    <!-- ══════════════════════════════
         CÓMO FUNCIONA
    ══════════════════════════════ -->
    <section class="section" id="como-funciona">
        <div class="container text-center">
            <div class="reveal">
                <span class="section-eyebrow"><i class="fas fa-route"></i> Proceso</span>
                <h2 class="section-title">¿Cómo funciona?</h2>
                <p class="section-subtitle">Simple, seguro y transparente. Tu denuncia en tres pasos.</p>
            </div>
            <div class="process-grid">
                <div class="process-card reveal reveal-delay-1">
                    <div class="process-num">1</div>
                    <div class="process-icon"><i class="fas fa-edit"></i></div>
                    <h5>Reporta el incidente</h5>
                    <p>Completa nuestro formulario seguro con los detalles del incidente. Adjunta evidencias como fotografías o documentos de soporte.</p>
                </div>
                <div class="process-card reveal reveal-delay-2">
                    <div class="process-num">2</div>
                    <div class="process-icon"><i class="fas fa-qrcode"></i></div>
                    <h5>Recibe tu código</h5>
                    <p>Obtén de inmediato un código único de seguimiento para consultar el estado y progreso de tu denuncia en cualquier momento.</p>
                </div>
                <div class="process-card reveal reveal-delay-3">
                    <div class="process-num">3</div>
                    <div class="process-icon"><i class="fas fa-eye"></i></div>
                    <h5>Seguimiento activo</h5>
                    <p>Consulta actualizaciones, respuestas oficiales y el progreso de tu denuncia a través de nuestro sistema en tiempo real.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════
         TIPOS DE DENUNCIAS
    ══════════════════════════════ -->
    <section class="section section-alt" id="tipos">
        <div class="container text-center">
            <div class="reveal">
                <span class="section-eyebrow"><i class="fas fa-layer-group"></i> Categorías</span>
                <h2 class="section-title">Tipos de Denuncias</h2>
                <p class="section-subtitle">Puedes reportar distintos incidentes que afecten a nuestra comunidad chocoana.</p>
            </div>
            <div class="types-grid">
                <div class="type-card reveal reveal-delay-1">
                    <div class="type-icon-wrap" style="background: rgba(220,38,38,.1);">
                        <i class="fas fa-user-times" style="color:#DC2626;"></i>
                    </div>
                    <h6>Acoso Laboral</h6>
                    <small>Comportamientos inadecuados en el entorno laboral</small>
                </div>
                <div class="type-card reveal reveal-delay-2">
                    <div class="type-icon-wrap" style="background: rgba(245,158,11,.1);">
                        <i class="fas fa-exclamation-triangle" style="color:#D97706;"></i>
                    </div>
                    <h6>Seguridad</h6>
                    <small>Riesgos y condiciones inseguras para la comunidad</small>
                </div>
                <div class="type-card reveal reveal-delay-3">
                    <div class="type-icon-wrap" style="background: rgba(19,78,155,.1);">
                        <i class="fas fa-gavel" style="color:var(--cv-blue-mid);"></i>
                    </div>
                    <h6>Ético</h6>
                    <small>Violaciones al código de ética e integridad</small>
                </div>
                <div class="type-card reveal reveal-delay-1">
                    <div class="type-icon-wrap" style="background: rgba(26,102,54,.1);">
                        <i class="fas fa-leaf" style="color:var(--cv-green);"></i>
                    </div>
                    <h6>Ambiental</h6>
                    <small>Afectaciones al medio ambiente del Chocó</small>
                </div>
                <div class="type-card reveal reveal-delay-2">
                    <div class="type-icon-wrap" style="background: rgba(139,92,246,.1);">
                        <i class="fas fa-hand-holding-usd" style="color:#7C3AED;"></i>
                    </div>
                    <h6>Corrupción</h6>
                    <small>Actos de corrupción y malversación de fondos</small>
                </div>
                <div class="type-card reveal reveal-delay-3">
                    <div class="type-icon-wrap" style="background: rgba(236,72,153,.1);">
                        <i class="fas fa-users" style="color:#DB2777;"></i>
                    </div>
                    <h6>Discriminación</h6>
                    <small>Tratos injustos por raza, género u origen</small>
                </div>
                <div class="type-card reveal reveal-delay-1">
                    <div class="type-icon-wrap" style="background: rgba(14,165,233,.1);">
                        <i class="fas fa-water" style="color:#0284C7;"></i>
                    </div>
                    <h6>Servicios Públicos</h6>
                    <small>Irregularidades en agua, luz o saneamiento</small>
                </div>
                <div class="type-card reveal reveal-delay-2">
                    <div class="type-icon-wrap" style="background: rgba(232,160,32,.1);">
                        <i class="fas fa-shield-alt" style="color:var(--cv-gold);"></i>
                    </div>
                    <h6>Otro</h6>
                    <small>Cualquier otro incidente que afecte tu comunidad</small>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════
         CONFIDENCIALIDAD
    ══════════════════════════════ -->
    <section class="conf-section" id="confidencialidad">
        <div class="container text-center" style="position:relative;z-index:2;">
            <div class="reveal">
                <span class="section-eyebrow" style="background:rgba(255,255,255,.12);color:var(--cv-gold-light);">
                    <i class="fas fa-shield-alt"></i> Seguridad
                </span>
                <h2 class="section-title" style="color:#fff;">Confidencialidad Garantizada</h2>
                <p class="section-subtitle" style="color:rgba(255,255,255,.72);max-width:580px;">
                    Tu identidad está protegida con los más altos estándares. Todas las denuncias 
                    son tratadas con máxima confidencialidad por personal especializado y autorizado.
                </p>
            </div>
            <div class="conf-grid">
                <div class="conf-card reveal reveal-delay-1">
                    <div class="conf-icon"><i class="fas fa-lock"></i></div>
                    <h5>100% Seguro</h5>
                    <p>Encriptación de datos y protocolos de seguridad avanzados en cada transmisión.</p>
                </div>
                <div class="conf-card reveal reveal-delay-2">
                    <div class="conf-icon"><i class="fas fa-user-secret"></i></div>
                    <h5>Anónimo</h5>
                    <p>Tu identidad permanece completamente protegida durante todo el proceso.</p>
                </div>
                <div class="conf-card reveal reveal-delay-3">
                    <div class="conf-icon"><i class="fas fa-clock"></i></div>
                    <h5>24/7 Disponible</h5>
                    <p>Sistema disponible las 24 horas del día, los 365 días del año sin interrupciones.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════
         BANDA CTA
    ══════════════════════════════ -->
    <section class="cta-band">
        <div class="container reveal">
            <h2>¿Listo para hacer tu denuncia?</h2>
            <p>Únete a los ciudadanos que están construyendo un Chocó más transparente y seguro.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap cta-btns">
                <a href="nueva-denuncia.php" class="btn-cta">
                    <i class="fas fa-plus-circle"></i> Nueva Denuncia
                </a>
                <a href="consultar.php" class="btn-cta-outline">
                    <i class="fas fa-search"></i> Consultar Estado
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════
         FOOTER
    ══════════════════════════════ -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8 col-md-7">
                    <a href="index.php" class="footer-logo">
                        <img src="assets/images/chocovisibleee.png" alt="ChocoVisible">
                        <span class="footer-logo-text">
                            <span class="choco">Choco</span><span class="visible">Visible</span>
                        </span>
                    </a>
                    <p class="mb-3">Sistema seguro de denuncia ciudadana para el desarrollo transparente y sostenible del Departamento del Chocó.</p>
                    <p class="mb-0">
                        <i class="fas fa-map-marker-alt me-2" style="color:#6EE7A0;"></i>
                        Quibdó, Departamento del Chocó, Colombia
                    </p>
                </div>
                <div class="col-lg-4 col-md-5 text-md-end text-center">
                    <div class="footer-badge">
                        <i class="fas fa-heart"></i> Construyendo un Chocó mejor
                    </div>
                    <p class="mb-1">&copy; <?php echo date('Y'); ?> ChocoVisible · Todos los derechos reservados.</p>
                    <small style="color:rgba(255,255,255,.35);">Comprometidos con la transparencia y el desarrollo sostenible</small>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="footer-bottom">
                <span>ChocoVisible · Denuncia Ciudadana</span>
                <a href="login.php" class="footer-admin-link">
                    <i class="fas fa-lock me-1"></i>Administración
                </a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {

        // ── Navbar: scroll shadow + active ──────────────────
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });

        // ── Hamburguesa ──────────────────────────────────────
        const toggle  = document.getElementById('navToggle');
        const drawer  = document.getElementById('navDrawer');
        const navIcon = document.getElementById('navIcon');

        toggle.addEventListener('click', () => {
            const open = drawer.classList.toggle('open');
            navIcon.className = open ? 'fas fa-times' : 'fas fa-bars';
        });

        // Cerrar drawer al hacer clic en un enlace
        drawer.querySelectorAll('.nav-link, .nav-cta').forEach(a => {
            a.addEventListener('click', () => {
                drawer.classList.remove('open');
                navIcon.className = 'fas fa-bars';
            });
        });

        // ── Scroll Reveal ────────────────────────────────────
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });
        reveals.forEach(el => observer.observe(el));

    })();
    </script>
</body>
</html>