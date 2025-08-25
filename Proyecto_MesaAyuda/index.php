<?php
session_start();
include 'conexion.php';

$mensaje = "";
$mostrar_mensaje = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contrasena = md5($_POST['contrasena']); // Encriptación simple para demo

    $sql = "SELECT * FROM usuarios WHERE correo='$correo' AND contrasena='$contrasena'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        $_SESSION['usuario'] = $usuario;
        header("Location: dashboard.php");
        exit();
    } else {
        $mensaje = "Correo o contraseña incorrectos.";
        $mostrar_mensaje = true;
    }
} else {
    // Si se recarga la página, no mostrar mensaje
    $mensaje = "";
    $mostrar_mensaje = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Mesa de Ayuda</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="fondo-login">
    <div class="fondo-nubes"></div>
    <div class="login-container animacion">
        <h2 class="titulo-login">Bienvenido a la Mesa de Ayuda</h2>
        <form method="POST">
            <div class="campo">
                <input type="email" name="correo" placeholder="Correo" required>
            </div>
            <div class="campo">
                <input type="password" name="contrasena" placeholder="Contraseña" required>
            </div>
            <div class="opciones">
                <a href="#">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
            <?php if($mostrar_mensaje && $mensaje) echo "<p class='error'>$mensaje</p>"; ?>
        </form>
    </div>
    <script src="script.js"></script>
</body>
</html>