<?php
/**
 * GymManagerFit - Asistente IA
 * Consultas de solo lectura y generación de reportes.
 * Requiere GEMINI_API_KEY configurada en el entorno del servidor web.
 */
session_start();
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['admin'])) {
    jsonResponse(['success' => false, 'error' => 'Tu sesión ha expirado. Inicia sesión nuevamente.'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Método no permitido.'], 405);
}

$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
    jsonResponse(['success' => false, 'error' => 'La sesión de seguridad no es válida. Recarga el panel e inténtalo nuevamente.'], 403);
}

$input = getJsonInput();
$action = $input['action'] ?? 'chat';
$apiKey = getenv('GEMINI_API_KEY');

if (!$apiKey) {
    jsonResponse([
        'success' => false,
        'error' => 'El asistente aún no está configurado. El administrador del servidor debe establecer GEMINI_API_KEY.'
    ], 503);
}

function aiCall(string $apiKey, string $instructions, string $prompt): string {
    if (!function_exists('curl_init')) {
        throw new RuntimeException('La extensión cURL de PHP no está habilitada.');
    }

    // Gemini recibe la clave mediante encabezado; no se expone en la URL ni al navegador.
    $payload = [
        'system_instruction' => [
            'parts' => [['text' => $instructions]]
        ],
        'contents' => [[
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]],
        'generationConfig' => [
            'maxOutputTokens' => 900,
            'temperature' => 0.4
        ]
    ];

    $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $apiKey
        ],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 45
    ]);

    $raw = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('No se pudo conectar con Gemini: ' . $curlError);
    }

    $data = json_decode($raw, true);
    if ($httpCode < 200 || $httpCode >= 300) {
        error_log('Gemini API HTTP ' . $httpCode . ': ' . substr((string)$raw, 0, 1200));
        if ($httpCode === 400 || $httpCode === 401 || $httpCode === 403) {
            throw new RuntimeException('Gemini rechazó la solicitud o la clave API. Verifica la clave y que tenga acceso a Gemini API.');
        }
        if ($httpCode === 429) {
            throw new RuntimeException('Se alcanzó el límite de solicitudes o cuota de Gemini. Espera un momento o revisa los límites de tu proyecto.');
        }
        throw new RuntimeException('Gemini no pudo completar la solicitud (HTTP ' . $httpCode . ').');
    }

    $parts = $data['candidates'][0]['content']['parts'] ?? [];
    $texts = [];
    foreach ($parts as $part) {
        if (isset($part['text']) && is_string($part['text'])) {
            $texts[] = $part['text'];
        }
    }
    $answer = trim(implode("\n", $texts));
    if ($answer === '') {
        $reason = $data['promptFeedback']['blockReason'] ?? null;
        if ($reason) {
            throw new RuntimeException('Gemini no generó una respuesta porque la solicitud fue bloqueada (' . $reason . ').');
        }
        throw new RuntimeException('Gemini devolvió una respuesta vacía. Inténtalo nuevamente.');
    }
    return $answer;
}

