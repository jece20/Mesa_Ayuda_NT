<?php
require_once 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    
    if (empty($correo) || empty($contrasena)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id_usuario, nombre, correo, contrasena, rol FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch();
            
            if ($usuario && $contrasena === $usuario['contrasena']) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['rol'] = $usuario['rol'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Credenciales incorrectas.';
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
    <title>Login - Mesa de Ayuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="form-container fade-in">
            <div class="text-center mb-4">
                <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                <h1 class="form-title">Mesa de Ayuda</h1>
                <p class="text-muted">Inicia sesión en tu cuenta</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
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
                    <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                </button>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted">¿No tienes una cuenta?</p>
                <a href="registro.php" class="btn btn-secondary">
                    <i class="fas fa-user-plus me-2"></i>Registrarse
                </a>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Usuarios de prueba: juan@correo.com, ana@correo.com, admin@correo.com (contraseña: 123)
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
