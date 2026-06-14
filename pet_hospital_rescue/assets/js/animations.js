/**
 * SISTEMA CENTRALIZADO DE ANIMACIONES
 * PET HOSPITAL AND RESCUE - Sistema de Rescate y Hospital
 * 
 * Utiliza Anime.js v3.2.2 para todas las animaciones
 * https://animejs.com/
 * 
 * Funcionalidad:
 * - Animaciones de entrada (fade, slide, scale)
 * - Animaciones de hover en tarjetas
 * - Animaciones de scroll
 * - Animaciones de transición de temas
 * - Animaciones suaves y precisas
 */

// ========================================================
// VERIFICAR QUE ANIME.JS ESTÉ DISPONIBLE
// ========================================================
if (typeof anime === 'undefined') {
    console.warn('Anime.js no está cargado. Las animaciones no se ejecutarán.');
}

// ========================================================
// OBJETO GLOBAL DE ANIMACIONES
// ========================================================
const HNHAnimations = {
    // Animaciones activas para evitar conflictos
    activeAnimations: new Map(),

    // ====================================================
    // 1. ANIMACIONES DE TARJETAS
    // ====================================================
    animateCards: function() {
        const cards = document.querySelectorAll('.card');
        if (cards.length === 0) return;

        cards.forEach((card, index) => {
            anime.set(card, {
                opacity: 0,
                translateY: 30,
                scale: 0.95
            });

            anime({
                targets: card,
                opacity: 1,
                translateY: 0,
                scale: 1,
                duration: 600,
                delay: index * 80,
                easing: 'easeOutCubic'
            });

            // Hover animation
            card.addEventListener('mouseenter', () => {
                anime({
                    targets: card,
                    scale: 1.05,
                    translateY: -8,
                    duration: 300,
                    easing: 'easeOutQuad'
                });
            });

            card.addEventListener('mouseleave', () => {
                anime({
                    targets: card,
                    scale: 1,
                    translateY: 0,
                    duration: 300,
                    easing: 'easeOutQuad'
                });
            });
        });
    },

    // ====================================================
    // 2. ANIMACIONES DE BOTONES
    // ====================================================
    animateButtons: function() {
        const buttons = document.querySelectorAll('button, .btn, .btn-nav');
        if (buttons.length === 0) return;

        buttons.forEach(button => {
            button.addEventListener('mousedown', function(e) {
                // Crear ripple effect
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.position = 'absolute';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.width = size + 'px';
                ripple.style.height = size + 'px';
                ripple.style.borderRadius = '50%';
                ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.6)';
                ripple.style.pointerEvents = 'none';

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                anime({
                    targets: ripple,
                    scale: [0, 2],
                    opacity: [1, 0],
                    duration: 600,
                    easing: 'easeOutQuad',
                    complete: () => ripple.remove()
                });
            });
        });
    },

    // ====================================================
    // 3. ANIMACIONES DE INPUTS Y FORMULARIOS
    // ====================================================
    animateFormInputs: function() {
        const inputs = document.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                anime({
                    targets: input,
                    boxShadow: '0 0 0 3px rgba(30, 144, 255, 0.2)',
                    duration: 300,
                    easing: 'easeOutQuad'
                });
            });

            input.addEventListener('blur', () => {
                anime({
                    targets: input,
                    boxShadow: 'none',
                    duration: 300,
                    easing: 'easeOutQuad'
                });
            });
        });
    },

    // ====================================================
    // 4. ANIMACIÓN DE TEMA (TRANSICIÓN SUAVE)
    // ====================================================
    animateThemeChange: function() {
        const body = document.body;
        anime({
            targets: body,
            opacity: [0.95, 1],
            duration: 300,
            easing: 'easeOutQuad'
        });

        // Animar el botón flotante
        const themeToggle = document.getElementById('floatingThemeToggle');
        if (themeToggle) {
            anime({
                targets: themeToggle.querySelector('i'),
                rotate: 360,
                duration: 500,
                easing: 'easeInOutQuad'
            });
        }
    },

    // ====================================================
    // 5. ANIMACIONES DE SCROLL (PARALLAX)
    // ====================================================
    setupScrollAnimations: function() {
        const elements = document.querySelectorAll('[data-scroll-animate]');
        if (elements.length === 0) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.dataset.animated) {
                    entry.target.dataset.animated = true;
                    
                    anime({
                        targets: entry.target,
                        opacity: [0, 1],
                        translateY: [30, 0],
                        duration: 600,
                        easing: 'easeOutCubic'
                    });
                }
            });
        }, { threshold: 0.1 });

        elements.forEach(el => observer.observe(el));
    },

    // ====================================================
    // 6. ANIMACIÓN DE NOTIFICACIONES
    // ====================================================
    showNotification: function(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = '16px 24px';
        notification.style.borderRadius = '8px';
        notification.style.fontSize = '14px';
        notification.style.fontWeight = '600';
        notification.style.zIndex = '10000';
        notification.style.minWidth = '300px';
        
        // Estilos por tipo
        const typeStyles = {
            success: {
                background: '#28a745',
                color: '#ffffff'
            },
            error: {
                background: '#dc3545',
                color: '#ffffff'
            },
            warning: {
                background: '#ffc107',
                color: '#000000'
            },
            info: {
                background: '#17a2b8',
                color: '#ffffff'
            }
        };

        const style = typeStyles[type] || typeStyles.info;
        Object.assign(notification.style, style);

        document.body.appendChild(notification);

        // Animación de entrada
        anime({
            targets: notification,
            opacity: [0, 1],
            translateX: [300, 0],
            duration: 400,
            easing: 'easeOutQuad'
        });

        // Animación de salida después de duration
        setTimeout(() => {
            anime({
                targets: notification,
                opacity: [1, 0],
                translateX: [0, 300],
                duration: 400,
                easing: 'easeInQuad',
                complete: () => notification.remove()
            });
        }, duration);
    },

    // ====================================================
    // 7. ANIMACIÓN DE CARGA
    // ====================================================
    showLoading: function(element = document.body) {
        const loader = document.createElement('div');
        loader.className = 'hnh-loader';
        loader.innerHTML = `
            <div class="spinner"></div>
            <p>Cargando...</p>
        `;
        loader.style.position = 'fixed';
        loader.style.top = '50%';
        loader.style.left = '50%';
        loader.style.transform = 'translate(-50%, -50%)';
        loader.style.zIndex = '9999';
        loader.style.textAlign = 'center';

        document.body.appendChild(loader);

        const spinner = loader.querySelector('.spinner');
        anime({
            targets: spinner,
            rotate: 360,
            duration: 1000,
            loop: true,
            easing: 'linear'
        });

        return loader;
    },

    hideLoading: function(loader) {
        if (loader) {
            anime({
                targets: loader,
                opacity: [1, 0],
                duration: 300,
                easing: 'easeOutQuad',
                complete: () => loader.remove()
            });
        }
    },

    // ====================================================
    // 8. ANIMACIÓN DE TABLA
    // ====================================================
    animateTableRows: function() {
        const rows = document.querySelectorAll('table tbody tr');
        
        rows.forEach((row, index) => {
            anime.set(row, {
                opacity: 0,
                translateX: -20
            });

            anime({
                targets: row,
                opacity: 1,
                translateX: 0,
                duration: 500,
                delay: index * 50,
                easing: 'easeOutCubic'
            });
        });
    },

    // ====================================================
    // 9. ANIMACIÓN MODAL / DIÁLOGO
    // ====================================================
    animateModal: function(modal, show = true) {
        if (show) {
            modal.style.display = 'block';
            anime({
                targets: modal,
                opacity: [0, 1],
                scale: [0.9, 1],
                duration: 300,
                easing: 'easeOutCubic'
            });
        } else {
            anime({
                targets: modal,
                opacity: [1, 0],
                scale: [1, 0.9],
                duration: 300,
                easing: 'easeInCubic',
                complete: () => modal.style.display = 'none'
            });
        }
    },

    // ====================================================
    // 10. INICIALIZACIÓN COMPLETA
    // ====================================================
    init: function() {
        if (typeof anime === 'undefined') {
            console.error('Anime.js no está disponible');
            return;
        }

        document.addEventListener('DOMContentLoaded', () => {
            this.animateCards();
            this.animateButtons();
            this.animateFormInputs();
            this.setupScrollAnimations();
            this.animateTableRows();
        });
    }
};

// ========================================================
// INICIALIZAR AUTOMÁTICAMENTE
// ========================================================
document.addEventListener('DOMContentLoaded', function() {
    if (typeof anime !== 'undefined') {
        HNHAnimations.init();
    }
});

// Export para uso en otros módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HNHAnimations;
}
