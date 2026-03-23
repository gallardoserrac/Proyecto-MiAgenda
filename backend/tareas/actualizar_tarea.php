<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" && $_SERVER["REQUEST_METHOD"] !== "PUT") {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

try {
    $conexion = new mysqli("localhost", "miagenda", "miagenda123$", "miagenda");
    
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión");
    }
    
    $datos = json_decode(file_get_contents("php://input"), true);
    $id = $datos["id"] ?? 0;
    $usuario_id = $_SESSION["usuario_id"];
    $titulo = $datos["titulo"] ?? "";
    $descripcion = $datos["descripcion"] ?? "";
    $hora = $datos["hora"] ?? "";
    $prioridad = $datos["prioridad"] ?? "";
    $completada = isset($datos["completada"]) ? (int)$datos["completada"] : null;
    
    if ($id <= 0) {
        echo json_encode(["error" => "ID inválido"]);
        exit;
    }
    
    $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, hora = ?, prioridad = ?";
    if ($completada !== null) {
        $sql .= ", completada = ?";
    }
    $sql .= " WHERE id = ? AND usuario_id = ?";
    
    $stmt = $conexion->prepare($sql);
    
    if ($completada !== null) {
        $stmt->bind_param("sssssii", $titulo, $descripcion, $hora, $prioridad, $completada, $id, $usuario_id);
    } else {
        $stmt->bind_param("ssssii", $titulo, $descripcion, $hora, $prioridad, $id, $usuario_id);
    }
    
    $stmt->execute();
    
    echo json_encode(["success" => true]);
    
    $stmt->close();
    $conexion->close();
    
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
