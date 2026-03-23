<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: formularios/inicio-sesion.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="../img/favicon-32x32.png" type="image/png">
  <title>MiAgenda</title>
</head>
<body>

  <header class="hero-header" id="inicio">

    <nav class="navbar">

      <a href="#inicio" class="logo-link">
        <img src="../img/android-chrome-192x192.png" alt="Logo MiAgenda">
      </a>

      <div class="nav-links">
        <a href="#inicio">Inicio</a>
        <a href="#calendario">Calendario</a>
        <a href="#actividades">Actividades</a>
        <a href="#exportar">Exportar</a>
        <a href="#logros">Logros</a>
        <a href="../backend/autentificacion/logout.php" style="color: #dc3545;">Cerrar Sesión (<?php echo htmlspecialchars($_SESSION["usuario"]); ?>)</a>
      </div>

    </nav>

    <div class="hero-text">
      <p>Organiza tus</p>
      <p>Actividades</p>
      <p>Escolares</p>
    </div>

  </header>

  <section class="estadisticas" id="logros">
    <div>
      <p class="tarjeta-estadisticas">Tareas Pendientes</p>
    </div>
    <div>
      <p class="tarjeta-estadisticas">Completadas Esta Semana</p>
    </div>
    <div>
      <p class="tarjeta-estadisticas">Próximos Exámenes</p>
    </div>
    <div>
      <p class="tarjeta-estadisticas">Promedio Tareas Completadas</p>
    </div>
  </section>

  <section class="calendario" id="calendario">
    <h2>Calendario Interactivo</h2>
    <p>Consulta tus tareas y actividades de manera sencilla.</p>
    
    <div class="header-calendario">
      <button id="prev">◀</button>
      <h2 id="titulo-calendario"></h2>
      <button id="next">▶</button>
    </div>
    
    <div id="calendario-grid"></div>
  </section>

  <section class="actividades" id="actividades">
    <h2>Tarjetas de Actividades</h2>
    <p>Crea y organiza tus actividades escolares con tarjetas fáciles de gestionar.</p>
  </section>

  <section class="exportar" id="exportar">
    <h2>Exportar PDF / CSV</h2>
    <button>Exportar</button>

    <div class="formato-pdf">
      <h3>Formato PDF</h3>
      <p>Exporta tus actividades en un documento PDF con tablas organizadas y fácil de imprimir.</p>
      <p>Formato profesional, listo para imprimir, incluye todos los detalles.</p>
    </div>

    <div class="formato-csv">
      <h3>Formato CSV</h3>
      <p>Descarga tus datos en formato CSV para importar en Excel, Google Sheets u otras aplicaciones.</p>
      <p>Compatibilidad con Excel, fácil de editar, datos estructurados.</p>
    </div>
  </section>

  <footer>
    <a href="#inicio" class="logo-link">
      <img src="../img/android-chrome-192x192.png" alt="Logo MiAgenda">
    </a>
  </footer>

  <script src="calendario.js"></script>

<script>
async function cargarEstadisticas() {
  try {
    const r = await fetch("../backend/estadisticas.php", { credentials: "same-origin" });
    const d = await r.json();
    if (d.pendientes) document.querySelectorAll(".tarjeta-estadisticas")[0].textContent = "Tareas Pendientes: " + d.pendientes;
    if (d.completadas !== undefined) document.querySelectorAll(".tarjeta-estadisticas")[1].textContent = "Completadas Esta Semana: " + d.completadas;
    if (d.examenes !== undefined) document.querySelectorAll(".tarjeta-estadisticas")[2].textContent = "Próximos Exámenes: " + d.examenes;
    if (d.promedio !== undefined) document.querySelectorAll(".tarjeta-estadisticas")[3].textContent = "Promedio Completadas: " + d.promedio + "%";
  } catch(e) { console.error("Error estadísticas:", e); }
}
cargarEstadisticas();
</script>
<script>
async function cargarEstadisticas(){try{const r=await fetch("../backend/estadisticas.php",{credentials:"same-origin"});const d=await r.json();if(d.pendientes)document.querySelectorAll(".tarjeta-estadisticas")[0].textContent="Tareas Pendientes: "+d.pendientes;if(d.completadas!==undefined)document.querySelectorAll(".tarjeta-estadisticas")[1].textContent="Completadas Esta Semana: "+d.completadas;if(d.examenes!==undefined)document.querySelectorAll(".tarjeta-estadisticas")[2].textContent="Próximos Exámenes: "+d.examenes;if(d.promedio!==undefined)document.querySelectorAll(".tarjeta-estadisticas")[3].textContent="Promedio Completadas: "+d.promedio+"%";}catch(e){console.error("Error estadísticas:",e);}}
cargarEstadisticas();
</script>
</body>
