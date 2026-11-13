/**
 * GymManagerFit - Lógica Frontend Administrador en Vanilla JavaScript
 * Conectado a la API PHP y MariaDB
 */

let allClientes = [];
let allPlanes = [];
let currentFilter = 'Todos';
let currentSearch = '';
let adminQrStartToken = 0;

document.addEventListener('DOMContentLoaded', () => {
  loadDashboard();
  loadPlanes();
});

// ─── Navegación entre vistas ──────────────────────────────────────────────────
function switchView(viewName) {
  const views = ['dashboard', 'clientes', 'asistencias', 'venta', 'caja', 'administradores', 'auditoria', 'asistente-ia'];
  views.forEach(v => {
    const el = document.getElementById(`view-${v}`);
    const nav = document.getElementById(`nav-${v}`);
    if (el) el.style.display = (v === viewName) ? 'block' : 'none';
    if (nav) nav.classList.toggle('active', v === viewName);
  });

  if (viewName === 'dashboard') loadDashboard();
  if (viewName === 'clientes') loadClientes();
  if (viewName === 'asistencias') loadAsistencias();
  if (viewName === 'venta') prepareVentaRapida();
  if (viewName === 'caja') loadCaja();
  if (viewName === 'auditoria') loadAuditoria();
  if (viewName === 'administradores') loadAdministradores();

  lucide.createIcons();
}

