const calendarioGrid = document.getElementById("calendario-grid");
const tituloCalendario = document.getElementById("titulo-calendario");
const prev = document.getElementById("prev");
const next = document.getElementById("next");
const fechaMinima = new Date(2026, 0, 1);
let fechaActual = new Date(2026, 0, 1);
let tareas = {};
const diasSemana = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
function keyFecha(a, m, d) { return `${a}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`; }
async function cargarTareas() {
  const año = fechaActual.getFullYear();
  const mes = fechaActual.getMonth() + 1;
  try {
    const response = await fetch(`../backend/tareas/obtener_tareas.php?mes=${mes}&año=${año}`, { method: "GET", credentials: "same-origin" });
    const data = await response.json();
    if (data.error) { alert("Error: " + data.error); return; }
    tareas = {};
    data.tareas.forEach(t => { const fk = t.fecha; if (!tareas[fk]) tareas[fk] = []; tareas[fk].push({ id: t.id, texto: t.titulo, hora: t.hora || "09:00", prioridad: t.prioridad || "media" }); });
    render();
  } catch (e) { console.error("Error:", e); }
}
async function crearTarea(fechaKey) {
  const titulo = prompt("Título:"); if (!titulo) return;
  const hora = prompt("Hora (HH:MM)", "09:00") || "09:00";
  const prioridad = prompt("Prioridad (alta/media/baja)", "media") || "media";
  try {
    const response = await fetch("../backend/tareas/crear_tarea.php", { method: "POST", credentials: "same-origin", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ titulo: titulo, descripcion: "", fecha: fechaKey, hora: hora, prioridad: prioridad }) });
    const data = await response.json();
    if (data.success) { cargarTareas(); } else { alert("Error: " + (data.error || "No se pudo crear")); }
  } catch (e) { alert("Error de conexión: " + e); }
}
async function eliminarTarea(id) {
  if (!confirm("¿Eliminar?")) return;
  try {
    const response = await fetch("../backend/tareas/eliminar_tarea.php", { method: "POST", credentials: "same-origin", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ id: id }) });
    const data = await response.json();
    if (data.success) { cargarTareas(); }
  } catch (e) { console.error("Error:", e); }
}
async function actualizarTarea(t) {
  const titulo = prompt("Editar título:", t.texto); if (titulo === null) return;
  const hora = prompt("Editar hora:", t.hora) || t.hora;
  const prioridad = prompt("Editar prioridad:", t.prioridad) || t.prioridad;
  try {
    const response = await fetch("../backend/tareas/actualizar_tarea.php", { method: "POST", credentials: "same-origin", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ id: t.id, titulo: titulo, descripcion: "", hora: hora, prioridad: prioridad }) });
    const data = await response.json();
    if (data.success) { cargarTareas(); }
  } catch (e) { console.error("Error:", e); }
}
function render() {
  calendarioGrid.innerHTML = "";
  let año = fechaActual.getFullYear(); let mes = fechaActual.getMonth();
  tituloCalendario.textContent = fechaActual.toLocaleDateString("es-ES", { month: "long", year: "numeric" }).toUpperCase();
  diasSemana.forEach(d => { const div = document.createElement("div"); div.className = "semana"; div.textContent = d; calendarioGrid.appendChild(div); });
  let primerDia = new Date(año, mes, 1); let offset = (primerDia.getDay() + 6) % 7;
  for (let i = 0; i < offset; i++) { calendarioGrid.appendChild(document.createElement("div")); }
  let total = new Date(año, mes + 1, 0).getDate();
  for (let dia = 1; dia <= total; dia++) {
    const fechaKey = keyFecha(año, mes, dia);
    const div = document.createElement("div"); div.className = "dia";
    const top = document.createElement("div"); top.className = "top";
    const num = document.createElement("b"); num.textContent = dia;
    const btn = document.createElement("button"); btn.textContent = "+"; btn.className = "addBtn"; btn.onclick = () => crearTarea(fechaKey);
    top.appendChild(num); top.appendChild(btn); div.appendChild(top);
    (tareas[fechaKey] || []).forEach((t) => {
      const tareaDiv = document.createElement("div"); tareaDiv.className = "tarea " + t.prioridad;
      const deleteBtn = document.createElement("span"); deleteBtn.textContent = "✕"; deleteBtn.style.cursor = "pointer"; deleteBtn.style.marginLeft = "auto";
      deleteBtn.onclick = (e) => { e.stopPropagation(); eliminarTarea(t.id); };
      const horaSpan = document.createElement("span"); horaSpan.className = "hora"; horaSpan.textContent = t.hora;
      const textoSpan = document.createElement("span"); textoSpan.textContent = t.texto;
      textoSpan.onclick = (e) => { e.stopPropagation(); actualizarTarea(t); };
      tareaDiv.append(horaSpan, textoSpan, deleteBtn); div.appendChild(tareaDiv);
    });
    calendarioGrid.appendChild(div);
  }
  prev.disabled = fechaActual <= fechaMinima;
}
prev.onclick = () => { let test = new Date(fechaActual); test.setMonth(test.getMonth() - 1); if (test >= fechaMinima) { fechaActual = test; cargarTareas(); } };
next.onclick = () => { fechaActual.setMonth(fechaActual.getMonth() + 1); cargarTareas(); };
cargarTareas();
