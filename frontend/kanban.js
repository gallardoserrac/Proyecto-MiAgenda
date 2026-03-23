let tarjetas = {};
let elementoArrastrado = null;

async function cargarKanban() {
  try {
    const r = await fetch("../backend/kanban/obtener_kanban.php", { credentials: "same-origin" });
    const d = await r.json();
    if (d.error) { alert("Error: " + d.error); return; }
    tarjetas = { pendiente: [], en_progreso: [], en_revision: [], completado: [] };
    d.tarjetas.forEach(t => {
      if (tarjetas[t.columna]) tarjetas[t.columna].push(t);
    });
    render();
  } catch (e) { console.error("Error:", e); }
}

async function crearTarjeta(columna) {
  const titulo = prompt("Título de la tarjeta:");
  if (!titulo) return;
  const color = "#ffffff";
  try {
    const r = await fetch("../backend/kanban/crear_tarjeta.php", {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ titulo: titulo, columna: columna, color: color })
    });
    const d = await r.json();
    if (d.success) { cargarKanban(); } else { alert("Error: " + d.error); }
  } catch (e) { alert("Error de conexión: " + e); }
}

async function actualizarTarjeta(id, datos) {
  try {
    const r = await fetch("../backend/kanban/actualizar_tarjeta.php", {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: id, ...datos })
    });
    const d = await r.json();
    if (d.success) { cargarKanban(); }
  } catch (e) { console.error("Error:", e); }
}

async function eliminarTarjeta(id) {
  if (!confirm("¿Eliminar tarjeta?")) return;
  try {
    const r = await fetch("../backend/kanban/eliminar_tarjeta.php", {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: id })
    });
    const d = await r.json();
    if (d.success) { cargarKanban(); }
  } catch (e) { console.error("Error:", e); }
}

function uid() { return Date.now().toString(36) + Math.random().toString(36).slice(2,7); }

function creaTarjetaHTML(t) {
  let card = document.createElement("div");
  card.className = "card";
  card.draggable = true;
  card.setAttribute("data-id", t.id);
  card.setAttribute("data-color", t.color || "#ffffff");
  card.style.background = t.color || "#ffffff";

  let top = document.createElement("div");
  top.className = "cardTop";

  let dots = document.createElement("div");
  dots.className = "dots";

  let btnClose = document.createElement("button");
  btnClose.className = "dotBtn dotClose";
  btnClose.title = "Eliminar";
  btnClose.onclick = (e) => { e.stopPropagation(); eliminarTarjeta(t.id); };

  let btnColor = document.createElement("button");
  btnColor.className = "dotBtn dotColor";
  btnColor.title = "Cambiar color";

  let picker = document.createElement("input");
  picker.type = "color";
  picker.className = "picker";
  picker.value = t.color || "#ffffff";
  picker.oninput = () => { actualizaColor(card, picker.value, t.id); };
  btnColor.onclick = (e) => { e.stopPropagation(); picker.click(); };

  dots.appendChild(btnClose);
  dots.appendChild(btnColor);

  let title = document.createElement("div");
  title.className = "titleMini";
  title.innerText = t.titulo || "tarjeta";

  top.appendChild(dots);
  top.appendChild(title);

  let content = document.createElement("div");
  content.className = "content";

  let txt = document.createElement("div");
  txt.className = "txt";
  txt.contentEditable = "true";
  txt.spellcheck = true;
  txt.innerText = t.titulo || "Nueva tarjeta";
  txt.onblur = () => { actualizaTitulo(t.id, txt.innerText); };

  content.appendChild(txt);
  content.appendChild(picker);

  card.appendChild(top);
  card.appendChild(content);

  card.ondragstart = () => { elementoArrastrado = card; };
  card.ondragend = () => { elementoArrastrado = null; };

  return card;
}

function actualizaColor(card, color, id) {
  card.setAttribute("data-color", color);
  card.style.background = color;
  actualizarTarjeta(id, { color: color });
}

function actualizaTitulo(id, titulo) {
  actualizarTarjeta(id, { titulo: titulo });
}

function render() {
  document.querySelectorAll(".zona").forEach(z => z.innerHTML = "");
  const map = {
    pendiente: document.querySelector('[data-col="pendiente"] .zona'),
    en_progreso: document.querySelector('[data-col="en_progreso"] .zona'),
    en_revision: document.querySelector('[data-col="en_revision"] .zona'),
    completado: document.querySelector('[data-col="completado"] .zona')
  };
  Object.keys(map).forEach(col => {
    (tarjetas[col] || []).forEach(t => {
      if (map[col]) map[col].appendChild(creaTarjetaHTML(t));
    });
  });
  setupDropzones();
}

function setupDropzones() {
  document.querySelectorAll(".zona").forEach(suelta => {
    suelta.ondragover = (e) => { e.preventDefault(); suelta.classList.add("dragover"); };
    suelta.ondragleave = () => { suelta.classList.remove("dragover"); };
    suelta.ondrop = () => {
      suelta.classList.remove("dragover");
      if (elementoArrastrado) {
        suelta.appendChild(elementoArrastrado);
        let id = elementoArrastrado.getAttribute("data-id");
        let col = suelta.closest(".col").getAttribute("data-col");
        actualizarTarjeta(id, { columna: col });
      }
    };
  });
}

document.querySelectorAll(".col .add").forEach(btn => {
  btn.onclick = () => {
    let col = btn.closest(".col");
    let columna = col.getAttribute("data-col");
    crearTarjeta(columna);
  };
});

cargarKanban();
