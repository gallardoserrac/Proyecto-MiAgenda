<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

try {
    $conexion = new mysqli("localhost", "miagenda", "miagenda123$", "miagenda");
    
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión");
    }
    
    $datos = json_decode(file_get_contents("php://input"), true);
    
    $usuario_id = $_SESSION["usuario_id"];
    $titulo = $datos["titulo"] ?? "";
    $descripcion = $datos["descripcion"] ?? "";
    $fecha = $datos["fecha"] ?? "";
    $hora = $datos["hora"] ?? "09:00";
    $prioridad = $datos["prioridad"] ?? "media";
    
    if (empty($titulo) || empty($fecha)) {
        echo json_encode(["error" => "Título y fecha son requeridos"]);
        exit;
    }
    
    $sql = "INSERT INTO tareas (usuario_id, creado_por, titulo, descripcion, fecha, hora, prioridad) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iisssss", $usuario_id, $usuario_id, $titulo, $descripcion, $fecha, $hora, $prioridad);
    $stmt->execute();
    
    $id = $conexion->insert_id;
    
    echo json_encode(["success" => true, "id" => $id]);
    
    $stmt->close();
    $conexion->close();
    
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
