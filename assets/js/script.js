/* =========================
   SCRIPT PRINCIPAL DEL SISTEMA
   Maneja:
   - Animaciones de scroll
   - Header dinámico
   - Aparición de elementos
   - Validaciones frontend
========================= */

/* =========================
   HEADER SCROLL (LOGO DINÁMICO)
========================= */
window.addEventListener("scroll", () => {
    const header = document.getElementById("header");

    if (window.scrollY > 50) {
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }
});

/* =========================
   ANIMACIÓN AL HACER SCROLL
   (ELEMENTOS APARECEN SUAVE)
========================= */
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
        }
    });
}, {
    threshold: 0.2
});

/* Selecciona elementos que se quiere animar */
document.querySelectorAll(".container, .cards a, .section h2").forEach(el => {
    el.style.opacity = "0";
    el.style.transform = "translateY(30px)";
    el.style.transition = "all 0.6s ease";

    observer.observe(el);
});

/* =========================
   SCROLL SUAVE EN LINKS
========================= */
document.querySelectorAll("a[href^='#']").forEach(anchor => {
    anchor.addEventListener("click", function(e) {
        e.preventDefault();

        const target = document.querySelector(this.getAttribute("href"));

        if (target) {
            target.scrollIntoView({
                behavior: "smooth"
            });
        }
    });
});

/* =========================
   VALIDACIONES DE FORMULARIOS
========================= */

/* Validar registro */
function validarRegistro(form) {
    const nombre = form.nombre.value.trim();
    const email = form.email.value.trim();
    const telefono = form.telefono.value.trim();
    const cedula = form.cedula.value.trim();
    const password = form.password.value;

    if (!nombre || !email || !telefono || !cedula || !password) {
        alert("Todos los campos son obligatorios");
        return false;
    }

    /* Validar email */
    const emailRegex = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(email)) {
        alert("Correo inválido");
        return false;
    }

    /* Validar cédula (formato simple RD) */
    if (cedula.length < 10) {
        alert("Cédula inválida");
        return false;
    }

    /* Validar contraseña */
    if (password.length < 6) {
        alert("La contraseña debe tener al menos 6 caracteres");
        return false;
    }

    return true;
}

/* Validar login */
function validarLogin(form) {
    const email = form.email.value.trim();
    const password = form.password.value;

    if (!email || !password) {
        alert("Debe completar todos los campos");
        return false;
    }

    return true;
}

/* =========================
   MENSAJES VISUALES AUTOMÁTICOS
========================= */
setTimeout(() => {
    document.querySelectorAll(".success, .error").forEach(el => {
        el.style.transition = "opacity 0.5s";
        el.style.opacity = "0";

        setTimeout(() => el.remove(), 500);
    });
}, 4000);

/* =========================
   PREPARADO PARA AJAX (FUTURO)
========================= */

/* Ejemplo base reutilizable */
async function enviarFormulario(url, datos) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : null;

    try {
        const headers = {
            "Content-Type": "application/json"
        };
        if (csrf) {
            headers['X-CSRF-Token'] = csrf;
        }

        const respuesta = await fetch(url, {
            method: "POST",
            headers: headers,
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();
        return resultado;

    } catch (error) {
        console.error("Error:", error);
        alert("Error de conexión");
    }
}