// ─── 1. DASHBOARD ─────────────────────────────────────────────────────────────
async function loadDashboard() {
  const setText = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value; };
  try {
    const res = await fetch('api/dashboard.php', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    if (!data.success) throw new Error(data.error || 'No se pudieron obtener las estadísticas.');

    const k = data.kpis || {};
    setText('kpi-activos', Number(k.activeClients || 0).toLocaleString('es-PE'));
    setText('kpi-porvencer', Number(k.expiringClients || 0).toLocaleString('es-PE'));
    setText('kpi-ingresos', 'S/ ' + Number(k.monthRevenue || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    setText('kpi-asistencias', Number(k.todayAssists || 0).toLocaleString('es-PE'));
    setText('kpi-total-socios', Number(k.totalClients || 0).toLocaleString('es-PE'));
    setText('kpi-ingresos-hoy', 'S/ ' + Number(k.todayRevenue || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    setText('kpi-pagos-pendientes', Number(k.pendingPayments || 0).toLocaleString('es-PE'));
    setText('kpi-monto-pendiente', 'Por cobrar: S/ ' + Number(k.pendingAmount || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

    renderDashboardBars('chart-asistencias', data.attendanceTrend || [], 'total', 'var(--primary)', v => `${v} asistencias`);
    renderDashboardBars('chart-ingresos', data.revenueTrend || [], 'total', 'var(--success)', v => `S/ ${Number(v).toFixed(2)}`);

    const tbody = document.getElementById('tbody-expiring');
    if (tbody) {
      if (!data.expiringList || data.expiringList.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:20px;">No hay vencimientos en los siguientes 7 días.</td></tr>';
      } else {
        tbody.innerHTML = data.expiringList.map(c => `
          <tr>
            <td><strong>${escapeHtml(c.name || '')}</strong></td>
            <td><code>${maskDni(c.dni || '')}</code></td>
            <td>${escapeHtml(c.phone || '—')}</td>
            <td>${escapeHtml(c.last_membership || '—')}</td>
            <td>${escapeHtml(c.end_date || '—')}</td>
            <td><span class="badge badge-warning">${Math.max(0, Number(c.days_remaining || 0))} días</span></td>
            <td><button onclick="iniciarCobroDirecto(${Number(c.id)})" class="btn btn-primary" style="padding:6px 12px;font-size:12px;">Renovar</button></td>
          </tr>`).join('');
      }
    }
    if (window.lucide) lucide.createIcons();
  } catch (e) {
    console.error('Error al cargar dashboard:', e);
    ['tbody-expiring', 'chart-asistencias', 'chart-ingresos'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerHTML = '<p style="color:var(--danger);font-size:13px;">No se pudieron cargar los datos. Intenta actualizar el panel.</p>';
    });
  }
}

function renderDashboardBars(containerId, rows, valueKey, barColor, valueLabel) {
  const container = document.getElementById(containerId);
  if (!container) return;
  if (!rows.length) {
    container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;">No hay datos disponibles.</p>';
    return;
  }
  const values = rows.map(row => Math.max(0, Number(row[valueKey] || 0)));
  const max = Math.max(...values, 1);
  container.innerHTML = rows.map((row, index) => {
    const value = values[index];
    const height = Math.max(value > 0 ? 5 : 2, (value / max) * 112);
    const label = escapeHtml(row.label || '');
    const tooltip = escapeHtml(valueLabel(value));
    return `<div title="${tooltip}" style="flex:1;min-width:0;height:100%;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;gap:7px;">
      <span style="font-size:10px;color:var(--text-secondary);white-space:nowrap;">${value > 0 ? (containerId === 'chart-ingresos' ? value.toFixed(0) : value) : ''}</span>
      <div style="width:100%;max-width:34px;height:${height}px;background:${barColor};border-radius:6px 6px 2px 2px;opacity:${value > 0 ? 0.9 : 0.2};"></div>
      <span style="font-size:10px;color:var(--text-muted);white-space:nowrap;">${label}</span>
    </div>`;
  }).join('');
}


// ─── 2. CLIENTES ──────────────────────────────────────────────────────────────
async function loadClientes() {
  try {
    const res = await fetch(`api/clientes.php?status=${encodeURIComponent(currentFilter)}&search=${encodeURIComponent(currentSearch)}`);
    const data = await res.json();
    if (!data.success) return;

    allClientes = data.data;
    renderClientesTable(allClientes);
  } catch (e) {
    console.error('Error al cargar clientes:', e);
  }
}

function renderClientesTable(list) {
  const tbody = document.getElementById('tbody-clientes');
  if (!list || list.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 24px;">No se encontraron socios con los filtros actuales.</td></tr>';
    return;
  }

  tbody.innerHTML = list.map(c => {
    const isInactive = (c.status === 'Inactivo' || c.status === 'Retirado');
    const badgeClass = c.status === 'Activo' ? 'badge-success' : (c.status === 'Por vencer' ? 'badge-warning' : (isInactive ? 'badge-secondary' : 'badge-danger'));

    return `
      <tr style="opacity: ${isInactive ? '0.6' : '1'};">
        <td>
          <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 12px;">
              ${escapeHtml(c.name.charAt(0))}
            </div>
            <div>
              <div style="font-weight: 700;">${escapeHtml(c.name)}</div>
              <div style="font-size: 11px; color: var(--text-muted);">${c.total_assists || 0} asistencias</div>
            </div>
          </div>
        </td>
        <td><code>${maskDni(c.dni)}</code></td>
        <td>${c.phone || '—'}</td>
        <td>${c.weight > 0 ? c.weight + ' kg' : '—'} / ${c.height > 0 ? c.height + ' m' : '—'}</td>
        <td><span class="badge ${badgeClass}">${c.status}</span></td>
        <td>
          <div style="font-weight: 600;">${escapeHtml(c.last_membership)}</div>
          <div style="font-size: 11px; color: var(--text-muted);">${c.end_date ? 'Vence: ' + c.end_date : 'Sin plan'}</div>
        </td>
        <td><strong style="color: var(--success);">${c.total_assists || 0}</strong> días</td>
        <td>
          <div style="display: flex; gap: 6px;">
            <button onclick="iniciarCobroDirecto(${c.id})" class="btn btn-secondary btn-icon" title="Cobrar / Renovar">
              <i data-lucide="credit-card" style="width: 14px; height: 14px; color: var(--primary);"></i>
            </button>
            <button onclick="editarCliente(${c.id})" class="btn btn-secondary btn-icon" title="Editar Ficha">
              <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
            </button>
            ${isInactive 
              ? `<button onclick="toggleEstadoCliente(${c.id}, 'Activo', '${escapeHtml(c.name)}')" class="btn btn-secondary btn-icon" title="Reactivar Socio (Pasar a Activo)">
                   <i data-lucide="user-check" style="width: 14px; height: 14px; color: var(--success);"></i>
                 </button>`
              : `<button onclick="toggleEstadoCliente(${c.id}, 'Inactivo', '${escapeHtml(c.name)}')" class="btn btn-secondary btn-icon" title="Desactivar Socio (Sin borrar de la BD)">
                   <i data-lucide="user-x" style="width: 14px; height: 14px; color: var(--warning);"></i>
                 </button>`
            }
          </div>
        </td>
      </tr>
    `;
  }).join('');

  lucide.createIcons();
}

function filterClientes(status, btn) {
  currentFilter = status;
  document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  loadClientes();
}

function handleGlobalSearch(query) {
  currentSearch = query.trim();
  loadClientes();
}

// ─── 3. MODAL REGISTRAR / EDITAR CLIENTE ──────────────────────────────────────
function openModalNuevoCliente() {
  document.getElementById('modalClienteTitle').textContent = 'Registrar Nuevo Socio';
  document.getElementById('cliId').value = '';
  document.getElementById('cliName').value = '';
  document.getElementById('cliDni').value = '';
  document.getElementById('cliPhone').value = '';
  document.getElementById('cliWeight').value = '';
  document.getElementById('cliHeight').value = '';
  document.getElementById('cliObjective').value = '';
  document.getElementById('cliNotes').value = '';
  document.getElementById('modalCliente').style.display = 'flex';
}

function editarCliente(id) {
  const c = allClientes.find(x => x.id === id);
  if (!c) return;

  document.getElementById('modalClienteTitle').textContent = 'Editar Ficha: ' + c.name;
  document.getElementById('cliId').value = c.id;
  document.getElementById('cliName').value = c.name;
  document.getElementById('cliDni').value = c.dni;
  document.getElementById('cliPhone').value = c.phone || '';
  document.getElementById('cliWeight').value = c.weight || '';
  document.getElementById('cliHeight').value = c.height || '';
  document.getElementById('cliObjective').value = c.objective || '';
  document.getElementById('cliNotes').value = c.notes || '';
  document.getElementById('modalCliente').style.display = 'flex';
}

function closeModalCliente() {
  document.getElementById('modalCliente').style.display = 'none';
}

async function handleGuardarCliente(e) {
  e.preventDefault();
  const id = document.getElementById('cliId').value;
  const name = document.getElementById('cliName').value.trim();
  const dni = document.getElementById('cliDni').value.trim();
  const phone = document.getElementById('cliPhone').value.trim();
  const weight = parseFloat(document.getElementById('cliWeight').value) || 0;
  const height = parseFloat(document.getElementById('cliHeight').value) || 0;
  const objective = document.getElementById('cliObjective').value.trim();
  const notes = document.getElementById('cliNotes').value.trim();

  if (!/^[0-9]{8}$/.test(dni)) {
    showToast('El DNI debe tener exactamente 8 dígitos numéricos.', 'error');
    return;
  }

  const endpoint = id ? 'api/clientes.php?action=update' : 'api/clientes.php';
  const res = await fetch(endpoint, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, name, dni, phone, weight, height, objective, notes })
  });
  const data = await res.json();
  if (data.success) {
    showToast(data.message);
    closeModalCliente();
    loadClientes();
  } else {
    showToast(data.error || 'No se pudo guardar el socio.', 'error');
  }
}

// Desactivar / Reactivar Socio (Soft deactivation sin borrar historial)
async function toggleEstadoCliente(id, newStatus, name) {
  const actionText = newStatus === 'Inactivo' ? 'desactivar' : 'reactivar';
  const accepted = await showConfirm({
    title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} socio`,
    message: `¿Deseas ${actionText} a \"${name}\"?\n\n${newStatus === 'Inactivo' ? 'El socio quedará inactivo, conservando sus pagos e historial.' : 'El socio volverá a estar activo.'}`,
    confirmText: actionText === 'desactivar' ? 'Desactivar' : 'Reactivar',
    danger: newStatus === 'Inactivo'
  });
  if (!accepted) return;

  try {
    const res = await fetch('api/clientes.php?action=toggle_status', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, status: newStatus })
    });
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.error || 'No se pudo actualizar el estado del socio.');
    showToast(data.message || 'Estado actualizado correctamente.', 'success');
    loadClientes(); loadDashboard();
  } catch (err) { showToast(err.message || 'Error de conexión.', 'error'); }
}

