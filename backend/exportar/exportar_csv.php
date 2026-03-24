<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION["usuario_id"])) {
    die("No autorizado - Debes iniciar sesion primero");
}

$usuario_id = $_SESSION["usuario_id"];
require_once "../configuracion.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="miagenda_export_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Titulo', 'Descripcion', 'Fecha', 'Hora', 'Prioridad', 'Completada']);

$sql = "SELECT id, titulo, descripcion, fecha, hora, prioridad, completada FROM tareas WHERE usuario_id = ? ORDER BY fecha ASC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $row['completada'] = $row['completada'] ? 'Si' : 'No';
    fputcsv($output, $row);
}

fclose($output);
$stmt->close();
$conexion->close();
exit;
