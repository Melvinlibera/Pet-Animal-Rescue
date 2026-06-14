<?php
/**
 * BOTÓN FLOTANTE DEL MODO OSCURO
 * Incluye este archivo en cualquier página que no tenga header
 * Sistema centralizado de temas con Anime.js
 */
?>
<!-- BOTÓN FLOTANTE DEL MODO OSCURO -->
<div id="floatingThemeToggle" class="floating-theme-toggle" title="Cambiar modo claro/oscuro" aria-label="Alternar modo claro y oscuro">
    <i class="bx bx-sun"></i>
</div>

<!-- Estilos y scripts para el botón flotante -->
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<script src="https://animejs.com/lib/anime.min.js"></script>
<style>
.floating-theme-toggle {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #0a1f44;
    color: #ffffff;
    border: 3px solid #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 24px;
    z-index: 9999;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.floating-theme-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
}

.floating-theme-toggle:active {
    transform: scale(0.95);
}

body.dark .floating-theme-toggle {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}

body.dark .floating-theme-toggle:hover {
    background: #334155;
}

@media (max-width: 768px) {
    .floating-theme-toggle {
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
}
</style>

<script>
// ====================================================
// SISTEMA CENTRALIZADO DE TEMAS
// ====================================================
const ThemeManager = {
    currentTheme: null,
    
    // Aplicar el tema con animación
    applyTheme: function(theme) {
        if (typeof anime === 'undefined') {
            this.applyThemeNoAnimation(theme);
            return;
        }

        document.body.classList.remove('light', 'dark');
        document.body.classList.add(theme);

        const themeToggle = document.getElementById('floatingThemeToggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                // Animar la rotación del icono
                anime({
                    targets: icon,
                    rotate: [0, 360],
                    duration: 500,
                    easing: 'easeInOutQuad',
                    begin: () => {
                        icon.className = theme === 'dark' ? 'bx bx-moon' : 'bx bx-sun';
                    }
                });
            }
            
            themeToggle.title = theme === 'dark' 
                ? 'Cambiar a modo claro' 
                : 'Cambiar a modo oscuro';
            themeToggle.setAttribute('aria-label', themeToggle.title);
        }

        this.currentTheme = theme;
    },

    // Fallback sin animación
    applyThemeNoAnimation: function(theme) {
        document.body.classList.remove('light', 'dark');
        document.body.classList.add(theme);

        const themeToggle = document.getElementById('floatingThemeToggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'bx bx-moon' : 'bx bx-sun';
            }
        }
        this.currentTheme = theme;
    },

    // Cargar tema guardado
    loadTheme: function() {
        const storedTheme = window.localStorage.getItem('hnh-theme');
        const defaultTheme = window.matchMedia('(prefers-color-scheme: dark)').matches 
            ? 'dark' 
            : 'light';
        return storedTheme === 'dark' || storedTheme === 'light' 
            ? storedTheme 
            : defaultTheme;
    },

    // Establecer tema
    setTheme: function(theme) {
        this.applyTheme(theme);
        window.localStorage.setItem('hnh-theme', theme);
    },

    // Toggle tema
    toggleTheme: function() {
        const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    let currentTheme = ThemeManager.loadTheme();
    ThemeManager.setTheme(currentTheme);

    const themeToggle = document.getElementById('floatingThemeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            ThemeManager.toggleTheme();
        });
    }
});

// Aplicar tema INMEDIATAMENTE para evitar flash
(function() {
    const storedTheme = localStorage.getItem('hnh-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = storedTheme || (prefersDark ? 'dark' : 'light');
    document.documentElement.style.colorScheme = theme;
    document.body.classList.add(theme);
})();
</script>