// ─── 4. VENTA RÁPIDA ──────────────────────────────────────────────────────────
async function loadPlanes() {
  try {
    const res = await fetch('api/membresias.php');
    const data = await res.json();
    if (data.success) allPlanes = data.data;
  } catch (e) {}
}

async function prepareVentaRapida() {
  await loadPlanes();
  const searchInput = document.getElementById('ventaBuscarCliente');
  const selectedInput = document.getElementById('ventaSelectCliente');
  const results = document.getElementById('ventaResultadosCliente');
  const selectedNote = document.getElementById('ventaClienteSeleccionado');

  // Inicializar el autocompletado una sola vez.
  if (searchInput && !searchInput.dataset.bound) {
    searchInput.dataset.bound = '1';
    let searchTimer = null;
    let requestSequence = 0;

    searchInput.addEventListener('input', () => {
      selectedInput.value = '';
      selectedNote.textContent = 'Selecciona un socio de los resultados.';
      const query = searchInput.value.trim();
      clearTimeout(searchTimer);
      if (query.length < 2) {
        hideVentaClientResults();
        selectedNote.textContent = 'Escribe al menos 2 caracteres para buscar.';
        return;
      }
      results.hidden = false;
      searchInput.setAttribute('aria-expanded', 'true');
      results.innerHTML = '<div class="venta-autocomplete-estado">Buscando socios...</div>';
      searchTimer = setTimeout(async () => {
        const currentRequest = ++requestSequence;
        try {
          const response = await fetch(`api/clientes.php?status=Todos&search=${encodeURIComponent(query)}`, {
            headers: { 'Accept': 'application/json' }
          });
          const data = await response.json();
          if (currentRequest !== requestSequence) return;
          if (!response.ok || !data.success) throw new Error(data.error || 'No se pudo realizar la búsqueda.');
          const matches = (data.data || []).slice(0, 8);
          if (!matches.length) {
            results.innerHTML = '<div class="venta-autocomplete-estado">No se encontraron socios.</div>';
            return;
          }
          results.innerHTML = matches.map(client => `
            <button type="button" class="venta-autocomplete-item" role="option"
              data-id="${Number(client.id)}" data-name="${escapeHtml(client.name || '')}"
              data-dni="${escapeHtml(client.dni || '')}" data-status="${escapeHtml(client.status || '')}">
              <span class="venta-autocomplete-avatar">${escapeHtml((client.name || '?').charAt(0).toUpperCase())}</span>
              <span class="venta-autocomplete-info"><strong>${escapeHtml(client.name || 'Sin nombre')}</strong><small>DNI: ${escapeHtml(client.dni || '—')}</small></span>
              <span class="venta-autocomplete-status">${escapeHtml(client.status || '')}</span>
            </button>`).join('');
        } catch (error) {
          if (currentRequest !== requestSequence) return;
          results.innerHTML = `<div class="venta-autocomplete-estado venta-autocomplete-error">${escapeHtml(error.message || 'Error al buscar socios.')}</div>`;
        }
      }, 250);
    });

    results.addEventListener('click', event => {
      const option = event.target.closest('.venta-autocomplete-item');
      if (!option) return;
      selectedInput.value = option.dataset.id;
      searchInput.value = `${option.dataset.name} — DNI: ${option.dataset.dni}`;
      selectedNote.textContent = `Socio seleccionado: ${option.dataset.name}`;
      hideVentaClientResults();
    });

    searchInput.addEventListener('keydown', event => {
      if (event.key === 'Escape') hideVentaClientResults();
      if (event.key === 'Enter' && !selectedInput.value) {
        event.preventDefault();
        const first = results.querySelector('.venta-autocomplete-item');
        if (first) first.click();
      }
    });
    document.addEventListener('click', event => {
      if (!event.target.closest('#ventaAutocomplete')) hideVentaClientResults();
    });
  }

  const selPlan = document.getElementById('ventaSelectPlan');
  if (selPlan && !selPlan.dataset.loaded) {
    selPlan.innerHTML = '<option value="">Selecciona un plan...</option>' +
      allPlanes.map(plan => `<option value="${plan.id}" data-price="${plan.price}" data-days="${plan.duration_days}" data-name="${escapeHtml(plan.name)}">${escapeHtml(plan.name)} — S/ ${plan.price}</option>`).join('');
    selPlan.dataset.loaded = '1';
  }
  recalculateVentaEndDate();
}

