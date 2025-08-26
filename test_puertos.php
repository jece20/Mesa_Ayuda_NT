<?php
/**
 * ARCHIVO DE PRUEBA DE PUERTOS MYSQL
 * Verifica qué puertos están disponibles para MySQL
 */

require_once 'config.php';

echo "<h1>🔍 PRUEBA DE PUERTOS MYSQL</h1>";
echo "<hr>";

// Mostrar información de configuración
showConfigInfo();

echo "<hr>";

// Escanear puertos disponibles
showAvailablePorts();

echo "<hr>";

// Probar conexiones en diferentes puertos
echo "<h3>🧪 PRUEBAS DE CONEXIÓN</h3>";

// Probar puerto 3306
echo "<h4>Puerto 3306 (Estándar)</h4>";
$config_3306 = getConfig('desarrollo');
$result_3306 = testConnection($config_3306);

if ($result_3306['success']) {
    echo "✅ <strong>Puerto 3306:</strong> {$result_3306['message']}<br>";
    
    // Probar consulta simple
    try {
        $stmt = $result_3306['pdo']->query("SELECT VERSION() as version");
        $version = $stmt->fetch()['version'];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;• Versión MySQL: $version<br>";
        
        // Probar base de datos
        $stmt = $result_3306['pdo']->query("SHOW DATABASES LIKE 'mesa_ayuda2'");
        if ($stmt->rowCount() > 0) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Base de datos 'mesa_ayuda2': ✅ Encontrada<br>";
        } else {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Base de datos 'mesa_ayuda2': ❌ No encontrada<br>";
        }
    } catch (Exception $e) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;• Error en consulta: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ <strong>Puerto 3306:</strong> {$result_3306['message']}<br>";
}

echo "<br>";

// Probar puerto 3307
echo "<h4>Puerto 3307 (Alternativo)</h4>";
$config_3307 = getConfig('desarrollo_alt');
$result_3307 = testConnection($config_3307);

if ($result_3307['success']) {
    echo "✅ <strong>Puerto 3307:</strong> {$result_3307['message']}<br>";
    
    // Probar consulta simple
    try {
        $stmt = $result_3307['pdo']->query("SELECT VERSION() as version");
        $version = $stmt->fetch()['version'];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;• Versión MySQL: $version<br>";
        
        // Probar base de datos
        $stmt = $result_3307['pdo']->query("SHOW DATABASES LIKE 'mesa_ayuda2'");
        if ($stmt->rowCount() > 0) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Base de datos 'mesa_ayuda2': ✅ Encontrada<br>";
        } else {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Base de datos 'mesa_ayuda2': ❌ No encontrada<br>";
        }
    } catch (Exception $e) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;• Error en consulta: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ <strong>Puerto 3307:</strong> {$result_3307['message']}<br>";
}

echo "<hr>";

// Función de conexión automática
echo "<h3>🚀 CONEXIÓN AUTOMÁTICA</h3>";
try {
    $pdo_auto = connectAuto();
    echo "✅ <strong>Conexión automática exitosa!</strong><br>";
    
    // Obtener información de la conexión
    $dsn = $pdo_auto->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    echo "&nbsp;&nbsp;&nbsp;&nbsp;• Estado: $dsn<br>";
    
    // Probar base de datos
    $stmt = $pdo_auto->query("SELECT DATABASE() as current_db");
    $current_db = $stmt->fetch()['current_db'];
    echo "&nbsp;&nbsp;&nbsp;&nbsp;• Base de datos actual: $current_db<br>";
    
} catch (Exception $e) {
    echo "❌ <strong>Error en conexión automática:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Información adicional
echo "<h3>📚 INFORMACIÓN ADICIONAL</h3>";
echo "<strong>¿Por qué diferentes puertos?</strong><br>";
echo "• Puerto 3306: Puerto estándar de MySQL<br>";
echo "• Puerto 3307: Se usa cuando 3306 está ocupado por otra instancia<br>";
echo "• Puerto 3308+: Puertos adicionales para múltiples instancias<br><br>";

echo "<strong>¿Cuándo usar puerto 3307?</strong><br>";
echo "• Cuando tienes múltiples versiones de MySQL<br>";
echo "• Cuando 3306 está ocupado por otro servicio<br>";
echo "• En entornos de desarrollo con múltiples proyectos<br><br>";

echo "<strong>¿Cómo cambiar el puerto en XAMPP?</strong><br>";
echo "1. Abre XAMPP Control Panel<br>";
echo "2. Haz clic en 'Config' en MySQL<br>";
echo "3. Selecciona 'my.ini'<br>";
echo "4. Cambia 'port=3306' por 'port=3307'<br>";
echo "5. Reinicia MySQL<br><br>";

echo "<strong>¿Cómo verificar qué puerto usa MySQL?</strong><br>";
echo "• En XAMPP: Ver el puerto en el panel de control<br>";
echo "• En phpMyAdmin: Ver la URL en el navegador<br>";
echo "• En línea de comandos: 'netstat -an | findstr 3306'<br>";

echo "<hr>";

// Enlaces útiles
echo "<h3>🔗 ENLACES ÚTILES</h3>";
echo "• <a href='test_conexion.php'>Prueba de Conexión General</a><br>";
echo "• <a href='login.php'>Probar Login del Sistema</a><br>";
echo "• <a href='config.php'>Ver Archivo de Configuración</a><br>";

echo "<hr>";
echo "<strong>🎯 RECOMENDACIÓN:</strong><br>";
echo "Si tu compañero usa puerto 3307, cambia en config.php:<br>";
echo "<code>\$entorno = 'desarrollo_alt';</code><br><br>";

echo "<strong>🎉 ¡Sistema preparado para múltiples puertos!</strong>";
?>
