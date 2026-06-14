/**
 * VALIDACIONES.JS - VALIDACIONES DEL LADO DEL CLIENTE
 * 
 * Funcionalidad:
 * - Validaciones para registro de usuarios
 * - Validaciones para login
 * - Validaciones para agendamiento de citas
 * - Validaciones de formato (email, cédula, teléfono, fecha)
 * - Mensajes de error personalizados
 * - Validaciones robustas con expresiones regulares
 * 
 * Autor: PET HOSPITAL AND RESCUE Development Team XD
 * 
 * Nota: Estas validaciones son complementarias a las validaciones del servidor.
 *       SIEMPRE se debe validar en el servidor también.
 */

/* =========================
   EXPRESIONES REGULARES
========================= */
const REGEX = {
    EMAIL: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    CEDULA: /^\d{11}$/,
    TELEFONO: /^\d{10}$/,
    NOMBRE: /^[a-zA-Záéíóúñ\s]{3,100}$/,
    PASSWORD_MIN: /^.{6,}$/
};

/* =========================
   FUNCIONES BASE DE VALIDACIÓN
========================= */

/**
 * Valida si un email tiene formato correcto
 * @param {string} email - Email a validar
 * @returns {boolean} - True si es válido
 */
function esEmailValido(email) {
    return REGEX.EMAIL.test(email);
}

/**
 * Valida si una cédula dominicana tiene formato correcto
 * @param {string} cedula - Cédula a validar
 * @returns {boolean} - True si es válida
 */
function esCedulaValida(cedula) {
    // Remover guiones y espacios
    const cedulaLimpia = cedula.replace(/[.-\s]/g, '');
    // Debe ser numérica y tener entre 9 y 11 dígitos
    return REGEX.CEDULA.test(cedulaLimpia);
}

/**
 * Valida si un teléfono tiene formato correcto
 * @param {string} telefono - Teléfono a validar
 * @returns {boolean} - True si es válido
 */
function esTelefonoValido(telefono) {
    // Remover caracteres especiales
    const telefonoLimpio = telefono.replace(/[.\-\s()]/g, '');
    // Debe ser numérico y tener entre 7 y 15 dígitos
    return REGEX.TELEFONO.test(telefonoLimpio);
}

/**
 * Valida si un nombre tiene formato correcto
 * @param {string} nombre - Nombre a validar
 * @returns {boolean} - True si es válido
 */
function esNombreValido(nombre) {
    return nombre.trim().length >= 3 && nombre.trim().length <= 100;
}

/**
 * Valida si una contraseña es fuerte
 * @param {string} password - Contraseña a validar
 * @returns {object} - Objeto con resultado y mensaje
 */
