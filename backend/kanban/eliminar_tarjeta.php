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
if (!$datos || !isset($datos["id"])) {
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}
try {
    $conexion = new mysqli("localhost", "miagenda", "miagenda123$", "miagenda");
    $usuario_id = (int)$_SESSION["usuario_id"];
    $id = (int)$datos["id"];
    $sql = "DELETE FROM kanban WHERE id = $id AND usuario_id = $usuario_id";
    if ($conexion->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => $conexion->error]);
    }
    $conexion->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