function hideVentaClientResults() {
  const results = document.getElementById('ventaResultadosCliente');
  const input = document.getElementById('ventaBuscarCliente');
  if (results) results.hidden = true;
  if (input) input.setAttribute('aria-expanded', 'false');
}

function iniciarCobroDirecto(clientId) {
  switchView('venta');
  setTimeout(async () => {
    const searchInput = document.getElementById('ventaBuscarCliente');
    const selectedInput = document.getElementById('ventaSelectCliente');
    try {
      const response = await fetch('api/clientes.php?status=Todos');
      const data = await response.json();
      const client = (data.data || []).find(item => String(item.id) === String(clientId));
      if (client) {
        selectedInput.value = client.id;
        searchInput.value = `${client.name} — DNI: ${client.dni}`;
        document.getElementById('ventaClienteSeleccionado').textContent = `Socio seleccionado: ${client.name}`;
      }
    } catch (error) {
      console.error('No se pudo precargar el socio para la venta:', error);
    }
  }, 100);
}

function handleSelectPlanChange() {
  const selPlan = document.getElementById('ventaSelectPlan');
  const opt = selPlan.options[selPlan.selectedIndex];
  if (opt && opt.value) {
    document.getElementById('ventaMonto').value = opt.getAttribute('data-price');
  }
  recalculateVentaEndDate();
}

function handleSelectClienteChange() {}

function recalculateVentaEndDate() {
  const startDateStr = document.getElementById('ventaFechaInicio').value;
  const selPlan = document.getElementById('ventaSelectPlan');
  const opt = selPlan.options[selPlan.selectedIndex];
  const days = opt && opt.value ? parseInt(opt.getAttribute('data-days'), 10) : 30;

  if (startDateStr) {
    const d = new Date(startDateStr);
    d.setDate(d.getDate() + days);
    document.getElementById('ventaFechaFin').value = d.toISOString().slice(0, 10);
  }
}

async function handleVentaSubmit(e) {
  e.preventDefault();
  const clientId = document.getElementById('ventaSelectCliente').value;
  const clientName = document.getElementById('ventaBuscarCliente').value.split(' — DNI:')[0].trim();
  if (!clientId) {
    showToast('Debes buscar y seleccionar un socio de la lista.', 'error');
    document.getElementById('ventaBuscarCliente').focus();
    return;
  }

  const selPlan = document.getElementById('ventaSelectPlan');
  const optPlan = selPlan.options[selPlan.selectedIndex];
  const planName = optPlan.getAttribute('data-name');
  const durationDays = parseInt(optPlan.getAttribute('data-days'), 10);

  const amount = parseFloat(document.getElementById('ventaMonto').value);
  const startDate = document.getElementById('ventaFechaInicio').value;
  const endDate = document.getElementById('ventaFechaFin').value;

  const btn = document.getElementById('btnVentaSubmit');
  btn.disabled = true;

  try {
    const res = await fetch('api/pagos.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ clientId, clientName, planName, amount, durationDays, startDate, endDate })
    });
    const data = await res.json();
    if (data.success) {
      showToast(data.message);
      switchView('caja');
    } else {
      showToast(data.error || 'No se pudo procesar el pago.', 'error');
    }
  } catch (err) {
    showToast('No se pudo conectar con el servidor. Intenta nuevamente.', 'error');
  } finally {
    btn.disabled = false;
  }
}

// ─── 5. CAJA & PAGOS ──────────────────────────────────────────────────────────
async function loadCaja() {
  try {
    const res = await fetch('api/pagos.php');
    const data = await res.json();
    if (!data.success) return;

    document.getElementById('caja-hoy').textContent = 'S/ ' + data.summary.todayRevenue.toFixed(2);
    document.getElementById('caja-mes').textContent = 'S/ ' + data.summary.monthRevenue.toFixed(2);
    document.getElementById('caja-total').textContent = 'S/ ' + data.summary.totalRevenue.toFixed(2);

    const tbody = document.getElementById('tbody-pagos');
    if (!data.data || data.data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 24px;">No hay transacciones registradas.</td></tr>';
      return;
    }

    tbody.innerHTML = data.data.map(p => `
      <tr>
        <td>#${p.id}</td>
        <td><strong>${escapeHtml(p.client_name)}</strong></td>
        <td>${escapeHtml(p.membership_name)}</td>
        <td><strong style="color: var(--success);">S/ ${parseFloat(p.amount).toFixed(2)}</strong></td>
        <td>${p.date}</td>
        <td><span class="badge badge-success">${p.status}</span></td>
      </tr>
    `).join('');
  } catch (e) {
    console.error('Error al cargar caja:', e);
  }
}

