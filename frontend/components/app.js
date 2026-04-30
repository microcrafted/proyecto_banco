class RegistroController {

    constructor() {
        this.form = document.getElementById("formRegistro");
        this.mensaje = document.getElementById("mensaje");

        this.init();
    }

    init() {
        this.form.addEventListener("submit", (e) => this.procesarFormulario(e));
    }

    procesarFormulario(e) {
        e.preventDefault();

        let datos = this.obtenerDatos();

        if (!this.validarCampos(datos)) {
            this.mostrarMensaje("Todos los campos son obligatorios", false);
            return;
        }

        if (!this.validarEmail(datos.email)) {
            this.mostrarMensaje("Correo inválido", false);
            return;
        }

        if (datos.password.length < 6) {
            this.mostrarMensaje("La contraseña debe tener mínimo 6 caracteres", false);
            return;
        }

        this.enviarDatos(datos);
    }

    obtenerDatos() {
        return {
            nombre: document.getElementById("nombre").value.trim(),
            apellido: document.getElementById("apellido").value.trim(),
            email: document.getElementById("email").value.trim(),
            password: document.getElementById("password").value.trim()
        };
    }

    validarCampos(datos) {
        return Object.values(datos).every(valor => valor !== "");
    }

    validarEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    mostrarMensaje(msg, ok) {
        this.mensaje.textContent = msg;
        this.mensaje.style.color = ok ? "green" : "red";
    }

    enviarDatos(datos) {
        fetch("../../backend/routes/registro.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datos)
        })
        .then(res => res.json())
        .then(data => {
            this.mostrarMensaje(data.mensaje, data.ok);
        })
        .catch(() => {
            this.mostrarMensaje("Error en el servidor", false);
        });
    }
}

new RegistroController();