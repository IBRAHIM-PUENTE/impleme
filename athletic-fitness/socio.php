<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Si no está autenticado como socio, redirigir al login
if (empty($_SESSION['socio'])) {
    header('Location: index.php');
    exit();
}

$socio = $_SESSION['socio'];
$db = getDB();

// Refrescar datos del socio desde MariaDB
$stmt = $db->prepare("SELECT * FROM clientes WHERE id = ? AND deleted_at IS NULL LIMIT 1");
$stmt->execute([$socio['id']]);
$socio = $stmt->fetch() ?: $socio;
$_SESSION['socio'] = $socio;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal del Socio - GymManagerFit</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Generador de Código QR en Vanilla JS (Ligero y sin dependencias) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <!-- Lector de Código QR por Cámara en Vanilla JS -->
  <script src="https://unpkg.com/html5-qrcode"></script>

  <style>
    .socio-layout {
      max-width: 1040px;
      margin: 24px auto;
      padding: 0 16px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
      gap: 20px;
    }
    .top-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 24px;
      background: var(--bg-secondary);
      border-bottom: 1px solid var(--border-color);
      position: sticky;
      top: 0;
      z-index: 50;
    }
    .pass-card {
      padding: 24px;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    .user-header-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 12px;
    }
    .avatar-circle {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), #7c3aed);
      color: #fff;
      font-size: 22px;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid;
    }
    .qr-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 18px;
      background: var(--bg-tertiary);
      border-radius: var(--radius-lg);
      border: 1px solid var(--border-color);
      gap: 10px;
    }
    .qr-frame {
      background: #ffffff;
      padding: 12px;
      border-radius: 16px;
      cursor: pointer;
      box-shadow: var(--shadow-md);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      transition: transform 0.15s;
    }
    .qr-frame:hover {
      transform: scale(1.02);
    }
    .progress-box {
      display: flex;
      flex-direction: column;
      gap: 8px;
      padding: 14px 16px;
      background: var(--bg-tertiary);
      border-radius: 12px;
      border: 1px solid var(--border-color);
    }
    .progress-bar-bg {
      width: 100%;
      height: 8px;
      background: var(--bg-secondary);
      border-radius: 99px;
      overflow: hidden;
    }
    .progress-bar-fill {
      height: 100%;
      border-radius: 99px;
      transition: width 0.4s ease;
    }
    .metrics-3grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
    }
    .metric-tile {
      padding: 14px 12px;
      background: var(--bg-tertiary);
      border-radius: 12px;
      border: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      gap: 10px;
    }
  </style>
