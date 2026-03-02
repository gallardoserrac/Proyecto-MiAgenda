
const calendarioGrid = document.getElementById("calendario-grid");
const tituloCalendario = document.getElementById("titulo-calendario");
const prev = document.getElementById("prev");
const next = document.getElementById("next");

const fechaMinima = new Date(2026, 0, 1);
let fechaActual = new Date(2026, 0, 1);

let tareas = JSON.parse(localStorage.getItem("tareas")) || {};

const diasSemana = [
  "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"
];

function keyFecha(a, m, d) {
  return `${a}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
}

function guardar() {
  localStorage.setItem("tareas", JSON.stringify(tareas));
}

function crearTarea(fechaKey) {
  let texto = prompt("Texto:");
  if (!texto) return;

  let hora = prompt("Hora (HH:MM)", "09:00");
  let prioridad = prompt("Prioridad: alta / media / baja", "media");

  if (!tareas[fechaKey]) tareas[fechaKey] = [];

  tareas[fechaKey].push({
    texto,
    hora,
    prioridad
  });

  guardar();
  render();
}

function render() {
  calendarioGrid.innerHTML = "";

  let año = fechaActual.getFullYear();
  let mes = fechaActual.getMonth();

  tituloCalendario.textContent = fechaActual
    .toLocaleDateString("es-ES", { month: "long", year: "numeric" })
    .toUpperCase();

  diasSemana.forEach(d => {
    const div = document.createElement("div");
    div.className = "semana";
    div.textContent = d;
    calendarioGrid.appendChild(div);
  });

  let primerDia = new Date(año, mes, 1);
  let offset = (primerDia.getDay() + 6) % 7;

  for (let i = 0; i < offset; i++) {
    calendarioGrid.appendChild(document.createElement("div"));
  }

  let total = new Date(año, mes + 1, 0).getDate();

  for (let dia = 1; dia <= total; dia++) {
    const fechaKey = keyFecha(año, mes, dia);

    const div = document.createElement("div");
    div.className = "dia";

    div.ondragover = e => {
      e.preventDefault();
      div.classList.add("dragover");
    };

    div.ondragleave = () => div.classList.remove("dragover");

    div.ondrop = e => {
      const data = JSON.parse(e.dataTransfer.getData("text"));

      tareas[data.from].splice(data.index, 1);

      if (!tareas[fechaKey]) tareas[fechaKey] = [];
      tareas[fechaKey].push(data.task);

      guardar();
      render();
    };

    const top = document.createElement("div");
    top.className = "top";

    const num = document.createElement("b");
    num.textContent = dia;

    const btn = document.createElement("button");
    btn.textContent = "+";
    btn.className = "addBtn";
    btn.onclick = () => crearTarea(fechaKey);

    top.appendChild(num);
    top.appendChild(btn);

    div.appendChild(top);

    (tareas[fechaKey] || [])
      .sort((a, b) => a.hora.localeCompare(b.hora))
      .forEach((t, i) => {

        const tareaDiv = document.createElement("div");
        tareaDiv.className = "tarea " + t.prioridad;
        tareaDiv.draggable = true;

        tareaDiv.ondragstart = e => {
          e.dataTransfer.setData("text", JSON.stringify({
            from: fechaKey,
            index: i,
            task: t
          }));
        };

        const deleteBtn = document.createElement("span");
        deleteBtn.textContent = "✕";
        deleteBtn.style.cursor = "pointer";
        deleteBtn.style.fontWeight = "bold";
        deleteBtn.style.marginLeft = "auto";
        
        deleteBtn.onclick = (e) => {
          e.stopPropagation();
          tareas[fechaKey].splice(i, 1);
          guardar();
          render();
        };

        const hora = document.createElement("span");
        hora.className = "hora";
        hora.textContent = t.hora;

        const texto = document.createElement("span");
        texto.textContent = t.texto;

        texto.onclick = (e) => {
          e.stopPropagation();
          t.texto = prompt("Editar:", t.texto) || t.texto;
          t.hora = prompt("Hora:", t.hora) || t.hora;
          t.prioridad = prompt("Prioridad:", t.prioridad) || t.prioridad;
          guardar();
          render();
        };

        tareaDiv.append(hora, texto, deleteBtn);

        div.appendChild(tareaDiv);
      });

    calendarioGrid.appendChild(div);
  }

  prev.disabled = fechaActual <= fechaMinima;
}

prev.onclick = () => {
  let test = new Date(fechaActual);
  test.setMonth(test.getMonth() - 1);
  if (test >= fechaMinima) {
    fechaActual = test;
    render();
  }
};

next.onclick = () => {
  fechaActual.setMonth(fechaActual.getMonth() + 1);
  render();
};

render();

console.log('Calendario cargado correctamente');