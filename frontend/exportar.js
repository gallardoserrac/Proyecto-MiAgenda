document.addEventListener('DOMContentLoaded', function() {
  const btnExportar = document.querySelector('.exportar button[data-i18n="exportar"]');
  
  if (btnExportar) {
    btnExportar.addEventListener('click', function(e) {
      e.preventDefault();
      
      const accion = prompt("¿Qué formato deseas exportar?\n\n1 = CSV\n2 = PDF (HTML)\n\nEscribe 1 o 2:");
      
      if (accion === '1') {
        window.open('../backend/exportar/exportar_csv.php', '_blank');
      } else if (accion === '2') {
        window.open('../backend/exportar/exportar_pdf.php', '_blank');
      } else if (accion !== null) {
        alert("Opción no válida. Por favor escribe 1 o 2.");
      }
    });
  }
});
