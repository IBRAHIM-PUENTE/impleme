<?php
// ==============================================================================
// GymManagerFit - API de Auditoría y Trazabilidad (MariaDB)
// ==============================================================================

require_once __DIR__ . '/../config/db.php';
$db = getDB();

$tabla = trim($_GET['tabla'] ?? 'Todos');
$accion = trim($_GET['accion'] ?? 'Todos');

$sql = "SELECT * FROM auditoria WHERE 1=1";
$params = [];

if ($tabla !== 'Todos' && !empty($tabla)) {
    $sql .= " AND tabla = ?";
    $params[] = $tabla;
}

if ($accion !== 'Todos' && !empty($accion)) {
    $sql .= " AND accion = ?";
    $params[] = $accion;
}

$sql .= " ORDER BY id DESC LIMIT 100";
$stmt = $db->prepare($sql);
$stmt->execute($params);

jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
