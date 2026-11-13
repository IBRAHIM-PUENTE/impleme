
document.getElementById("loginForm").addEventListener("submit", function(e) {

    e.preventDefault();

    const usuario = document.getElementById("usuario").value.trim();
    const password = document.getElementById("password").value;

    if (usuario === "" || password === "") {
        alert("Por favor, completa todos los campos.");
        return;
    }

    // Simulación visual de acceso exitoso.
    // Los datos no se envían ni se almacenan.

    alert("¡Bienvenido a Athletic Fitness!\nAcceso de demostración exitoso.");

    window.location.href = "dashboard.php";

});
