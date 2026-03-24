let idiomaActual = 'es';
let traducciones = {};

async function cargarIdioma(lang) {
  try {
    const response = await fetch('lang/' + lang + '.json');
    traducciones = await response.json();
    aplicarTraducciones();
  } catch (e) {
    console.error('Error cargando idioma:', e);
  }
}

function aplicarTraducciones() {
  document.querySelectorAll('[data-i18n]').forEach(function(el) {
    const key = el.getAttribute('data-i18n');
    if (traducciones[key]) {
      el.textContent = traducciones[key];
    }
  });
}

function cambiarIdioma(lang) {
  idiomaActual = lang;
  localStorage.setItem('idioma', lang);
  cargarIdioma(lang);
}

document.addEventListener('DOMContentLoaded', function() {
  const idiomaGuardado = localStorage.getItem('idioma') || 'es';
  cargarIdioma(idiomaGuardado);
  
  const selector = document.getElementById('selector-idioma');
  if (selector) {
    selector.addEventListener('change', function(e) {
      cambiarIdioma(e.target.value);
    });
  }
});