</head>
<body>

  <!-- Top Bar -->
  <header class="top-header">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), #7c3aed); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-glow);">
        <i data-lucide="sparkles" style="color: #fff; width: 18px; height: 18px;"></i>
      </div>
      <div>
        <h1 style="font-size: 17px; font-weight: 800; line-height: 1.1;">GymManagerFit</h1>
        <span style="font-size: 11px; color: var(--primary); font-weight: 600;">Pase Digital del Socio</span>
      </div>
    </div>

    <button onclick="logout()" class="btn btn-secondary" style="padding: 7px 14px; font-size: 12.5px;">
      <i data-lucide="log-out" style="width: 15px; height: 15px;"></i>
      <span>Salir</span>
    </button>
  </header>

  <!-- Main Grid (Todo a la vista sin pestañas ocultas) -->
  <main class="socio-layout">

    <!-- COLUMNA IZQUIERDA: PASE DIGITAL + QR + CHECK-IN -->
    <section>
      <div class="card pass-card">

        <!-- Socio Info & Status -->
        <div class="user-header-row">
          <div style="display: flex; align-items: center; gap: 14px;">
            <div class="avatar-circle" id="socioAvatar" style="border-color: <?= $socio['status'] === 'Activo' ? 'var(--success)' : ($socio['status'] === 'Por vencer' ? 'var(--warning)' : 'var(--danger)') ?>">
              <?= strtoupper(substr($socio['name'], 0, 1)) ?>
            </div>
            <div>
              <h2 style="font-size: 19px; font-weight: 800;"><?= htmlspecialchars($socio['name']) ?></h2>
              <div style="font-size: 12.5px; color: var(--text-secondary); margin-top: 3px;">
                DNI: <strong><?= htmlspecialchars($socio['dni']) ?></strong> <?= !empty($socio['phone']) ? '· Cel: ' . htmlspecialchars($socio['phone']) : '' ?>
              </div>
            </div>
          </div>

          <span class="badge <?= $socio['status'] === 'Activo' ? 'badge-success' : ($socio['status'] === 'Por vencer' ? 'badge-warning' : 'badge-danger') ?>" id="statusBadge">
            <?= htmlspecialchars($socio['status']) ?>
          </span>
        </div>

        <!-- Código QR visible -->
        <div class="qr-box">
          <div class="qr-frame" onclick="openQRModal()" title="Clic para ver en grande">
            <div id="qrcodeCanvas"></div>
            <div style="display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #475569; margin-top: 4px;">
              <i data-lucide="maximize-2" style="width: 12px; height: 12px;"></i>
              <span>Toca para ampliar</span>
            </div>
          </div>
          <div style="font-size: 12px; color: var(--text-muted); text-align: center;">
            Muestra este código en recepción o marca asistencia con el botón abajo
          </div>
        </div>

        <!-- Progreso de Membresía -->
        <div class="progress-box">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px; font-weight: 700;">
              <i data-lucide="award" style="width: 15px; height: 15px; color: var(--primary);"></i>
              <?= htmlspecialchars($socio['last_membership'] ?: 'Plan Mensual') ?>
            </span>
            <span style="font-size: 12.5px; font-weight: 700; color: <?= $socio['status'] === 'Activo' ? 'var(--success)' : 'var(--danger)' ?>;">
              <?= $socio['status'] === 'Vencido' ? 'Vencida' : ($socio['days_remaining'] . ' días restantes') ?>
            </span>
          </div>

          <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width: <?= min(100, max(0, ($socio['days_remaining'] / 30) * 100)) ?>%; background: <?= $socio['status'] === 'Activo' ? 'var(--success)' : 'var(--danger)' ?>;"></div>
          </div>

          <div style="display: flex; justify-content: space-between; font-size: 11.5px; color: var(--text-muted);">
            <span>Inicio: <strong><?= $socio['start_date'] ?: '—' ?></strong></span>
            <span>Vence: <strong><?= $socio['end_date'] ?: '—' ?></strong></span>
          </div>
        </div>

        <!-- Alerta de Feedback -->
        <div id="feedbackAlert" style="display: none; padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; border: 1px solid;"></div>

        <!-- Botones de Acción de Asistencia -->
        <div style="display: flex; gap: 10px;">
          <button onclick="registrarAsistenciaDirecta()" id="btnCheckIn" class="btn btn-primary" style="flex: 1; padding: 14px; font-size: 13.5px; <?= $socio['last_assist_date'] === date('Y-m-d') ? 'background: linear-gradient(135deg, #059669, #10b981);' : '' ?>">
            <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
            <span id="btnCheckInText">
              <?= $socio['last_assist_date'] === date('Y-m-d') ? 'Asistencia de Hoy Confirmada (' . substr($socio['last_assist_time'], 0, 5) . ')' : 'Registrar Mi Asistencia Ahora' ?>
            </span>
          </button>

          <button onclick="openScannerModal()" class="btn btn-secondary" title="Abrir escáner QR de cámara">
            <i data-lucide="camera" style="width: 17px; height: 17px;"></i>
          </button>
        </div>

      </div>
    </section>

    <!-- COLUMNA DERECHA: ASISTENCIAS + FICHA FÍSICA -->
    <section style="display: flex; flex-direction: column; gap: 20px;">

      <!-- Tarjeta de Asistencias -->
      <div class="card" style="padding: 22px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <i data-lucide="activity" style="width: 18px; height: 18px; color: var(--success);"></i>
            <h3 style="font-size: 15.5px; font-weight: 700;">Mis Asistencias</h3>
          </div>
          <span class="badge badge-success">En vivo</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
          <div style="padding: 16px; background: var(--bg-tertiary); border-radius: 12px; border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 4px;">
            <div style="font-size: 28px; font-weight: 800; color: var(--success); line-height: 1.1;" id="totalAssistsCount">
              <?= $socio['total_assists'] ?: 0 ?>
            </div>
            <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 500;">Días Entrenados</div>
          </div>

          <div style="padding: 16px; background: var(--bg-tertiary); border-radius: 12px; border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 4px;">
            <div style="font-size: 17px; font-weight: 800; color: var(--primary);" id="lastAssistDateText">
              <?= $socio['last_assist_date'] ?: 'Sin registro' ?>
            </div>
            <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 500;">
              Última Visita <?= $socio['last_assist_time'] ? '(' . substr($socio['last_assist_time'], 0, 5) . ')' : '' ?>
            </div>
          </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; background: var(--bg-tertiary); border-radius: 10px; border: 1px solid var(--border-color); font-size: 12.5px; color: var(--text-secondary);">
          <div style="width: 10px; height: 10px; border-radius: 50%; background: <?= $socio['last_assist_date'] === date('Y-m-d') ? 'var(--success)' : 'var(--warning)' ?>; flex-shrink: 0;"></div>
          <span>
            <?= $socio['last_assist_date'] === date('Y-m-d') 
              ? '¡Excelente! Ya registraste tu entrenamiento de hoy. 💪' 
              : 'Aún no registras asistencia hoy. Presiona el botón para marcar tu entrada.' ?>
          </span>
        </div>
      </div>

      <!-- Tarjeta de Ficha Física y Objetivos -->
      <div class="card" style="padding: 22px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
          <i data-lucide="target" style="width: 18px; height: 18px; color: var(--primary);"></i>
          <h3 style="font-size: 15.5px; font-weight: 700;">Mi Ficha Corporal y Objetivo</h3>
        </div>

        <?php
          $weight = (float)$socio['weight'];
          $height = (float)$socio['height'];
          $imc = ($weight > 0 && $height > 0) ? round($weight / ($height * $height), 1) : null;
          $imcCat = 'Saludable';
          if ($imc) {
            if ($imc < 18.5) $imcCat = 'Bajo peso';
            elseif ($imc < 25) $imcCat = 'Peso ideal';
            elseif ($imc < 30) $imcCat = 'Sobrepeso';
            else $imcCat = 'Obesidad';
          }
        ?>

        <div class="metrics-3grid" style="margin-bottom: 16px;">
          <div class="metric-tile">
            <i data-lucide="weight" style="width: 18px; height: 18px; color: var(--primary);"></i>
            <div>
              <div style="font-size: 15px; font-weight: 800;"><?= $weight ? $weight . ' kg' : '—' ?></div>
              <div style="font-size: 11px; color: var(--text-muted);">Peso</div>
            </div>
          </div>

          <div class="metric-tile">
            <i data-lucide="ruler" style="width: 18px; height: 18px; color: var(--primary);"></i>
            <div>
              <div style="font-size: 15px; font-weight: 800;"><?= $height ? $height . ' m' : '—' ?></div>
              <div style="font-size: 11px; color: var(--text-muted);">Estatura</div>
            </div>
          </div>

          <div class="metric-tile">
            <i data-lucide="shield-check" style="width: 18px; height: 18px; color: var(--success);"></i>
            <div>
              <div style="font-size: 15px; font-weight: 800; color: var(--success);"><?= $imc ?: '—' ?></div>
              <div style="font-size: 11px; color: var(--text-muted);">IMC (<?= $imcCat ?>)</div>
            </div>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
          <div style="padding: 12px 14px; background: var(--bg-tertiary); border-radius: 10px; border: 1px solid var(--border-color); font-size: 13px;">
            <div style="font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Objetivo Deportivo</div>
            <div style="font-weight: 700; color: var(--text-primary); margin-top: 2px;">
              <?= htmlspecialchars($socio['objective'] ?: 'Mejorar condición física y salud') ?>
            </div>
          </div>

          <?php if (!empty($socio['notes']) && $socio['notes'] !== 'Ninguna'): ?>
          <div style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 10px; font-size: 12.5px; color: var(--text-secondary);">
            <i data-lucide="alert-circle" style="width: 16px; height: 16px; color: var(--warning); flex-shrink: 0;"></i>
            <span><strong>Observaciones:</strong> <?= htmlspecialchars($socio['notes']) ?></span>
          </div>
          <?php endif; ?>
        </div>

      </div>

    </section>

  </main>

  <!-- MODAL 1: QR FULLSCREEN PARA RECEPCIÓN -->
  <div class="modal-overlay" id="qrModal" style="display: none;" onclick="closeQRModal()">
    <div class="modal-card" onclick="event.stopPropagation();" style="text-align: center;">
      <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 4px;"><?= htmlspecialchars($socio['name']) ?></h3>
      <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">Acerca este código al escáner de la recepción</p>
      
      <div style="display: flex; justify-content: center; padding: 20px; background: #fff; border-radius: 16px; margin-bottom: 16px;">
        <div id="qrcodeCanvasLarge"></div>
      </div>

      <div style="font-size: 18px; font-weight: 800; letter-spacing: 0.08em; margin-bottom: 20px;">
        DNI: <?= htmlspecialchars($socio['dni']) ?>
      </div>

      <button onclick="closeQRModal()" class="btn btn-primary" style="width: 100%;">Cerrar</button>
    </div>
  </div>

  <!-- MODAL 2: ESCÁNER QR POR CÁMARA -->
  <div class="modal-overlay" id="scannerModal" style="display: none;" onclick="closeScannerModal()">
    <div class="modal-card" onclick="event.stopPropagation();">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 700;">Lector QR de Asistencia</h3>
        <button onclick="closeScannerModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer;">✕</button>
      </div>

      <div id="qrReaderContainer" style="width: 100%; border-radius: 12px; overflow: hidden; border: 2px dashed var(--primary); margin-bottom: 14px;"></div>
      <div id="scannerFeedback" style="font-size: 13px; text-align: center; color: var(--text-muted);">Apunta tu cámara al código QR de acceso</div>
    </div>
  </div>

  <script>
    lucide.createIcons();

    const socioDni = "<?= $socio['dni'] ?>";

    // 1. Renderizar código QR del socio
    new QRCode(document.getElementById("qrcodeCanvas"), {
      text: socioDni,
      width: 160,
      height: 160,
      colorDark: "#0f172a",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });

    let largeQrCreated = false;
    function openQRModal() {
      document.getElementById('qrModal').style.display = 'flex';
      if (!largeQrCreated) {
        new QRCode(document.getElementById("qrcodeCanvasLarge"), {
          text: socioDni,
          width: 240,
          height: 240,
          colorDark: "#0f172a",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });
        largeQrCreated = true;
      }
    }
    function closeQRModal() {
      document.getElementById('qrModal').style.display = 'none';
    }

    // 2. Registro de Asistencia Directa en 1 Clic
    async function registrarAsistenciaDirecta() {
      const btn = document.getElementById('btnCheckIn');
      const text = document.getElementById('btnCheckInText');
      const alertBox = document.getElementById('feedbackAlert');

      btn.disabled = true;
      text.textContent = 'Registrando en MariaDB...';

      try {
        const res = await fetch('api/asistencias.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ dni: socioDni, metodo: 'Portal Socio Directo' })
        });
        const data = await res.json();

        if (data.success) {
          playAudioSuccess();
          alertBox.style.display = 'block';
          alertBox.style.background = 'var(--success-glow)';
          alertBox.style.color = 'var(--success)';
          alertBox.style.borderColor = 'var(--success)';
          alertBox.textContent = data.message;

          btn.style.background = 'linear-gradient(135deg, #059669, #10b981)';
          text.textContent = 'Asistencia Confirmada Hoy';
          document.getElementById('totalAssistsCount').textContent = data.client.total_assists;
          document.getElementById('lastAssistDateText').textContent = data.client.last_assist_date;
        } else {
          alertBox.style.display = 'block';
          alertBox.style.background = 'var(--danger-glow)';
          alertBox.style.color = 'var(--danger)';
          alertBox.style.borderColor = 'var(--danger)';
          alertBox.textContent = data.error || 'No se pudo registrar la asistencia.';
        }
      } catch (e) {
        alert('Error al conectar con MariaDB: ' + e.message);
      } finally {
        btn.disabled = false;
      }
    }

    // 3. Audio Synth Feedback
    function playAudioSuccess() {
      try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.setValueAtTime(587.33, ctx.currentTime);
        osc.frequency.setValueAtTime(880, ctx.currentTime + 0.12);
        gain.gain.setValueAtTime(0.15, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
        osc.start(); osc.stop(ctx.currentTime + 0.35);
      } catch (e) {}
    }

    // 4. Lector QR por Cámara
    let html5Qr = null;
    function openScannerModal() {
      document.getElementById('scannerModal').style.display = 'flex';
      html5Qr = new Html5Qrcode("qrReaderContainer");
      html5Qr.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        async (decodedText) => {
          html5Qr.stop().catch(()=>{});
          document.getElementById('scannerFeedback').textContent = 'Procesando DNI: ' + decodedText;
          // Marcar asistencia con el DNI detectado
          const res = await fetch('api/asistencias.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ dni: decodedText, metodo: 'Cámara QR' })
          });
          const d = await res.json();
          alert(d.success ? d.message : d.error);
          closeScannerModal();
          location.reload();
        },
        () => {}
      ).catch(e => {
        document.getElementById('scannerFeedback').textContent = 'No se pudo iniciar la cámara: ' + e;
      });
    }

    function closeScannerModal() {
      if (html5Qr) { html5Qr.stop().catch(()=>{}); }
      document.getElementById('scannerModal').style.display = 'none';
    }

    // 5. Cerrar Sesión
    async function logout() {
      await fetch('api/auth.php?action=logout');
      window.location.href = 'index.php';
    }
  </script>

</body>
</html>
