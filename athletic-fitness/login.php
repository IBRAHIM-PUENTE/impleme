<?php
session_start();
// Si ya está autenticado, redirigir automáticamente
if (!empty($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}
if (!empty($_SESSION['socio'])) {
    header('Location: socio.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GymManagerFit - Iniciar Sesión</title>
  <link rel="stylesheet" href="css/style.css">
  <!-- Lucide Icons via CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --primary: #dc2626;
      --primary-hover: #b91c1c;
      --primary-glow: rgba(220, 38, 38, 0.20);
      --shadow-glow: 0 0 25px rgba(220, 38, 38, 0.25);
    }
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-primary);
      padding: 20px;
    }
    .login-card {
      width: 100%;
      max-width: 440px;
      padding: 40px 32px;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
    }
    .brand-header {
      text-align: center;
      margin-bottom: 24px;
    }
    .brand-logo {
      width: 50px;
      height: 50px;
      border-radius: var(--radius-md);
      background: linear-gradient(135deg, var(--primary), #991b1b);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 14px auto;
      box-shadow: var(--shadow-glow);
    }
    .brand-title {
      font-size: 26px;
      font-weight: 800;
      color: var(--text-primary);
    }
    .brand-subtitle {
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 4px;
    }
    .role-tabs {
      display: flex;
      background: var(--bg-tertiary);
      padding: 4px;
      border-radius: var(--radius-md);
      margin-bottom: 22px;
      border: 1px solid var(--border-color);
    }
    .role-tab-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px;
      background: transparent;
      border: none;
      color: var(--text-secondary);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      border-radius: var(--radius-sm);
      transition: var(--transition);
    }
    .role-tab-btn.active {
      background: var(--bg-secondary);
      color: var(--primary);
      box-shadow: var(--shadow-sm);
    }
    .error-alert {
      background: var(--danger-glow);
      color: var(--danger);
      border: 1px solid rgba(239, 68, 68, 0.25);
      padding: 12px 16px;
      border-radius: var(--radius-md);
      font-size: 13px;
      display: none;
      align-items: center;
      gap: 10px;
      margin-bottom: 18px;
    }
    
    /* Estilo mejorado para el footer y el botón de retorno */
    .login-footer {
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid var(--border-color);
      text-align: center;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .server-info {
      font-size: 11.5px;
      color: var(--text-muted);
    }
    .back-home-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 8px 14px;
      background: var(--bg-tertiary);
      color: var(--text-secondary);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      font-size: 12.5px;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s ease;
      width: 100%;
    }
    .back-home-link:hover {
      background: var(--bg-primary);
      color: var(--primary);
      border-color: var(--primary);
      transform: translateY(-1px);
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="login-card card">

    <!-- Brand Header -->
    <div class="brand-header">
      <div class="brand-logo">
        <i data-lucide="sparkles" style="color: #fff; width: 26px; height: 26px;"></i>
      </div>
      <h1 class="brand-title">GymManagerFit</h1>
      <p class="brand-subtitle">Control Administrativo y Portal Deportivo</p>
    </div>

    <!-- Role Tabs: Admin vs Socio -->
    <div class="role-tabs">
      <button type="button" class="role-tab-btn active" id="tabAdminBtn" onclick="switchRole('admin')">
        <i data-lucide="shield" style="width: 15px; height: 15px;"></i>
        <span>Administrador</span>
      </button>
      <button type="button" class="role-tab-btn" id="tabSocioBtn" onclick="switchRole('socio')">
        <i data-lucide="user" style="width: 15px; height: 15px;"></i>
        <span>Portal Socio</span>
      </button>
    </div>

    <!-- Error message alert -->
    <div class="error-alert" id="errorBox">
      <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
      <span id="errorText">Error</span>
    </div>

    <!-- TAB 1: FORMULARIO ADMINISTRADOR -->
    <form id="adminForm" onsubmit="handleAdminLogin(event)">
      <div class="form-group">
        <label class="form-label">Correo Electrónico Admin</label>
        <div style="position: relative;">
          <input type="email" id="adminEmail" class="form-control" placeholder="admin@gimfit.com" required value="admin@gimfit.com">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Contraseña</label>
        <div style="position: relative;">
          <input type="password" id="adminPassword" class="form-control" placeholder="••••••••" required value="Gimfit2026!">
        </div>
      </div>

      <button type="submit" class="btn btn-primary" id="btnAdminSubmit" style="width: 100%; padding: 12px; margin-top: 10px;">
        <i data-lucide="key" style="width: 16px; height: 16px;"></i>
        <span>Ingresar al Sistema ERP</span>
      </button>
    </form>

    <!-- TAB 2: FORMULARIO PORTAL SOCIO (DNI) -->
    <form id="socioForm" onsubmit="handleSocioLogin(event)" style="display: none;">
      <div class="form-group">
        <label class="form-label">Número de DNI del Socio</label>
        <input type="text" id="socioDni" class="form-control" placeholder="Ingresa tus 8 dígitos" maxlength="8" required style="font-size: 16px; font-weight: 700; letter-spacing: 0.05em;">
        <span style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px;">
          Accede a tu carnet digital con código QR de acceso y registro de asistencia.
        </span>
      </div>

      <button type="submit" class="btn btn-primary" id="btnSocioSubmit" style="width: 100%; padding: 12px; margin-top: 10px; background: linear-gradient(135deg, var(--primary), #991b1b);">
        <i data-lucide="user-check" style="width: 16px; height: 16px;"></i>
        <span>Ingresar a Mi Portal Deportivo</span>
      </button>
    </form>

    <!-- Footer Mejorado -->
    <div class="login-footer">
      <a href="index.php" class="back-home-link">
        <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
        <span>Volver a la página principal</span>
      </a>
      <span class="server-info">Servidor Linux Debian · Base de Datos MariaDB</span>
    </div>

  </div>
</div>

<script>
  lucide.createIcons();

  function switchRole(role) {
    const tabAdmin = document.getElementById('tabAdminBtn');
    const tabSocio = document.getElementById('tabSocioBtn');
    const adminForm = document.getElementById('adminForm');
    const socioForm = document.getElementById('socioForm');
    hideError();

    if (role === 'admin') {
      tabAdmin.classList.add('active');
      tabSocio.classList.remove('active');
      adminForm.style.display = 'block';
      socioForm.style.display = 'none';
    } else {
      tabSocio.classList.add('active');
      tabAdmin.classList.remove('active');
      adminForm.style.display = 'none';
      socioForm.style.display = 'block';
      document.getElementById('socioDni').focus();
    }
  }

  function showError(msg) {
    const box = document.getElementById('errorBox');
    document.getElementById('errorText').textContent = msg;
    box.style.display = 'flex';
  }

  function hideError() {
    document.getElementById('errorBox').style.display = 'none';
  }

  async function handleAdminLogin(e) {
    e.preventDefault();
    hideError();
    const email = document.getElementById('adminEmail').value.trim();
    const password = document.getElementById('adminPassword').value.trim();
    const btn = document.getElementById('btnAdminSubmit');

    btn.disabled = true;
    btn.innerHTML = 'Verificando credenciales...';

    try {
      const res = await fetch('api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ role: 'admin', email, password })
      });
      const data = await res.json();
      if (data.success) {
        window.location.href = 'admin.php';
      } else {
        showError(data.error || 'Credenciales incorrectas.');
      }
    } catch (err) {
      showError('Error de conexión con el servidor MariaDB/PHP.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="key" style="width: 16px; height: 16px;"></i><span>Ingresar al Sistema ERP</span>';
      lucide.createIcons();
    }
  }

  async function handleSocioLogin(e) {
    e.preventDefault();
    hideError();
    const dni = document.getElementById('socioDni').value.trim();
    const btn = document.getElementById('btnSocioSubmit');

    if (!/^[0-9]{8}$/.test(dni)) {
      showError('El DNI debe tener exactamente 8 dígitos numéricos.');
      return;
    }

    btn.disabled = true;
    btn.innerHTML = 'Buscando socio...';

    try {
      const res = await fetch('api/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ role: 'socio', dni })
      });
      const data = await res.json();
      if (data.success) {
        window.location.href = 'socio.php';
      } else {
        showError(data.error || 'Socio no encontrado.');
      }
    } catch (err) {
      showError('Error al conectar con la base de datos.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="user-check" style="width: 16px; height: 16px;"></i><span>Ingresar a Mi Portal Deportivo</span>';
      lucide.createIcons();
    }
  }
</script>

</body>
</html>