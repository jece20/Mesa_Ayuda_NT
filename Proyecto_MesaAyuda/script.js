// Animación de entrada
document.addEventListener('DOMContentLoaded', function() {
    let login = document.querySelector('.login-container');
    if (login) login.classList.add('animacion');
});

// Mostrar formulario de ticket
function mostrarFormulario() {
    document.getElementById('formulario-ticket').style.display = 'block';
}