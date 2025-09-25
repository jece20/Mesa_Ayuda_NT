<<<<<<< HEAD
<?php
/**
 * ARCHIVO DE PRUEBA DE CONEXIÓN
 * Usar solo para verificar que la base de datos funcione correctamente
 * ELIMINAR después de las pruebas
 */

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'mesa_ayuda2';
$username = 'root';
$password = '';

echo "<h1>🔍 PRUEBA DE CONEXIÓN - mesa_ayuda2</h1>";
echo "<hr>";

try {
    // Intentar conexión
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong>Conexión exitosa a la base de datos!</strong><br><br>";
    
    // Verificar tablas
    echo "<h3>📋 TABLAS ENCONTRADAS:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tablas)) {
        echo "❌ No se encontraron tablas. Verifica que hayas ejecutado el script SQL.<br>";
    } else {
        echo "✅ Se encontraron " . count($tablas) . " tablas:<br>";
        foreach ($tablas as $tabla) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• $tabla<br>";
        }
    }
    
    echo "<br><h3>👥 USUARIOS ENCONTRADOS:</h3>";
    try {
        $stmt = $pdo->query("SELECT nombre, correo, rol FROM usuarios");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($usuarios)) {
            echo "❌ No se encontraron usuarios. Verifica que hayas ejecutado el script SQL.<br>";
        } else {
            echo "✅ Se encontraron " . count($usuarios) . " usuarios:<br>";
            foreach ($usuarios as $usuario) {
                $rol_color = $usuario['rol'] === 'admin' ? '🔴' : ($usuario['rol'] === 'tecnico' ? '🟡' : '🔵');
                echo "&nbsp;&nbsp;&nbsp;&nbsp;$rol_color <strong>{$usuario['nombre']}</strong> ({$usuario['correo']}) - {$usuario['rol']}<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error al consultar usuarios: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>🎫 TICKETS ENCONTRADOS:</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets");
        $total_tickets = $stmt->fetch()['total'];
        echo "✅ Total de tickets: <strong>$total_tickets</strong><br>";
        
        if ($total_tickets > 0) {
            $stmt = $pdo->query("SELECT asunto, estado, prioridad FROM tickets LIMIT 3");
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "&nbsp;&nbsp;&nbsp;&nbsp;Ejemplos:<br>";
            foreach ($tickets as $ticket) {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;• {$ticket['asunto']} - {$ticket['estado']} ({$ticket['prioridad']})<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error al consultar tickets: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>💬 RESPUESTAS ENCONTRADAS:</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM respuestas");
        $total_respuestas = $stmt->fetch()['total'];
        echo "✅ Total de respuestas: <strong>$total_respuestas</strong><br>";
    } catch (Exception $e) {
        echo "❌ Error al consultar respuestas: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>📊 ESTADÍSTICAS GENERALES:</h3>";
    try {
        $stmt = $pdo->query("SELECT * FROM vista_estadisticas_generales");
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($stats) {
            echo "✅ Estadísticas del sistema:<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Total tickets: {$stats['total_tickets']}<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Pendientes: {$stats['tickets_pendientes']}<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• En proceso: {$stats['tickets_en_proceso']}<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Resueltos: {$stats['tickets_resueltos']}<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Cerrados: {$stats['tickets_cerrados']}<br>";
        } else {
            echo "❌ No se pudieron obtener las estadísticas.<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error al consultar estadísticas: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>🔐 PRUEBA DE LOGIN:</h3>";
    try {
        $email = 'juan@correo.com';
        $password = '123';
        
        $stmt = $pdo->prepare("SELECT id_usuario, nombre, correo, contrasena, rol FROM usuarios WHERE correo = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            if ($password === $usuario['contrasena']) {
                echo "✅ <strong>Login exitoso!</strong><br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;Usuario: {$usuario['nombre']}<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;Rol: {$usuario['rol']}<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$usuario['id_usuario']}<br>";
            } else {
                echo "❌ Contraseña incorrecta para {$usuario['correo']}<br>";
            }
        } else {
            echo "❌ Usuario no encontrado: $email<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error en prueba de login: " . $e->getMessage() . "<br>";
    }
    
} catch(PDOException $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br><br>";
    
    echo "<h3>🔧 SOLUCIÓN DE PROBLEMAS:</h3>";
    echo "1. Verifica que XAMPP esté ejecutándose<br>";
    echo "2. Verifica que MySQL esté activo<br>";
    echo "3. Verifica que la base de datos 'mesa_ayuda2' exista<br>";
    echo "4. Verifica las credenciales en conexion.php<br>";
    echo "5. Ejecuta el script mesa_ayuda2.sql en phpMyAdmin<br>";
}

echo "<hr>";
echo "<h3>📝 PRÓXIMOS PASOS:</h3>";
echo "1. Si todo está funcionando, elimina este archivo<br>";
echo "2. Ve a <a href='login.php'>login.php</a> para probar el sistema<br>";
echo "3. Usa las credenciales de prueba:<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;• Email: juan@correo.com<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;• Contraseña: 123<br>";

echo "<br><strong>🎉 ¡Sistema listo para usar!</strong>";
?>
=======
<?php
/**
 * ARCHIVO DE PRUEBA DE CONEXIÓN
 * Usar solo para verificar que la base de datos funcione correctamente
 * ELIMINAR después de las pruebas
 */

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'mesa_ayuda2';
$username = 'root';
$password = '';

echo "<h1>🔍 PRUEBA DE CONEXIÓN - mesa_ayuda2</h1>";
echo "<hr>";

try {
    // Intentar conexión
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong>Conexión exitosa a la base de datos!</strong><br><br>";
    
    // Verificar tablas
    echo "<h3>📋 TABLAS ENCONTRADAS:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tablas)) {
        echo "❌ No se encontraron tablas. Verifica que hayas ejecutado el script SQL.<br>";
    } else {
        echo "✅ Se encontraron " . count($tablas) . " tablas:<br>";
        foreach ($tablas as $tabla) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• $tabla<br>";
        }
    }
    
    echo "<br><h3>👥 USUARIOS ENCONTRADOS:</h3>";
    try {
        $stmt = $pdo->query("SELECT nombre, correo, rol FROM usuarios");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($usuarios)) {
            echo "❌ No se encontraron usuarios. Verifica que hayas ejecutado el script SQL.<br>";
        } else {
            echo "✅ Se encontraron " . count($usuarios) . " usuarios:<br>";
            foreach ($usuarios as $usuario) {
                $rol_color = $usuario['rol'] === 'admin' ? '🔴' : ($usuario['rol'] === 'tecnico' ? '🟡' : '🔵');
                echo "&nbsp;&nbsp;&nbsp;&nbsp;$rol_color <strong>{$usuario['nombre']}</strong> ({$usuario['correo']}) - {$usuario['rol']}<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error al consultar usuarios: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>🎫 TICKETS ENCONTRADOS:</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets");
        $total_tickets = $stmt->fetch()['total'];
        echo "✅ Total de tickets: <strong>$total_tickets</strong><br>";
        
        if ($total_tickets > 0) {
            $stmt = $pdo->query("SELECT asunto, estado, prioridad FROM tickets LIMIT 3");
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "&nbsp;&nbsp;&nbsp;&nbsp;Ejemplos:<br>";
            foreach ($tickets as $ticket) {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;• {$ticket['asunto']} - {$ticket['estado']} ({$ticket['prioridad']})<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error al consultar tickets: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>💬 RESPUESTAS ENCONTRADAS:</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM respuestas");
        $total_respuestas = $stmt->fetch()['total'];
        echo "✅ Total de respuestas: <strong>$total_respuestas</strong><br>";
    } catch (Exception $e) {
        echo "❌ Error al consultar respuestas: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>📊 ESTADÍSTICAS GENERALES:</h3>";
    try {
        $stmt = $pdo->query("SELECT * FROM vista_estadisticas_generales");
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($stats) {
            echo "✅ Estadísticas del sistema:<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Total tickets: {$stats['total_tickets']}<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Pendientes: {$stats['tickets_pendientes']}<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• En proceso: {$stats['tickets_en_proceso']}<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Resueltos: {$stats['tickets_resueltos']}<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;• Cerrados: {$stats['tickets_cerrados']}<br>";
        } else {
            echo "❌ No se pudieron obtener las estadísticas.<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error al consultar estadísticas: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>🔐 PRUEBA DE LOGIN:</h3>";
    try {
        $email = 'juan@correo.com';
        $password = '123';
        
        $stmt = $pdo->prepare("SELECT id_usuario, nombre, correo, contrasena, rol FROM usuarios WHERE correo = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            if ($password === $usuario['contrasena']) {
                echo "✅ <strong>Login exitoso!</strong><br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;Usuario: {$usuario['nombre']}<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;Rol: {$usuario['rol']}<br>";
                echo "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$usuario['id_usuario']}<br>";
            } else {
                echo "❌ Contraseña incorrecta para {$usuario['correo']}<br>";
            }
        } else {
            echo "❌ Usuario no encontrado: $email<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error en prueba de login: " . $e->getMessage() . "<br>";
    }
    
} catch(PDOException $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br><br>";
    
    echo "<h3>🔧 SOLUCIÓN DE PROBLEMAS:</h3>";
    echo "1. Verifica que XAMPP esté ejecutándose<br>";
    echo "2. Verifica que MySQL esté activo<br>";
    echo "3. Verifica que la base de datos 'mesa_ayuda2' exista<br>";
    echo "4. Verifica las credenciales en conexion.php<br>";
    echo "5. Ejecuta el script mesa_ayuda2.sql en phpMyAdmin<br>";
}

echo "<hr>";
echo "<h3>📝 PRÓXIMOS PASOS:</h3>";
echo "1. Si todo está funcionando, elimina este archivo<br>";
echo "2. Ve a <a href='login.php'>login.php</a> para probar el sistema<br>";
echo "3. Usa las credenciales de prueba:<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;• Email: juan@correo.com<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;• Contraseña: 123<br>";

echo "<br><strong>🎉 ¡Sistema listo para usar!</strong>";
?>
>>>>>>> 9c5133c (Agregué un pipeline)
