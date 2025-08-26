<?php
require_once 'conexion.php';
verificarLogin();

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];

// Obtener estadísticas según el rol
try {
    if ($rol === 'cliente') {
        // Estadísticas para clientes
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tickets WHERE id_usuario = ?");
        $stmt->execute([$usuario_id]);
        $total_tickets = $stmt->fetch()['total'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as pendientes FROM tickets WHERE id_usuario = ? AND estado = 'Pendiente'");
        $stmt->execute([$usuario_id]);
        $tickets_pendientes = $stmt->fetch()['pendientes'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as resueltos FROM tickets WHERE id_usuario = ? AND estado = 'Resuelto'");
        $stmt->execute([$usuario_id]);
        $tickets_resueltos = $stmt->fetch()['resueltos'];
        
        // Tickets del cliente
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_usuario = ? ORDER BY fecha_creacion DESC LIMIT 5");
        $stmt->execute([$usuario_id]);
        $tickets = $stmt->fetchAll();
        
    } elseif ($rol === 'tecnico') {
        // Estadísticas para técnicos
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tickets WHERE estado IN ('Pendiente', 'En proceso')");
        $stmt->execute();
        $total_pendientes = $stmt->fetch()['total'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as en_proceso FROM tickets WHERE estado = 'En proceso'");
        $stmt->execute();
        $en_proceso = $stmt->fetch()['en_proceso'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as resueltos FROM tickets WHERE estado = 'Resuelto'");
        $stmt->execute();
        $resueltos = $stmt->fetch()['resueltos'];
        
        // Tickets pendientes para el técnico
        $stmt = $pdo->prepare("SELECT t.*, u.nombre as cliente FROM tickets t 
                              JOIN usuarios u ON t.id_usuario = u.id_usuario 
                              WHERE t.estado IN ('Pendiente', 'En proceso') 
                              ORDER BY t.prioridad DESC, t.fecha_creacion ASC LIMIT 5");
        $stmt->execute();
        $tickets = $stmt->fetchAll();
        
    } else { // admin
        // Estadísticas para administradores
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tickets");
        $stmt->execute();
        $total_tickets = $stmt->fetch()['total'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as usuarios FROM usuarios");
        $stmt->execute();
        $total_usuarios = $stmt->fetch()['usuarios'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as pendientes FROM tickets WHERE estado = 'Pendiente'");
        $stmt->execute();
        $tickets_pendientes = $stmt->fetch()['pendientes'];
        
        // Todos los tickets
        $stmt = $pdo->prepare("SELECT t.*, u.nombre as cliente FROM tickets t 
                              JOIN usuarios u ON t.id_usuario = u.id_usuario 
                              ORDER BY t.fecha_creacion DESC LIMIT 5");
        $stmt->execute();
        $tickets = $stmt->fetchAll();
    }
} catch(PDOException $e) {
    $error = 'Error al cargar las estadísticas.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mesa de Ayuda</title>
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
        <!-- Header del Dashboard -->
        <div class="dashboard-header fade-in">
            <h1 class="dashboard-title">Bienvenido, <?php echo htmlspecialchars($nombre); ?></h1>
            <p class="dashboard-subtitle">
                <?php 
                if ($rol === 'cliente') echo 'Panel de Cliente - Gestiona tus tickets de soporte';
                elseif ($rol === 'tecnico') echo 'Panel de Técnico - Resuelve tickets de soporte';
                else echo 'Panel de Administrador - Gestiona todo el sistema';
                ?>
            </p>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <?php if ($rol === 'cliente'): ?>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-ticket-alt fa-3x text-primary mb-3"></i>
                            <h3 class="card-title"><?php echo $total_tickets; ?></h3>
                            <p class="card-text">Total de Tickets</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h3 class="card-title"><?php echo $tickets_pendientes; ?></h3>
                            <p class="card-text">Pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h3 class="card-title"><?php echo $tickets_resueltos; ?></h3>
                            <p class="card-text">Resueltos</p>
                        </div>
                    </div>
                </div>
            <?php elseif ($rol === 'tecnico'): ?>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h3 class="card-title"><?php echo $total_pendientes; ?></h3>
                            <p class="card-text">Tickets Pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-tools fa-3x text-info mb-3"></i>
                            <h3 class="card-title"><?php echo $en_proceso; ?></h3>
                            <p class="card-text">En Proceso</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h3 class="card-title"><?php echo $resueltos; ?></h3>
                            <p class="card-text">Resueltos</p>
                        </div>
                    </div>
                </div>
            <?php else: // admin ?>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-ticket-alt fa-3x text-primary mb-3"></i>
                            <h3 class="card-title"><?php echo $total_tickets; ?></h3>
                            <p class="card-text">Total de Tickets</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-info mb-3"></i>
                            <h3 class="card-title"><?php echo $total_usuarios; ?></h3>
                            <p class="card-text">Total de Usuarios</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h3 class="card-title"><?php echo $tickets_pendientes; ?></h3>
                            <p class="card-text">Tickets Pendientes</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Acciones Rápidas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if ($rol === 'cliente'): ?>
                                <div class="col-md-6">
                                    <a href="crear_ticket.php" class="btn btn-primary btn-lg w-100 mb-3">
                                        <i class="fas fa-plus me-2"></i>Crear Nuevo Ticket
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="mis_tickets.php" class="btn btn-secondary btn-lg w-100 mb-3">
                                        <i class="fas fa-list me-2"></i>Ver Mis Tickets
                                    </a>
                                </div>
                            <?php elseif ($rol === 'tecnico'): ?>
                                <div class="col-md-6">
                                    <a href="tickets_pendientes.php" class="btn btn-warning btn-lg w-100 mb-3">
                                        <i class="fas fa-clock me-2"></i>Ver Tickets Pendientes
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="todos_tickets.php" class="btn btn-info btn-lg w-100 mb-3">
                                        <i class="fas fa-list me-2"></i>Ver Todos los Tickets
                                    </a>
                                </div>
                            <?php else: // admin ?>
                                <div class="col-md-4">
                                    <a href="gestion_usuarios.php" class="btn btn-primary btn-lg w-100 mb-3">
                                        <i class="fas fa-users me-2"></i>Gestionar Usuarios
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="todos_tickets.php" class="btn btn-info btn-lg w-100 mb-3">
                                        <i class="fas fa-list me-2"></i>Ver Todos los Tickets
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="estadisticas.php" class="btn btn-success btn-lg w-100 mb-3">
                                        <i class="fas fa-chart-bar me-2"></i>Ver Estadísticas
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets Recientes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            <?php 
                            if ($rol === 'cliente') echo 'Mis Tickets Recientes';
                            elseif ($rol === 'tecnico') echo 'Tickets Pendientes';
                            else echo 'Todos los Tickets';
                            ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($tickets)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay tickets para mostrar.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <?php if ($rol !== 'cliente'): ?>
                                                <th>Cliente</th>
                                            <?php endif; ?>
                                            <th>Asunto</th>
                                            <th>Categoría</th>
                                            <th>Prioridad</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $ticket): ?>
                                            <tr>
                                                <td>#<?php echo $ticket['id_ticket']; ?></td>
                                                <?php if ($rol !== 'cliente'): ?>
                                                    <td><?php echo htmlspecialchars($ticket['cliente']); ?></td>
                                                <?php endif; ?>
                                                <td><?php echo htmlspecialchars($ticket['asunto']); ?></td>
                                                <td><?php echo htmlspecialchars($ticket['categoria']); ?></td>
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
                                                    <a href="ver_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" 
                                                       class="btn btn-action btn-view btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($rol === 'tecnico' && $ticket['estado'] === 'Pendiente'): ?>
                                                        <a href="responder_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" 
                                                           class="btn btn-action btn-edit btn-sm">
                                                            <i class="fas fa-reply"></i>
                                                        </a>
                                                    <?php endif; ?>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
