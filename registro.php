<?php
require_once 'conexion.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];
    
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($confirmar_contrasena)) {
        $error = 'Por favor, complete todos los campos.';
    } elseif ($contrasena !== $confirmar_contrasena) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($contrasena) < 3) {
        $error = 'La contraseña debe tener al menos 3 caracteres.';
    } else {
        try {
            // Verificar si el correo ya existe
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            
            if ($stmt->fetch()) {
                $error = 'Este correo electrónico ya está registrado.';
            } else {
                // Insertar nuevo usuario
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, 'cliente')");
                $stmt->execute([$nombre, $correo, $contrasena]);
                
                $success = 'Usuario registrado exitosamente. Ahora puedes iniciar sesión.';
                
                // Limpiar formulario
                $nombre = $correo = '';
            }
        } catch(PDOException $e) {
            $error = 'Error en el sistema. Intente más tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Mesa de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="form-container fade-in">
            <div class="text-center mb-4">
                <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                <h1 class="form-title">Crear Cuenta</h1>
                <p class="text-muted">Únete a nuestra mesa de ayuda</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombre" class="form-label">
                        <i class="fas fa-user me-2"></i>Nombre Completo
                    </label>
                    <input type="text" class="form-control" id="nombre" name="nombre" 
                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" 
                           required>
                </div>
                
                <div class="mb-3">
                    <label for="correo" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Correo Electrónico
                    </label>
                    <input type="email" class="form-control" id="correo" name="correo" 
                           value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>" 
                           required>
                </div>
                
                <div class="mb-3">
                    <label for="contrasena" class="form-label">
                        <i class="fas fa-lock me-2"></i>Contraseña
                    </label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena" 
                           minlength="3" required>
                    <small class="text-muted">Mínimo 3 caracteres</small>
                </div>
                
                <div class="mb-3">
                    <label for="confirmar_contrasena" class="form-label">
                        <i class="fas fa-lock me-2"></i>Confirmar Contraseña
                    </label>
                    <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" 
                           minlength="3" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                </button>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted">¿Ya tienes una cuenta?</p>
                <a href="login.php" class="btn btn-secondary">
                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
