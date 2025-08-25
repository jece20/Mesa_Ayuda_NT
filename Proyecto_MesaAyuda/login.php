<?php
session_start();

// Configuración de conexión
$host = "localhost";
$dbname = "mesa_de_ayuda";
$username = "root";
$password = "";
$port = 3307; // Usa 3306 si es el puerto por defecto. Si tu MySQL está en 3307, cámbialo aquí.

$conn = new mysqli($host, $username, $password, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión a MySQL: " . $conn->connect_error);
}

// Inicializar error
$error = "";

// Procesar login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['password'];

    // Preparar consulta segura
    $stmt = $conn->prepare("SELECT nombre_usuario, password_hash FROM usuarios WHERE nombre_usuario = ? LIMIT 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Validar credenciales
    if ($row && password_verify($clave, $row['password_hash'])) {
        $_SESSION['usuario'] = $row['nombre_usuario'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "⚠️ Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-card {
      width: 100%;
      max-width: 420px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="card login-card p-4">
    <div class="card-body">
      <h3 class="card-title text-center mb-4">🔐 Iniciar Sesión</h3>
      
      <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label for="usuario" class="form-label">Usuario</label>
          <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Ingresa tu usuario" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="form-check mb-3">
          <input type="checkbox" class="form-check-input" id="remember" name="remember">
          <label class="form-check-label" for="remember">Recuérdame</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
      </form>
      
      <div class="mt-3 text-center">
        <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
        <p class="mt-2">¿Nuevo aquí? <a href="registro.php" class="fw-bold text-decoration-none">Regístrate</a></p>
      </div>
    </div>
  </div>
</body>
</html>