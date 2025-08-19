<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Mesa de Ayuda</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            background: #e74c3c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
        .dashboard {
            max-width: 600px;
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            padding: 40px 30px 30px 30px;
            position: relative;
        }
        .panel-opciones button {
            margin: 10px 10px 10px 0;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(90deg, #355c7d 0%, #6c5b7b 100%);
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .panel-opciones button:hover {
            background: linear-gradient(90deg, #6c5b7b 0%, #355c7d 100%);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <form method="post" style="position:relative;">
        <button type="submit" name="logout" class="logout-btn">Salir</button>
    </form>
    <div class="dashboard">
        <h2>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></h2>
        <div class="panel-opciones">
            <button onclick="mostrarFormulario()">📩 Crear Nuevo Ticket</button>
            <button>🔍 Consultar Ticket</button>
            <button>📊 Estado de Mis Tickets</button>
            <button>📂 Historial de Tickets</button>
        </div>
        <div id="formulario-ticket" style="display:none;">
            <h3>Crear Nuevo Ticket</h3>
            <form>
                <label>Categoría:</label>
                <select>
                    <option>Internet</option>
                    <option>Telefonía Fija</option>
                    <option>TV Digital</option>
                    <option>Facturación</option>
                    <option>Otro</option>
                </select>
                <label>Prioridad:</label>
                <select>
                    <option>Baja</option>
                    <option>Media</option>
                    <option>Alta</option>
                    <option>Urgente</option>
                </select>
                <label>Asunto del Ticket:</label>
                <input type="text" required>
                <label>Descripción:</label>
                <textarea required></textarea>
                <label>Adjuntar archivo (opcional):</label>
                <input type="file">
                <button type="submit">ENVIAR TICKET</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
<?php
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>