function buildReport(PDO $db, string $type): array {
    switch ($type) {
        case 'socios':
            $rows = $db->query("SELECT status AS estado, COUNT(*) AS cantidad
                FROM clientes WHERE deleted_at IS NULL GROUP BY status
                ORDER BY cantidad DESC")->fetchAll();
            return ['title' => 'Reporte de socios por estado', 'headers' => ['Estado', 'Cantidad'],
                'rows' => array_map(fn($r) => [$r['estado'], (int)$r['cantidad']], $rows)];

        case 'asistencias':
            $rows = $db->query("SELECT fecha, COUNT(*) AS asistencias
                FROM asistencias WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY fecha ORDER BY fecha ASC")->fetchAll();
            return ['title' => 'Reporte de asistencias - últimos 7 días', 'headers' => ['Fecha', 'Asistencias'],
                'rows' => array_map(fn($r) => [$r['fecha'], (int)$r['asistencias']], $rows)];

        case 'ingresos':
            $rows = $db->query("SELECT DATE_FORMAT(`date`, '%Y-%m') AS periodo,
                    COALESCE(SUM(amount), 0) AS ingresos, COUNT(*) AS operaciones
                FROM pagos WHERE deleted_at IS NULL AND status = 'Completado'
                    AND `date` >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01')
                GROUP BY DATE_FORMAT(`date`, '%Y-%m') ORDER BY periodo ASC")->fetchAll();
            return ['title' => 'Reporte de ingresos - últimos 6 meses', 'headers' => ['Periodo', 'Ingresos (S/)', 'Operaciones'],
                'rows' => array_map(fn($r) => [$r['periodo'], number_format((float)$r['ingresos'], 2, '.', ''), (int)$r['operaciones']], $rows)];

        case 'vencimientos':
            $rows = $db->query("SELECT name, last_membership, end_date, DATEDIFF(end_date, CURDATE()) AS dias_restantes
                FROM clientes WHERE deleted_at IS NULL AND end_date BETWEEN CURDATE()
                    AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                    AND status NOT IN ('Inactivo','Retirado')
                ORDER BY end_date ASC LIMIT 200")->fetchAll();
            return ['title' => 'Reporte de membresías por vencer - próximos 30 días',
                'headers' => ['Socio', 'Membresía', 'Fecha de vencimiento', 'Días restantes'],
                'rows' => array_map(fn($r) => [$r['name'], $r['last_membership'], $r['end_date'], (int)$r['dias_restantes']], $rows)];

        case 'pendientes':
            $rows = $db->query("SELECT client_name, membership_name, amount, `date`, status
                FROM pagos WHERE deleted_at IS NULL AND status <> 'Completado'
                ORDER BY `date` DESC LIMIT 200")->fetchAll();
            return ['title' => 'Reporte de pagos pendientes', 'headers' => ['Socio', 'Membresía', 'Monto (S/)', 'Fecha', 'Estado'],
                'rows' => array_map(fn($r) => [$r['client_name'], $r['membership_name'], number_format((float)$r['amount'], 2, '.', ''), $r['date'], $r['status']], $rows)];

        case 'resumen':
        default:
            $total = (int)$db->query("SELECT COUNT(*) FROM clientes WHERE deleted_at IS NULL")->fetchColumn();
            $active = (int)$db->query("SELECT COUNT(*) FROM clientes WHERE deleted_at IS NULL AND status = 'Activo'")->fetchColumn();
            $today = (int)$db->query("SELECT COUNT(*) FROM asistencias WHERE fecha = CURDATE()")->fetchColumn();
            $revenue = (float)$db->query("SELECT COALESCE(SUM(amount),0) FROM pagos
                WHERE deleted_at IS NULL AND status='Completado'
                AND `date` >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND `date` <= CURDATE()")->fetchColumn();
            $pending = (float)$db->query("SELECT COALESCE(SUM(amount),0) FROM pagos
                WHERE deleted_at IS NULL AND status <> 'Completado'")->fetchColumn();
            return ['title' => 'Resumen general de GymManagerFit',
                'headers' => ['Indicador', 'Valor'],
                'rows' => [
                    ['Socios registrados', $total],
                    ['Socios activos', $active],
                    ['Asistencias de hoy', $today],
                    ['Ingresos del mes (S/)', number_format($revenue, 2, '.', '')],
                    ['Monto pendiente (S/)', number_format($pending, 2, '.', '')]
                ]];
    }
}

try {
    $db = getDB();
    $instructions = "Eres el asistente administrativo de GymManagerFit. Responde en español, con claridad y brevedad. "
        . "Usa exclusivamente los datos proporcionados por el sistema para cifras y hechos del gimnasio. "
        . "No inventes datos ni afirmes que ejecutaste acciones. Si los datos no permiten responder, indícalo. "
        . "No solicites ni reveles contraseñas, claves API ni información sensible. "
        . "Eres de solo lectura: nunca propongas ejecutar SQL ni modificar registros. "
        . "Para reportes, resume tendencias y hallazgos sin alterar los datos originales.";

    if ($action === 'report') {
        $type = (string)($input['type'] ?? 'resumen');
        $allowed = ['resumen', 'socios', 'asistencias', 'ingresos', 'vencimientos', 'pendientes'];
        if (!in_array($type, $allowed, true)) {
            jsonResponse(['success' => false, 'error' => 'Tipo de reporte no reconocido.'], 400);
        }
        $report = buildReport($db, $type);
        $analysisRows = $report['rows'];
        // Los nombres de socios se muestran solo en el panel; no se envían al proveedor de IA.
        if (in_array($type, ['vencimientos', 'pendientes'], true)) {
            $analysisRows = array_map(fn($row) => array_slice($row, 1), $analysisRows);
        }
        $prompt = "Prepara un breve análisis administrativo del siguiente reporte. "
            . "Menciona cifras importantes, patrones observables y una recomendación prudente. "
            . "Si no hay filas, indícalo sin inventar información. No intentes identificar personas.\n\n"
            . "Título: " . $report['title'] . "\n"
            . "Columnas (sin datos identificatorios): " . json_encode(array_slice($report['headers'], in_array($type, ['vencimientos', 'pendientes'], true) ? 1 : 0), JSON_UNESCAPED_UNICODE) . "\n"
            . "Datos: " . json_encode($analysisRows, JSON_UNESCAPED_UNICODE);
        $report['analysis'] = aiCall($apiKey, $instructions, $prompt);
        jsonResponse(['success' => true, 'report' => $report]);
    }

    if ($action !== 'chat') {
        jsonResponse(['success' => false, 'error' => 'Acción no reconocida.'], 400);
    }

    $message = trim((string)($input['message'] ?? ''));
    if ($message === '' || strlen($message) > 4800) {
        jsonResponse(['success' => false, 'error' => 'Escribe una consulta de hasta 1200 caracteres.'], 400);
    }

    // Contexto agregado y de solo lectura; no se envían DNI, teléfonos ni datos personales.
    $context = buildReport($db, 'resumen');
    $context['sociosPorEstado'] = buildReport($db, 'socios')['rows'];
    $context['asistenciasUltimos7Dias'] = buildReport($db, 'asistencias')['rows'];
    $context['ingresosUltimos6Meses'] = buildReport($db, 'ingresos')['rows'];
    $dueRows = buildReport($db, 'vencimientos')['rows'];
    $pendingRows = buildReport($db, 'pendientes')['rows'];
    // En las conversaciones se comparten solo datos agregados/no identificatorios.
    $context['proximosVencimientos'] = array_map(fn($row) => array_slice($row, 1), $dueRows);
    $context['pagosPendientes'] = array_map(fn($row) => array_slice($row, 1), $pendingRows);

    $prompt = "Datos actuales consultados desde MariaDB de GymManagerFit:\n"
        . json_encode($context, JSON_UNESCAPED_UNICODE)
        . "\n\nPregunta del administrador:\n" . $message
        . "\n\nResponde usando estos datos. Si pregunta algo no incluido, explica qué información falta.";
    $answer = aiCall($apiKey, $instructions, $prompt);
    jsonResponse(['success' => true, 'answer' => $answer]);

} catch (Throwable $e) {
    error_log('GymManagerFit Assistant: ' . $e->getMessage());
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
