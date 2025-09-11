<?php
require_once 'conexion.php';
verificarRol('cliente');

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];

// Filtros
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';
$prioridad_filtro = isset($_GET['prioridad']) ? $_GET['prioridad'] : '';
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Construir consulta con filtros
$where_conditions = ["t.id_usuario = ?"];
$params = [$usuario_id];

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

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

try {
    // Obtener tickets del usuario con filtros
    $sql = "SELECT t.*, tec.nombre as tecnico_asignado 
            FROM tickets t 
            LEFT JOIN usuarios tec ON t.id_tecnico_asignado = tec.id_usuario
            $where_clause 
            ORDER BY t.fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll();
    
    // Obtener opciones para filtros
    $stmt = $pdo->prepare("SELECT DISTINCT estado FROM tickets WHERE id_usuario = ? ORDER BY estado");
    $stmt->execute([$usuario_id]);
    $estados = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT DISTINCT prioridad FROM tickets WHERE id_usuario = ? ORDER BY prioridad");
    $stmt->execute([$usuario_id]);
    $prioridades = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT DISTINCT categoria FROM tickets WHERE id_usuario = ? ORDER BY categoria");
    $stmt->execute([$usuario_id]);
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
    <title>Mis Tickets - Mesa de Ayuda</title>
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
                        <a class="nav-link" href="crear_ticket.php">
                            <i class="fas fa-plus me-1"></i>Nuevo Ticket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="mis_tickets.php">
                            <i class="fas fa-ticket-alt me-1"></i>Mis Tickets
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
                    <i class="fas fa-ticket-alt me-2"></i>Mis Tickets
                </h1>
                <p class="dashboard-subtitle">Aquí puedes ver todos los tickets que has creado.</p>
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
                                <option value="">Todos</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado['estado']; ?>" <?php echo $estado_filtro === $estado['estado'] ? 'selected' : ''; ?>>
                                        <?php echo $estado['estado']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
                            <a href="mis_tickets.php" class="btn btn-secondary mt-4">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Tickets -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Historial de Tickets
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($tickets)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No has creado ningún ticket todavía.</p>
                            <a href="crear_ticket.php" class="btn btn-primary">Crear mi primer ticket</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Asunto</th>
                                        <th>Técnico Asignado</th>
                                        <th>Estado</th>
                                        <th>Última Actualización</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                            <td>#<?php echo $ticket['id_ticket']; ?></td>
                                            <td><?php echo htmlspecialchars($ticket['asunto']); ?></td>
                                            <td><?php echo htmlspecialchars($ticket['tecnico_asignado'] ?? 'Sin asignar'); ?></td>
                                            <td>
                                                <span class="estado-<?php echo str_replace(' ', '-', strtolower($ticket['estado'])); ?>">
                                                    <?php echo $ticket['estado']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['fecha_ultima_actualizacion'])); ?></td>
                                            <td>
                                                <a href="ver_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-action btn-view btn-sm">
                                                    <i class="fas fa-eye"></i> Ver
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
