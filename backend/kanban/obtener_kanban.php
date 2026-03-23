<?php
session_start();
header("Content-Type: application/json");
if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}
try {
    $conexion = new mysqli("localhost", "miagenda", "miagenda123$", "miagenda");
    $usuario_id = (int)$_SESSION["usuario_id"];
    $r = $conexion->query("SELECT id, titulo, descripcion, columna, prioridad, color, orden FROM kanban WHERE usuario_id = $usuario_id ORDER BY orden ASC");
    $tarjetas = [];
    while ($f = $r->fetch_assoc()) {
        $tarjetas[] = $f;
    }
    echo json_encode(["tarjetas" => $tarjetas]);
    $conexion->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