function esContraseñaFuerte(password) {
    const resultado = {
        valida: true,
        fortaleza: 'débil',
        mensajes: []
    };

    if (password.length < 6) {
        resultado.valida = false;
        resultado.mensajes.push("Mínimo 6 caracteres");
    }

    if (password.length < 8) {
        resultado.fortaleza = 'débil';
    } else if (password.length < 12) {
        resultado.fortaleza = 'media';
    } else {
        resultado.fortaleza = 'fuerte';
    }

    if (!/[A-Z]/.test(password)) {
        resultado.mensajes.push("Incluye mayúsculas");
    }

    if (!/[0-9]/.test(password)) {
        resultado.mensajes.push("Incluye números");
    }

    if (!/[!@#$%^&*]/.test(password)) {
        resultado.mensajes.push("Incluye caracteres especiales");
    }

    return resultado;
}

/**
 * Valida si una fecha es válida y no está en el pasado
 * @param {string} fecha - Fecha en formato YYYY-MM-DD
 * @returns {object} - Objeto con resultado y mensaje
 */
function esFechaValida(fecha) {
    const resultado = {
        valida: true,
        mensaje: ""
    };

    const fechaObj = new Date(fecha);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    if (isNaN(fechaObj.getTime())) {
        resultado.valida = false;
        resultado.mensaje = "Fecha inválida";
        return resultado;
    }

    if (fechaObj < hoy) {
        resultado.valida = false;
        resultado.mensaje = "No se puede agendar una fecha pasada";
        return resultado;
    }

    // Validar que no sea más de 6 meses en el futuro
    const fechaMax = new Date();
    fechaMax.setMonth(fechaMax.getMonth() + 6);

    if (fechaObj > fechaMax) {
        resultado.valida = false;
        resultado.mensaje = "La fecha debe estar dentro de los próximos 6 meses";
        return resultado;
    }

    return resultado;
}

/**
 * Valida si una hora es válida
 * @param {string} hora - Hora en formato HH:MM
 * @returns {boolean} - True si es válida
 */
function esHoraValida(hora) {
    const regex = /^([0-1][0-9]|2[0-3]):[0-5][0-9]$/;
    return regex.test(hora);
}

/**
 * Mostrar error visual en un campo
 * @param {HTMLElement} campo - Campo del formulario
 * @param {string} mensaje - Mensaje de error
 */
function mostrarErrorCampo(campo, mensaje) {
    if (!campo) return;
    
    // Remover error anterior si existe
    const errorExistente = campo.parentElement.querySelector('.error-mensaje');
    if (errorExistente) {
        errorExistente.remove();
    }

    // Crear elemento de error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-mensaje';
    errorDiv.textContent = mensaje;
    errorDiv.style.cssText = `
        color: #dc3545;
        font-size: 12px;
        margin-top: 4px;
        display: block;
    `;

    // Agregar borde rojo al campo
    campo.style.borderColor = '#dc3545';

    // Insertar mensaje de error
    campo.parentElement.appendChild(errorDiv);
}

/**
 * Limpiar error visual de un campo
 * @param {HTMLElement} campo - Campo del formulario
 */
function limpiarErrorCampo(campo) {
    if (!campo) return;
    
    const errorExistente = campo.parentElement.querySelector('.error-mensaje');
    if (errorExistente) {
        errorExistente.remove();
    }

    // Restaurar color del borde
    campo.style.borderColor = '';
}

/* =========================
   VALIDACIÓN DE FORMULARIO DE REGISTRO
========================= */

/**
 * Valida el formulario de registro completo
 * @returns {boolean} - True si es válido, false si no
 */
function validarRegistro() {
    let esValido = true;

    // Obtener campos
    const nombre = document.querySelector("[name='nombre']");
    const cedula = document.querySelector("[name='cedula']");
    const telefono = document.querySelector("[name='telefono']");
    const correo = document.querySelector("[name='correo']");
    const password = document.querySelector("[name='password']");
    const seguro = document.querySelector("[name='seguro']");

    // Validar nombre
    if (!nombre || !nombre.value.trim()) {
        mostrarErrorCampo(nombre, "El nombre es obligatorio");
        esValido = false;
    } else if (!esNombreValido(nombre.value)) {
        mostrarErrorCampo(nombre, "El nombre debe tener entre 3 y 100 caracteres");
        esValido = false;
    } else {
        limpiarErrorCampo(nombre);
    }

    // Validar cédula
    if (!cedula || !cedula.value.trim()) {
        mostrarErrorCampo(cedula, "La cédula es obligatoria");
        esValido = false;
    } else if (!esCedulaValida(cedula.value)) {
        mostrarErrorCampo(cedula, "Cédula inválida (debe tener exactamente 11 dígitos)");
        esValido = false;
    } else {
        limpiarErrorCampo(cedula);
    }

    // Validar teléfono
    if (!telefono || !telefono.value.trim()) {
        mostrarErrorCampo(telefono, "El teléfono es obligatorio");
        esValido = false;
    } else if (!esTelefonoValido(telefono.value)) {
        mostrarErrorCampo(telefono, "Teléfono inválido (debe tener 10 dígitos)");
        esValido = false;
    } else {
        limpiarErrorCampo(telefono);
    }

    // Validar correo
    if (!correo || !correo.value.trim()) {
        mostrarErrorCampo(correo, "El correo es obligatorio");
        esValido = false;
    } else if (!esEmailValido(correo.value)) {
        mostrarErrorCampo(correo, "Correo inválido");
        esValido = false;
    } else {
        limpiarErrorCampo(correo);
    }

    // Validar contraseña
    if (!password || !password.value) {
        mostrarErrorCampo(password, "La contraseña es obligatoria");
        esValido = false;
    } else {
        const validacion = esContraseñaFuerte(password.value);
        if (!validacion.valida) {
            mostrarErrorCampo(password, validacion.mensajes.join(", "));
            esValido = false;
        } else {
            limpiarErrorCampo(password);
        }
    }

    // Validar seguro
    if (!seguro || !seguro.value) {
        mostrarErrorCampo(seguro, "Debes indicar si tienes seguro veterinario");
        esValido = false;
    } else {
        limpiarErrorCampo(seguro);

        // Si tiene seguro, validar que haya seleccionado uno
        if (seguro.value === "si") {
            const nombreSeguro = document.querySelector("[name='nombre_seguro']");
            if (!nombreSeguro || !nombreSeguro.value) {
                mostrarErrorCampo(nombreSeguro, "Selecciona tu seguro veterinario");
                esValido = false;
            } else {
                limpiarErrorCampo(nombreSeguro);
            }
        }
    }

    return esValido;
}

/* =========================
   VALIDACIÓN DE FORMULARIO DE LOGIN
========================= */

/**
 * Valida el formulario de login
 * @returns {boolean} - True si es válido, false si no
 */
function validarLogin() {
    let esValido = true;

    const correo = document.querySelector("[name='correo']");
    const password = document.querySelector("[name='password']");

    // Validar correo
    if (!correo || !correo.value.trim()) {
        mostrarErrorCampo(correo, "El correo es obligatorio");
        esValido = false;
    } else if (!esEmailValido(correo.value)) {
        mostrarErrorCampo(correo, "Correo inválido");
        esValido = false;
    } else {
        limpiarErrorCampo(correo);
    }

    // Validar contraseña
    if (!password || !password.value) {
        mostrarErrorCampo(password, "La contraseña es obligatoria");
        esValido = false;
    } else if (password.value.length < 6) {
        mostrarErrorCampo(password, "Contraseña incorrecta");
        esValido = false;
    } else {
        limpiarErrorCampo(password);
    }

    return esValido;
}

/* =========================
   VALIDACIÓN DE AGENDAMIENTO DE CITA
========================= */

/**
 * Valida el formulario de agendamiento de cita
 * @returns {boolean} - True si es válido, false si no
 */
function validarAgendamiento() {
    let esValido = true;

    const especialidad = document.querySelector("[name='especialidad']");
    const veterinario = document.querySelector("[name='veterinario']");
    const fecha = document.querySelector("[name='fecha']");
    const hora = document.querySelector("[name='hora']");

    // Validar especialidad
    if (!especialidad || !especialidad.value) {
        mostrarErrorCampo(especialidad, "Selecciona una especialidad");
        esValido = false;
    } else {
        limpiarErrorCampo(especialidad);
    }

    // Validar veterinario
    if (!veterinario || !veterinario.value) {
        mostrarErrorCampo(veterinario, "Selecciona un veterinario");
        esValido = false;
    } else {
        limpiarErrorCampo(veterinario);
    }

    // Validar fecha
    if (!fecha || !fecha.value) {
        mostrarErrorCampo(fecha, "La fecha es obligatoria");
        esValido = false;
    } else {
        const validacionFecha = esFechaValida(fecha.value);
        if (!validacionFecha.valida) {
            mostrarErrorCampo(fecha, validacionFecha.mensaje);
            esValido = false;
        } else {
            limpiarErrorCampo(fecha);
        }
    }

    // Validar hora
    if (!hora || !hora.value) {
        mostrarErrorCampo(hora, "La hora es obligatoria");
        esValido = false;
    } else if (!esHoraValida(hora.value)) {
        mostrarErrorCampo(hora, "Hora inválida");
        esValido = false;
    } else {
        limpiarErrorCampo(hora);
    }

    return esValido;
}

/* =========================
   INICIALIZACIÓN DE VALIDACIONES EN TIEMPO REAL
========================= */

/**
 * Inicializa validaciones en tiempo real para un formulario
 */
document.addEventListener('DOMContentLoaded', function() {
    // Validar email en tiempo real
    const emailInputs = document.querySelectorAll("input[type='email']");
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !esEmailValido(this.value)) {
                mostrarErrorCampo(this, "Correo inválido");
            } else if (this.value) {
                limpiarErrorCampo(this);
            }
        });
    });

    // Validar cédula en tiempo real
    const cedulaInputs = document.querySelectorAll("input[name='cedula']");
    cedulaInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !esCedulaValida(this.value)) {
                mostrarErrorCampo(this, "Cédula inválida");
            } else if (this.value) {
                limpiarErrorCampo(this);
            }
        });
    });

    // Validar teléfono en tiempo real
    const telefonoInputs = document.querySelectorAll("input[name='telefono']");
    telefonoInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !esTelefonoValido(this.value)) {
                mostrarErrorCampo(this, "Teléfono inválido");
            } else if (this.value) {
                limpiarErrorCampo(this);
            }
        });
    });

    // Validar fecha en tiempo real
    const fechaInputs = document.querySelectorAll("input[name='fecha']");
    fechaInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value) {
                const validacion = esFechaValida(this.value);
                if (!validacion.valida) {
                    mostrarErrorCampo(this, validacion.mensaje);
                } else {
                    limpiarErrorCampo(this);
                }
            }
        });
    });
});
