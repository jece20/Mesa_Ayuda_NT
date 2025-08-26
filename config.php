<?php
/**
 * ARCHIVO DE CONFIGURACIÓN
 * Configuración centralizada para diferentes entornos
 */

// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =====================================================

// Configuración por defecto (XAMPP estándar)
$config = [
    'desarrollo' => [
        'host' => 'localhost',
        'port' => 3306,
        'dbname' => 'mesa_ayuda2',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ],
    
    // Configuración alternativa (puerto 3307)
    'desarrollo_alt' => [
        'host' => 'localhost',
        'port' => 3307,
        'dbname' => 'mesa_ayuda2',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ],
    
    // Configuración para producción (ejemplo)
    'produccion' => [
        'host' => 'localhost',
        'port' => 3306,
        'dbname' => 'mesa_ayuda2',
        'username' => 'usuario_prod',
        'password' => 'password_seguro',
        'charset' => 'utf8'
    ]
];

// Seleccionar entorno (cambiar según necesidad)
$entorno = 'desarrollo'; // Cambiar a 'desarrollo_alt' si usas puerto 3307

// Función para obtener configuración
function getConfig($entorno = 'desarrollo') {
    global $config;
    return $config[$entorno] ?? $config['desarrollo'];
}

// Función para crear string de conexión
function getConnectionString($config) {
    return "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
}

// Función para probar conexión
function testConnection($config) {
    try {
        $dsn = getConnectionString($config);
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ['success' => true, 'message' => "Conexión exitosa en puerto {$config['port']}", 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Error en puerto {$config['port']}: " . $e->getMessage()];
    }
}

// Función para conectar automáticamente
function connectAuto() {
    global $config;
    
    // Probar puerto 3306 primero
    $result = testConnection($config['desarrollo']);
    if ($result['success']) {
        return $result['pdo'];
    }
    
    // Si falla, probar puerto 3307
    $result = testConnection($config['desarrollo_alt']);
    if ($result['success']) {
        return $result['pdo'];
    }
    
    // Si ambos fallan, mostrar error
    throw new Exception("No se pudo conectar a MySQL en ningún puerto disponible. Verifica que MySQL esté ejecutándose.");
}

// =====================================================
// CONFIGURACIÓN DE LA APLICACIÓN
// =====================================================

// Configuración general
define('APP_NAME', 'Mesa de Ayuda');
define('APP_VERSION', '2.0');
define('APP_URL', 'http://localhost/MesaAyuda_Final/');

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora
define('SESSION_NAME', 'MesaAyuda_Session');

// Configuración de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Configuración de paginación
define('ITEMS_PER_PAGE', 10);

// =====================================================
// CONFIGURACIÓN DE SEGURIDAD
// =====================================================

// Configuración de contraseñas
define('MIN_PASSWORD_LENGTH', 6);
define('PASSWORD_REQUIRE_SPECIAL', false);

// Configuración de intentos de login
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutos

// =====================================================
// CONFIGURACIÓN DE LOGS
// =====================================================

// Habilitar logs
define('ENABLE_LOGS', true);
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// =====================================================
// INFORMACIÓN DEL SISTEMA
// =====================================================

// Detectar entorno automáticamente
function detectEnvironment() {
    if (isset($_SERVER['HTTP_HOST'])) {
        if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
            return 'desarrollo';
        } else {
            return 'produccion';
        }
    }
    return 'desarrollo';
}

// Función para mostrar información de configuración
function showConfigInfo() {
    global $config;
    $current_env = detectEnvironment();
    $current_config = getConfig($current_env);
    
    echo "<h3>🔧 Información de Configuración</h3>";
    echo "<strong>Entorno detectado:</strong> $current_env<br>";
    echo "<strong>Host:</strong> {$current_config['host']}<br>";
    echo "<strong>Puerto:</strong> {$current_config['port']}<br>";
    echo "<strong>Base de datos:</strong> {$current_config['dbname']}<br>";
    echo "<strong>Usuario:</strong> {$current_config['username']}<br>";
    echo "<strong>Charset:</strong> {$current_config['charset']}<br>";
}

// =====================================================
// CONFIGURACIÓN DE PUERTOS COMUNES
// =====================================================

// Puertos comunes de MySQL
$puertos_comunes = [
    3306 => 'MySQL estándar (XAMPP por defecto)',
    3307 => 'MySQL alternativo (cuando 3306 está ocupado)',
    3308 => 'MySQL tercer puerto',
    3309 => 'MySQL cuarto puerto'
];

// Función para escanear puertos disponibles
function scanPorts($host = 'localhost') {
    global $puertos_comunes;
    $puertos_disponibles = [];
    
    foreach ($puertos_comunes as $puerto => $descripcion) {
        $connection = @fsockopen($host, $puerto, $errno, $errstr, 1);
        if (is_resource($connection)) {
            $puertos_disponibles[$puerto] = $descripcion;
            fclose($connection);
        }
    }
    
    return $puertos_disponibles;
}

// Función para mostrar puertos disponibles
function showAvailablePorts() {
    $puertos = scanPorts();
    echo "<h3>🔍 Puertos MySQL Disponibles</h3>";
    
    if (empty($puertos)) {
        echo "❌ No se detectaron puertos MySQL activos<br>";
        echo "Verifica que MySQL esté ejecutándose<br>";
    } else {
        echo "✅ Puertos detectados:<br>";
        foreach ($puertos as $puerto => $descripcion) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Puerto $puerto: $descripcion<br>";
        }
    }
}

?>
