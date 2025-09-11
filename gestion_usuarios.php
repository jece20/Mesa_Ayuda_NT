<?php
require_once 'conexion.php';
verificarRol('admin');

$usuario_id = $_SESSION['usuario_id'];
$nombre = $_SESSION['nombre'];
$success = '';
$error = '';

// Procesar cambios de rol
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cambiar_rol'])) {
    $id_usuario = $_POST['id_usuario'];
    $nuevo_rol = $_POST['nuevo_rol'];
    
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET rol = ? WHERE id_usuario = ?");
        $stmt->execute([$nuevo_rol, $id_usuario]);
        $success = 'Rol del usuario actualizado exitosamente.';
    } catch(PDOException $e) {
        $error = 'Error al actualizar el rol del usuario.';
    }
}

// Procesar eliminación de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_usuario'])) {
    $id_usuario = $_POST['id_usuario'];
    
    if ($id_usuario == $usuario_id) {
        $error = 'No puedes eliminar tu propia cuenta.';
    } else {
        try {
            // Verificar si el usuario tiene tickets
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tickets WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);
            $tiene_tickets = $stmt->fetch()['total'] > 0;
            
            if ($tiene_tickets) {
                $error = 'No se puede eliminar un usuario que tiene tickets asociados.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
                $stmt->execute([$id_usuario]);
                $success = 'Usuario eliminado exitosamente.';
            }
        } catch(PDOException $e) {
            $error = 'Error al eliminar el usuario.';
        }
    }
}

// Procesar creación de nuevo usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_usuario'])) {
    $nombre_nuevo = trim($_POST['nombre_nuevo']);
    $correo_nuevo = trim($_POST['correo_nuevo']);
    $contrasena_nueva = $_POST['contrasena_nueva'];
    $rol_nuevo = $_POST['rol_nuevo'];

    if (empty($nombre_nuevo) || empty($correo_nuevo) || empty($contrasena_nueva) || empty($rol_nuevo)) {
        $error = 'Por favor, complete todos los campos para crear el usuario.';
    } else {
        try {
            // Verificar si el correo ya existe
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo_nuevo]);
            
            if ($stmt->fetch()) {
                $error = 'El correo electrónico ya está registrado.';
            } else {
                // Insertar nuevo usuario
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre_nuevo, $correo_nuevo, $contrasena_nueva, $rol_nuevo]);
                $success = 'Usuario creado exitosamente.';
            }
        } catch(PDOException $e) {
            $error = 'Error al crear el usuario.';
        }
    }
}

