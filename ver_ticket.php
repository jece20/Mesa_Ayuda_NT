<?php
require_once 'conexion.php';
verificarLogin();

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$ticket_id = $_GET['id'];

try {
    // Obtener información del ticket
    if ($rol === 'cliente') {
        $stmt = $pdo->prepare("SELECT t.*, u.nombre as cliente FROM tickets t 
                              JOIN usuarios u ON t.id_usuario = u.id_usuario 
                              WHERE t.id_ticket = ? AND t.id_usuario = ?");
        $stmt->execute([$ticket_id, $usuario_id]);
    } else {
        $stmt = $pdo->prepare("SELECT t.*, u.nombre as cliente FROM tickets t 
                              JOIN usuarios u ON t.id_usuario = u.id_usuario 
                              WHERE t.id_ticket = ?");
        $stmt->execute([$ticket_id]);
    }
    
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        header('Location: dashboard.php');
        exit();
    }
    
    // Obtener respuestas del ticket con nombre del usuario
$stmt = $pdo->prepare("    SELECT r.*, u.nombre as nombre_usuario 
    FROM respuestas r 
    JOIN usuarios u ON r.id_usuario = u.id_usuario 
    WHERE r.id_ticket = ? 
    ORDER BY r.fecha_respuesta ASC");
$stmt->execute([$ticket_id]);
$respuestas = $stmt->fetchAll();
    
} catch(PDOException $e) {
    header('Location: dashboard.php');
    exit();
}

// Si el usuario es admin, obtener la lista de técnicos
$tecnicos = [];
if ($rol === 'admin') {
    try {
        $stmt = $pdo->prepare("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'tecnico' AND activo = TRUE");
        $stmt->execute();
        $tecnicos = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Error al cargar la lista de técnicos.';
    }
}

// Procesar asignación de técnico (solo para admin)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asignar_tecnico'])) {
    if ($rol === 'admin') {
        $id_tecnico_asignado = $_POST['id_tecnico'];
        
        try {
            // Usar el procedimiento almacenado que ya existe
            $stmt = $pdo->prepare("CALL AsignarTecnico(?, ?, ?)");
            $stmt->execute([$ticket_id, $id_tecnico_asignado, $usuario_id]);
            
            // Opcional: Cambiar estado a 'En proceso' al asignar
            if ($ticket['estado'] === 'Pendiente') {
                $stmt = $pdo->prepare("UPDATE tickets SET estado = 'En proceso' WHERE id_ticket = ?");
                $stmt->execute([$ticket_id]);
            }

            header("Location: ver_ticket.php?id=$ticket_id&asignacion=exito");
            exit();
        } catch (PDOException $e) {
            $error = 'Error al asignar el técnico.';
        }
    }
}

// Procesar nueva respuesta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);
    
    if (!empty($mensaje)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO respuestas (id_ticket, id_usuario, mensaje) VALUES (?, ?, ?)");
            $stmt->execute([$ticket_id, $_SESSION['usuario_id'], $mensaje]);
            
            // Actualizar estado del ticket si es técnico
            if ($rol === 'tecnico' && $ticket['estado'] === 'Pendiente') {
                $stmt = $pdo->prepare("UPDATE tickets SET estado = 'En proceso' WHERE id_ticket = ?");
                $stmt->execute([$ticket_id]);
                $ticket['estado'] = 'En proceso';
            }
            
            // Redirigir para evitar reenvío del formulario
            header("Location: ver_ticket.php?id=$ticket_id");
            exit();
        } catch(PDOException $e) {
            $error = 'Error al enviar la respuesta.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo $ticket_id; ?> - Mesa de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
    <style>
        #notification-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1050;
            display: none;
        }
    </style>
</head>
<body>
    <div id="notification-bar"></div>

    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-headset me-2"></i>Mesa de Ayuda
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <?php if ($rol === 'cliente'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="crear_ticket.php">
                                <i class="fas fa-plus me-1"></i>Nuevo Ticket
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mis_tickets.php">
                                <i class="fas fa-ticket-alt me-1"></i>Mis Tickets
                            </a>
                        </li>
                    <?php elseif ($rol === 'tecnico'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="tickets_pendientes.php">
                                <i class="fas fa-clock me-1"></i>Tickets Pendientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="todos_tickets.php">
                                <i class="fas fa-list me-1"></i>Todos los Tickets
                            </a>
                        </li>
                    <?php elseif ($rol === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="gestion_usuarios.php">
                                <i class="fas fa-users me-1"></i>Gestión Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="todos_tickets.php">
                                <i class="fas fa-list me-1"></i>Todos los Tickets
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($nombre); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="perfil.php">
                                <i class="fas fa-user-edit me-2"></i>Mi Perfil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-container fade-in">
            <!-- Header del Ticket -->
            <div class="dashboard-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="dashboard-title">
                            <i class="fas fa-ticket-alt me-2"></i>Ticket #<?php echo $ticket_id; ?>
                        </h1>
                        <p class="dashboard-subtitle"><?php echo htmlspecialchars($ticket['asunto']); ?></p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary fs-6"><?php echo $ticket['categoria']; ?></span>
                        <span class="badge bg-<?php echo $ticket['prioridad'] === 'Urgente' ? 'danger' : ($ticket['prioridad'] === 'Alta' ? 'warning' : 'success'); ?> fs-6 ms-2">
                            <?php echo $ticket['prioridad']; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Información del Ticket -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Descripción del Problema
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>Detalles del Ticket
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Cliente:</strong><br>
                                <?php echo htmlspecialchars($ticket['cliente']); ?>
                            </div>
                            <div class="mb-3">
                                <strong>Estado:</strong><br>
                                <span id="ticket-status" class="estado-<?php echo str_replace(' ', '-', strtolower($ticket['estado'])); ?>">
                                    <?php echo $ticket['estado']; ?>
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Fecha de Creación:</strong><br>
                                <?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?>
                            </div>
                            <div class="mb-3">
                                <strong>Última Actualización:</strong><br>
                                <span id="last-update"><?php echo date('d/m/Y H:i', strtotime($ticket['fecha_ultima_actualizacion'])); ?></span>
                            </div>
                            
                            <?php if ($rol === 'admin'): ?>
                                <hr>
                                <div class="mb-3">
                                    <strong>Técnico Asignado:</strong><br>
                                    <?php 
                                    $nombre_tecnico_asignado = 'Sin asignar';
                                    if ($ticket['id_tecnico_asignado']) {
                                        $stmt_tecnico = $pdo->prepare("SELECT nombre FROM usuarios WHERE id_usuario = ?");
                                        $stmt_tecnico->execute([$ticket['id_tecnico_asignado']]);
                                        $tecnico_actual = $stmt_tecnico->fetch();
                                        if ($tecnico_actual) {
                                            $nombre_tecnico_asignado = $tecnico_actual['nombre'];
                                        }
                                    }
                                    echo htmlspecialchars($nombre_tecnico_asignado);
                                    ?>
                                </div>
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="id_tecnico" class="form-label"><strong>Asignar a:</strong></label>
                                        <select name="id_tecnico" id="id_tecnico" class="form-control">
                                            <option value="">-- Seleccionar Técnico --</option>
                                            <?php foreach ($tecnicos as $tecnico): ?>
                                                <option value="<?php echo $tecnico['id_usuario']; ?>" <?php echo ($ticket['id_tecnico_asignado'] == $tecnico['id_usuario']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($tecnico['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="asignar_tecnico" class="btn btn-primary w-100">
                                        <i class="fas fa-user-check me-2"></i>Asignar Técnico
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($rol === 'tecnico' && $ticket['estado'] === 'Pendiente'): ?>
                                <form method="POST" action="" class="mt-3">
                                    <button type="submit" name="cambiar_estado" value="en_proceso" class="btn btn-warning w-100">
                                        <i class="fas fa-tools me-2"></i>Marcar como En Proceso
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($rol === 'tecnico' && $ticket['estado'] === 'En proceso'): ?>
                                <form method="POST" action="" class="mt-3">
                                    <button type="submit" name="cambiar_estado" value="resuelto" class="btn btn-success w-100">
                                        <i class="fas fa-check me-2"></i>Marcar como Resuelto
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Respuestas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Conversación del Ticket
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($respuestas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay respuestas aún. Sé el primero en comentar.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($respuestas as $respuesta): ?>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($respuesta['nombre_usuario']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($respuesta['fecha_respuesta'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($respuesta['mensaje'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Formulario de respuesta -->
                    <hr class="my-4">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">
                                <i class="fas fa-reply me-2"></i>Agregar Respuesta
                            </label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="4" 
                                      placeholder="Escribe tu respuesta aquí..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Respuesta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ticketId = <?php echo $ticket_id; ?>;
            const statusSpan = document.getElementById('ticket-status');
            const notificationBar = document.getElementById('notification-bar');

            setInterval(function () {
                fetch(`get_estado_ticket.php?id=${ticketId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        const nuevoEstado = data.estado;
                        const estadoActual = statusSpan.textContent.trim();

                        if (nuevoEstado !== estadoActual) {
                            // Actualizar el texto y la clase del estado
                            statusSpan.textContent = nuevoEstado;
                            statusSpan.className = 'estado-' + nuevoEstado.toLowerCase().replace(' ', '-');

                            // Mostrar notificación
                            notificationBar.innerHTML = '<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                                'El estado del ticket ha sido actualizado a: <strong>' + nuevoEstado + '</strong>' +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>';
                            notificationBar.style.display = 'block';

                            // Actualizar la fecha de última actualización (opcional, pero recomendado)
                            document.getElementById('last-update').textContent = new Date().toLocaleString('es-ES');
                        }
                    })
                    .catch(error => console.error('Error al verificar el estado del ticket:', error));
            }, 5000); // Verificar cada 5 segundos
        });
    </script>
</body>
</html>
