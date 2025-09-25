<<<<<<< HEAD
<?php
session_start();

$host = 'localhost';
$dbname = 'mesa_ayuda2';
$username = 'root';
$password = '';

// Configuración de puertos - Puedes cambiar esto según tu configuración
$puerto = 3306; // Puerto por defecto de MySQL
$puerto_alternativo = 3307; // Puerto alternativo común

try {
    // Intentar conexión con puerto principal
    $pdo = new PDO("mysql:host=$host;port=$puerto;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Si falla, intentar con puerto alternativo
    try {
        $pdo = new PDO("mysql:host=$host;port=$puerto_alternativo;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e2) {
        die("Error de conexión: No se pudo conectar a MySQL en los puertos $puerto o $puerto_alternativo. " . $e2->getMessage());
    }
}

// Función para verificar si el usuario está logueado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Función para verificar rol específico
function verificarRol($rol) {
    verificarLogin();
    if ($_SESSION['rol'] !== $rol) {
        header('Location: dashboard.php');
        exit();
    }
}
?>
=======
<?php
session_start();

$host = 'localhost';
$dbname = 'mesa_ayuda2';
$username = 'root';
$password = '';

// Configuración de puertos - Puedes cambiar esto según tu configuración
$puerto = 3306; // Puerto por defecto de MySQL
$puerto_alternativo = 3307; // Puerto alternativo común

try {
    // Intentar conexión con puerto principal
    $pdo = new PDO("mysql:host=$host;port=$puerto;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Si falla, intentar con puerto alternativo
    try {
        $pdo = new PDO("mysql:host=$host;port=$puerto_alternativo;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e2) {
        die("Error de conexión: No se pudo conectar a MySQL en los puertos $puerto o $puerto_alternativo. " . $e2->getMessage());
    }
}

// Función para verificar si el usuario está logueado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Función para verificar rol específico
function verificarRol($rol) {
    verificarLogin();
    if ($_SESSION['rol'] !== $rol) {
        header('Location: dashboard.php');
        exit();
    }
}
?>
>>>>>>> 9c5133c (Agregué un pipeline)