// Obtener lista de usuarios
try {
    $stmt = $pdo->prepare("SELECT id_usuario, nombre, correo, rol, 
                          (SELECT COUNT(*) FROM tickets WHERE id_usuario = u.id_usuario) as total_tickets
                          FROM usuarios u ORDER BY nombre");
    $stmt->execute();
    $usuarios = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = 'Error al cargar la lista de usuarios.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Mesa de Ayuda</title>
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
                        <a class="nav-link active" href="gestion_usuarios.php">
                            <i class="fas fa-users me-1"></i>Gestión Usuarios
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
                    <i class="fas fa-users me-2"></i>Gestión de Usuarios
                </h1>
                <p class="dashboard-subtitle">Administra los usuarios del sistema</p>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Lista de Usuarios
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalCrear()">
                        <i class="fas fa-plus me-2"></i>Crear Nuevo Usuario
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($usuarios)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay usuarios registrados.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Tickets</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td>#<?php echo $usuario['id_usuario']; ?></td>
                                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $usuario['rol'] === 'admin' ? 'danger' : 
                                                        ($usuario['rol'] === 'tecnico' ? 'warning' : 'primary'); 
                                                ?>">
                                                    <?php echo ucfirst($usuario['rol']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo $usuario['total_tickets']; ?> tickets
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($usuario['id_usuario'] != $usuario_id): ?>
                                                    <!-- Cambiar Rol -->
                                                    <button type="button" class="btn btn-action btn-edit btn-sm" 
                                                            onclick="abrirModalRol(<?php echo $usuario['id_usuario']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>', '<?php echo $usuario['rol']; ?>')">
                                                        <i class="fas fa-user-edit"></i>
                                                    </button>
                                                    
                                                    <!-- Eliminar Usuario -->
                                                    <button type="button" class="btn btn-action btn-delete btn-sm" 
                                                            onclick="abrirModalEliminar(<?php echo $usuario['id_usuario']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>', <?php echo $usuario['total_tickets']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">Tu cuenta</span>
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
    
    <!-- Modal para crear usuario -->
    <div class="modal fade" id="crearUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre_nuevo" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="nombre_nuevo" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo_nuevo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo_nuevo" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena_nueva" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena_nueva" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol_nuevo" class="form-label">Rol</label>
                            <select class="form-control" name="rol_nuevo" required>
                                <option value="tecnico" selected>Técnico</option>
                                <option value="cliente">Cliente</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="crear_usuario" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar rol (reutilizable) -->
    <div class="modal fade" id="cambiarRolModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Rol de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <p>¿Estás seguro de que quieres cambiar el rol de <strong id="nombreUsuarioRol"></strong>?</p>
                        <div class="mb-3">
                            <label for="nuevo_rol" class="form-label">Nuevo Rol:</label>
                            <select class="form-control" name="nuevo_rol" id="nuevo_rol" required>
                                <option value="cliente">Cliente</option>
                                <option value="tecnico">Técnico</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <input type="hidden" name="id_usuario" id="id_usuario_rol">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="cambiar_rol" class="btn btn-primary">Cambiar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal para eliminar usuario (reutilizable) -->
    <div class="modal fade" id="eliminarUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Advertencia!</strong> Esta acción no se puede deshacer.
                    </div>
                    <p>¿Estás seguro de que quieres eliminar al usuario <strong id="nombreUsuarioEliminar"></strong>?</p>
                    <div class="alert alert-info" id="infoTickets" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="mensajeTickets"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="" style="display: inline;" id="formEliminar">
                        <input type="hidden" name="id_usuario" id="id_usuario_eliminar">
                        <button type="submit" name="eliminar_usuario" class="btn btn-danger" 
                                onclick="return confirm('¿Estás completamente seguro?')">
                            <i class="fas fa-trash me-2"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Script para manejar mejor los modales y evitar parpadeo
        document.addEventListener('DOMContentLoaded', function() {
            // Prevenir múltiples instancias de modales
            let modalInstances = {};
            
            // Función para inicializar modales
            function initializeModals() {
                const modalElements = document.querySelectorAll('.modal');
                
                modalElements.forEach(function(modal) {
                    const modalId = modal.getAttribute('id');
                    
                    // Crear instancia del modal si no existe
                    if (!modalInstances[modalId]) {
                        modalInstances[modalId] = new bootstrap.Modal(modal, {
                            backdrop: 'static',
                            keyboard: true,
                            focus: true
                        });
                    }
                });
            }
            
            // Inicializar modales al cargar la página
            initializeModals();
            
            // Limpiar instancias cuando se cierran los modales
            document.addEventListener('hidden.bs.modal', function(e) {
                const modalId = e.target.getAttribute('id');
                if (modalInstances[modalId]) {
                    // Resetear el estado del modal
                    e.target.classList.remove('show');
                    e.target.style.display = 'none';
                    e.target.removeAttribute('aria-modal');
                    e.target.removeAttribute('role');
                    e.target.setAttribute('aria-hidden', 'true');
                }
            });
            
            // Prevenir cierre accidental de modales
            document.addEventListener('show.bs.modal', function(e) {
                e.target.classList.add('show');
                e.target.style.display = 'block';
                e.target.setAttribute('aria-modal', 'true');
                e.target.setAttribute('role', 'dialog');
                e.target.removeAttribute('aria-hidden');
            });
        });
        
        // Función para abrir modal de creación
        function abrirModalCrear() {
            const modal = new bootstrap.Modal(document.getElementById('crearUsuarioModal'));
            modal.show();
        }

        // Función para abrir modal de cambio de rol
        function abrirModalRol(idUsuario, nombreUsuario, rolActual) {
            // Cerrar cualquier modal abierto
            const modalesAbiertos = document.querySelectorAll('.modal.show');
            modalesAbiertos.forEach(function(modal) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
            
            // Esperar un momento y luego abrir el modal
            setTimeout(function() {
                // Llenar los datos del modal
                document.getElementById('nombreUsuarioRol').textContent = nombreUsuario;
                document.getElementById('id_usuario_rol').value = idUsuario;
                document.getElementById('nuevo_rol').value = rolActual;
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('cambiarRolModal'), {
                    backdrop: 'static',
                    keyboard: true,
                    focus: true
                });
                modal.show();
            }, 150);
        }
        
        // Función para abrir modal de eliminación
        function abrirModalEliminar(idUsuario, nombreUsuario, totalTickets) {
            // Cerrar cualquier modal abierto
            const modalesAbiertos = document.querySelectorAll('.modal.show');
            modalesAbiertos.forEach(function(modal) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
            
            // Esperar un momento y luego abrir el modal
            setTimeout(function() {
                // Llenar los datos del modal
                document.getElementById('nombreUsuarioEliminar').textContent = nombreUsuario;
                document.getElementById('id_usuario_eliminar').value = idUsuario;
                
                // Mostrar/ocultar información de tickets
                const infoTickets = document.getElementById('infoTickets');
                const formEliminar = document.getElementById('formEliminar');
                
                if (totalTickets > 0) {
                    document.getElementById('mensajeTickets').textContent = 
                        'Este usuario tiene ' + totalTickets + ' tickets asociados y no puede ser eliminado.';
                    infoTickets.style.display = 'block';
                    formEliminar.style.display = 'none';
                } else {
                    infoTickets.style.display = 'none';
                    formEliminar.style.display = 'inline';
                }
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('eliminarUsuarioModal'), {
                    backdrop: 'static',
                    keyboard: true,
                    focus: true
                });
                modal.show();
            }, 150);
        }
    </script>
</body>
</html>
