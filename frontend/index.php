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
  <style>
    .kanban{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:10px 16px 16px;max-width:1200px;margin:0 auto}
    .col{background:#fff;border:1px solid #e7e7ee;border-radius:12px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,.06)}
    .col h3{margin:0;padding:12px 12px;border-bottom:1px solid #eee;font-size:14px;letter-spacing:.2px}
    .top{display:flex;gap:8px;align-items:center;justify-content:space-between;padding:10px 12px;border-bottom:1px solid #eee}
    .top button{border:1px solid #e3e3ee;background:#111;color:#fff;border-radius:10px;padding:8px 10px;font-size:12px;cursor:pointer}
    .top button:active{transform:translateY(1px)}
    .zona{padding:12px;min-height:360px}
    .dragover{outline:2px dashed #b9b9c9;outline-offset:-6px;background:#fafaff}
    .card{border:1px solid #e9e9f2;border-radius:12px;margin:0 0 10px;box-shadow:0 3px 10px rgba(0,0,0,.05);cursor:grab;overflow:hidden}
    .card:active{cursor:grabbing}
    .cardTop{display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-bottom:1px solid rgba(0,0,0,.06);background:rgba(255,255,255,.65)}
    .dots{display:flex;gap:7px;align-items:center}
    .dotBtn{width:12px;height:12px;border-radius:999px;border:1px solid rgba(0,0,0,.14);cursor:pointer;padding:0;display:inline-block}
    .dotClose{background:#ff5f57}
    .dotColor{background:#febc2e}
    .titleMini{font-size:12px;color:#333;opacity:.9;user-select:none}
    .content{padding:10px}
    .txt{min-height:22px;outline:none}
    .picker{position:absolute;left:-9999px;top:-9999px}
  </style>
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
        <a href="#kanban">Kanban</a>
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

  <section class="kanban-section" id="kanban">
    <h2>Kanban</h2>
    <p>Organiza tus tareas visualmente arrastrando y soltando tarjetas.</p>
    <div class="kanban">
      <div class="col" data-col="pendiente"><h3>Por hacer</h3><div class="top"><span></span><button class="add">+ Añadir</button></div><div class="zona" id="suelta-1"></div></div>
      <div class="col" data-col="en_progreso"><h3>En curso</h3><div class="top"><span></span><button class="add">+ Añadir</button></div><div class="zona" id="suelta-2"></div></div>
      <div class="col" data-col="en_revision"><h3>En revisión</h3><div class="top"><span></span><button class="add">+ Añadir</button></div><div class="zona" id="suelta-3"></div></div>
      <div class="col" data-col="completado"><h3>Hecho</h3><div class="top"><span></span><button class="add">+ Añadir</button></div><div class="zona" id="suelta-4"></div></div>
    </div>
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
  <script src="kanban.js"></script>

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
