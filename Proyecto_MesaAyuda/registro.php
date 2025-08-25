<?php
session_start();
$host = "127.0.0.1";  // mejor usar IP
$username = "root";
$password = "";
$dbname = "mesa_de_ayuda";
$port = 3307;

$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['password'];
    $rol = $_POST['rol'];

    // Encriptar contraseña
    $hash = password_hash($clave, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, password_hash, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $hash, $rol);

    if ($stmt->execute()) {
        $mensaje = "✅ Usuario registrado con éxito";
    } else {
        $mensaje = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      body {
          background: linear-gradient(135deg, #667eea, #764ba2);
          min-height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
      }
      .card {
          border-radius: 15px;
          box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.2);
      }
  </style>
</head>
<body>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
              <div class="card p-4">
                  <div class="card-body">
                      <h3 class="text-center mb-4">Registro de Usuario</h3>
                      
                      <form method="POST">
                          <div class="mb-3">
                              <label class="form-label">Usuario</label>
                              <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Contraseña</label>
                              <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Rol</label>
                              <select name="rol" class="form-select" required>
                                  <option value="">Selecciona un rol</option>
                                  <option value="Administrador">Administrador</option>
                                  <option value="Cliente">Cliente</option>
                                  <option value="Técnico">Técnico</option>
                              </select>
                          </div>
                          <button type="submit" class="btn btn-primary w-100">Registrar</button>
                      </form>
                      
                      <div class="text-center mt-3">
                          <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
                      </div>

                      <!-- Mensaje -->
                      <?php if ($mensaje) echo $mensaje; ?>
                  </div>
              </div>
          </div>
      </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>