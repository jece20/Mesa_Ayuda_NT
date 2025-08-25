<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg p-4 text-center" style="max-width: 400px; width: 100%;">
    <h2 class="mb-3 text-primary">Bienvenido 👋</h2>
    <h5 class="mb-3 text-dark">
      <?php echo htmlspecialchars($_SESSION['usuario']); ?>
    </h5>
    <p class="text-muted">Has iniciado sesión correctamente.</p>
    <a href="logout.php" class="btn btn-danger w-100 mt-3">Cerrar Sesión</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
