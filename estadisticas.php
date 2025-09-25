<<<<<<< HEAD
<?php
require_once 'conexion.php';

// Verificar que sea administrador
verificarRol('admin');

// Obtener estadísticas generales
$stats = [];

// Total de usuarios por rol
$stmt = $pdo->query("SELECT rol, COUNT(*) as total FROM usuarios GROUP BY rol");
$stats['usuarios_por_rol'] = $stmt->fetchAll();

// Total de tickets por estado
$stmt = $pdo->query("SELECT estado, COUNT(*) as total FROM tickets GROUP BY estado");
$stats['tickets_por_estado'] = $stmt->fetchAll();

// Total de tickets por prioridad
$stmt = $pdo->query("SELECT prioridad, COUNT(*) as total FROM tickets GROUP BY prioridad");
$stats['tickets_por_prioridad'] = $stmt->fetchAll();

// Total de tickets por categoría
$stmt = $pdo->query("SELECT categoria, COUNT(*) as total FROM tickets GROUP BY categoria");
$stats['tickets_por_categoria'] = $stmt->fetchAll();

// Tickets por mes (últimos 6 meses)
try {
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
            COUNT(*) as total
        FROM tickets 
        WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
        ORDER BY mes DESC
    ");
    $stats['tickets_por_mes'] = $stmt->fetchAll();
} catch (Exception $e) {
    $stats['tickets_por_mes'] = [];
}

