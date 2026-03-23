<?php
session_start();
header("Content-Type: application/json");
if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}
$input = file_get_contents("php://input");
$datos = json_decode($input, true);
if (!$datos) {
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}
try {
    $conexion = new mysqli("localhost", "miagenda", "miagenda123$", "miagenda");
    $usuario_id = (int)$_SESSION["usuario_id"];
    $titulo = $conexion->real_escape_string($datos["titulo"] ?? "Nueva tarjeta");
    $columna = $conexion->real_escape_string($datos["columna"] ?? "pendiente");
    $color = $conexion->real_escape_string($datos["color"] ?? "#ffffff");
    $prioridad = $conexion->real_escape_string($datos["prioridad"] ?? "media");
    $sql = "INSERT INTO kanban (usuario_id, creado_por, titulo, columna, color, prioridad) VALUES ($usuario_id, $usuario_id, '$titulo', '$columna', '$color', '$prioridad')";
    if ($conexion->query($sql)) {
        echo json_encode(["success" => true, "id" => $conexion->insert_id]);
    } else {
        echo json_encode(["error" => $conexion->error]);
    }
    $conexion->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
