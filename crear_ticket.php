<?php
require_once 'conexion.php';
verificarRol('cliente');

$usuario_id = $_SESSION['usuario_id'];
$nombre = $_SESSION['nombre'];
$success = '';
$error = '';

// Obtener lista de técnicos para el dropdown
try {
    $stmt_tecnicos = $pdo->prepare("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'tecnico' AND activo = TRUE ORDER BY nombre");
    $stmt_tecnicos->execute();
    $tecnicos = $stmt_tecnicos->fetchAll();
} catch (PDOException $e) {
    $tecnicos = [];
    $error = "No se pudo cargar la lista de técnicos.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = $_POST['categoria'];
    $prioridad = $_POST['prioridad'];
    $id_tecnico_asignado = !empty($_POST['id_tecnico_asignado']) ? $_POST['id_tecnico_asignado'] : null;

    if (empty($asunto) || empty($descripcion) || empty($categoria)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (id_usuario, asunto, descripcion, categoria, prioridad, id_tecnico_asignado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$usuario_id, $asunto, $descripcion, $categoria, $prioridad, $id_tecnico_asignado]);
            
            $success = 'Ticket creado exitosamente. Un técnico lo revisará pronto.';
            
            // Limpiar formulario
            $_POST = [];
        } catch(PDOException $e) {
            $error = 'Error al crear el ticket. Intente más tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Ticket - Mesa de Ayuda</title>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="crear_ticket.php">
                            <i class="fas fa-plus me-1"></i>Nuevo Ticket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mis_tickets.php">
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
                    <i class="fas fa-plus me-2"></i>Crear Nuevo Ticket
                </h1>
                <p class="dashboard-subtitle">Describe tu problema y un técnico te ayudará</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="asunto" class="form-label">
                                        <i class="fas fa-heading me-2"></i>Asunto del Ticket *
                                    </label>
                                    <input type="text" class="form-control" id="asunto" name="asunto" 
                                           value="<?php echo isset($_POST['asunto']) ? htmlspecialchars($_POST['asunto']) : ''; ?>" 
                                           placeholder="Describe brevemente el problema" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="categoria" class="form-label">
                                        <i class="fas fa-tags me-2"></i>Categoría *
                                    </label>
                                    <select class="form-control" id="categoria" name="categoria" required>
                                        <option value="General" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'General') ? 'selected' : ''; ?>>General</option>
                                        <option value="Software" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Software') ? 'selected' : ''; ?>>Software</option>
                                        <option value="Hardware" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Hardware') ? 'selected' : ''; ?>>Hardware</option>
                                        <option value="Red" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Red') ? 'selected' : ''; ?>>Red</option>
                                        <option value="Cuenta" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Cuenta') ? 'selected' : ''; ?>>Cuenta</option>
                                        <option value="Otro" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">
                                        <i class="fas fa-align-left me-2"></i>Descripción Detallada *
                                    </label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="6" 
                                              placeholder="Describe detalladamente el problema, incluye pasos para reproducirlo, mensajes de error, etc." required><?php echo isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : ''; ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prioridad" class="form-label">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Prioridad
                                    </label>
                                    <select class="form-control" id="prioridad" name="prioridad">
                                        <option value="Baja" <?php echo (isset($_POST['prioridad']) && $_POST['prioridad'] === 'Baja') ? 'selected' : ''; ?>>Baja</option>
                                        <option value="Media" <?php echo (isset($_POST['prioridad']) && $_POST['prioridad'] === 'Media') ? 'selected' : ''; ?>>Media</option>
                                        <option value="Alta" <?php echo (isset($_POST['prioridad']) && $_POST['prioridad'] === 'Alta') ? 'selected' : ''; ?>>Alta</option>
                                        <option value="Urgente" <?php echo (isset($_POST['prioridad']) && $_POST['prioridad'] === 'Urgente') ? 'selected' : ''; ?>>Urgente</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="id_tecnico_asignado" class="form-label">
                                        <i class="fas fa-user-cog me-2"></i>Asignar a Técnico (Opcional)
                                    </label>
                                    <select class="form-control" id="id_tecnico_asignado" name="id_tecnico_asignado">
                                        <option value="">Sin Asignar</option>
                                        <?php foreach ($tecnicos as $tecnico): ?>
                                            <option value="<?php echo $tecnico['id_usuario']; ?>"
                                                <?php echo (isset($_POST['id_tecnico_asignado']) && $_POST['id_tecnico_asignado'] == $tecnico['id_usuario']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tecnico['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle me-2"></i>Consejos para un ticket efectivo:
                                        </h6>
                                        <ul class="card-text small">
                                            <li>Describe el problema claramente</li>
                                            <li>Incluye pasos para reproducirlo</li>
                                            <li>Menciona mensajes de error</li>
                                            <li>Especifica tu sistema operativo</li>
                                            <li>Adjunta capturas si es posible</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="dashboard.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Crear Ticket
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
