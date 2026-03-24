<?php
session_start();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="miagenda_export_' . date('Y-m-d') . '.csv"');

if (!isset($_SESSION["usuario_id"])) {
    die("No autorizado");
}

$usuario_id = $_SESSION["usuario_id"];
require_once "../conexion.php";

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Título', 'Descripción', 'Fecha', 'Hora', 'Prioridad', 'Estado']);

$sql = "SELECT id, titulo, descripcion, fecha, hora, prioridad, estado FROM tareas WHERE usuario_id = ? ORDER BY fecha ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$stmt->close();
$conn->close();
exit;
