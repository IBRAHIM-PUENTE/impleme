<?php
// ==============================================================================
// GymManagerFit - API de Clientes (CRUD y Desactivación en MariaDB)
// ==============================================================================

require_once __DIR__ . '/../config/db.php';
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// ─── GET: Listar clientes con recálculo dinámico de días ──────────────────────
if ($method === 'GET') {
    $search = trim($_GET['search'] ?? '');
    $status = trim($_GET['status'] ?? 'Todos');

    $sql = "SELECT * FROM clientes WHERE deleted_at IS NULL";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR dni LIKE ? OR phone LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($status !== 'Todos' && !empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    // Actualizar dinámicamente días restantes según fecha actual
    $today = new DateTime();
    foreach ($rows as &$c) {
        if (!empty($c['end_date']) && $c['status'] !== 'Inactivo' && $c['status'] !== 'Retirado') {
            $end = new DateTime($c['end_date']);
            $diff = (int)$today->diff($end)->format('%r%a');
            $c['days_remaining'] = $diff >= 0 ? $diff : 0;
            if ($diff < 0) {
                $c['status'] = 'Vencido';
            } elseif ($diff <= 5 && $diff >= 0) {
                $c['status'] = 'Por vencer';
            } else {
                $c['status'] = 'Activo';
            }
        }
    }

    jsonResponse(['success' => true, 'data' => $rows]);
}

// ─── POST: Crear, Actualizar o Desactivar ─────────────────────────────────────
if ($method === 'POST') {
    $input = getJsonInput();

    // 1. Desactivar / Reactivar socio (status = Inactivo / Activo)
    if ($action === 'toggle_status') {
        $id = (int)($input['id'] ?? 0);
        $newStatus = trim($input['status'] ?? 'Inactivo');

        $stmt = $db->prepare("UPDATE clientes SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);

        jsonResponse([
            'success' => true,
            'message' => "Estado del socio actualizado a '$newStatus' correctamente en MariaDB."
        ]);
    }

    // 2. Actualizar socio existente
    if ($action === 'update' || !empty($input['id'])) {
        $id = (int)$input['id'];
        $name = trim($input['name'] ?? '');
        $dni = trim($input['dni'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $weight = (float)($input['weight'] ?? 0);
        $height = (float)($input['height'] ?? 0);
        $objective = trim($input['objective'] ?? '');
        $notes = trim($input['notes'] ?? '');
        $status = trim($input['status'] ?? 'Activo');

        if (empty($name) || !preg_match('/^[0-9]{8}$/', $dni)) {
            jsonResponse(['success' => false, 'error' => 'Nombre obligatorio y DNI debe tener 8 dígitos numéricos.'], 400);
        }

        $stmt = $db->prepare("UPDATE clientes SET name=?, dni=?, phone=?, weight=?, height=?, objective=?, notes=?, status=? WHERE id=?");
        $stmt->execute([$name, $dni, $phone, $weight, $height, $objective, $notes, $status, $id]);

        jsonResponse(['success' => true, 'message' => "Socio '$name' actualizado correctamente."]);
    }

    // 3. Crear nuevo socio
    $name = trim($input['name'] ?? '');
    $dni = trim($input['dni'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $weight = (float)($input['weight'] ?? 0);
    $height = (float)($input['height'] ?? 0);
    $objective = trim($input['objective'] ?? '');
    $notes = trim($input['notes'] ?? '');

    if (empty($name)) {
        jsonResponse(['success' => false, 'error' => 'El nombre del socio es obligatorio.'], 400);
    }
    if (!preg_match('/^[0-9]{8}$/', $dni)) {
        jsonResponse(['success' => false, 'error' => 'El DNI debe tener exactamente 8 dígitos numéricos.'], 400);
    }

    // Comprobar si DNI ya existe
    $stmtCheck = $db->prepare("SELECT id FROM clientes WHERE dni = ? AND deleted_at IS NULL LIMIT 1");
    $stmtCheck->execute([$dni]);
    if ($stmtCheck->fetch()) {
        jsonResponse(['success' => false, 'error' => 'Ya existe un socio registrado con el DNI ' . $dni . '.'], 400);
    }

    $stmt = $db->prepare("INSERT INTO clientes (name, dni, phone, weight, height, objective, notes, status, last_membership, days_remaining) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo', 'Ninguna', 0)");
    $stmt->execute([$name, $dni, $phone, $weight, $height, $objective, $notes]);

    jsonResponse([
        'success' => true,
        'id' => $db->lastInsertId(),
        'message' => "Socio '$name' registrado exitosamente en MariaDB."
    ]);
}

jsonResponse(['error' => 'Método no soportado.'], 405);
