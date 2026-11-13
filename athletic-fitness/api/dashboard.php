<?php
// GymManagerFit - Indicadores del panel administrativo (MariaDB)
require_once __DIR__ . '/../config/db.php';

try {
    $db = getDB();
    $today = date('Y-m-d');
    $firstDayOfMonth = date('Y-m-01');
    $sevenDaysLater = date('Y-m-d', strtotime('+7 days'));
    $sixDaysAgo = date('Y-m-d', strtotime('-6 days'));

    $clients = $db->query("SELECT COUNT(*) AS total,
        COALESCE(SUM(status = 'Activo'), 0) AS active,
        COALESCE(SUM(status = 'Vencido'), 0) AS expired,
        COALESCE(SUM(status = 'Inactivo'), 0) AS inactive,
        COALESCE(SUM(status = 'Retirado'), 0) AS retired
        FROM clientes WHERE deleted_at IS NULL")->fetch(PDO::FETCH_ASSOC) ?: [];

    $stmtExpiring = $db->prepare("SELECT id, name, dni, phone, last_membership, end_date,
        DATEDIFF(end_date, ?) AS days_remaining, status FROM clientes
        WHERE deleted_at IS NULL AND end_date BETWEEN ? AND ?
        AND status NOT IN ('Inactivo', 'Retirado') ORDER BY end_date ASC LIMIT 7");
    $stmtExpiring->execute([$today, $today, $sevenDaysLater]);
    $expiringList = $stmtExpiring->fetchAll(PDO::FETCH_ASSOC);

    $stmtExpiringCount = $db->prepare("SELECT COUNT(*) FROM clientes WHERE deleted_at IS NULL
        AND end_date BETWEEN ? AND ? AND status NOT IN ('Inactivo', 'Retirado')");
    $stmtExpiringCount->execute([$today, $sevenDaysLater]);
    $expiringCount = (int)$stmtExpiringCount->fetchColumn();

    $stmtRevenue = $db->prepare("SELECT
        COALESCE(SUM(CASE WHEN `date` BETWEEN ? AND ? AND status = 'Completado' THEN amount ELSE 0 END), 0) AS month_revenue,
        COALESCE(SUM(CASE WHEN `date` = ? AND status = 'Completado' THEN amount ELSE 0 END), 0) AS today_revenue
        FROM pagos WHERE deleted_at IS NULL");
    $stmtRevenue->execute([$firstDayOfMonth, $today, $today]);
    $revenue = $stmtRevenue->fetch(PDO::FETCH_ASSOC) ?: [];

    $pending = $db->query("SELECT COUNT(*) AS total, COALESCE(SUM(amount), 0) AS amount
        FROM pagos WHERE deleted_at IS NULL AND status <> 'Completado'")->fetch(PDO::FETCH_ASSOC) ?: [];

    $stmtWeek = $db->prepare("SELECT fecha, COUNT(*) AS total FROM asistencias
        WHERE fecha BETWEEN ? AND ? GROUP BY fecha ORDER BY fecha ASC");
    $stmtWeek->execute([$sixDaysAgo, $today]);
    $weekMap = [];
    foreach ($stmtWeek->fetchAll(PDO::FETCH_ASSOC) as $row) $weekMap[$row['fecha']] = (int)$row['total'];
    $attendanceTrend = [];
    for ($i = 0; $i < 7; $i++) {
        $day = date('Y-m-d', strtotime($sixDaysAgo . " +$i days"));
        $attendanceTrend[] = ['label' => date('d/m', strtotime($day)), 'total' => $weekMap[$day] ?? 0];
    }

    $sixMonthsAgo = date('Y-m-01', strtotime('-5 months'));
    $stmtMonths = $db->prepare("SELECT DATE_FORMAT(`date`, '%Y-%m') AS month_key,
        COALESCE(SUM(CASE WHEN status = 'Completado' THEN amount ELSE 0 END), 0) AS total
        FROM pagos WHERE deleted_at IS NULL AND `date` >= ? GROUP BY DATE_FORMAT(`date`, '%Y-%m')");
    $stmtMonths->execute([$sixMonthsAgo]);
    $monthMap = [];
    foreach ($stmtMonths->fetchAll(PDO::FETCH_ASSOC) as $row) $monthMap[$row['month_key']] = (float)$row['total'];
    $revenueTrend = [];
    for ($i = 0; $i < 6; $i++) {
        $month = date('Y-m', strtotime($sixMonthsAgo . " +$i months"));
        $revenueTrend[] = ['label' => date('m/y', strtotime($month . '-01')), 'total' => $monthMap[$month] ?? 0];
    }

    $todayAssistsStmt = $db->prepare("SELECT COUNT(*) FROM asistencias WHERE fecha = ?");
    $todayAssistsStmt->execute([$today]);

    jsonResponse([
        'success' => true,
        'generatedAt' => date('d/m/Y H:i:s'),
        'kpis' => [
            'totalClients' => (int)($clients['total'] ?? 0),
            'activeClients' => (int)($clients['active'] ?? 0),
            'expiringClients' => $expiringCount,
            'expiredClients' => (int)($clients['expired'] ?? 0),
            'inactiveClients' => (int)($clients['inactive'] ?? 0),
            'retiredClients' => (int)($clients['retired'] ?? 0),
            'monthRevenue' => (float)($revenue['month_revenue'] ?? 0),
            'todayRevenue' => (float)($revenue['today_revenue'] ?? 0),
            'todayAssists' => (int)$todayAssistsStmt->fetchColumn(),
            'pendingPayments' => (int)($pending['total'] ?? 0),
            'pendingAmount' => (float)($pending['amount'] ?? 0)
        ],
        'expiringList' => $expiringList,
        'attendanceTrend' => $attendanceTrend,
        'revenueTrend' => $revenueTrend
    ]);
} catch (Throwable $e) {
    error_log('Dashboard API: ' . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'No se pudo cargar la información del panel.'], 500);
}
