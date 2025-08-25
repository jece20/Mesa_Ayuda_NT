<?php
// Conexión a la base de datos MySQL
$conexion = new mysqli("localhost", "root", "", "mesa_ayuda");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>