<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    die("No autorizado");
}

$usuario_id = $_SESSION["usuario_id"];
require_once "../configuracion.php";

$sql = "SELECT id, titulo, descripcion, fecha, hora, prioridad, completada FROM tareas WHERE usuario_id = ? ORDER BY fecha ASC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$tareas = [];
while ($row = $result->fetch_assoc()) {
    $row['completada'] = $row['completada'] ? 'Si' : 'No';
    $tareas[] = $row;
}

$stmt->close();
$conexion->close();

header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="miagenda_export_' . date('Y-m-d') . '.html"');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MiAgenda - Exportacion</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #3498db; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .alta { color: #e74c3c; font-weight: bold; }
        .media { color: #f39c12; }
        .baja { color: #27ae60; }
        .si { color: #27ae60; font-weight: bold; }
        .no { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>MiAgenda - Reporte de Tareas</h1>
    <p><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION["usuario"]); ?></p>
    <p><strong>Total de tareas:</strong> <?php echo count($tareas); ?></p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titulo</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Prioridad</th>
                <th>Completada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tareas as $tarea): ?>
            <tr>
                <td><?php echo $tarea['id']; ?></td>
                <td><?php echo htmlspecialchars($tarea['titulo']); ?></td>
                <td><?php echo $tarea['fecha']; ?></td>
                <td><?php echo $tarea['hora']; ?></td>
                <td class="<?php echo $tarea['prioridad']; ?>"><?php echo $tarea['prioridad']; ?></td>
                <td class="<?php echo strtolower($tarea['completada']); ?>"><?php echo $tarea['completada']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