// Top técnicos más activos
try {
    $stmt = $pdo->query("
        SELECT 
            u.nombre,
            COUNT(r.id_respuesta) as respuestas
        FROM usuarios u
        LEFT JOIN respuestas r ON u.id_usuario = r.id_usuario
        WHERE u.rol = 'tecnico'
        GROUP BY u.id_usuario, u.nombre
        ORDER BY respuestas DESC
        LIMIT 5
    ");
    $stats['tecnicos_activos'] = $stmt->fetchAll();
} catch (Exception $e) {
    $stats['tecnicos_activos'] = [];
}

// Tiempo promedio de resolución (tickets resueltos)
try {
    $stmt = $pdo->query("
        SELECT 
            AVG(TIMESTAMPDIFF(HOUR, fecha_creacion, fecha_ultima_actualizacion)) as tiempo_promedio_horas
        FROM tickets 
        WHERE estado IN ('Resuelto', 'Cerrado usuario', 'Cerrado automático')
    ");
    $tiempo_promedio = $stmt->fetch();
    $stats['tiempo_promedio_resolucion'] = round($tiempo_promedio['tiempo_promedio_horas'], 1);
} catch (Exception $e) {
    $stats['tiempo_promedio_resolucion'] = 0;
}

// Total de respuestas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM respuestas");
$stats['total_respuestas'] = $stmt->fetch()['total'];

// Usuarios más activos (más tickets creados)
$stmt = $pdo->query("
    SELECT 
        u.nombre,
        COUNT(t.id_ticket) as tickets_creados
    FROM usuarios u
    LEFT JOIN tickets t ON u.id_usuario = t.id_usuario
    WHERE u.rol = 'cliente'
    GROUP BY u.id_usuario, u.nombre
    ORDER BY tickets_creados DESC
    LIMIT 5
");
$stats['clientes_activos'] = $stmt->fetchAll();

// Estadísticas de rendimiento
$total_tickets = array_sum(array_column($stats['tickets_por_estado'], 'total'));
$tickets_resueltos = 0;
foreach ($stats['tickets_por_estado'] as $estado) {
    if (in_array($estado['estado'], ['Resuelto', 'Cerrado usuario', 'Cerrado automático'])) {
        $tickets_resueltos += $estado['total'];
    }
}
$stats['tasa_resolucion'] = $total_tickets > 0 ? round(($tickets_resueltos / $total_tickets) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas del Sistema - Mesa de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-headset me-2"></i>Mesa de Ayuda
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['nombre']); ?>
                </span>
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center mb-3">
                    <i class="fas fa-chart-line me-3"></i>Estadísticas del Sistema
                </h1>
                <p class="text-center text-muted">Panel de métricas y análisis para administradores</p>
            </div>
        </div>

        <!-- Métricas Principales -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <h4 class="card-title"><?php echo array_sum(array_column($stats['usuarios_por_rol'], 'total')); ?></h4>
                        <p class="card-text">Total Usuarios</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-ticket-alt fa-2x text-info mb-2"></i>
                        <h4 class="card-title"><?php echo $total_tickets; ?></h4>
                        <p class="card-text">Total Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-comments fa-2x text-success mb-2"></i>
                        <h4 class="card-title"><?php echo $stats['total_respuestas']; ?></h4>
                        <p class="card-text">Total Respuestas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
                        <h4 class="card-title"><?php echo $stats['tasa_resolucion']; ?>%</h4>
                        <p class="card-text">Tasa de Resolución</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <!-- Gráfico de Tickets por Estado -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Tickets por Estado</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartEstados" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Tickets por Prioridad -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Tickets por Prioridad</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPrioridades" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tickets por Mes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Tickets por Mes (Últimos 6 meses)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartMeses" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablas de Información -->
        <div class="row mb-4">
            <!-- Usuarios por Rol -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Usuarios por Rol</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rol</th>
                                        <th>Cantidad</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_usuarios = array_sum(array_column($stats['usuarios_por_rol'], 'total'));
                                    foreach ($stats['usuarios_por_rol'] as $rol): 
                                        $porcentaje = $total_usuarios > 0 ? round(($rol['total'] / $total_usuarios) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?php echo $rol['rol'] === 'admin' ? 'danger' : ($rol['rol'] === 'tecnico' ? 'warning' : 'primary'); ?>">
                                                <?php echo ucfirst($rol['rol']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $rol['total']; ?></td>
                                        <td><?php echo $porcentaje; ?>%</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Técnicos -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Top Técnicos Más Activos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Posición</th>
                                        <th>Técnico</th>
                                        <th>Respuestas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['tecnicos_activos'] as $index => $tecnico): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index === 0): ?>
                                                <i class="fas fa-trophy text-warning"></i> 1°
                                            <?php elseif ($index === 1): ?>
                                                <i class="fas fa-medal text-secondary"></i> 2°
                                            <?php elseif ($index === 2): ?>
                                                <i class="fas fa-award text-bronze"></i> 3°
                                            <?php else: ?>
                                                <?php echo ($index + 1) . '°'; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($tecnico['nombre']); ?></td>
                                        <td><?php echo $tecnico['respuestas']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Métricas de Tiempo</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Tiempo promedio de resolución:</strong> <?php echo $stats['tiempo_promedio_resolucion']; ?> horas</p>
                        <p><strong>Tasa de resolución:</strong> <?php echo $stats['tasa_resolucion']; ?>%</p>
                        <p><strong>Total de tickets resueltos:</strong> <?php echo $tickets_resueltos; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Base de datos:</strong> mesa_ayuda2</p>
                        <p><strong>Versión del sistema:</strong> 2.0</p>
                        <p><strong>Fecha de generación:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <a href="dashboard.php" class="btn btn-primary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </a>
                <a href="gestion_usuarios.php" class="btn btn-success me-2">
                    <i class="fas fa-users me-2"></i>Gestionar Usuarios
                </a>
                <a href="todos_tickets.php" class="btn btn-info">
                    <i class="fas fa-list me-2"></i>Ver Todos los Tickets
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Gráfico de Tickets por Estado
        const ctxEstados = document.getElementById('chartEstados').getContext('2d');
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($stats['tickets_por_estado'], 'estado')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($stats['tickets_por_estado'], 'total')); ?>,
                    backgroundColor: [
                        '#28a745', // Verde para Resuelto
                        '#ffc107', // Amarillo para En proceso
                        '#dc3545', // Rojo para Pendiente
                        '#6c757d', // Gris para Cerrado
                        '#17a2b8'  // Azul para otros
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Tickets por Prioridad
        const ctxPrioridades = document.getElementById('chartPrioridades').getContext('2d');
        new Chart(ctxPrioridades, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($stats['tickets_por_prioridad'], 'prioridad')); ?>,
                datasets: [{
                    label: 'Cantidad de Tickets',
                    data: <?php echo json_encode(array_column($stats['tickets_por_prioridad'], 'total')); ?>,
                    backgroundColor: [
                        '#28a745', // Verde para Baja
                        '#17a2b8', // Azul para Media
                        '#ffc107', // Amarillo para Alta
                        '#dc3545'  // Rojo para Urgente
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Tickets por Mes
        const ctxMeses = document.getElementById('chartMeses').getContext('2d');
        new Chart(ctxMeses, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($mes) { return date('M Y', strtotime($mes['mes'] . '-01')); }, $stats['tickets_por_mes'])); ?>,
                datasets: [{
                    label: 'Tickets Creados',
                    data: <?php echo json_encode(array_column($stats['tickets_por_mes'], 'total')); ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
=======
<?php
require_once 'conexion.php';

// Verificar que sea administrador
verificarRol('admin');

// Obtener estadísticas generales
$stats = [];

// Total de usuarios por rol
$stmt = $pdo->query("SELECT rol, COUNT(*) as total FROM usuarios GROUP BY rol");
$stats['usuarios_por_rol'] = $stmt->fetchAll();

// Total de tickets por estado
$stmt = $pdo->query("SELECT estado, COUNT(*) as total FROM tickets GROUP BY estado");
$stats['tickets_por_estado'] = $stmt->fetchAll();

// Total de tickets por prioridad
$stmt = $pdo->query("SELECT prioridad, COUNT(*) as total FROM tickets GROUP BY prioridad");
$stats['tickets_por_prioridad'] = $stmt->fetchAll();

// Total de tickets por categoría
$stmt = $pdo->query("SELECT categoria, COUNT(*) as total FROM tickets GROUP BY categoria");
$stats['tickets_por_categoria'] = $stmt->fetchAll();

// Tickets por mes (últimos 6 meses)
try {
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
            COUNT(*) as total
        FROM tickets 
        WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
        ORDER BY mes DESC
    ");
    $stats['tickets_por_mes'] = $stmt->fetchAll();
} catch (Exception $e) {
    $stats['tickets_por_mes'] = [];
}

// Top técnicos más activos
try {
    $stmt = $pdo->query("
        SELECT 
            u.nombre,
            COUNT(r.id_respuesta) as respuestas
        FROM usuarios u
        LEFT JOIN respuestas r ON u.id_usuario = r.id_usuario
        WHERE u.rol = 'tecnico'
        GROUP BY u.id_usuario, u.nombre
        ORDER BY respuestas DESC
        LIMIT 5
    ");
    $stats['tecnicos_activos'] = $stmt->fetchAll();
} catch (Exception $e) {
    $stats['tecnicos_activos'] = [];
}

// Tiempo promedio de resolución (tickets resueltos)
try {
    $stmt = $pdo->query("
        SELECT 
            AVG(TIMESTAMPDIFF(HOUR, fecha_creacion, fecha_ultima_actualizacion)) as tiempo_promedio_horas
        FROM tickets 
        WHERE estado IN ('Resuelto', 'Cerrado usuario', 'Cerrado automático')
    ");
    $tiempo_promedio = $stmt->fetch();
    $stats['tiempo_promedio_resolucion'] = round($tiempo_promedio['tiempo_promedio_horas'], 1);
} catch (Exception $e) {
    $stats['tiempo_promedio_resolucion'] = 0;
}

// Total de respuestas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM respuestas");
$stats['total_respuestas'] = $stmt->fetch()['total'];

// Usuarios más activos (más tickets creados)
$stmt = $pdo->query("
    SELECT 
        u.nombre,
        COUNT(t.id_ticket) as tickets_creados
    FROM usuarios u
    LEFT JOIN tickets t ON u.id_usuario = t.id_usuario
    WHERE u.rol = 'cliente'
    GROUP BY u.id_usuario, u.nombre
    ORDER BY tickets_creados DESC
    LIMIT 5
");
$stats['clientes_activos'] = $stmt->fetchAll();

// Estadísticas de rendimiento
$total_tickets = array_sum(array_column($stats['tickets_por_estado'], 'total'));
$tickets_resueltos = 0;
foreach ($stats['tickets_por_estado'] as $estado) {
    if (in_array($estado['estado'], ['Resuelto', 'Cerrado usuario', 'Cerrado automático'])) {
        $tickets_resueltos += $estado['total'];
    }
}
$stats['tasa_resolucion'] = $total_tickets > 0 ? round(($tickets_resueltos / $total_tickets) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas del Sistema - Mesa de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-headset me-2"></i>Mesa de Ayuda
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['nombre']); ?>
                </span>
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center mb-3">
                    <i class="fas fa-chart-line me-3"></i>Estadísticas del Sistema
                </h1>
                <p class="text-center text-muted">Panel de métricas y análisis para administradores</p>
            </div>
        </div>

        <!-- Métricas Principales -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <h4 class="card-title"><?php echo array_sum(array_column($stats['usuarios_por_rol'], 'total')); ?></h4>
                        <p class="card-text">Total Usuarios</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-ticket-alt fa-2x text-info mb-2"></i>
                        <h4 class="card-title"><?php echo $total_tickets; ?></h4>
                        <p class="card-text">Total Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-comments fa-2x text-success mb-2"></i>
                        <h4 class="card-title"><?php echo $stats['total_respuestas']; ?></h4>
                        <p class="card-text">Total Respuestas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
                        <h4 class="card-title"><?php echo $stats['tasa_resolucion']; ?>%</h4>
                        <p class="card-text">Tasa de Resolución</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <!-- Gráfico de Tickets por Estado -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Tickets por Estado</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartEstados" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Tickets por Prioridad -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Tickets por Prioridad</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPrioridades" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tickets por Mes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Tickets por Mes (Últimos 6 meses)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartMeses" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablas de Información -->
        <div class="row mb-4">
            <!-- Usuarios por Rol -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Usuarios por Rol</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rol</th>
                                        <th>Cantidad</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_usuarios = array_sum(array_column($stats['usuarios_por_rol'], 'total'));
                                    foreach ($stats['usuarios_por_rol'] as $rol): 
                                        $porcentaje = $total_usuarios > 0 ? round(($rol['total'] / $total_usuarios) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?php echo $rol['rol'] === 'admin' ? 'danger' : ($rol['rol'] === 'tecnico' ? 'warning' : 'primary'); ?>">
                                                <?php echo ucfirst($rol['rol']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $rol['total']; ?></td>
                                        <td><?php echo $porcentaje; ?>%</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Técnicos -->
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Top Técnicos Más Activos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Posición</th>
                                        <th>Técnico</th>
                                        <th>Respuestas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['tecnicos_activos'] as $index => $tecnico): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index === 0): ?>
                                                <i class="fas fa-trophy text-warning"></i> 1°
                                            <?php elseif ($index === 1): ?>
                                                <i class="fas fa-medal text-secondary"></i> 2°
                                            <?php elseif ($index === 2): ?>
                                                <i class="fas fa-award text-bronze"></i> 3°
                                            <?php else: ?>
                                                <?php echo ($index + 1) . '°'; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($tecnico['nombre']); ?></td>
                                        <td><?php echo $tecnico['respuestas']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Métricas de Tiempo</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Tiempo promedio de resolución:</strong> <?php echo $stats['tiempo_promedio_resolucion']; ?> horas</p>
                        <p><strong>Tasa de resolución:</strong> <?php echo $stats['tasa_resolucion']; ?>%</p>
                        <p><strong>Total de tickets resueltos:</strong> <?php echo $tickets_resueltos; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Base de datos:</strong> mesa_ayuda2</p>
                        <p><strong>Versión del sistema:</strong> 2.0</p>
                        <p><strong>Fecha de generación:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <a href="dashboard.php" class="btn btn-primary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </a>
                <a href="gestion_usuarios.php" class="btn btn-success me-2">
                    <i class="fas fa-users me-2"></i>Gestionar Usuarios
                </a>
                <a href="todos_tickets.php" class="btn btn-info">
                    <i class="fas fa-list me-2"></i>Ver Todos los Tickets
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Gráfico de Tickets por Estado
        const ctxEstados = document.getElementById('chartEstados').getContext('2d');
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($stats['tickets_por_estado'], 'estado')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($stats['tickets_por_estado'], 'total')); ?>,
                    backgroundColor: [
                        '#28a745', // Verde para Resuelto
                        '#ffc107', // Amarillo para En proceso
                        '#dc3545', // Rojo para Pendiente
                        '#6c757d', // Gris para Cerrado
                        '#17a2b8'  // Azul para otros
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Tickets por Prioridad
        const ctxPrioridades = document.getElementById('chartPrioridades').getContext('2d');
        new Chart(ctxPrioridades, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($stats['tickets_por_prioridad'], 'prioridad')); ?>,
                datasets: [{
                    label: 'Cantidad de Tickets',
                    data: <?php echo json_encode(array_column($stats['tickets_por_prioridad'], 'total')); ?>,
                    backgroundColor: [
                        '#28a745', // Verde para Baja
                        '#17a2b8', // Azul para Media
                        '#ffc107', // Amarillo para Alta
                        '#dc3545'  // Rojo para Urgente
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Tickets por Mes
        const ctxMeses = document.getElementById('chartMeses').getContext('2d');
        new Chart(ctxMeses, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($mes) { return date('M Y', strtotime($mes['mes'] . '-01')); }, $stats['tickets_por_mes'])); ?>,
                datasets: [{
                    label: 'Tickets Creados',
                    data: <?php echo json_encode(array_column($stats['tickets_por_mes'], 'total')); ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
>>>>>>> 9c5133c (Agregué un pipeline)
