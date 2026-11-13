<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Validar que el usuario esté autenticado como Administrador
if (empty($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

$admin = $_SESSION['admin'];
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
$csrfToken = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
  <title>GymManagerFit ERP - Panel Administrador</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Lector QR con Cámara -->
  <script src="https://unpkg.com/html5-qrcode"></script>

  <style>
    /* Layout Administrador */
    .admin-wrapper {
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      width: 250px;
      background: var(--bg-secondary);
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      transition: var(--transition);
    }
    .sidebar-brand {
      padding: 20px 22px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid var(--border-color);
    }
    .brand-icon-box {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: linear-gradient(135deg, var(--primary), #7c3aed);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow-glow);
    }
    .nav-list {
      list-style: none;
      padding: 16px 12px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex: 1;
    }
    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border-radius: var(--radius-md);
      color: var(--text-secondary);
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: var(--transition);
    }
    .nav-item:hover, .nav-item.active {
      background: var(--bg-tertiary);
      color: var(--primary);
    }
    .nav-item.active {
      box-shadow: var(--shadow-sm);
    }
    .admin-user-card {
      padding: 16px;
      margin: 12px;
      background: var(--bg-tertiary);
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      gap: 10px;
      border: 1px solid var(--border-color);
    }
    .admin-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      font-size: 14px;
    }
    .admin-user-top-btn {
      display: flex;
      align-items: center;
      gap: 11px;
      min-width: 220px;
      max-width: 290px;
      padding: 9px 13px;
      border: 1px solid var(--border-color);
      border-radius: 14px;
      background: var(--bg-tertiary);
      color: var(--text-primary);
      text-align: left;
      cursor: pointer;
      transition: background .2s ease, border-color .2s ease, transform .2s ease, box-shadow .2s ease;
    }
    .admin-user-top-btn:hover {
      background: var(--bg-secondary);
      border-color: var(--primary);
      box-shadow: 0 5px 18px rgba(0,0,0,.18);
      transform: translateY(-1px);
    }
    .admin-user-top-btn:focus-visible { outline: 2px solid var(--primary); outline-offset: 3px; }
    .admin-user-top-btn .admin-avatar { width: 44px; height: 44px; flex: 0 0 44px; font-size: 16px; }
    .admin-user-top-info { display:flex; flex-direction:column; min-width:0; flex:1; gap:3px; }
    .admin-user-top-name { font-size:14px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .admin-user-top-email { font-size:12px; color:var(--text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .admin-user-top-icon { width:18px; height:18px; flex:0 0 18px; color:var(--text-muted); }
    .admin-navbar {
      min-height: 76px;
      background: rgba(17, 24, 39, .94);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--border-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      padding: 12px 28px;
      position: sticky;
      top: 0;
      z-index: 40;
    }
    .search-input-box {
      position: relative;
      width: min(440px, 48vw);
      flex: 0 1 440px;
      display: flex;
      align-items: center;
    }
    .search-input-box input {
      width: 100%;
      height: 46px;
      padding: 0 16px 0 46px;
      border-radius: 13px;
      background: #1f2937;
      border: 1px solid #374151;
      font-size: 13.5px;
      box-shadow: inset 0 1px 1px rgba(255,255,255,.025);
    }
    .search-input-box input:focus {
      background: #202c3d;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--primary-glow);
    }
    .search-input-box i, .search-input-box svg {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      width: 18px;
      height: 18px;
      pointer-events: none;
    }
    .dashboard-heading {
      display:flex; justify-content:space-between; align-items:flex-end;
      gap:16px; margin-bottom:24px;
    }
    .dashboard-heading h2 { font-size:25px !important; line-height:1.2; margin-bottom:6px; }
    .dashboard-heading p { font-size:13px; color:var(--text-muted); }
    #view-dashboard { max-width: 1500px; padding: 30px 32px 40px; }
    #view-dashboard .kpis-grid {
      display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px;
    }
    #view-dashboard .kpi-card {
      min-height:112px; padding:20px; border:1px solid var(--border-color);
      border-radius:16px; background:linear-gradient(145deg,#151f30 0%,#111827 72%);
      box-shadow:0 8px 22px rgba(0,0,0,.16); display:flex; align-items:center; gap:16px;
      transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease;
    }
    #view-dashboard .kpi-card:hover { transform:translateY(-2px); border-color:#4b5563; box-shadow:0 12px 26px rgba(0,0,0,.22); }
    #view-dashboard .kpi-icon { width:48px; height:48px; flex:0 0 48px; border-radius:14px; display:grid; place-items:center; }
    #view-dashboard .kpi-val { font-family:var(--font-heading); font-size:25px; line-height:1.15; font-weight:800; letter-spacing:-.04em; }
    #view-dashboard .kpi-title { color:var(--text-muted); font-size:12px; font-weight:600; margin-top:5px; }
    #view-dashboard .card { padding:22px; border-color:#303b4d; box-shadow:0 8px 24px rgba(0,0,0,.14); }
    #view-dashboard .table-container { border-radius:12px; }
    #view-dashboard .table th { white-space:nowrap; }
    @media (max-width: 1100px) {
      #view-dashboard .kpis-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }
    @media (max-width: 700px) {
      .admin-navbar { padding:10px 14px; gap:10px; flex-wrap:wrap; }
      .search-input-box { width:100%; flex:1 1 100%; order:2; }
      .nav-actions { margin-left:auto; gap:7px; flex-wrap:wrap; justify-content:flex-end; }
      .admin-user-top-btn { min-width:0; max-width:230px; padding:7px 9px; gap:8px; }
      .admin-user-top-btn .admin-avatar { width:36px; height:36px; flex-basis:36px; }
      .admin-user-top-name { font-size:12px; }
      .admin-user-top-email { font-size:10px; }
      #view-dashboard { padding:22px 16px 30px; }
      #view-dashboard .kpis-grid { grid-template-columns:1fr; }
      .dashboard-heading { align-items:flex-start; flex-direction:column; }
    }
    .nav-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .filter-tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 18px;
      flex-wrap: wrap;
    }
    .filter-tab {
      padding: 7px 14px;
      background: var(--bg-tertiary);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      color: var(--text-secondary);
      font-size: 12.5px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
    }
    .filter-tab.active {
      background: var(--primary);
      color: #fff;
      border-color: var(--primary);
      box-shadow: var(--shadow-sm);
    }
  </style>
</head>
<body>

<div class="admin-wrapper">

  <!-- ─── SIDEBAR DE NAVEGACIÓN ───────────────────────────────────────────── -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon-box">
        <i data-lucide="sparkles" style="color: #fff; width: 20px; height: 20px;"></i>
      </div>
      <div>
        <h2 style="font-size: 16px; font-weight: 800; line-height: 1.1;">GymManagerFit</h2>
        <span style="font-size: 11px; color: var(--primary); font-weight: 600;">Panel ERP Linux</span>
      </div>
    </div>

    <ul class="nav-list">
      <li>
        <a class="nav-item active" id="nav-dashboard" onclick="switchView('dashboard')">
          <i data-lucide="layout-dashboard" style="width: 17px; height: 17px;"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a class="nav-item" id="nav-clientes" onclick="switchView('clientes')">
          <i data-lucide="users" style="width: 17px; height: 17px;"></i>
          <span>Socios & Clientes</span>
        </a>
      </li>
      <li>
        <a class="nav-item" id="nav-asistencias" onclick="switchView('asistencias')">
          <i data-lucide="calendar-check" style="width: 17px; height: 17px;"></i>
          <span>Asistencias</span>
        </a>
      </li>
      <li>
        <a class="nav-item" id="nav-venta" onclick="switchView('venta')">
          <i data-lucide="sparkles" style="width: 17px; height: 17px;"></i>
          <span>Venta Rápida</span>
        </a>
      </li>
      <li>
        <a class="nav-item" id="nav-caja" onclick="switchView('caja')">
          <i data-lucide="dollar-sign" style="width: 17px; height: 17px;"></i>
          <span>Caja & Pagos</span>
        </a>
      </li>
      <li>
        <a class="nav-item" id="nav-administradores" onclick="switchView('administradores')">
          <i data-lucide="shield-check" style="width: 17px; height: 17px;"></i>
          <span>Administradores</span>
        </a>
      </li>
      <li>
        <a class="nav-item" id="nav-auditoria" onclick="switchView('auditoria')">
          <i data-lucide="shield" style="width: 17px; height: 17px;"></i>
          <span>Auditoría</span>
        </a>
      </li>
      <li><a class="nav-item" id="nav-asistente-ia" onclick="switchView('asistente-ia')"><i data-lucide="bot" style="width:17px;height:17px;"></i><span>Asistente IA</span></a></li>
    </ul>

  </aside>

  <!-- ─── CONTENIDO PRINCIPAL ─────────────────────────────────────────────── -->
  <div class="main-content">

    <!-- Navbar superior -->
    <header class="admin-navbar">
      <div class="search-input-box">
        <i data-lucide="search"></i>
        <input type="text" id="globalSearch" class="form-control" placeholder="Buscar socio por DNI o nombre..." oninput="handleGlobalSearch(this.value)">
      </div>

      <div class="nav-actions">
        <!-- Botón Lector QR -->
        <button onclick="openLectorQRModal()" class="btn btn-success" style="padding: 8px 14px; font-size: 13px;" title="Lector QR de Asistencias">
          <i data-lucide="qr-code" style="width: 15px; height: 15px;"></i>
          <span>Lector QR</span>
        </button>

        <!-- Botón Venta Rápida -->
        <button onclick="switchView('venta')" class="btn btn-primary" style="padding: 8px 14px; font-size: 13px;">
          <i data-lucide="zap" style="width: 15px; height: 15px;"></i>
          <span>Venta Rápida</span>
        </button>

        <!-- Perfil del administrador y cierre de sesión: toda la tarjeta es clicable -->
        <button type="button" class="admin-user-top-btn" onclick="logoutAdmin()" title="Cerrar sesión" aria-label="Cerrar sesión">
          <span class="admin-avatar"><?= strtoupper(substr($admin['nombre'], 0, 1)) ?></span>
          <span class="admin-user-top-info">
            <span class="admin-user-top-name"><?= htmlspecialchars($admin['nombre']) ?></span>
            <span class="admin-user-top-email"><?= htmlspecialchars($admin['email']) ?></span>
          </span>
          <i data-lucide="log-out" class="admin-user-top-icon" aria-hidden="true"></i>
        </button>
      </div>
    </header>

    <!-- ═══ VISTA 1: DASHBOARD ═══════════════════════════════════════════════ -->
    <main class="page-container" id="view-dashboard">
      <div class="dashboard-heading">
        <div>
          <h2>Panel de Control General</h2>
          <p>Resumen operativo del gimnasio · Información actualizada desde MariaDB</p>
        </div>
        <button type="button" class="btn btn-secondary" onclick="loadDashboard()" title="Actualizar indicadores">
          <i data-lucide="refresh-cw" style="width:15px;height:15px;"></i><span>Actualizar datos</span>
        </button>
      </div>

      <!-- KPIs Grid -->
      <div class="kpis-grid">
        <div class="kpi-card">
          <div class="kpi-icon" style="background: var(--success-glow); color: var(--success);">
            <i data-lucide="user-check" style="width: 24px; height: 24px;"></i>
          </div>
          <div>
            <div class="kpi-val" id="kpi-activos">0</div>
            <div class="kpi-title">Socios Activos</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon" style="background: var(--warning-glow); color: var(--warning);">
            <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
          </div>
          <div>
            <div class="kpi-val" id="kpi-porvencer">0</div>
            <div class="kpi-title">Por Vencer (&le; 5 días)</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon" style="background: var(--primary-glow); color: var(--primary);">
            <i data-lucide="dollar-sign" style="width: 24px; height: 24px;"></i>
          </div>
          <div>
            <div class="kpi-val" id="kpi-ingresos">S/ 0</div>
            <div class="kpi-title">Ingresos del Mes</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon" style="background: rgba(124, 58, 237, 0.15); color: #7c3aed;">
            <i data-lucide="activity" style="width: 24px; height: 24px;"></i>
          </div>
          <div>
            <div class="kpi-val" id="kpi-asistencias">0</div>
            <div class="kpi-title">Asistencias Hoy</div>
          </div>
        </div>
      </div>

      <!-- Resumen adicional del negocio -->
      <div class="kpis-grid dashboard-secondary-kpis" style="margin-top:16px; margin-bottom:24px;">
        <div class="kpi-card"><div class="kpi-icon" style="background:var(--primary-glow);color:var(--primary);"><i data-lucide="users" style="width:22px;height:22px;"></i></div><div><div class="kpi-val" id="kpi-total-socios">0</div><div class="kpi-title">Socios registrados</div></div></div>
        <div class="kpi-card"><div class="kpi-icon" style="background:var(--success-glow);color:var(--success);"><i data-lucide="wallet" style="width:22px;height:22px;"></i></div><div><div class="kpi-val" id="kpi-ingresos-hoy">S/ 0.00</div><div class="kpi-title">Ingresos de hoy</div></div></div>
        <div class="kpi-card"><div class="kpi-icon" style="background:var(--danger-glow);color:var(--danger);"><i data-lucide="receipt" style="width:22px;height:22px;"></i></div><div><div class="kpi-val" id="kpi-pagos-pendientes">0</div><div class="kpi-title">Pagos pendientes</div><div class="kpi-title" id="kpi-monto-pendiente">S/ 0.00</div></div></div>
      </div>

      <!-- Tendencias -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px;margin-bottom:24px;">
        <section class="card">
          <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px;"><div><h3 style="font-size:16px;font-weight:700;">Asistencias recientes</h3><p style="font-size:12px;color:var(--text-muted);">Últimos 7 días</p></div><i data-lucide="activity" style="color:var(--primary);"></i></div>
          <div id="chart-asistencias" style="display:flex;align-items:flex-end;gap:10px;height:170px;padding-top:12px;"><p style="color:var(--text-muted);font-size:13px;">Cargando datos...</p></div>
        </section>
        <section class="card">
          <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px;"><div><h3 style="font-size:16px;font-weight:700;">Ingresos mensuales</h3><p style="font-size:12px;color:var(--text-muted);">Últimos 6 meses · pagos completados</p></div><i data-lucide="chart-no-axes-column-increasing" style="color:var(--success);"></i></div>
          <div id="chart-ingresos" style="display:flex;align-items:flex-end;gap:10px;height:170px;padding-top:12px;"><p style="color:var(--text-muted);font-size:13px;">Cargando datos...</p></div>
        </section>
      </div>

      <!-- Expiring soon list card -->
      <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <h3 style="font-size: 16px; font-weight: 700;">⚠️ Próximos Vencimientos (Siguientes 7 días)</h3>
          <span class="badge badge-warning">Atención prioritaria</span>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Socio</th>
                <th>DNI</th>
                <th>Teléfono</th>
                <th>Membresía</th>
                <th>Fecha Vence</th>
                <th>Días Restantes</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody id="tbody-expiring">
              <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">Cargando vencimientos...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- ═══ VISTA: ADMINISTRADORES ═══════════════════════════════════════════ -->
    <main class="page-container" id="view-administradores" style="display: none;">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
        <div>
          <h2 style="font-size:22px;font-weight:800;">Administradores</h2>
          <p style="font-size:13px;color:var(--text-muted);">Gestiona las cuentas con acceso al panel administrativo.</p>
        </div>
        <button class="btn btn-primary" onclick="openAdminForm()"><i data-lucide="user-plus" style="width:16px;height:16px;"></i> Nuevo administrador</button>
      </div>
      <div class="card">
        <div class="table-container">
          <table class="table">
            <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Creado</th><th>Acciones</th></tr></thead>
            <tbody id="tbody-administradores"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:20px;">Cargando administradores...</td></tr></tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- ═══ VISTA 2: SOCIOS & CLIENTES ═══════════════════════════════════════ -->
    <main class="page-container" id="view-clientes" style="display: none;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
          <h2 style="font-size: 22px; font-weight: 800;">Padrón de Socios</h2>
          <p style="font-size: 13px; color: var(--text-muted);">Gestión, fichas médicas y control de estado (Activo/Inactivo)</p>
        </div>
        <button onclick="openModalNuevoCliente()" class="btn btn-primary">
          <i data-lucide="user-plus" style="width: 16px; height: 16px;"></i>
          <span>Registrar Nuevo Socio</span>
        </button>
      </div>

      <!-- Pestañas de Filtro -->
      <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterClientes('Todos', this)">Todos</button>
        <button class="filter-tab" onclick="filterClientes('Activo', this)">Activos</button>
        <button class="filter-tab" onclick="filterClientes('Por vencer', this)">Por Vencer</button>
        <button class="filter-tab" onclick="filterClientes('Vencido', this)">Vencidos</button>
        <button class="filter-tab" onclick="filterClientes('Inactivo', this)">Inactivos / Desactivados</button>
      </div>

      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Nombre del Socio</th>
              <th>DNI (Enmascarado)</th>
              <th>Celular</th>
              <th>Ficha Física</th>
              <th>Estado</th>
              <th>Plan Actual</th>
              <th>Asistencias</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-clientes">
            <tr><td colspan="8" style="text-align: center; color: var(--text-muted);">Cargando socios desde MariaDB...</td></tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- ═══ VISTA 3: ASISTENCIAS ═════════════════════════════════════════════ -->
    <main class="page-container" id="view-asistencias" style="display: none;">
      <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        <div>
          <h2 style="font-size:22px; font-weight:800;">Control de Asistencias</h2>
          <p style="font-size:13px; color:var(--text-muted);">Registro e historial de ingresos de los socios.</p>
        </div>
        <button onclick="openLectorQRModal()" class="btn btn-success">
          <i data-lucide="scan-line" style="width:16px;height:16px;"></i> Registrar asistencia
        </button>
      </div>

      <div class="kpis-grid" style="margin-bottom:20px;">
        <div class="kpi-card"><div class="kpi-icon" style="background:var(--primary-glow);color:var(--primary);"><i data-lucide="calendar-check"></i></div><div><div class="kpi-val" id="att-total">0</div><div class="kpi-title">Asistencias encontradas</div></div></div>
        <div class="kpi-card"><div class="kpi-icon" style="background:var(--success-glow);color:var(--success);"><i data-lucide="users"></i></div><div><div class="kpi-val" id="att-unique">0</div><div class="kpi-title">Socios distintos</div></div></div>
        <div class="kpi-card"><div class="kpi-icon" style="background:var(--warning-glow);color:var(--warning);"><i data-lucide="clock"></i></div><div><div class="kpi-val" id="att-last">—</div><div class="kpi-title">Último ingreso</div></div></div>
      </div>

      <div class="card" style="margin-bottom:18px;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
          <h3 style="font-size:15px;font-weight:700;">Filtros del historial</h3>
          <button class="btn btn-secondary" onclick="resetAttendanceFilters()">Limpiar filtros</button>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label" for="att-from">Desde</label><input class="form-control" type="date" id="att-from"></div>
          <div class="form-group"><label class="form-label" for="att-to">Hasta</label><input class="form-control" type="date" id="att-to"></div>
          <div class="form-group"><label class="form-label" for="att-search">Buscar socio</label><input class="form-control" type="search" id="att-search" placeholder="Nombre o DNI"></div>
          <div class="form-group" style="justify-content:flex-end;"><label class="form-label">&nbsp;</label><button class="btn btn-primary" onclick="loadAsistencias()"><i data-lucide="search" style="width:15px;height:15px;"></i> Aplicar filtros</button></div>
        </div>
      </div>

      <div class="table-container">
        <table class="table">
          <thead><tr><th>Fecha</th><th>Hora</th><th>Socio</th><th>DNI</th><th>Método</th><th>Estado</th></tr></thead>
          <tbody id="tbody-asistencias"><tr><td colspan="6" style="text-align:center;padding:22px;color:var(--text-muted);">Selecciona los filtros para consultar el historial.</td></tr></tbody>
        </table>
      </div>
      <p id="att-results-note" style="font-size:12px;color:var(--text-muted);margin-top:10px;"></p>
    </main>

    <!-- ═══ VISTA 4: VENTA RÁPIDA ════════════════════════════════════════════ -->
    <main class="page-container" id="view-venta" style="display: none;">
      <div style="margin-bottom: 20px;">
        <h2 style="font-size: 22px; font-weight: 800;">Venta Rápida de Membresías</h2>
        <p style="font-size: 13px; color: var(--text-muted);">Asigna planes, cobra y actualiza el estado del socio en MariaDB en un solo paso.</p>
      </div>

      <div class="card" style="max-width: 650px;">
        <form id="formVentaRapida" onsubmit="handleVentaSubmit(event)">
          <div class="form-group">
            <label class="form-label" for="ventaBuscarCliente">Seleccionar Socio *</label>
            <div class="venta-autocomplete" id="ventaAutocomplete">
              <input type="text" id="ventaBuscarCliente" class="form-control" placeholder="Escribe el nombre o DNI del socio..." autocomplete="off" required aria-autocomplete="list" aria-controls="ventaResultadosCliente" aria-expanded="false">
              <input type="hidden" id="ventaSelectCliente" value="">
              <div id="ventaResultadosCliente" class="venta-autocomplete-resultados" role="listbox" hidden></div>
              <small id="ventaClienteSeleccionado" class="venta-cliente-seleccionado" aria-live="polite">Busca y selecciona un socio para continuar.</small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Plan de Membresía *</label>
              <select id="ventaSelectPlan" class="form-control" required onchange="handleSelectPlanChange()">
                <option value="">Selecciona un plan...</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Monto a Cobrar (S/) *</label>
              <input type="number" step="0.5" id="ventaMonto" class="form-control" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Fecha de Inicio *</label>
              <input type="date" id="ventaFechaInicio" class="form-control" required value="<?= date('Y-m-d') ?>" onchange="recalculateVentaEndDate()">
            </div>
            <div class="form-group">
              <label class="form-label">Fecha de Vencimiento</label>
              <input type="date" id="ventaFechaFin" class="form-control" required readonly style="background: var(--bg-hover);">
            </div>
          </div>

          <button type="submit" class="btn btn-primary" id="btnVentaSubmit" style="width: 100%; padding: 14px; font-size: 14px; margin-top: 10px;">
            <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
            <span>Confirmar Cobro y Activar Membresía</span>
          </button>
        </form>
      </div>
    </main>

    <!-- ═══ VISTA 4: CAJA & PAGOS ════════════════════════════════════════════ -->
    <main class="page-container" id="view-caja" style="display: none;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
          <h2 style="font-size: 22px; font-weight: 800;">Caja y Reporte Financiero</h2>
          <p style="font-size: 13px; color: var(--text-muted);">Registro de todas las transacciones cobradas</p>
        </div>
        <a href="api/pagos.php?action=export_csv" class="btn btn-secondary">
          <i data-lucide="file-spreadsheet" style="width: 16px; height: 16px; color: var(--success);"></i>
          <span>Exportar Reporte a Excel (CSV)</span>
        </a>
      </div>

      <div class="kpis-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="kpi-card">
          <div class="kpi-icon" style="background: var(--success-glow); color: var(--success);"><i data-lucide="dollar-sign"></i></div>
          <div><div class="kpi-val" id="caja-hoy">S/ 0</div><div class="kpi-title">Ingresos Hoy</div></div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background: var(--primary-glow); color: var(--primary);"><i data-lucide="calendar"></i></div>
          <div><div class="kpi-val" id="caja-mes">S/ 0</div><div class="kpi-title">Ingresos Este Mes</div></div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background: rgba(124, 58, 237, 0.15); color: #7c3aed;"><i data-lucide="wallet"></i></div>
          <div><div class="kpi-val" id="caja-total">S/ 0</div><div class="kpi-title">Recaudación Total</div></div>
        </div>
      </div>

      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Socio / Cliente</th>
              <th>Plan Contratado</th>
              <th>Monto Cobrado</th>
              <th>Fecha de Pago</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody id="tbody-pagos">
            <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Cargando pagos...</td></tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- ═══ VISTA 5: AUDITORÍA ═══════════════════════════════════════════════ -->
    <main class="page-container" id="view-auditoria" style="display: none;">
      <div style="margin-bottom: 20px;">
        <h2 style="font-size: 22px; font-weight: 800;">Historial de Auditoría</h2>
        <p style="font-size: 13px; color: var(--text-muted);">Registros inmutables generados automáticamente por los Triggers de MariaDB.</p>
      </div>

      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Fecha y Hora</th>
              <th>Tabla</th>
              <th>Acción</th>
              <th>Descripción del Evento</th>
              <th>Usuario</th>
            </tr>
          </thead>
          <tbody id="tbody-auditoria">
            <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Cargando registros de auditoría...</td></tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- ASISTENTE IA: CONSULTAS Y REPORTES -->
    <main class="page-container ai-placeholder-view" id="view-asistente-ia" style="display:none;">
      <div class="dashboard-heading">
        <div><h2>Asistente IA</h2><p>Consulta información del gimnasio y genera reportes administrativos con datos de MariaDB.</p></div>
        <span class="ai-status-pill"><span></span> Asistente conectado</span>
      </div>
      <section class="ai-workspace-card ai-live-workspace">
        <div class="ai-live-header">
          <div class="ai-live-brand">
            <div class="ai-hero-icon"><i data-lucide="bot"></i></div>
            <div><h3>Asistente de gestión GymManagerFit</h3><p>Consultas de solo lectura · Reportes administrativos</p></div>
          </div>
          <button type="button" class="btn btn-secondary ai-clear-btn" onclick="clearAiChat()"><i data-lucide="refresh-ccw"></i> Nueva conversación</button>
        </div>
        <div class="ai-report-tools">
          <strong><i data-lucide="file-chart-column"></i> Generar un reporte</strong>
          <div class="ai-report-buttons">
            <button type="button" class="btn btn-secondary" onclick="generateAiReport('resumen')">Resumen general</button>
            <button type="button" class="btn btn-secondary" onclick="generateAiReport('socios')">Socios</button>
            <button type="button" class="btn btn-secondary" onclick="generateAiReport('asistencias')">Asistencias</button>
            <button type="button" class="btn btn-secondary" onclick="generateAiReport('ingresos')">Ingresos</button>
            <button type="button" class="btn btn-secondary" onclick="generateAiReport('vencimientos')">Vencimientos</button>
            <button type="button" class="btn btn-secondary" onclick="generateAiReport('pendientes')">Pagos pendientes</button>
          </div>
        </div>
        <div id="aiChatMessages" class="ai-chat-messages" aria-live="polite">
          <div class="ai-message ai-message-assistant"><div class="ai-message-avatar"><i data-lucide="bot"></i></div><div class="ai-message-bubble"><strong>Asistente IA</strong><p>¡Hola! Puedo ayudarte a consultar socios, asistencias, ingresos, vencimientos y pagos pendientes. También puedo generar reportes descargables.</p></div></div>
        </div>
        <div id="aiReportResult" class="ai-report-result" style="display:none;"></div>
        <form id="aiChatForm" class="ai-chat-composer" onsubmit="sendAiMessage(event)">
          <textarea id="aiChatInput" rows="2" maxlength="1200" placeholder="Escribe una consulta, por ejemplo: ¿Cuánto ingresó el gimnasio este mes?" aria-label="Escribe tu consulta"></textarea>
          <button type="submit" class="btn btn-primary" id="aiSendButton"><i data-lucide="send"></i><span>Enviar</span></button>
        </form>
        <small class="ai-note">La IA consulta datos mediante funciones PHP de solo lectura. No puede registrar, editar ni eliminar información. Las respuestas pueden requerir verificación administrativa.</small>
      </section>
    </main>

  </div>
</div>

<!-- MODAL GLOBAL: CONFIRMACION DE OPERACIONES -->
<div class="modal-overlay" id="modalConfirmacion" style="display:none;" role="presentation" onclick="resolverConfirmacion(false)">
  <div class="modal-card confirm-card" role="dialog" aria-modal="true" aria-labelledby="confirmTitle" aria-describedby="confirmMessage" onclick="event.stopPropagation();">
    <div class="confirm-icon" id="confirmIcon"><i data-lucide="alert-circle"></i></div>
    <h3 id="confirmTitle">Confirmar operación</h3>
    <p id="confirmMessage">¿Deseas continuar?</p>
    <div class="confirm-actions">
      <button type="button" class="btn btn-secondary" onclick="resolverConfirmacion(false)">Cancelar</button>
      <button type="button" class="btn btn-primary" id="confirmAccept" onclick="resolverConfirmacion(true)">Continuar</button>
    </div>
  </div>
</div>

<!-- ─── MODAL 1: REGISTRAR / EDITAR CLIENTE ────────────────────────────────── -->
<div class="modal-overlay" id="modalCliente" style="display: none;" onclick="closeModalCliente()">
  <div class="modal-card" onclick="event.stopPropagation();" style="max-width: 520px;">
    <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px;" id="modalClienteTitle">Registrar Nuevo Socio</h3>
    <form id="formCliente" onsubmit="handleGuardarCliente(event)">
      <input type="hidden" id="cliId" value="">

      <div class="form-group">
        <label class="form-label">Nombre Completo *</label>
        <input type="text" id="cliName" class="form-control" required placeholder="Ej. Juan Pérez">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">DNI (8 dígitos) *</label>
          <input type="text" id="cliDni" class="form-control" maxlength="8" required placeholder="Ej. 72345678">
        </div>
        <div class="form-group">
          <label class="form-label">Celular</label>
          <input type="text" id="cliPhone" class="form-control" placeholder="Ej. 987654321">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Peso (kg)</label>
          <input type="number" step="0.1" id="cliWeight" class="form-control" placeholder="70.5">
        </div>
        <div class="form-group">
          <label class="form-label">Estatura (m)</label>
          <input type="number" step="0.01" id="cliHeight" class="form-control" placeholder="1.75">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Objetivo Deportivo</label>
        <input type="text" id="cliObjective" class="form-control" placeholder="Ej. Pérdida de peso y fuerza">
      </div>

      <div class="form-group">
        <label class="form-label">Observaciones / Lesiones</label>
        <textarea id="cliNotes" class="form-control" rows="2" placeholder="Ej. Dolor lumbar, asma"></textarea>
      </div>

      <div style="display: flex; gap: 10px; margin-top: 20px;">
        <button type="button" onclick="closeModalCliente()" class="btn btn-secondary" style="flex: 1;">Cancelar</button>
        <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar en MariaDB</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: CREAR / EDITAR ADMINISTRADOR -->


<div class="modal-overlay" id="modalAdminForm" style="display:none;" onclick="closeAdminForm()">
  <div class="modal-card" onclick="event.stopPropagation();" style="max-width:500px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
      <h3 id="adminFormTitle" style="font-size:17px;font-weight:800;">Nuevo administrador</h3>
      <button type="button" onclick="closeAdminForm()" class="btn btn-secondary">Cerrar</button>
    </div>
    <form id="formAdmin" onsubmit="saveAdmin(event)">
      <input type="hidden" id="adminId">
      <div class="form-group"><label class="form-label" for="adminNombre">Nombre completo</label><input class="form-control" id="adminNombre" maxlength="100" required></div>
      <div class="form-group"><label class="form-label" for="adminEmail">Correo electrónico</label><input class="form-control" id="adminEmail" type="email" maxlength="150" required></div>
      <div class="form-group"><label class="form-label" for="adminPassword">Contraseña <span id="adminPasswordHint" style="color:var(--text-muted);font-weight:400;">(mínimo 10 caracteres)</span></label><input class="form-control" id="adminPassword" type="password" minlength="10" autocomplete="new-password"><small style="color:var(--text-muted);">Al editar, déjala vacía para conservar la contraseña actual.</small></div>
      <div style="display:flex;gap:10px;margin-top:20px;"><button type="button" class="btn btn-secondary" onclick="closeAdminForm()" style="flex:1;">Cancelar</button><button type="submit" class="btn btn-primary" style="flex:1;">Guardar administrador</button></div>
    </form>
  </div>
</div>

<!-- ─── MODAL 2: LECTOR QR DE ASISTENCIAS (CAMARA + DNI MANUAL) ─────────────── -->
<div class="modal-overlay qr-modal-overlay" id="modalLectorQR" style="display: none;" aria-hidden="true" onclick="if(event.target === this) closeLectorQRModal(event)">
  <div class="modal-card qr-modal-card" onclick="event.stopPropagation();" style="max-width: 540px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <h3 style="font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="qr-code" style="color: var(--primary);"></i>
        <span>Lector de Asistencias</span>
      </h3>
      <button type="button" id="btnCerrarLectorQR" aria-label="Cerrar lector de asistencias" title="Cerrar lector" class="qr-modal-close" onclick="return closeLectorQRModal(event)" onpointerdown="closeLectorQRModal(event)"><i data-lucide="x" aria-hidden="true"></i></button>
    </div>

    <!-- Toggle modo cámara vs modo DNI manual -->
    <div style="display: flex; gap: 6px; margin-bottom: 16px; background: var(--bg-tertiary); padding: 4px; border-radius: var(--radius-md);">
      <button onclick="setLectorMode('camera')" id="btnModeCamera" class="filter-tab active" style="flex: 1; text-align: center;">📷 Cámara QR</button>
      <button onclick="setLectorMode('manual')" id="btnModeManual" class="filter-tab" style="flex: 1; text-align: center;">⌨️ DNI Manual</button>
    </div>

    <!-- Modo Cámara -->
    <div id="boxCameraScanner">
      <div id="adminQrReader" style="width: 100%; border-radius: 12px; overflow: hidden; border: 2px dashed var(--primary); margin-bottom: 12px;"></div>
      <div style="font-size: 12px; color: var(--text-muted); text-align: center;">Apunta la cámara al carnet digital del socio.</div>
    </div>

    <!-- Modo DNI Manual -->
    <form id="formDniManual" onsubmit="handleManualDniCheckIn(event)" style="display: none;">
      <div class="form-group">
        <label class="form-label">Número de DNI del Socio (8 dígitos)</label>
        <input type="text" id="manualDniInput" class="form-control" maxlength="8" placeholder="Ingresa los 8 dígitos..." required style="font-size: 16px; font-weight: 700; letter-spacing: 0.05em;">
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Confirmar Asistencia</button>
    </form>

    <!-- Feedback Box -->
    <div id="checkInResultBox" style="display: none; margin-top: 16px; padding: 14px; border-radius: 10px; border: 1px solid; text-align: center;">
      <div id="checkInResultIcon" style="font-size: 28px; margin-bottom: 6px;"></div>
      <div id="checkInResultMsg" style="font-weight: 700; font-size: 14px;"></div>
    </div>
  </div>
</div>

<script src="js/admin.js"></script>
<script>
  lucide.createIcons();
</script>

</body>
</html>
