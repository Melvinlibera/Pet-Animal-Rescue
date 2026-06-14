/**
 * MAIN.JS - LÓGICA PRINCIPAL DEL SITIO
 * 
 * Funcionalidad:
 * - Manejo dinámico del header con efecto de scroll
 * - Logo se desplaza de centro a izquierda al hacer scroll
 * - Cambio de estilos del header según la posición del scroll
 * - Optimización de performance con throttling
 * 
 * Autor: PET HOSPITAL AND RESCUE Development Team XD
 * 
 */

/* =========================
   VARIABLES GLOBALES
========================= */
let lastScroll = 0;
let ticking = false;
const SCROLL_THRESHOLD = 80;  // Umbral para cambiar estilos
const HIDE_THRESHOLD = 150;   // Umbral para ocultar header

/* =========================
   FUNCIÓN DE THROTTLE
   Optimiza el rendimiento limitando la frecuencia de ejecución
========================= */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

/* =========================
   FUNCIÓN PRINCIPAL DE SCROLL
   Maneja todos los efectos del header al hacer scroll
========================= */
function handleScroll() {
    const header = document.getElementById("header");
    const currentScroll = window.scrollY;

    if (!header) return; // Validación de seguridad

    /* =========================
       EFECTO 1: CAMBIO DE ESTILO DEL HEADER
       Al pasar el umbral, el header cambia de fondo transparente a azul marino
    ========================= */
    if (currentScroll > SCROLL_THRESHOLD) {
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }

    /* =========================
       EFECTO 2: MOSTRAR/OCULTAR HEADER
       Al hacer scroll hacia abajo, el header se oculta
       Al hacer scroll hacia arriba, el header se muestra
    ========================= */
    if (currentScroll > HIDE_THRESHOLD) {
        if (currentScroll > lastScroll) {
            // Scroll hacia abajo → ocultar header
            header.style.transform = "translateY(-100%)";
        } else {
            // Scroll hacia arriba → mostrar header
            header.style.transform = "translateY(0)";
        }
    } else {
        // En la parte superior, siempre mostrar el header
        header.style.transform = "translateY(0)";
    }

    lastScroll = currentScroll;
    ticking = false;
}

/* =========================
   EVENT LISTENER DE SCROLL
   Utiliza requestAnimationFrame para mejor performance
========================= */
window.addEventListener("scroll", () => {
    if (!ticking) {
        window.requestAnimationFrame(handleScroll);
        ticking = true;
    }
}, { passive: true }); // passive: true mejora el rendimiento

/* =========================
   INICIALIZACIÓN AL CARGAR LA PÁGINA
========================= */
document.addEventListener("DOMContentLoaded", function() {
    // Llamar a handleScroll una vez al cargar para aplicar estilos iniciales
    handleScroll();

    // Agregar efecto de suavidad a los enlaces internos
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // El manejo del tema está centralizado en floating_theme_toggle.php
    // No duplicar funcionalidad aquí

    // CSRF protection: inyectar token en todos los formularios y en axios headers
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : null;
    if (csrf) {
        // Axios header global
        if (window.axios) {
            window.axios.defaults.headers.common['X-CSRF-Token'] = csrf;
        }

        document.querySelectorAll('form').forEach(form => {
            if (!form.querySelector('input[name="csrf_token"]')) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'csrf_token';
                input.value = csrf;
                form.appendChild(input);
            }
        });
    }
});

/* =========================
   FUNCIONES DE UTILIDAD
========================= */

/**
 * Función para mostrar notificaciones
 * @param {string} mensaje - El mensaje a mostrar
 * @param {string} tipo - Tipo de notificación: 'success', 'error', 'warning', 'info'
 * @param {number} duracion - Duración en milisegundos (por defecto 3000)
 */
function mostrarNotificacion(mensaje, tipo = 'info', duracion = 3000) {
    // Usar el sistema de notificaciones centralizado de Anime.js
    if (typeof HNHAnimations !== 'undefined' && HNHAnimations.showNotification) {
        HNHAnimations.showNotification(mensaje, tipo, duracion);
    }
}

/**
 * Función para validar email
 * @param {string} email - Email a validar
 * @returns {boolean} - True si es válido, false si no
 */
function esEmailValido(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Función para validar cédula dominicana
 * @param {string} cedula - Cédula a validar
 * @returns {boolean} - True si es válida, false si no
 */
function esCedulaValida(cedula) {
    // Validación básica: debe tener exactamente 11 dígitos
    const regex = /^\d{11}$/;
    return regex.test(cedula.replace(/[.-]/g, ''));
}

/**
 * Función para validar teléfono dominicano
 * @param {string} telefono - Teléfono a validar
 * @returns {boolean} - True si es válido, false si no
 */
function esTelefonoValido(telefono) {
    // Validación básica: debe tener entre 7 y 15 dígitos
    const regex = /^\d{7,15}$/;
    return regex.test(telefono.replace(/[.\-\s()]/g, ''));
}

/**
 * Función para formatear moneda
 * @param {number} cantidad - Cantidad a formatear
 * @returns {string} - Cantidad formateada
 */
function formatearMoneda(cantidad) {
    return new Intl.NumberFormat('es-DO', {
        style: 'currency',
        currency: 'DOP'
    }).format(cantidad);
}

/* =========================
   ANIMACIONES CSS DINÁMICAS
========================= */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

/* =========================
   EXPORTAR FUNCIONES
   Para uso en otros scripts
========================= */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        mostrarNotificacion,
        esEmailValido,
        esCedulaValida,
        esTelefonoValido,
        formatearMoneda
    };
}
