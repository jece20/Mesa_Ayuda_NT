<?php
require_once 'conexion.php';
verificarLogin();

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];

// Filtros
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';
$prioridad_filtro = isset($_GET['prioridad']) ? $_GET['prioridad'] : '';
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Construir consulta con filtros
$where_conditions = [];
$params = [];

// Filtro base según el rol del usuario
if ($rol === 'tecnico') {
    $where_conditions[] = "t.id_tecnico_asignado = ?";
    $params[] = $usuario_id;
} elseif ($rol === 'cliente') {
    $where_conditions[] = "t.id_usuario = ?";
    $params[] = $usuario_id;
}
// El admin no tiene restricciones

if ($estado_filtro) {
    $where_conditions[] = "t.estado = ?";
    $params[] = $estado_filtro;
}

if ($prioridad_filtro) {
    $where_conditions[] = "t.prioridad = ?";
    $params[] = $prioridad_filtro;
}

if ($categoria_filtro) {
    $where_conditions[] = "t.categoria = ?";
    $params[] = $categoria_filtro;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

try {
    // Obtener tickets con filtros
    $sql = "SELECT t.*, u.nombre as cliente, tec.nombre as tecnico_asignado 
            FROM tickets t 
            JOIN usuarios u ON t.id_usuario = u.id_usuario
            LEFT JOIN usuarios tec ON t.id_tecnico_asignado = tec.id_usuario
            $where_clause 
            ORDER BY t.prioridad DESC, t.fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll();
    
    // Obtener opciones para filtros
    $stmt = $pdo->prepare("SELECT DISTINCT estado FROM tickets ORDER BY estado");
    $stmt->execute();
    $estados = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT DISTINCT prioridad FROM tickets ORDER BY prioridad");
    $stmt->execute();
    $prioridades = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT DISTINCT categoria FROM tickets ORDER BY categoria");
    $stmt->execute();
    $categorias = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = 'Error al cargar los tickets.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos los Tickets - Mesa de Ayuda</title>
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
                            <a class="nav-link active" href="todos_tickets.php">
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
                            <a class="nav-link active" href="todos_tickets.php">
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
            <div class="dashboard-header">
                <h1 class="dashboard-title">
                    <i class="fas fa-list me-2"></i>Todos los Tickets
                </h1>
                <p class="dashboard-subtitle">Gestiona todos los tickets del sistema</p>
            </div>
            
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-control" id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado['estado']; ?>" 
                                            <?php echo $estado_filtro === $estado['estado'] ? 'selected' : ''; ?>>
                                        <?php echo $estado['estado']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="prioridad" class="form-label">Prioridad</label>
                            <select class="form-control" id="prioridad" name="prioridad">
                                <option value="">Todas las prioridades</option>
                                <?php foreach ($prioridades as $prioridad): ?>
                                    <option value="<?php echo $prioridad['prioridad']; ?>" 
                                            <?php echo $prioridad_filtro === $prioridad['prioridad'] ? 'selected' : ''; ?>>
                                        <?php echo $prioridad['prioridad']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-control" id="categoria" name="categoria">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['categoria']; ?>" 
                                            <?php echo $categoria_filtro === $categoria['categoria'] ? 'selected' : ''; ?>>
                                        <?php echo $categoria['categoria']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-search me-2"></i>Filtrar
                                </button>
                                <a href="todos_tickets.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Lista de Tickets -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>Lista de Tickets
                        <span class="badge bg-primary ms-2"><?php echo count($tickets); ?> tickets</span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($tickets)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay tickets que coincidan con los filtros aplicados.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Asunto</th>
                                        <?php if ($rol === 'admin'): ?>
                                            <th>Técnico Asignado</th>
                                        <?php endif; ?>
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
                                            <td><?php echo htmlspecialchars($ticket['cliente']); ?></td>
                                            <td>
                                                <div class="fw-bold"><?php echo htmlspecialchars($ticket['asunto']); ?></div>
                                                <small class="text-muted">
                                                    <?php echo substr(htmlspecialchars($ticket['descripcion']), 0, 100); ?>...
                                                </small>
                                            </td>
                                            <?php if ($rol === 'admin'): ?>
                                                <td>
                                                    <?php if ($ticket['tecnico_asignado']): ?>
                                                        <span class="badge bg-info"><?php echo htmlspecialchars($ticket['tecnico_asignado']); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Sin asignar</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $ticket['categoria']; ?></span>
                                            </td>
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
                                                    <a href="ver_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" 
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
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