// ─── 6. AUDITORÍA ─────────────────────────────────────────────────────────────
async function loadAuditoria() {
  try {
    const res = await fetch('api/auditoria.php');
    const data = await res.json();
    if (!data.success) return;

    const tbody = document.getElementById('tbody-auditoria');
    if (!data.data || data.data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">Sin eventos de auditoría registrados.</td></tr>';
      return;
    }

    tbody.innerHTML = data.data.map(a => {
      const badgeAction = a.accion === 'CREAR' ? 'badge-success' : (a.accion === 'ACTUALIZAR' ? 'badge-warning' : 'badge-danger');
      return `
        <tr>
          <td><span style="font-size: 12px; color: var(--text-muted);">${a.created_at}</span></td>
          <td><strong>${a.tabla}</strong></td>
          <td><span class="badge ${badgeAction}">${a.accion}</span></td>
          <td>${escapeHtml(a.descripcion)}</td>
          <td><code>${escapeHtml(a.usuario)}</code></td>
        </tr>
      `;
    }).join('');
  } catch (e) {
    console.error('Error al cargar auditoría:', e);
  }
}

// ─── 7. HISTORIAL Y CONSULTA DE ASISTENCIAS ──────────────────────────────────
async function loadAsistencias() {
  const tbody = document.getElementById('tbody-asistencias');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:22px;color:var(--text-muted);">Consultando asistencias...</td></tr>';
  const params = new URLSearchParams();
  const from = document.getElementById('att-from').value;
  const to = document.getElementById('att-to').value;
  const search = document.getElementById('att-search').value.trim();
  if (from) params.set('from', from);
  if (to) params.set('to', to);
  if (search) params.set('search', search);
  try {
    const res = await fetch(`api/asistencias.php?${params.toString()}`);
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.error || 'No se pudo consultar el historial.');
    const rows = data.data || [];
    document.getElementById('att-total').textContent = data.summary?.total ?? rows.length;
    document.getElementById('att-unique').textContent = data.summary?.uniqueClients ?? '—';
    document.getElementById('att-last').textContent = rows.length ? String(rows[0].hora).slice(0,5) : '—';
    document.getElementById('att-results-note').textContent = `${rows.length} registro(s) encontrados. Se muestran como máximo 500 registros.`;
    if (!rows.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:22px;color:var(--text-muted);">No se encontraron asistencias con los filtros seleccionados.</td></tr>';
      return;
    }
    tbody.innerHTML = rows.map(a => `
      <tr>
        <td>${escapeHtml(a.fecha)}</td>
        <td><strong>${escapeHtml(String(a.hora).slice(0,8))}</strong></td>
        <td>${escapeHtml(a.nombre)}</td>
        <td><code>${maskDni(a.dni)}</code></td>
        <td><span class="badge badge-secondary">${escapeHtml(a.metodo || 'No especificado')}</span></td>
        <td><span class="badge badge-success">Registrada</span></td>
      </tr>`).join('');
    lucide.createIcons();
  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:22px;color:var(--danger);">${escapeHtml(err.message)}</td></tr>`;
  }
}

function resetAttendanceFilters() {
  document.getElementById('att-from').value = '';
  document.getElementById('att-to').value = '';
  document.getElementById('att-search').value = '';
  loadAsistencias();
}

// ─── 7. LECTOR QR DE ASISTENCIA (CÁMARA + DNI MANUAL) ─────────────────────────
let adminHtml5Qr = null;

function openLectorQRModal() {
  const modal = document.getElementById('modalLectorQR');
  if (!modal) return;
  modal.hidden = false;
  modal.style.setProperty('display', 'flex', 'important');
  modal.setAttribute('aria-hidden', 'false');
  setLectorMode('camera');
}

function closeLectorQRModal(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
    if (typeof event.stopImmediatePropagation === 'function') event.stopImmediatePropagation();
  }

  // Cierre visual inmediato: no depende de que la cámara responda.
  const modal = document.getElementById('modalLectorQR');
  adminQrStartToken++;
  if (modal) {
    modal.setAttribute('aria-hidden', 'true');
    modal.style.setProperty('display', 'none', 'important');
    modal.hidden = true;
  }

  const resultBox = document.getElementById('checkInResultBox');
  if (resultBox) resultBox.style.display = 'none';
  const reader = document.getElementById('adminQrReader');
  if (reader) reader.innerHTML = '';

  const scanner = adminHtml5Qr;
  adminHtml5Qr = null;
  if (scanner) {
    // Detener la cámara en segundo plano, sin bloquear el cierre del modal.
    Promise.resolve().then(async () => {
      try { await scanner.stop(); } catch (_) {}
      try { scanner.clear(); } catch (_) {}
    });
  }
  return false;
}

// Delegación en fase de captura: el cierre sigue funcionando aunque otro
// script haya reemplazado el manejador del botón.
document.addEventListener('click', (event) => {
  const closeButton = event.target && event.target.closest
    ? event.target.closest('#btnCerrarLectorQR') : null;
  if (closeButton) closeLectorQRModal(event);
}, true);

document.addEventListener('keydown', (event) => {
  const modal = document.getElementById('modalLectorQR');
  if (event.key === 'Escape' && modal && modal.getAttribute('aria-hidden') === 'false') {
    closeLectorQRModal(event);
  }
});

