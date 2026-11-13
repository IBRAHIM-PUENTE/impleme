<?php
// GymManagerFit - Gestión de cuentas administrativas.
session_start();
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['admin']['id']) || (($_SESSION['admin']['rol'] ?? '') !== 'admin')) {
    jsonResponse(['success' => false, 'error' => 'No tienes permisos para administrar estas cuentas.'], 403);
}

$db = getDB();
$currentId = (int) $_SESSION['admin']['id'];
// La cuenta principal se identifica por el correo inicial del sistema.
// Su identidad queda protegida aunque otros administradores tengan rol 'admin'.
$principalStmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND rol = 'admin' LIMIT 1");
$principalStmt->execute(['admin@gimfit.com']);
$principalId = (int) ($principalStmt->fetchColumn() ?: 0);
$isPrincipal = ($principalId > 0 && $currentId === $principalId);
$check = $db->prepare('SELECT id, activo FROM usuarios WHERE id = ? LIMIT 1');
$check->execute([$currentId]);
$current = $check->fetch();
if (!$current || !(int)$current['activo']) {
    session_unset();
    session_destroy();
    jsonResponse(['success' => false, 'error' => 'Tu cuenta está inactiva. Inicia sesión con una cuenta habilitada.'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
if ($method === 'GET') {
    $stmt = $db->query('SELECT id, nombre, email, rol, activo, creado_en FROM usuarios ORDER BY nombre ASC');
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll(), 'currentUserId' => $currentId, 'principalUserId' => $principalId, 'canManage' => $isPrincipal]);
}
if ($method === 'POST') {
    // Solo el administrador principal puede crear, editar, activar o desactivar cuentas administrativas.
    if (!$isPrincipal) {
        jsonResponse(['success' => false, 'error' => 'Solo el administrador principal puede gestionar cuentas administrativas.'], 403);
    }
    $input = getJsonInput();
    if ($action === 'toggle') {
        $id = (int)($input['id'] ?? 0);
        $activo = (int)($input['activo'] ?? -1);
        if ($id <= 0 || !in_array($activo, [0, 1], true)) jsonResponse(['success' => false, 'error' => 'Solicitud inválida.'], 400);
        if ($id === $principalId) jsonResponse(['success' => false, 'error' => 'La cuenta del administrador principal está protegida y no puede desactivarse.'], 403);
        if ($id === $currentId) jsonResponse(['success' => false, 'error' => 'No puedes cambiar el estado de tu propia cuenta.'], 400);
        if ($activo === 0) {
            $count = (int)$db->query('SELECT COUNT(*) FROM usuarios WHERE activo = 1')->fetchColumn();
            if ($count <= 1) jsonResponse(['success' => false, 'error' => 'Debe existir al menos un administrador activo.'], 400);
        }
        $stmt = $db->prepare('UPDATE usuarios SET activo = ? WHERE id = ?');
        $stmt->execute([$activo, $id]);
        $exists = $db->prepare('SELECT id FROM usuarios WHERE id = ?');
        $exists->execute([$id]);
        if (!$exists->fetch()) jsonResponse(['success' => false, 'error' => 'La cuenta indicada no existe.'], 404);
        jsonResponse(['success' => true, 'message' => $activo ? 'Cuenta activada correctamente.' : 'Cuenta desactivada correctamente.']);
    }
    $id = isset($input['id']) && $input['id'] !== '' ? (int)$input['id'] : 0;
    if ($id > 0 && $id === $principalId) {
        jsonResponse(['success' => false, 'error' => 'La cuenta del administrador principal está protegida y no puede editarse ni cambiar su contraseña desde este módulo.'], 403);
    }
    $nombre = trim($input['nombre'] ?? '');
    $email = strtolower(trim($input['email'] ?? ''));
    $password = (string)($input['password'] ?? '');
    if ($nombre === '' || mb_strlen($nombre) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        jsonResponse(['success' => false, 'error' => 'Ingresa un nombre válido y un correo electrónico válido.'], 400);
    }
    try {
        if ($id > 0) {
            if ($password !== '' && strlen($password) < 10) jsonResponse(['success' => false, 'error' => 'La contraseña debe tener al menos 10 caracteres.'], 400);
            if ($password !== '') {
                $stmt = $db->prepare('UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE id = ?');
                $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_DEFAULT), $id]);
            } else {
                $stmt = $db->prepare('UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?');
                $stmt->execute([$nombre, $email, $id]);
            }
            jsonResponse(['success' => true, 'message' => 'Datos del administrador actualizados correctamente.']);
        }
        if (strlen($password) < 10) jsonResponse(['success' => false, 'error' => 'La contraseña debe tener al menos 10 caracteres.'], 400);
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES (?, ?, ?, 'admin', 1)");
        $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_DEFAULT)]);
        jsonResponse(['success' => true, 'message' => 'Nuevo administrador creado correctamente.']);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') jsonResponse(['success' => false, 'error' => 'Ya existe una cuenta con ese correo electrónico.'], 409);
        jsonResponse(['success' => false, 'error' => 'No se pudo guardar la cuenta administrativa.'], 500);
    }
}
jsonResponse(['success' => false, 'error' => 'Método no permitido.'], 405);
