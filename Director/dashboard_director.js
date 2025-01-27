// Función para mostrar la fecha y hora en tiempo real
function actualizarFechaHora() {
    const fechaHoraElement = document.getElementById('fecha-hora');
    const ahora = new Date();

    const opcionesFecha = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fecha = ahora.toLocaleDateString('es-ES', opcionesFecha);

    const hora = ahora.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });

    fechaHoraElement.textContent = `${fecha} - ${hora}`;
}

// Actualizar la fecha y hora cada segundo
setInterval(actualizarFechaHora, 1000);

// Mostrar la fecha y hora al cargar la página
actualizarFechaHora();