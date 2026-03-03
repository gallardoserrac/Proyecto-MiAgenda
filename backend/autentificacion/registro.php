
<?php

session_start();

require_once '../configuracion.php';

$error = "";
$exito = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario  = $_POST['usuario'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirmarcontrasena'];
    $fecha_nacimiento = $_POST['fechanacimiento'] ?? NULL;



    if ($usuario == "" || $email == "" || $password == "" || $confirm == "") {
        $error = "Rellena todos los campos obligatorios.";
    } elseif ($password !== $confirm) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } else {


        $sql = "SELECT id FROM usuarios WHERE email = '$email'";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            $error = "El email ya está registrado.";
        } else {

            $password_seguro = password_hash($password, PASSWORD_DEFAULT);

            $sql = "
            INSERT INTO usuarios (
                usuario,
                email,
                password,
                rol,
                fecha_nacimiento,
                activo
            ) VALUES (
                '$usuario',
                '$email',
                '$password_seguro',
                'alumno',
                " . ($fecha_nacimiento ? "'$fecha_nacimiento'" : "NULL") . ",
                1
            )
        ";


            if ($conexion->query($sql) === TRUE) {
                $exito = "Registro exitoso. Ahora puedes iniciar sesión.";
            } else {
                $error = "Error al registrar: " . $conexion->error;
            }

        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../img/favicon-32x32.png" type="image/png">
    <link rel="stylesheet" href="registro.css">
    <title>MiAgenda - Registro</title>
</head>
<body>

<main>

  <div class="contenedor">
    
    <div class="izquierda">

      <img src="../../img/android-chrome-192x192.png" alt="Logo MiAgenda">

      <div class="texto-izquierda">

        <h2>¡Bienvenido!</h2>
        <h3>Únete a MiAgenda para superar todo lo que te propongas</h3>
        <h3>y tener un futuro brillante de manera ordenada</h3>

      </div>

    </div>

    <div class="linea-divisora"></div>

    <div class="derecha">

      <?php if ($error != ""): ?>
        <div style="background-color: #c62828; color: white; padding: 12px; border-radius: 6px; margin-bottom: 16px; text-align: center; font-weight: 600;">
            <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($exito != ""): ?>
        <div style="background-color: #2e7d32; color: white; padding: 12px; border-radius: 6px; margin-bottom: 16px; text-align: center; font-weight: 600;">
            <?= $exito ?>
            <br><br>
            <a href="login.php" style="color: white; text-decoration: underline;">Ir al login</a>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="on">

        <fieldset>

          <legend>Registro de Usuario</legend>

          <label for="usuario">Usuario</label>
          <input type="text" name="usuario" id="usuario" placeholder="Usuario" minlength="3" maxlength="15" autocomplete="username" required value="<?= isset($usuario) ? htmlspecialchars($usuario) : '' ?>">

          <label for="email">Correo Electrónico</label>
          <input type="email" name="email" id="email" placeholder="Correo Electrónico" autocomplete="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

          <label for="password">Contraseña</label>
          <input type="password" name="password" id="password" placeholder="Contraseña" minlength="8" autocomplete="new-password" required>

          <label for="confirmarcontrasena">Confirmar Contraseña</label>
          <input type="password" name="confirmarcontrasena" id="confirmarcontrasena" placeholder="Confirmar Contraseña" minlength="8" autocomplete="new-password" required>

          <label for="fechanacimiento">Fecha de Nacimiento</label>
          <input type="date" name="fechanacimiento" id="fechanacimiento">

        </fieldset>

        <div class="botones">

          <button type="submit">Registrarse</button>
          <button type="reset">Borrar</button>

        </div>

        <p class="cuenta">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>

      </form>
    </div>
  </div>
</main>
</body>
</html>