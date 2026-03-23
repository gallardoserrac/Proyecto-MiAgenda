
<?php

session_start();
require_once '../configuracion.php';

$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email == "" || $password == "") {
        $error = "Rellena todos los campos.";
    } else {

        $sql = "
            SELECT id, usuario, email, password, rol, activo
            FROM usuarios
            WHERE email = '$email'
        ";

        $resultado = $conexion->query($sql);

        if ($resultado->num_rows == 1) {

            $fila = $resultado->fetch_assoc();

    
            if (password_verify($password, $fila['password'])) {
                
                if ($fila['activo'] == 1) {

                    $_SESSION['usuario_id'] = $fila['id'];
                    $_SESSION['usuario'] = $fila['usuario'];
                    $_SESSION['email'] = $fila['email'];
                    $_SESSION['rol'] = $fila['rol'];

    
                    if ($fila['rol'] === 'profesor') {
                        header("Location: ../admin/index.php");
                    } else {
                        header("Location: ../../frontend/index.html");
                    }
                    exit;
                    
                } else {
                    $error = "Tu cuenta está desactivada.";
                }
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no existe.";
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
    <link rel="stylesheet" href="inicio-sesion.css">
    <title>MiAgenda - Inicio de Sesión</title>
</head>
<body>

<main>

  <div class="contenedor">

    <div class="izquierda">

      <img src="../../img/android-chrome-192x192.png" alt="Logo MiAgenda">

      <div class="texto-izquierda">

        <h2>¡Bienvenido de nuevo!</h2>

      </div>

    </div>

    <div class="linea-divisora"></div>

    <div class="derecha">

      <?php if ($error != ""): ?>
        <div style="background-color: #c62828; color: white; padding: 12px; border-radius: 6px; margin-bottom: 16px; text-align: center; font-weight: 600;">
            <?= $error ?>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="on">

        <fieldset>

          <legend>Inicio de Sesión</legend>

          <label for="email">Correo electrónico</label>
          <input type="email" name="email" id="email" placeholder="Correo Electrónico" autocomplete="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

          <label for="password">Contraseña</label>
          <input type="password" name="password" id="password" placeholder="Contraseña" minlength="8" autocomplete="current-password" required>

        </fieldset>

        <div class="botones">

          <button type="submit">Iniciar sesión</button>
          <button type="reset">Borrar</button>

        </div>

        <p class="cuenta">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>

      </form>

    </div>
  </div>

</main>

</body>
</html>