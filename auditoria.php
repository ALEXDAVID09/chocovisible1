<?php
require_once 'config.php';

// ── Auth ───────────────────────────────────────────────────────────────────
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php'); exit();
}

// Logout desde aquí si viene el parámetro
if (isset($_GET['logout'])) { session_destroy(); header('Location: login.php'); exit(); }

$pdo = conectarDB();

// ── Filtros ────────────────────────────────────────────────────────────────
$filtro_admin  = $_GET['admin']  ?? 'todos';
$filtro_accion = $_GET['accion'] ?? 'todos';
$filtro_fecha  = $_GET['fecha']  ?? 'todos';
$page          = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page      = 20;
$offset        = ($page - 1) * $per_page;

$where_conditions = [];
$params           = [];

if ($filtro_admin !== 'todos') {
    $where_conditions[] = "aa.admin_id = ?";
    $params[]           = $filtro_admin;
}
if ($filtro_accion !== 'todos') {
    $where_conditions[] = "aa.accion LIKE ?";
    $params[]           = "%$filtro_accion%";
}
if ($filtro_fecha !== 'todos') {
    switch ($filtro_fecha) {
        case 'hoy':    $where_conditions[] = "DATE(aa.fecha) = CURDATE()"; break;
        case 'semana': $where_conditions[] = "aa.fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)"; break;
        case 'mes':    $where_conditions[] = "aa.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)"; break;
    }
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// ── Stats ──────────────────────────────────────────────────────────────────
$stats = $pdo->query("SELECT
    COUNT(*) as total_acciones,
    COUNT(DISTINCT admin_id) as admins_activos,
    SUM(CASE WHEN accion LIKE '%Login%'      THEN 1 ELSE 0 END) as total_logins,
    SUM(CASE WHEN accion LIKE '%Actualizar%' THEN 1 ELSE 0 END) as total_actualizaciones,
    SUM(CASE WHEN accion LIKE '%Archivar%'   THEN 1 ELSE 0 END) as total_archivados,
    SUM(CASE WHEN accion LIKE '%Eliminar%'   THEN 1 ELSE 0 END) as total_eliminados,
    SUM(CASE WHEN DATE(fecha) = CURDATE()    THEN 1 ELSE 0 END) as acciones_hoy
FROM admin_actividades")->fetch(PDO::FETCH_ASSOC);

// ── Paginación ─────────────────────────────────────────────────────────────
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_actividades aa $where_clause");
$count_stmt->execute($params);
$total_registros = $count_stmt->fetchColumn();
$total_paginas   = ceil($total_registros / $per_page);

// ── Actividades ────────────────────────────────────────────────────────────
$act_stmt = $pdo->prepare("SELECT aa.*, a.username, a.nombre_completo, a.rol
    FROM admin_actividades aa
    INNER JOIN administradores a ON aa.admin_id = a.id
    $where_clause
    ORDER BY aa.fecha DESC
    LIMIT $per_page OFFSET $offset");
$act_stmt->execute($params);

// ── Admins para filtro ─────────────────────────────────────────────────────
$admins = $pdo->query("SELECT id, username, nombre_completo FROM administradores ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);

// ── Helpers ────────────────────────────────────────────────────────────────
function obtenerIconoAccion($accion) {
    if (stripos($accion,'login')       !== false) return 'fa-sign-in-alt';
    if (stripos($accion,'actualizar')  !== false) return 'fa-edit';
    if (stripos($accion,'desarchivar') !== false) return 'fa-undo';
    if (stripos($accion,'archivar')    !== false) return 'fa-archive';
    if (stripos($accion,'exportar')    !== false) return 'fa-file-export';
    if (stripos($accion,'eliminar')    !== false) return 'fa-trash-alt';
    if (stripos($accion,'registro')    !== false) return 'fa-user-plus';
    return 'fa-cog';
}

function obtenerColorAccion($accion) {
    if (stripos($accion,'login')       !== false) return 'info';
    if (stripos($accion,'actualizar')  !== false) return 'primary';
    if (stripos($accion,'desarchivar') !== false) return 'success';
    if (stripos($accion,'archivar')    !== false) return 'secondary';
    if (stripos($accion,'exportar')    !== false) return 'warning';
    if (stripos($accion,'eliminar')    !== false) return 'danger';
    if (stripos($accion,'registro')    !== false) return 'purple';
    return 'dark';
}

function buildPagLink($fa, $fc, $ff, $p) {
    return "?admin=$fa&accion=$fc&fecha=$ff&page=$p";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría · ChocoVisible Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/chocovisibleee.png">
    <style>
        /* ══ TOKENS ══ */
        :root {
            --cv-green:       #1A6636;
            --cv-green-mid:   #248C4A;
            --cv-green-light: #32B060;
            --cv-blue:        #0C3460;
            --cv-blue-mid:    #134E9B;
            --cv-blue-light:  #1A73D6;
            --cv-gold:        #E8A020;
            --cv-gold-light:  #F5C842;
            --cv-gray:        #6B7280;
            --cv-danger:      #DC2626;
            --cv-purple:      #7C3AED;
            --cv-white:       #FFFFFF;
            --cv-off:         #F6F8FA;
            --cv-border:      #E2E8F0;
            --cv-text:        #111827;
            --cv-text-2:      #374151;
            --cv-muted:       #6B7280;
            --cv-radius:      12px;
            --cv-radius-sm:   8px;
            --cv-shadow:      0 2px 12px rgba(0,0,0,.07);
            --cv-shadow-md:   0 8px 28px rgba(12,52,96,.12);
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { font-family:'Inter',sans-serif; background:var(--cv-off); color:var(--cv-text); min-height:100vh; }

        /* ══ TOPBAR ══ */
        .topbar {
            background:linear-gradient(135deg,var(--cv-blue) 0%,var(--cv-blue-mid) 50%,var(--cv-green) 100%);
            position:sticky; top:0; z-index:1000;
            box-shadow:0 2px 16px rgba(12,52,96,.25);
        }
        .topbar-inner { display:flex; align-items:center; height:62px; gap:20px; padding:0 24px; }
        .topbar-brand { display:flex; align-items:center; gap:10px; text-decoration:none; flex-shrink:0; }
        .topbar-brand img { height:36px; width:36px; object-fit:contain; }
        .topbar-brand-text { font-size:18px; font-weight:700; line-height:1; }
        .topbar-brand-text .choco { color:#6EE7A0; }
        .topbar-brand-text .vis   { color:#BAE6FD; }
        .topbar-brand-text .sep   { color:rgba(255,255,255,.4); margin:0 2px; }
        .topbar-brand-text .admin { color:rgba(255,255,255,.8); font-size:13px; font-weight:500; }
        .topbar-nav { display:flex; align-items:center; gap:4px; margin-left:auto; }
        .topbar-link {
            display:flex; align-items:center; gap:6px;
            color:rgba(255,255,255,.8); text-decoration:none;
            font-size:13.5px; font-weight:500;
            padding:7px 13px; border-radius:var(--cv-radius-sm);
            transition:background .2s, color .2s; white-space:nowrap;
        }
        .topbar-link:hover { background:rgba(255,255,255,.12); color:#fff; }
        .topbar-link.active { background:rgba(255,255,255,.15); color:#fff; }
        .topbar-link.danger { color:#FCA5A5; }
        .topbar-link.danger:hover { background:rgba(220,38,38,.2); color:#FECACA; }
        .topbar-user {
            display:flex; align-items:center; gap:8px;
            color:rgba(255,255,255,.7); font-size:13px;
            padding:0 12px; border-left:1px solid rgba(255,255,255,.15); margin-left:4px;
        }
        .user-avatar {
            width:32px; height:32px; border-radius:50%;
            background:rgba(255,255,255,.15);
            display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700; color:#fff;
            border:1.5px solid rgba(255,255,255,.25);
        }
        @media(max-width:768px){
            .topbar-user span, .topbar-link span { display:none; }
            .topbar-inner { padding:0 14px; }
        }

        /* ══ LAYOUT ══ */
        .page-wrap { padding:28px 24px 60px; max-width:1400px; margin:0 auto; }

        /* ══ PAGE HEADER ══ */
        .page-head {
            background:linear-gradient(135deg,var(--cv-blue),var(--cv-blue-mid) 50%,var(--cv-green) 100%);
            border-radius:var(--cv-radius); padding:28px 32px;
            margin-bottom:24px;
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
            position:relative; overflow:hidden;
        }
        .page-head::before {
            content:''; position:absolute; inset:0;
            background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);
            background-size:24px 24px;
        }
        .page-head-left { position:relative; z-index:1; }
        .page-head h1 { font-size:22px; font-weight:800; color:#fff; margin-bottom:4px; display:flex; align-items:center; gap:10px; }
        .page-head p  { font-size:13.5px; color:rgba(255,255,255,.7); margin:0; }
        .live-badge {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(34,197,94,.2); border:1px solid rgba(34,197,94,.4);
            color:#86EFAC; padding:5px 12px; border-radius:50px;
            font-size:12px; font-weight:600; position:relative; z-index:1;
        }
        .live-dot {
            width:7px; height:7px; border-radius:50%;
            background:#22c55e; display:inline-block;
            animation:pulseDot 2s infinite;
        }
        @keyframes pulseDot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }

        /* ══ STATS ══ */
        .stats-grid {
            display:grid; grid-template-columns:repeat(7,1fr); gap:12px; margin-bottom:22px;
        }
        @media(max-width:1200px){ .stats-grid{ grid-template-columns:repeat(4,1fr); } }
        @media(max-width:640px) { .stats-grid{ grid-template-columns:repeat(2,1fr); } }

        .stat-card {
            background:var(--cv-white); border:1px solid var(--cv-border);
            border-radius:var(--cv-radius); padding:16px 14px; text-align:center;
            position:relative; overflow:hidden;
            transition:transform .25s, box-shadow .25s; cursor:default;
        }
        .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
        .stat-card:hover { transform:translateY(-4px); box-shadow:var(--cv-shadow-md); }

        .stat-card.s-total    ::before { background:linear-gradient(90deg,var(--cv-blue-light),var(--cv-blue-mid)); }
        .stat-card.s-admins   ::before { background:linear-gradient(90deg,var(--cv-green),var(--cv-green-mid)); }
        .stat-card.s-logins   ::before { background:linear-gradient(90deg,#F59E0B,#D97706); }
        .stat-card.s-updates  ::before { background:linear-gradient(90deg,var(--cv-blue-light),var(--cv-blue)); }
        .stat-card.s-archives ::before { background:linear-gradient(90deg,#9CA3AF,#6B7280); }
        .stat-card.s-deletes  ::before { background:linear-gradient(90deg,#EF4444,#DC2626); }
        .stat-card.s-today    ::before { background:linear-gradient(90deg,var(--cv-gold),#D97706); }

        .stat-icon {
            width:44px; height:44px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 10px; font-size:15px; color:#fff;
        }
        .stat-card.s-total   .stat-icon { background:linear-gradient(135deg,var(--cv-blue-light),var(--cv-blue-mid)); }
        .stat-card.s-admins  .stat-icon { background:linear-gradient(135deg,var(--cv-green),var(--cv-green-mid)); }
        .stat-card.s-logins  .stat-icon { background:linear-gradient(135deg,#F59E0B,#D97706); }
        .stat-card.s-updates .stat-icon { background:linear-gradient(135deg,var(--cv-blue-light),var(--cv-blue)); }
        .stat-card.s-archives .stat-icon{ background:linear-gradient(135deg,#9CA3AF,#6B7280); }
        .stat-card.s-deletes .stat-icon { background:linear-gradient(135deg,#EF4444,#DC2626); }
        .stat-card.s-today   .stat-icon { background:linear-gradient(135deg,var(--cv-gold),#D97706); }

        .stat-num { font-size:26px; font-weight:800; line-height:1; margin-bottom:4px; }
        .stat-card.s-total   .stat-num { color:var(--cv-blue-mid); }
        .stat-card.s-admins  .stat-num { color:var(--cv-green); }
        .stat-card.s-logins  .stat-num { color:#D97706; }
        .stat-card.s-updates .stat-num { color:var(--cv-blue-light); }
        .stat-card.s-archives .stat-num{ color:var(--cv-gray); }
        .stat-card.s-deletes .stat-num { color:var(--cv-danger); }
        .stat-card.s-today   .stat-num { color:var(--cv-gold); }
        .stat-label { font-size:11.5px; color:var(--cv-muted); font-weight:500; }

        /* ══ FILTROS ══ */
        .filter-card {
            background:var(--cv-white); border:1px solid var(--cv-border);
            border-radius:var(--cv-radius); padding:18px 22px; margin-bottom:20px;
            box-shadow:var(--cv-shadow);
        }
        .filter-card h6 { font-size:13px; font-weight:700; color:var(--cv-text-2); margin-bottom:14px; display:flex; align-items:center; gap:6px; }
        .filter-card h6 i { color:var(--cv-green-mid); }
        .filter-card .form-label { font-size:12px; font-weight:600; color:var(--cv-muted); text-transform:uppercase; letter-spacing:.4px; margin-bottom:5px; }
        .filter-card .form-select, .filter-card .form-control {
            border:1.5px solid var(--cv-border); border-radius:var(--cv-radius-sm);
            padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif;
            background:var(--cv-off); color:var(--cv-text);
            transition:border-color .2s, box-shadow .2s;
        }
        .filter-card .form-select:focus, .filter-card .form-control:focus {
            border-color:var(--cv-green-mid); box-shadow:0 0 0 3px rgba(36,140,74,.12); background:#fff; outline:none;
        }

        /* ══ TABLA ══ */
        .table-card {
            background:var(--cv-white); border:1px solid var(--cv-border);
            border-radius:var(--cv-radius); overflow:hidden; box-shadow:var(--cv-shadow);
        }
        .table-head-bar {
            padding:16px 22px;
            background:linear-gradient(135deg,var(--cv-green),var(--cv-blue-mid));
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
        }
        .table-head-bar h5 { font-size:14.5px; font-weight:700; color:#fff; margin:0; display:flex; align-items:center; gap:8px; }
        .table-head-bar small { font-size:12px; color:rgba(255,255,255,.7); }

        table { width:100%; border-collapse:collapse; }
        thead th {
            background:var(--cv-off); border-bottom:1.5px solid var(--cv-border);
            padding:11px 14px; font-size:11.5px; font-weight:700;
            color:var(--cv-muted); text-transform:uppercase; letter-spacing:.4px;
            white-space:nowrap;
        }
        tbody tr { border-bottom:1px solid rgba(0,0,0,.04); transition:background .15s; }
        tbody tr:hover { background:#FAFBFF; }
        tbody tr:last-child { border-bottom:none; }
        td { padding:13px 14px; font-size:13.5px; vertical-align:middle; }

        /* ── Dot de tiempo ── */
        .time-dot {
            width:9px; height:9px; border-radius:50%; display:inline-block; margin-right:7px; flex-shrink:0;
        }
        .dot-recent { background:#22C55E; animation:pulseDot 2s infinite; }
        .dot-normal { background:var(--cv-blue-light); }
        .dot-old    { background:#D1D5DB; }

        /* ── Avatar admin ── */
        .admin-row { display:flex; align-items:center; gap:10px; }
        .admin-av {
            width:36px; height:36px; border-radius:50%; flex-shrink:0;
            background:linear-gradient(135deg,var(--cv-green),var(--cv-blue-mid));
            color:#fff; display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700;
            box-shadow:0 2px 8px rgba(26,102,54,.25);
        }
        .admin-av.role-superadmin { background:linear-gradient(135deg,var(--cv-gold),#D97706); }
        .admin-name  { font-size:13.5px; font-weight:600; color:var(--cv-text); }
        .admin-user  { font-size:12px; color:var(--cv-muted); }

        /* ── Action badges ── */
        .action-badge {
            display:inline-flex; align-items:center; gap:6px;
            padding:5px 11px; border-radius:var(--cv-radius-sm);
            font-size:12px; font-weight:600; white-space:nowrap;
        }
        .ab-info      { background:#EFF6FF; color:#1E40AF; }
        .ab-primary   { background:rgba(26,102,54,.1); color:var(--cv-green); }
        .ab-secondary { background:#F3F4F6; color:#374151; }
        .ab-success   { background:#ECFDF5; color:#065F46; }
        .ab-warning   { background:#FFFBEB; color:#92400E; }
        .ab-danger    { background:#FEF2F2; color:#991B1B; }
        .ab-purple    { background:#F5F3FF; color:#5B21B6; }
        .ab-dark      { background:#1F2937; color:#F9FAFB; }

        /* ── Meta info ── */
        .meta-chip {
            display:inline-flex; align-items:center; gap:4px;
            background:var(--cv-off); border:1px solid var(--cv-border);
            padding:2px 8px; border-radius:4px;
            font-size:11px; color:var(--cv-muted); font-weight:500;
        }
        .meta-chip i { font-size:10px; }

        /* ── IP code ── */
        .ip-code {
            font-family:'Courier New',monospace; font-size:12.5px;
            background:var(--cv-off); border:1px solid var(--cv-border);
            padding:3px 8px; border-radius:4px; color:var(--cv-text-2);
            white-space:nowrap;
        }

        /* ── Row reciente highlight ── */
        tbody tr.row-recent { background:rgba(34,197,94,.04); }
        tbody tr.row-recent:hover { background:rgba(34,197,94,.07); }

        /* ── Row danger (eliminaciones) ── */
        tbody tr.row-danger { background:rgba(220,38,38,.03); }
        tbody tr.row-danger:hover { background:rgba(220,38,38,.06); }

        /* ══ EMPTY STATE ══ */
        .empty-state { text-align:center; padding:52px 24px; color:var(--cv-muted); }
        .empty-state .empty-icon { width:72px; height:72px; border-radius:50%; background:var(--cv-off); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:1.8rem; color:var(--cv-muted); opacity:.6; }
        .empty-state h6 { font-size:15px; font-weight:600; color:var(--cv-text-2); margin-bottom:6px; }
        .empty-state p { font-size:13.5px; }

        /* ══ PAGINACIÓN ══ */
        .pagination { justify-content:center; margin-top:20px; gap:4px; }
        .page-link {
            color:var(--cv-green); border:1.5px solid var(--cv-border);
            border-radius:var(--cv-radius-sm) !important; padding:7px 13px;
            font-size:13px; font-weight:500; transition:all .2s;
        }
        .page-link:hover { background:var(--cv-green); color:#fff; border-color:var(--cv-green); }
        .page-item.active .page-link { background:linear-gradient(135deg,var(--cv-green),var(--cv-blue-mid)); border-color:transparent; color:#fff; }
        .page-item.disabled .page-link { opacity:.4; }

        /* ══ BOTONES ══ */
        .btn-cv {
            display:inline-flex; align-items:center; gap:6px;
            padding:9px 18px; border-radius:var(--cv-radius-sm);
            font-size:13.5px; font-weight:600; border:none; cursor:pointer;
            text-decoration:none; transition:opacity .2s, transform .15s;
        }
        .btn-cv:hover { opacity:.85; transform:translateY(-1px); }
        .btn-cv-primary { background:linear-gradient(135deg,var(--cv-green),var(--cv-blue-mid)); color:#fff; box-shadow:0 3px 10px rgba(26,102,54,.25); }
        .btn-cv-outline { background:transparent; border:1.5px solid var(--cv-border); color:var(--cv-text-2); }
        .btn-cv-outline:hover { border-color:var(--cv-muted); }

        /* ══ AUTO-REFRESH BADGE ══ */
        .refresh-badge {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);
            color:rgba(255,255,255,.85); padding:5px 12px; border-radius:50px;
            font-size:12px; font-weight:500;
        }
        #countdown { font-weight:700; color:#86EFAC; }

        /* ══ FOOTER ══ */
        .footer-bar {
            background:var(--cv-white); border:1px solid var(--cv-border);
            border-radius:var(--cv-radius); padding:16px 22px;
            margin-top:24px;
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
        }
        .footer-bar span { font-size:13px; color:var(--cv-muted); }
        .footer-bar a { color:var(--cv-blue-mid); text-decoration:none; font-size:13px; font-weight:500; }
        .footer-bar a:hover { text-decoration:underline; }

        @media(max-width:768px){ .page-wrap{ padding:16px 12px 40px; } td,th{ padding:9px 10px; } }
    </style>
</head>
<body>

    <!-- ══ TOPBAR ══ -->
    <nav class="topbar">
        <div class="topbar-inner">
            <a href="admin.php" class="topbar-brand">
                <img src="assets/images/chocovisibleee.png" alt="ChocoVisible">
                <span class="topbar-brand-text">
                    <span class="choco">Choco</span><span class="vis">Visible</span>
                    <span class="sep">·</span><span class="admin">Admin</span>
                </span>
            </a>
            <div class="topbar-nav">
                <a href="admin.php"     class="topbar-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="auditoria.php" class="topbar-link active"><i class="fas fa-history"></i><span>Auditoría</span></a>
                <a href="index.php" target="_blank" class="topbar-link"><i class="fas fa-external-link-alt"></i><span>Ver sitio</span></a>
                <a href="?logout=1"     class="topbar-link danger"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a>
            </div>
            <div class="topbar-user">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?></div>
                <span><?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin') ?></span>
            </div>
        </div>
    </nav>

    <div class="page-wrap">

        <!-- ── Page header ── -->
        <div class="page-head">
            <div class="page-head-left">
                <h1><i class="fas fa-history"></i>Auditoría del Sistema</h1>
                <p>Registro completo de actividades de administradores · <?= date('d/m/Y H:i') ?></p>
            </div>
            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;position:relative;z-index:1;">
                <?php if ($filtro_fecha === 'hoy'): ?>
                <div class="refresh-badge">
                    <span class="live-dot"></span>
                    Auto-refresh en <span id="countdown">30</span>s
                </div>
                <?php endif; ?>
                <a href="auditoria.php" class="btn-cv btn-cv-outline" style="background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.25);color:#fff;">
                    <i class="fas fa-sync-alt"></i> <span>Actualizar</span>
                </a>
            </div>
        </div>

        <!-- ── Estadísticas ── -->
        <div class="stats-grid">
            <div class="stat-card s-total">
                <div class="stat-icon"><i class="fas fa-list"></i></div>
                <div class="stat-num"><?= $stats['total_acciones'] ?? 0 ?></div>
                <div class="stat-label">Total Acciones</div>
            </div>
            <div class="stat-card s-admins">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-num"><?= $stats['admins_activos'] ?? 0 ?></div>
                <div class="stat-label">Admins Activos</div>
            </div>
            <div class="stat-card s-logins">
                <div class="stat-icon"><i class="fas fa-sign-in-alt"></i></div>
                <div class="stat-num"><?= $stats['total_logins'] ?? 0 ?></div>
                <div class="stat-label">Inicios Sesión</div>
            </div>
            <div class="stat-card s-updates">
                <div class="stat-icon"><i class="fas fa-edit"></i></div>
                <div class="stat-num"><?= $stats['total_actualizaciones'] ?? 0 ?></div>
                <div class="stat-label">Actualizaciones</div>
            </div>
            <div class="stat-card s-archives">
                <div class="stat-icon"><i class="fas fa-archive"></i></div>
                <div class="stat-num"><?= $stats['total_archivados'] ?? 0 ?></div>
                <div class="stat-label">Archivados</div>
            </div>
            <div class="stat-card s-deletes">
                <div class="stat-icon"><i class="fas fa-trash-alt"></i></div>
                <div class="stat-num"><?= $stats['total_eliminados'] ?? 0 ?></div>
                <div class="stat-label">Eliminaciones</div>
            </div>
            <div class="stat-card s-today">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-num"><?= $stats['acciones_hoy'] ?? 0 ?></div>
                <div class="stat-label">Hoy</div>
            </div>
        </div>

        <!-- ── Filtros ── -->
        <div class="filter-card">
            <h6><i class="fas fa-filter"></i>Filtrar registros</h6>
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Administrador</label>
                    <select name="admin" class="form-select">
                        <option value="todos" <?= $filtro_admin==='todos'?'selected':'' ?>>Todos los administradores</option>
                        <?php foreach ($admins as $adm): ?>
                        <option value="<?= $adm['id'] ?>" <?= $filtro_admin==$adm['id']?'selected':'' ?>>
                            <?= htmlspecialchars($adm['nombre_completo']) ?> (@<?= htmlspecialchars($adm['username']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Tipo de acción</label>
                    <select name="accion" class="form-select">
                        <option value="todos"       <?= $filtro_accion==='todos'       ?'selected':'' ?>>Todas las acciones</option>
                        <option value="Login"       <?= $filtro_accion==='Login'       ?'selected':'' ?>>Inicios de sesión</option>
                        <option value="Actualizar"  <?= $filtro_accion==='Actualizar'  ?'selected':'' ?>>Actualizaciones</option>
                        <option value="Archivar"    <?= $filtro_accion==='Archivar'    ?'selected':'' ?>>Archivados</option>
                        <option value="Desarchivar" <?= $filtro_accion==='Desarchivar' ?'selected':'' ?>>Desarchivados</option>
                        <option value="Exportar"    <?= $filtro_accion==='Exportar'    ?'selected':'' ?>>Exportaciones</option>
                        <option value="Eliminar"    <?= $filtro_accion==='Eliminar'    ?'selected':'' ?>>Eliminaciones</option>
                        <option value="Registro"    <?= $filtro_accion==='Registro'    ?'selected':'' ?>>Registros</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label">Período</label>
                    <select name="fecha" class="form-select">
                        <option value="todos"  <?= $filtro_fecha==='todos' ?'selected':'' ?>>Todo el tiempo</option>
                        <option value="hoy"    <?= $filtro_fecha==='hoy'   ?'selected':'' ?>>Hoy</option>
                        <option value="semana" <?= $filtro_fecha==='semana'?'selected':'' ?>>Última semana</option>
                        <option value="mes"    <?= $filtro_fecha==='mes'   ?'selected':'' ?>>Último mes</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-cv btn-cv-primary flex-fill">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="auditoria.php" class="btn-cv btn-cv-outline" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- ── Tabla ── -->
        <div class="table-card">
            <div class="table-head-bar">
                <h5><i class="fas fa-list-alt"></i>Registro de Actividades</h5>
                <small>
                    Mostrando <?= min($per_page, max(0, $total_registros - $offset)) ?> de <?= $total_registros ?> registros
                    <?= $total_paginas > 1 ? "· Página $page de $total_paginas" : "" ?>
                </small>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:140px;"><i class="fas fa-clock me-1"></i>Fecha y hora</th>
                            <th style="width:200px;"><i class="fas fa-user me-1"></i>Administrador</th>
                            <th style="width:180px;"><i class="fas fa-cog me-1"></i>Acción</th>
                            <th><i class="fas fa-info-circle me-1"></i>Descripción</th>
                            <th style="width:130px;"><i class="fas fa-network-wired me-1"></i>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($total_registros === 0): ?>
                        <tr><td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-search"></i></div>
                                <h6>Sin registros</h6>
                                <p>No hay actividades que coincidan con los filtros seleccionados.</p>
                            </div>
                        </td></tr>
                    <?php else: ?>
                    <?php while ($act = $act_stmt->fetch(PDO::FETCH_ASSOC)):
                        $elapsed   = time() - strtotime($act['fecha']);
                        $es_recent = $elapsed < 3600;
                        $es_old    = $elapsed > 86400 * 7;
                        $dot_class = $es_recent ? 'dot-recent' : ($es_old ? 'dot-old' : 'dot-normal');
                        $row_class = $es_recent ? 'row-recent' : (stripos($act['accion'],'eliminar')!==false ? 'row-danger' : '');

                        // Iniciales
                        $ini = '';
                        foreach (explode(' ', $act['nombre_completo']) as $p) {
                            $ini .= strtoupper(substr($p,0,1));
                            if (strlen($ini)>=2) break;
                        }

                        $color = obtenerColorAccion($act['accion']);
                        $ab_class = "ab-$color";
                        $icon  = obtenerIconoAccion($act['accion']);

                        // Tiempo relativo
                        if ($elapsed < 60)            $ago = 'Hace un momento';
                        elseif ($elapsed < 3600)      $ago = 'Hace '.floor($elapsed/60).' min';
                        elseif ($elapsed < 86400)     $ago = 'Hace '.floor($elapsed/3600).'h';
                        elseif ($elapsed < 86400*7)   $ago = 'Hace '.floor($elapsed/86400).' días';
                        else                           $ago = date('d/m/Y',strtotime($act['fecha']));
                    ?>
                        <tr class="<?= $row_class ?>">
                            <td>
                                <div style="display:flex;align-items:center;">
                                    <span class="time-dot <?= $dot_class ?>"></span>
                                    <div>
                                        <div style="font-size:13px;font-weight:600;color:var(--cv-text);"><?= date('d/m/Y',strtotime($act['fecha'])) ?></div>
                                        <div style="font-size:12px;color:var(--cv-muted);"><?= date('H:i:s',strtotime($act['fecha'])) ?></div>
                                        <div style="font-size:11px;color:var(--cv-muted);margin-top:1px;"><?= $ago ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="admin-row">
                                    <div class="admin-av <?= strtolower($act['rol']??'')==='superadmin'?'role-superadmin':'' ?>"><?= $ini ?></div>
                                    <div>
                                        <div class="admin-name"><?= htmlspecialchars($act['nombre_completo']) ?></div>
                                        <div class="admin-user">@<?= htmlspecialchars($act['username']) ?></div>
                                        <?php if (!empty($act['rol'])): ?>
                                        <div style="font-size:11px;color:var(--cv-muted);margin-top:2px;"><?= htmlspecialchars($act['rol']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="action-badge <?= $ab_class ?>">
                                    <i class="fas <?= $icon ?>"></i>
                                    <?= htmlspecialchars($act['accion']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-size:13.5px;color:var(--cv-text-2);line-height:1.55;max-width:480px;">
                                    <?= htmlspecialchars($act['descripcion']) ?>
                                </div>
                                <?php if ($act['tabla_afectada']): ?>
                                <div style="margin-top:5px;display:flex;gap:6px;flex-wrap:wrap;">
                                    <span class="meta-chip"><i class="fas fa-table"></i><?= htmlspecialchars($act['tabla_afectada']) ?></span>
                                    <?php if ($act['registro_id']): ?>
                                    <span class="meta-chip"><i class="fas fa-hashtag"></i>ID <?= $act['registro_id'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="ip-code"><?= htmlspecialchars($act['ip_address']) ?></span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Paginación ── -->
        <?php if ($total_paginas > 1): ?>
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="<?= buildPagLink($filtro_admin,$filtro_accion,$filtro_fecha,$page-1) ?>"><i class="fas fa-chevron-left"></i> Ant.</a></li>
                <?php endif; ?>
                <?php
                $ini = max(1,$page-2); $fin = min($total_paginas,$page+2);
                if ($ini>1){ echo '<li class="page-item"><a class="page-link" href="'.buildPagLink($filtro_admin,$filtro_accion,$filtro_fecha,1).'">1</a></li>'; if($ini>2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>'; }
                for($i=$ini;$i<=$fin;$i++) echo '<li class="page-item '.($i==$page?'active':'').'"><a class="page-link" href="'.buildPagLink($filtro_admin,$filtro_accion,$filtro_fecha,$i).'">'.$i.'</a></li>';
                if($fin<$total_paginas){ if($fin<$total_paginas-1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>'; echo '<li class="page-item"><a class="page-link" href="'.buildPagLink($filtro_admin,$filtro_accion,$filtro_fecha,$total_paginas).'">'.$total_paginas.'</a></li>'; }
                ?>
                <?php if ($page < $total_paginas): ?>
                <li class="page-item"><a class="page-link" href="<?= buildPagLink($filtro_admin,$filtro_accion,$filtro_fecha,$page+1) ?>">Sig. <i class="fas fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <!-- ── Footer ── -->
        <div class="footer-bar">
            <span><i class="fas fa-shield-alt me-2" style="color:var(--cv-green-mid);"></i>ChocoVisible · Registro de Auditoría · <?= $total_registros ?> actividades registradas</span>
            <a href="admin.php"><i class="fas fa-arrow-left me-1"></i>Volver al Dashboard</a>
        </div>

    </div><!-- /page-wrap -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
    (function(){

        /* ── Auto-refresh con countdown cuando filtro = hoy ── */
        <?php if ($filtro_fecha === 'hoy'): ?>
        let sec = 30;
        const cd = document.getElementById('countdown');
        const timer = setInterval(() => {
            sec--;
            if (cd) cd.textContent = sec;
            if (sec <= 0) { clearInterval(timer); location.reload(); }
        }, 1000);
        <?php endif; ?>

        /* ── Tooltips Bootstrap ── */
        document.querySelectorAll('[title]').forEach(el => new bootstrap.Tooltip(el, { trigger:'hover' }));

        /* ── Hover suave stats ── */
        document.querySelectorAll('.stat-card').forEach(c => {
            c.addEventListener('mouseenter', () => c.style.transform = 'translateY(-4px)');
            c.addEventListener('mouseleave', () => c.style.transform = '');
        });

    })();
    </script>
</body>
</html>