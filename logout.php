<<<<<<< HEAD
<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: login.php');
exit();
?>
=======
<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: login.php');
exit();
?>
>>>>>>> 9c5133c (Agregué un pipeline)
