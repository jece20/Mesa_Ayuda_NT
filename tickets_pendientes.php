<?php
require_once 'conexion.php';
verificarRol('tecnico');

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];

// Construir consulta para tickets pendientes y en proceso del técnico
try {
    $sql = "SELECT t.*, u.nombre as cliente 
            FROM tickets t 
            JOIN usuarios u ON t.id_usuario = u.id_usuario
            WHERE t.id_tecnico_asignado = ? AND t.estado IN ('Pendiente', 'En proceso')
            ORDER BY t.prioridad DESC, t.fecha_creacion ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);
    $tickets = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = 'Error al cargar los tickets pendientes.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Pendientes - Mesa de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-headset me-2"></i>Mesa de Ayuda
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="tickets_pendientes.php">
                            <i class="fas fa-clock me-1"></i>Tickets Pendientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="todos_tickets.php">
                            <i class="fas fa-list me-1"></i>Todos los Tickets
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($nombre); ?>
                        </a>
                        <ul class="dropdown-menu">
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
            <div class="dashboard-header">
                <h1 class="dashboard-title">
                    <i class="fas fa-clock me-2"></i>Mis Tickets Pendientes
                </h1>
                <p class="dashboard-subtitle">Tickets asignados a ti que requieren acción.</p>
            </div>

            <!-- Lista de Tickets -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list-ul me-2"></i>Lista de Tickets
                        <span class="badge bg-warning ms-2"><?php echo count($tickets); ?> tickets</span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php elseif (empty($tickets)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">¡Buen trabajo! No tienes tickets pendientes.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Asunto</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Fecha de Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                            <td>#<?php echo $ticket['id_ticket']; ?></td>
                                            <td><?php echo htmlspecialchars($ticket['cliente']); ?></td>
                                            <td><?php echo htmlspecialchars($ticket['asunto']); ?></td>
                                            <td>
                                                <span class="prioridad-<?php echo strtolower($ticket['prioridad']); ?>">
                                                    <?php echo $ticket['prioridad']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="estado-<?php echo str_replace(' ', '-', strtolower($ticket['estado'])); ?>">
                                                    <?php echo $ticket['estado']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?></td>
                                            <td>
                                                <a href="ver_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-action btn-view btn-sm">
                                                    <i class="fas fa-eye"></i> Ver / Responder
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
