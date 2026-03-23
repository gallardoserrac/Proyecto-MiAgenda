<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

try {
    $conexion = new mysqli("localhost", "miagenda", "miagenda123$", "miagenda");
    
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    
    $usuario_id = $_SESSION["usuario_id"];
    $mes = isset($_GET["mes"]) ? (int)$_GET["mes"] : date("n");
    $año = isset($_GET["año"]) ? (int)$_GET["año"] : date("Y");
    
    $sql = "SELECT id, titulo, descripcion, fecha, hora, prioridad, completada 
            FROM tareas 
            WHERE usuario_id = ? 
            AND MONTH(fecha) = ? 
            AND YEAR(fecha) = ?
            ORDER BY fecha, hora";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $usuario_id, $mes, $año);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $tareas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $tareas[] = $fila;
    }
    
    echo json_encode(["tareas" => $tareas]);
    
    $stmt->close();
    $conexion->close();
    
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
