<?php
require_once 'conexion.php';
header('Content-Type: application/json');

// Verificar que el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Validar que se ha proporcionado un ID de ticket
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID de ticket no válido']);
    exit();
}

$ticket_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

try {
    // Obtener el ticket para verificar permisos y estado
    $stmt = $pdo->prepare("SELECT estado, id_usuario, id_tecnico_asignado FROM tickets WHERE id_ticket = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        echo json_encode(['error' => 'Ticket no encontrado']);
        exit();
    }

    // Verificar permisos: el usuario debe ser el creador, el técnico asignado o un admin
    if ($rol !== 'admin' && $ticket['id_usuario'] != $usuario_id && $ticket['id_tecnico_asignado'] != $usuario_id) {
        echo json_encode(['error' => 'Acceso denegado']);
        exit();
    }

    // Devolver el estado actual del ticket
    echo json_encode(['estado' => $ticket['estado']]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos']);
    exit();
}
?>