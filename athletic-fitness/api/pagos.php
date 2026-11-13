<?php
// ==============================================================================
// GymManagerFit - API de Caja y Pagos Financieros (MariaDB)
// ==============================================================================

require_once __DIR__ . '/../config/db.php';
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// ─── GET: Listado de Pagos o Exportación CSV ──────────────────────────────────
if ($method === 'GET') {
    if ($action === 'export_csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_caja_gymfit_' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');
        // BOM UTF-8 para Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['ID', 'Socio / Cliente', 'Membresía / Plan', 'Monto (S/)', 'Fecha de Pago', 'Estado']);

        $stmt = $db->query("SELECT id, client_name, membership_name, amount, date, status FROM pagos WHERE deleted_at IS NULL ORDER BY date DESC, id DESC");
        while ($row = $stmt->fetch()) {
            fputcsv($output, [$row['id'], $row['client_name'], $row['membership_name'], $row['amount'], $row['date'], $row['status']]);
        }
        fclose($output);
        exit();
    }

    // Listado normal con resumen de KPIs de caja
    $stmt = $db->query("SELECT * FROM pagos WHERE deleted_at IS NULL ORDER BY date DESC, id DESC");
    $payments = $stmt->fetchAll();

    $today = date('Y-m-d');
    $firstDayOfMonth = date('Y-m-01');

    $totalRevenue = 0;
    $monthRevenue = 0;
    $todayRevenue = 0;

    foreach ($payments as $p) {
        $amt = (float)$p['amount'];
        $totalRevenue += $amt;
        if ($p['date'] >= $firstDayOfMonth) {
            $monthRevenue += $amt;
        }
        if ($p['date'] === $today) {
            $todayRevenue += $amt;
        }
    }

    jsonResponse([
        'success' => true,
        'data' => $payments,
        'summary' => [
            'totalRevenue' => $totalRevenue,
            'monthRevenue' => $monthRevenue,
            'todayRevenue' => $todayRevenue,
            'totalTransactions' => count($payments)
        ]
    ]);
}

// ─── POST: Registrar Pago & Asignar Membresía a Cliente ───────────────────────
if ($method === 'POST') {
    $input = getJsonInput();

    $clientId = (int)($input['clientId'] ?? 0);
    $clientName = trim($input['clientName'] ?? '');
    $planName = trim($input['planName'] ?? '');
    $amount = (float)($input['amount'] ?? 0);
    $durationDays = (int)($input['durationDays'] ?? 30);
    $startDate = trim($input['startDate'] ?? date('Y-m-d'));
    $endDate = trim($input['endDate'] ?? date('Y-m-d', strtotime("+$durationDays days")));

    if ($amount < 0 || empty($planName)) {
        jsonResponse(['success' => false, 'error' => 'Monto y plan válidos requeridos.'], 400);
    }

    $db->beginTransaction();
    try {
        // 1. Si es cliente existente, actualizar su membresía
        if ($clientId > 0) {
            $stmtUp = $db->prepare("UPDATE clientes SET 
                status = 'Activo',
                last_membership = ?,
                days_remaining = ?,
                start_date = ?,
                end_date = ?
                WHERE id = ?");
            $stmtUp->execute([$planName, $durationDays, $startDate, $endDate, $clientId]);
        }

        // 2. Registrar la fila de pago
        $stmtPay = $db->prepare("INSERT INTO pagos (client_id, client_name, membership_name, amount, date, status) 
                                VALUES (?, ?, ?, ?, ?, 'Completado')");
        $stmtPay->execute([$clientId ?: null, $clientName, $planName, $amount, $startDate]);

        $db->commit();

        jsonResponse([
            'success' => true,
            'message' => "¡Pago de S/ $amount registrado y membresía '$planName' asignada con éxito!"
        ]);
    } catch (Exception $e) {
        $db->rollBack();
        jsonResponse(['success' => false, 'error' => 'Error al procesar el pago: ' . $e->getMessage()], 500);
    }
}

jsonResponse(['error' => 'Método no soportado.'], 405);
