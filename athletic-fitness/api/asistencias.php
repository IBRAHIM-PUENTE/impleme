<?php
// ============================================================================== 
// GymManagerFit - API de Asistencias (historial, filtros y registro QR/DNI)
// ============================================================================== 

require_once __DIR__ . '/../config/db.php';
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ─── GET: Historial con filtros por fechas, socio o DNI ────────────────────────
if ($method === 'GET') {
    $clientId = (int)($_GET['client_id'] ?? 0);
    $from = trim($_GET['from'] ?? '');
    $to = trim($_GET['to'] ?? '');
    $search = trim($_GET['search'] ?? '');

    foreach (['from' => $from, 'to' => $to] as $field => $value) {
        if ($value !== '') {
            $date = DateTime::createFromFormat('Y-m-d', $value);
            if (!$date || $date->format('Y-m-d') !== $value) {
                jsonResponse(['success' => false, 'error' => "La fecha '$field' no tiene un formato válido."], 400);
            }
        }
    }
    if ($from !== '' && $to !== '' && $from > $to) {
        jsonResponse(['success' => false, 'error' => 'La fecha inicial no puede ser posterior a la fecha final.'], 400);
    }

    $where = [];
    $params = [];
    if ($clientId > 0) {
        $where[] = 'client_id = ?';
        $params[] = $clientId;
    }
    if ($from !== '') { $where[] = 'fecha >= ?'; $params[] = $from; }
    if ($to !== '') { $where[] = 'fecha <= ?'; $params[] = $to; }
    if ($search !== '') {
        $where[] = '(nombre LIKE ? OR dni LIKE ?)';
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $stmt = $db->prepare("SELECT id, client_id, dni, nombre, fecha, hora, metodo, created_at FROM asistencias $whereSql ORDER BY fecha DESC, hora DESC LIMIT 500");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $summaryStmt = $db->prepare("SELECT COUNT(*) AS total, COUNT(DISTINCT client_id) AS uniqueClients FROM asistencias $whereSql");
    $summaryStmt->execute($params);
    $summary = $summaryStmt->fetch();

    jsonResponse([
        'success' => true,
        'data' => $rows,
        'summary' => [
            'total' => (int)$summary['total'],
            'uniqueClients' => (int)$summary['uniqueClients']
        ]
    ]);
}

// ─── POST: Registrar asistencia por QR o DNI ──────────────────────────────────
if ($method === 'POST') {
    $input = getJsonInput();
    $dni = trim($input['dni'] ?? '');
    $metodo = trim($input['metodo'] ?? 'QR');

    if (!preg_match('/^[0-9]{8}$/', $dni)) {
        jsonResponse(['success' => false, 'error' => 'Ingresa un DNI válido de exactamente 8 dígitos.'], 400);
    }

    // Limitar el método a los canales disponibles en la interfaz.
    $metodo = stripos($metodo, 'manual') !== false ? 'DNI Manual' : 'QR';

    $stmt = $db->prepare("SELECT * FROM clientes WHERE dni = ? AND deleted_at IS NULL LIMIT 1");
    $stmt->execute([$dni]);
    $client = $stmt->fetch();

    if (!$client) {
        jsonResponse(['success' => false, 'type' => 'not_found', 'error' => 'No se encontró un socio con ese DNI.'], 404);
    }

    if (in_array($client['status'], ['Vencido', 'Inactivo', 'Retirado'], true)) {
        jsonResponse([
            'success' => false,
            'type' => 'expired',
            'client' => $client,
            'error' => 'La membresía está vencida o inactiva. Acérquese a recepción para regularizarla.'
        ], 403);
    }

    $todayDate = date('Y-m-d');
    $todayTime = date('H:i:s');

    try {
        $db->beginTransaction();

        // Impide que el mismo socio acumule más de una asistencia en el mismo día.
        $duplicate = $db->prepare('SELECT id FROM asistencias WHERE client_id = ? AND fecha = ? LIMIT 1 FOR UPDATE');
        $duplicate->execute([$client['id'], $todayDate]);
        if ($duplicate->fetch()) {
            $db->rollBack();
            jsonResponse([
                'success' => false,
                'type' => 'duplicate',
                'error' => 'Este socio ya tiene una asistencia registrada el día de hoy.'
            ], 409);
        }

        $newTotal = (int)$client['total_assists'] + 1;
        $stmtUp = $db->prepare('UPDATE clientes SET total_assists = ?, last_assist_date = ?, last_assist_time = ? WHERE id = ?');
        $stmtUp->execute([$newTotal, $todayDate, $todayTime, $client['id']]);

        $stmtHist = $db->prepare('INSERT INTO asistencias (client_id, dni, nombre, fecha, hora, metodo) VALUES (?, ?, ?, ?, ?, ?)');
        $stmtHist->execute([$client['id'], $client['dni'], $client['name'], $todayDate, $todayTime, $metodo]);

        $db->commit();

        $client['total_assists'] = $newTotal;
        $client['last_assist_date'] = $todayDate;
        $client['last_assist_time'] = $todayTime;
        $isExpiring = $client['status'] === 'Por vencer';

        jsonResponse([
            'success' => true,
            'type' => $isExpiring ? 'warning' : 'success',
            'client' => $client,
            'message' => $isExpiring
                ? "Asistencia registrada. Atención: la membresía vence en {$client['days_remaining']} días."
                : "Bienvenido(a) {$client['name']}. Asistencia #$newTotal registrada correctamente."
        ]);
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('Error al registrar asistencia: ' . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'No se pudo registrar la asistencia. Inténtelo nuevamente.'], 500);
    }
}

jsonResponse(['success' => false, 'error' => 'Método no soportado.'], 405);