function setLectorMode(mode) {
  const btnCam = document.getElementById('btnModeCamera');
  const btnMan = document.getElementById('btnModeManual');
  const boxCam = document.getElementById('boxCameraScanner');
  const formMan = document.getElementById('formDniManual');

  if (mode === 'camera') {
    btnCam.classList.add('active');
    btnMan.classList.remove('active');
    boxCam.style.display = 'block';
    formMan.style.display = 'none';
    startAdminCameraScanner();
  } else {
    btnMan.classList.add('active');
    btnCam.classList.remove('active');
    formMan.style.display = 'block';
    boxCam.style.display = 'none';
    if (adminHtml5Qr) adminHtml5Qr.stop().catch(() => {});
    document.getElementById('manualDniInput').focus();
  }
}

function startAdminCameraScanner() {
  const modal = document.getElementById('modalLectorQR');
  if (!modal || modal.style.display === 'none') return;
  if (typeof Html5Qrcode === 'undefined') {
    setLectorMode('manual');
    showToast('Este navegador no permite iniciar el lector QR. Puedes ingresar el DNI manualmente.', 'warning');
    return;
  }

  const token = ++adminQrStartToken;
  const previousScanner = adminHtml5Qr;
  if (previousScanner) Promise.resolve(previousScanner.stop()).catch(() => {});
  const scanner = new Html5Qrcode('adminQrReader');
  adminHtml5Qr = scanner;

  scanner.start(
    { facingMode: 'environment' },
    { fps: 10, qrbox: { width: 220, height: 220 } },
    (decodedText) => {
      if (token !== adminQrStartToken) return;
      Promise.resolve(scanner.stop()).catch(() => {});
      procesarAsistenciaPorDni(decodedText, 'Lector QR Cámara');
    },
    () => {}
  ).then(() => {
    // Si se cerró el modal mientras el navegador pedía permiso, apaga la cámara.
    if (token !== adminQrStartToken || !document.getElementById('modalLectorQR') ||
        document.getElementById('modalLectorQR').style.display === 'none') {
      return Promise.resolve(scanner.stop()).catch(() => {});
    }
  }).catch(err => {
    if (token !== adminQrStartToken) return;
    console.warn('Error de cámara:', err);
    setLectorMode('manual');
  });
}

function handleManualDniCheckIn(e) {
  e.preventDefault();
  const dni = document.getElementById('manualDniInput').value.trim();
  procesarAsistenciaPorDni(dni, 'Lector DNI Manual');
}

async function procesarAsistenciaPorDni(dni, metodo) {
  const resBox = document.getElementById('checkInResultBox');
  const resIcon = document.getElementById('checkInResultIcon');
  const resMsg = document.getElementById('checkInResultMsg');

  try {
    const res = await fetch('api/asistencias.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ dni, metodo })
    });
    const data = await res.json();

    resBox.style.display = 'block';
    if (data.success) {
      playBeep(true);
      resBox.style.background = 'var(--success-glow)';
      resBox.style.color = 'var(--success)';
      resBox.style.borderColor = 'var(--success)';
      resIcon.textContent = '✅';
      resMsg.textContent = data.message;
      loadDashboard();
      if (document.getElementById('view-clientes').style.display !== 'none') loadClientes();
      if (document.getElementById('view-asistencias')?.style.display !== 'none') loadAsistencias();
    } else {
      playBeep(false);
      resBox.style.background = 'var(--danger-glow)';
      resBox.style.color = 'var(--danger)';
      resBox.style.borderColor = 'var(--danger)';
      resIcon.textContent = '❌';
      resMsg.textContent = data.error || 'Ingreso denegado.';
    }
  } catch (err) {
    showToast('No se pudo registrar la asistencia. Verifica la conexión e intenta nuevamente.', 'error');
  }
}

function playBeep(success) {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.frequency.setValueAtTime(success ? 880 : 220, ctx.currentTime);
    gain.gain.setValueAtTime(0.15, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
    osc.start(); osc.stop(ctx.currentTime + 0.3);
  } catch (e) {}
}

// ─── Utilidades ───────────────────────────────────────────────────────────────
function maskDni(dni) {
  if (!dni || dni.length < 6) return dni;
  return `${dni.slice(0, 2)}****${dni.slice(-2)}`;
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str).replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m]);
}

let confirmResolver = null;

function showConfirm({ title = 'Confirmar operación', message = '¿Deseas continuar?', confirmText = 'Continuar', danger = false } = {}) {
  const modal = document.getElementById('modalConfirmacion');
  if (!modal) { console.error('No se encontró el modal de confirmación.'); return Promise.resolve(false); }
  document.getElementById('confirmTitle').textContent = title;
  document.getElementById('confirmMessage').textContent = message;
  document.getElementById('confirmAccept').textContent = confirmText;
  document.getElementById('confirmAccept').className = danger ? 'btn btn-danger' : 'btn btn-primary';
  document.getElementById('confirmIcon').innerHTML = danger ? '<i data-lucide="alert-triangle"></i>' : '<i data-lucide="help-circle"></i>';
  modal.style.display = 'flex';
  if (window.lucide) lucide.createIcons();
  return new Promise(resolve => { confirmResolver = resolve; });
}

