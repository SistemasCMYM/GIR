{{-- 
    Estándar de colores para niveles de riesgo psicosocial
    Para ser incluido en todas las vistas del módulo psicosocial
    ACTUALIZADO: Colores globales definidos en Inicio-modern.css
--}}
<style>
/* Colores estándar para niveles de riesgo psicosocial - HEREDADOS DE CSS GLOBAL */
.risk-level-sin-riesgo {
    background-color: var(--risk-sin-riesgo) !important;
    color: white !important;
}

.risk-level-bajo {
    background-color: var(--risk-bajo) !important;
    color: white !important;
}

.risk-level-medio {
    background-color: var(--risk-medio) !important;
    color: black !important;
}

.risk-level-alto {
    background-color: var(--risk-alto) !important;
    color: white !important;
}

.risk-level-muy-alto {
    background-color: var(--risk-muy-alto) !important;
    color: white !important;
}

/* Para badges específicamente - usando variables CSS globales */
.badge.risk-sin-riesgo {
    background-color: var(--risk-sin-riesgo) !important;
    color: white !important;
}

.badge.risk-bajo {
    background-color: var(--risk-bajo) !important;
    color: white !important;
}

.badge.risk-medio {
    background-color: var(--risk-medio) !important;
    color: black !important;
}

.badge.risk-alto {
    background-color: var(--risk-alto) !important;
    color: white !important;
}

.badge.risk-muy-alto {
    background-color: var(--risk-muy-alto) !important;
    color: white !important;
}
</style>

<script>
// Colores estándar para Chart.js - ACTUALIZADOS
window.psicosocialRiskColors = {
    sin_riesgo: '#008235',    // Verde
    bajo: '#00D364',          // Verde claro  
    medio: '#FFD600',         // Amarillo
    alto: '#DA3D3D',          // Rojo
    muy_alto: '#D10000'       // Rojo oscuro
};

// Array de colores en orden estándar - ACTUALIZADOS
window.psicosocialRiskColorsArray = [
    '#008235',   // Sin Riesgo
    '#00D364',   // Bajo
    '#FFD600',   // Medio  
    '#DA3D3D',   // Alto
    '#D10000'    // Muy Alto
];

// Función helper para obtener color por nivel
window.getPsicosocialRiskColor = function(nivel) {
    switch(nivel.toLowerCase().replace(' ', '_')) {
        case 'sin_riesgo':
        case 'sin riesgo':
            return window.psicosocialRiskColors.sin_riesgo;
        case 'bajo':
        case 'riesgo_bajo':
        case 'riesgo bajo':
            return window.psicosocialRiskColors.bajo;
        case 'medio':
        case 'riesgo_medio':
        case 'riesgo medio':
            return window.psicosocialRiskColors.medio;
        case 'alto':
        case 'riesgo_alto':
        case 'riesgo alto':
            return window.psicosocialRiskColors.alto;
        case 'muy_alto':
        case 'muy alto':
        case 'riesgo_muy_alto':
        case 'riesgo muy alto':
            return window.psicosocialRiskColors.muy_alto;
        default:
            return '#6c757d'; // Color gris por defecto
    }
};

// Función para obtener colores desde CSS variables
window.getCSSRiskColor = function(nivel) {
    const root = document.documentElement;
    switch(nivel.toLowerCase().replace(' ', '_')) {
        case 'sin_riesgo':
        case 'sin riesgo':
            return getComputedStyle(root).getPropertyValue('--risk-sin-riesgo');
        case 'bajo':
        case 'riesgo_bajo':
        case 'riesgo bajo':
            return getComputedStyle(root).getPropertyValue('--risk-bajo');
        case 'medio':
        case 'riesgo_medio':
        case 'riesgo medio':
            return getComputedStyle(root).getPropertyValue('--risk-medio');
        case 'alto':
        case 'riesgo_alto':
        case 'riesgo alto':
            return getComputedStyle(root).getPropertyValue('--risk-alto');
        case 'muy_alto':
        case 'muy alto':
        case 'riesgo_muy_alto':
        case 'riesgo muy alto':
            return getComputedStyle(root).getPropertyValue('--risk-muy-alto');
        default:
            return '#6c757d';
    }
};
</script>
