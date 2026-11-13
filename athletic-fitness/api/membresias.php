<?php
// ==============================================================================
// GymManagerFit - API de Membresías (Catálogo de Planes)
// ==============================================================================

require_once __DIR__ . '/../config/db.php';
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->query("SELECT * FROM membresias WHERE deleted_at IS NULL ORDER BY price ASC");
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

if ($method === 'POST') {
    $input = getJsonInput();
    $action = $_GET['action'] ?? '';

    // Actualizar precio de plan existente
    if ($action === 'update_price') {
        $id = trim($input['id'] ?? '');
        $price = (float)($input['price'] ?? 0);
        if ($price < 0) {
            jsonResponse(['success' => false, 'error' => 'El precio debe ser mayor o igual a 0.'], 400);
        }
        $stmt = $db->prepare("UPDATE membresias SET price = ? WHERE id = ?");
        $stmt->execute([$price, $id]);
        jsonResponse(['success' => true, 'message' => "Precio actualizado a S/ $price."]);
    }

    // Crear nuevo plan
    $name = trim($input['name'] ?? '');
    $price = (float)($input['price'] ?? 0);
    $days = (int)($input['durationDays'] ?? 30);
    $id = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));

    if (empty($name) || $days <= 0) {
        jsonResponse(['success' => false, 'error' => 'Nombre y duración válida requeridos.'], 400);
    }

    $stmt = $db->prepare("INSERT INTO membresias (id, name, price, duration_days) VALUES (?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE name=VALUES(name), price=VALUES(price), duration_days=VALUES(duration_days)");
    $stmt->execute([$id, $name, $price, $days]);

    jsonResponse(['success' => true, 'message' => "Plan '$name' guardado exitosamente."]);
}

jsonResponse(['error' => 'Método no soportado.'], 405);