function resolverConfirmacion(accepted) {
  const modal = document.getElementById('modalConfirmacion');
  if (modal) modal.style.display = 'none';
  if (confirmResolver) { const resolve = confirmResolver; confirmResolver = null; resolve(Boolean(accepted)); }
}

document.addEventListener('keydown', event => {
  if (event.key === 'Escape' && confirmResolver) resolverConfirmacion(false);
});

function showToast(msg, type = 'success') {
  const t = document.createElement('div');
  const validType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
  t.className = `toast-msg toast-${validType}`;
  const accent = document.createElement('div'); accent.className = 'toast-accent';
  const text = document.createElement('span'); text.textContent = String(msg || 'Operación finalizada.');
  t.append(accent, text);
  t.setAttribute('role', validType === 'error' ? 'alert' : 'status');
  t.setAttribute('aria-live', validType === 'error' ? 'assertive' : 'polite');
  document.body.appendChild(t);
  window.setTimeout(() => t.remove(), 4000);
}

async function logoutAdmin() {
  await fetch('api/auth.php?action=logout');
  window.location.href = 'index.php';
}


// ─── ADMINISTRACIÓN DE CUENTAS ADMINISTRATIVAS ───────────────────────────────
async function loadAdministradores() {
  const tbody = document.getElementById('tbody-administradores');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;">Cargando...</td></tr>';
  try {
    const res = await fetch('api/administradores.php');
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.error || 'No se pudo cargar la lista.');
    const canManage = data.canManage === true;
    const header = document.querySelector('#view-administradores .btn.btn-primary[onclick*="openAdminForm"]');
    if (header) header.style.display = canManage ? '' : 'none';
    const subtitle = document.querySelector('#view-administradores p');
    if (subtitle) subtitle.textContent = canManage
      ? 'Gestiona las cuentas con acceso al panel administrativo.'
      : 'Consulta las cuentas administrativas. La gestión está reservada al administrador principal.';
    tbody.innerHTML = data.data.map(a => `
      <tr>
        <td><strong>${escapeHtml(a.nombre)}</strong></td>
        <td>${escapeHtml(a.email)}</td>
        <td>${escapeHtml(a.rol)}</td>
        <td><span class="badge ${Number(a.activo) ? 'badge-success' : 'badge-warning'}">${Number(a.activo) ? 'Activo' : 'Inactivo'}</span></td>
        <td>${escapeHtml(a.creado_en || '—')}</td>
        <td style="white-space:nowrap;">
          ${Number(a.id) === Number(data.principalUserId) ? '<span class="badge badge-success">Administrador principal · Protegido</span>' : (canManage ? `
            <button class="btn btn-secondary" style="padding:6px 9px;" onclick='editAdmin(${JSON.stringify({id:a.id,nombre:a.nombre,email:a.email}).replace(/'/g,"&#39;")})'>Editar</button>
            <button class="btn ${Number(a.activo) ? 'btn-secondary' : 'btn-success'}" style="padding:6px 9px;margin-left:5px;" onclick="toggleAdmin(${Number(a.id)},${Number(a.activo) ? 0 : 1})">${Number(a.activo) ? 'Desactivar' : 'Activar'}</button>
          ` : '<span style="color:var(--text-muted);font-size:12px;">Solo lectura</span>')}
        </td>
      </tr>`).join('') || '<tr><td colspan="6" style="text-align:center;padding:20px;">No hay administradores registrados.</td></tr>';
  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="6" style="color:var(--danger);padding:20px;">${escapeHtml(err.message)}</td></tr>`;
  }
}

function openAdminForm(admin = null) {
  document.getElementById('formAdmin').reset();
  document.getElementById('adminId').value = admin?.id || '';
  document.getElementById('adminFormTitle').textContent = admin ? 'Editar administrador' : 'Nuevo administrador';
  document.getElementById('adminNombre').value = admin?.nombre || '';
  document.getElementById('adminEmail').value = admin?.email || '';
  document.getElementById('adminPassword').required = !admin;
  document.getElementById('adminPassword').value = '';
  document.getElementById('modalAdminForm').style.display = 'flex';
}
function editAdmin(admin) { openAdminForm(admin); }
function closeAdminForm() { document.getElementById('modalAdminForm').style.display = 'none'; }

async function saveAdmin(event) {
  event.preventDefault();
  const id = document.getElementById('adminId').value;
  const payload = {
    id: id ? Number(id) : null,
    nombre: document.getElementById('adminNombre').value.trim(),
    email: document.getElementById('adminEmail').value.trim(),
    password: document.getElementById('adminPassword').value
  };
  try {
    const res = await fetch('api/administradores.php', {
      method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.error || 'No se pudo guardar la cuenta.');
    closeAdminForm();
    showToast(data.message || 'Administrador guardado correctamente.');
    loadAdministradores();
  } catch (err) { showToast(err.message); }
}

async function toggleAdmin(id, activo) {
  const action = activo ? 'activar' : 'desactivar';
  if (!window.confirm(`¿Confirmas ${action} esta cuenta administrativa?`)) return;
  try {
    const res = await fetch('api/administradores.php?action=toggle', {
      method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id, activo})
    });
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.error || 'No se pudo cambiar el estado.');
    showToast(data.message || 'Estado actualizado.');
    loadAdministradores();
  } catch (err) { showToast(err.message); }
}


// ─── ASISTENTE IA: CONSULTAS Y REPORTES DE SOLO LECTURA ──────────────────────
function aiCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function appendAiMessage(text, role = 'assistant') {
  const container = document.getElementById('aiChatMessages');
  if (!container) return;
  const item = document.createElement('div');
  item.className = `ai-message ai-message-${role}`;
  const avatar = document.createElement('div');
  avatar.className = 'ai-message-avatar';
  avatar.textContent = role === 'user' ? 'Tú' : 'IA';
  const bubble = document.createElement('div');
  bubble.className = 'ai-message-bubble';
  const label = document.createElement('strong');
  label.textContent = role === 'user' ? 'Tú' : 'Asistente IA';
  const paragraph = document.createElement('p');
  paragraph.textContent = text;
  bubble.append(label, paragraph);
  item.append(avatar, bubble);
  container.appendChild(item);
  container.scrollTop = container.scrollHeight;
}

async function aiRequest(payload) {
  const response = await fetch('api/asistente.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-Token': aiCsrfToken()
    },
    body: JSON.stringify(payload)
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok || !data.success) {
    throw new Error(data.error || `No se pudo completar la solicitud (HTTP ${response.status}).`);
  }
  return data;
}

async function sendAiMessage(event) {
  event.preventDefault();
  const input = document.getElementById('aiChatInput');
  const button = document.getElementById('aiSendButton');
  const message = input?.value.trim();
  if (!message) return;
  appendAiMessage(message, 'user');
  input.value = '';
  input.disabled = true;
  button.disabled = true;
  button.innerHTML = '<i data-lucide="loader-circle"></i><span>Consultando...</span>';
  lucide.createIcons();
  try {
    const data = await aiRequest({action: 'chat', message});
    appendAiMessage(data.answer || 'No se recibió una respuesta.');
  } catch (error) {
    appendAiMessage(`No pude completar la consulta: ${error.message}`);
  } finally {
    input.disabled = false;
    button.disabled = false;
    button.innerHTML = '<i data-lucide="send"></i><span>Enviar</span>';
    input.focus();
    lucide.createIcons();
  }
}

function clearAiChat() {
  const container = document.getElementById('aiChatMessages');
  const report = document.getElementById('aiReportResult');
  if (container) {
    container.innerHTML = '';
    appendAiMessage('¡Hola! Puedo ayudarte a consultar socios, asistencias, ingresos, vencimientos y pagos pendientes. También puedo generar reportes descargables.');
  }
  if (report) { report.style.display = 'none'; report.innerHTML = ''; }
}

async function generateAiReport(type) {
  const result = document.getElementById('aiReportResult');
  if (!result) return;
  result.style.display = 'block';
  result.innerHTML = '<div class="ai-report-loading">Preparando el reporte con los datos actuales...</div>';
  try {
    const data = await aiRequest({action: 'report', type});
    const report = data.report;
    result.innerHTML = '';
    const header = document.createElement('div');
    header.className = 'ai-report-result-header';
    const title = document.createElement('h3');
    title.textContent = report.title || 'Reporte administrativo';
    const exportButton = document.createElement('button');
    exportButton.type = 'button';
    exportButton.className = 'btn btn-secondary';
    exportButton.textContent = 'Descargar CSV';
    exportButton.addEventListener('click', () => downloadAiReportCsv(report));
    header.append(title, exportButton);

    const analysis = document.createElement('p');
    analysis.className = 'ai-report-analysis';
    analysis.textContent = report.analysis || 'Reporte generado.';
    const tableWrap = document.createElement('div');
    tableWrap.className = 'ai-report-table-wrap';
    const table = document.createElement('table');
    table.className = 'ai-report-table';
    const thead = document.createElement('thead');
    const headRow = document.createElement('tr');
    (report.headers || []).forEach(value => {
      const th = document.createElement('th'); th.textContent = value; headRow.appendChild(th);
    });
    thead.appendChild(headRow);
    const tbody = document.createElement('tbody');
    (report.rows || []).forEach(row => {
      const tr = document.createElement('tr');
      row.forEach(value => { const td = document.createElement('td'); td.textContent = value ?? ''; tr.appendChild(td); });
      tbody.appendChild(tr);
    });
    if (!(report.rows || []).length) {
      const tr = document.createElement('tr');
      const td = document.createElement('td'); td.colSpan = Math.max(1, (report.headers || []).length);
      td.textContent = 'No hay registros para este periodo.'; tr.appendChild(td); tbody.appendChild(tr);
    }
    table.append(thead, tbody);
    tableWrap.appendChild(table);
    result.append(header, analysis, tableWrap);
    result.scrollIntoView({behavior: 'smooth', block: 'nearest'});
  } catch (error) {
    result.innerHTML = '';
    const message = document.createElement('p');
    message.className = 'ai-report-error';
    message.textContent = error.message;
    result.appendChild(message);
  }
}

function downloadAiReportCsv(report) {
  const rows = [report.headers || [], ...(report.rows || [])];
  const csv = rows.map(row => row.map(value => {
    const text = String(value ?? '').replace(/"/g, '""');
    return `"${text}"`;
  }).join(';')).join('\r\n');
  const blob = new Blob(['\ufeff' + csv], {type: 'text/csv;charset=utf-8;'});
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `${(report.title || 'reporte').toLowerCase().replace(/[^a-z0-9]+/g, '-')}.csv`;
  document.body.appendChild(link);
  link.click();
  link.remove();
  URL.revokeObjectURL(url);
}
