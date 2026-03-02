
<?php

$conexion = new mysqli(
  "localhost",
  "miagenda",           
  "miagenda123$",       
  "miagenda"            
);

if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

?>