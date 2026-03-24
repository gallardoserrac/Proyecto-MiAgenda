<?php
session_start();
header('Content-Type: application/json');

// Verificar que sea profesor
if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] !== 'profesor') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado. Solo profesores.']);
    exit;
}

require_once "../configuracion.php";

$accion = $_GET['accion'] ?? '';
$usuario_id = $_SESSION["usuario_id"];

switch ($accion) {
    case 'listar':
        $sql = "SELECT id, usuario, email, fecha_nacimiento, fecha_registro, activo FROM usuarios WHERE rol = 'alumno' ORDER BY usuario ASC";
        $result = $conexion->query($sql);
        $alumnos = [];
        while ($row = $result->fetch_assoc()) {
            $alumnos[] = $row;
        }
        echo json_encode(['alumnos' => $alumnos]);
        break;

    case 'ver_tareas':
        $alumno_id = $_GET['alumno_id'] ?? 0;
        if (empty($alumno_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de alumno requerido']);
            exit;
        }
        $sql = "SELECT id, titulo, descripcion, fecha, hora, prioridad, completada, estado_kanban FROM tareas WHERE usuario_id = ? ORDER BY fecha ASC";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $alumno_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tareas = [];
        while ($row = $result->fetch_assoc()) {
            $tareas[] = $row;
        }
        echo json_encode(['tareas' => $tareas]);
        $stmt->close();
        break;

    case 'crear_tarea':
        $data = json_decode(file_get_contents('php://input'), true);
        $alumno_id = $data['alumno_id'] ?? 0;
        $titulo = $data['titulo'] ?? '';
        $descripcion = $data['descripcion'] ?? '';
        $fecha = $data['fecha'] ?? '';
        $hora = $data['hora'] ?? NULL;
        $prioridad = $data['prioridad'] ?? 'media';
        $estado_kanban = $data['estado_kanban'] ?? 'pendiente';
        
        if (empty($alumno_id) || empty($titulo) || empty($fecha)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }
        
        $sql = "INSERT INTO tareas (usuario_id, titulo, descripcion, fecha, hora, prioridad, estado_kanban) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("issssss", $alumno_id, $titulo, $descripcion, $fecha, $hora, $prioridad, $estado_kanban);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conexion->insert_id]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al crear: ' . $conexion->error]);
        }
        $stmt->close();
        break;

    case 'editar_tarea':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;
        $titulo = $data['titulo'] ?? '';
        $descripcion = $data['descripcion'] ?? '';
        $fecha = $data['fecha'] ?? '';
        $hora = $data['hora'] ?? NULL;
        $prioridad = $data['prioridad'] ?? 'media';
        $estado_kanban = $data['estado_kanban'] ?? 'pendiente';
        $completada = $data['completada'] ?? 0;
        
        if (empty($id) || empty($titulo) || empty($fecha)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }
        
        $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, fecha = ?, hora = ?, prioridad = ?, estado_kanban = ?, completada = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssssii", $titulo, $descripcion, $fecha, $hora, $prioridad, $estado_kanban, $completada, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al editar: ' . $conexion->error]);
        }
        $stmt->close();
        break;

    case 'eliminar_tarea':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;
        
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            exit;
        }
        
        $sql = "DELETE FROM tareas WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar: ' . $conexion->error]);
        }
        $stmt->close();
        break;

    case 'activar_alumno':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;
        $activo = $data['activo'] ?? 1;
        
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            exit;
        }
        
        $sql = "UPDATE usuarios SET activo = ? WHERE id = ? AND rol = 'alumno'";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $activo, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error: ' . $conexion->error]);
        }
        $stmt->close();
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no válida']);
}

$conexion->close();
?>
