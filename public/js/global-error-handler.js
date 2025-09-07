/**
 * Manejador global de errores JavaScript para MuniApp
 * Previene que errores de librerías externas rompan la aplicación
 */

(function() {
    'use strict';

    // Manejo global de errores de JavaScript
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno,
            colno: e.colno,
            error: e.error
        });
        
        // Manejar errores específicos conocidos
        if (e.message && e.message.includes('Google Maps')) {
            console.warn('Google Maps error detected - this is usually due to API key or network issues');
        }
        
        if (e.message && e.message.includes('initMap is not a function')) {
            console.warn('Google Maps initMap function not found - checking for element existence');
        }
        
        if (e.message && e.message.includes('ApexCharts')) {
            console.warn('ApexCharts error - element might not exist on this page');
        }
        
        if (e.message && e.message.includes('PerfectScrollbar')) {
            console.warn('PerfectScrollbar error - element might not exist on this page');
        }
        
        // No mostrar errores molestos al usuario en producción
        return true; // Prevenir que el error se propague al usuario
    });

    // Detectar errores de promesas no manejadas
    window.addEventListener('unhandledrejection', function(e) {
        console.error('Unhandled Promise Rejection:', e.reason);
        
        // Manejar rechazos específicos conocidos
        if (e.reason && e.reason.toString().includes('Loading of script failed')) {
            console.warn('Script loading failed - this might be a network issue');
        }
        
        e.preventDefault(); // Prevenir que aparezca en la consola del usuario
    });

    // Función utilitaria para verificar si un elemento existe antes de inicializar librerías
    window.safeInit = function(elementSelector, initFunction, libraryName) {
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.querySelector(elementSelector);
            if (element) {
                try {
                    initFunction(element);
                    console.log(`${libraryName} initialized successfully for ${elementSelector}`);
                } catch (error) {
                    console.error(`Error initializing ${libraryName}:`, error);
                }
            } else {
                console.log(`Element ${elementSelector} not found - skipping ${libraryName} initialization`);
            }
        });
    };

    // Función para cargar scripts de manera condicional
    window.conditionalScriptLoad = function(scriptSrc, condition, callback) {
        if (condition) {
            const script = document.createElement('script');
            script.src = scriptSrc;
            script.onload = function() {
                console.log(`Script ${scriptSrc} loaded successfully`);
                if (callback) callback();
            };
            script.onerror = function() {
                console.error(`Error loading script: ${scriptSrc}`);
            };
            document.head.appendChild(script);
        }
    };

    // Función para verificar dependencias antes de ejecutar código
    window.checkDependencies = function(dependencies, callback) {
        let allLoaded = true;
        dependencies.forEach(function(dep) {
            if (typeof window[dep] === 'undefined') {
                console.warn(`Dependency ${dep} not loaded`);
                allLoaded = false;
            }
        });
        
        if (allLoaded && callback) {
            callback();
        } else if (!allLoaded) {
            console.warn('Not all dependencies loaded, retrying in 500ms');
            setTimeout(function() {
                window.checkDependencies(dependencies, callback);
            }, 500);
        }
    };

    // Detectar cuando todos los scripts han terminado de cargar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM completamente cargado - MuniApp initialized');
        
        // Verificar elementos comunes que requieren inicialización
        const commonElements = {
            '.apex-charts': 'ApexCharts',
            '.perfect-scrollbar': 'PerfectScrollbar',
            '#map, #mapa': 'Google Maps',
            '.tinymce': 'TinyMCE'
        };
        
        Object.keys(commonElements).forEach(function(selector) {
            const elements = document.querySelectorAll(selector);
            if (elements.length > 0) {
                console.log(`Found ${elements.length} elements for ${commonElements[selector]}`);
            }
        });

        // Verificar si hay elementos que necesitan dashboard scripts
        const dashboardElements = [
            '#chart1', '#chart2', '#chart3', '#chart4', '#chart5', '#chart6'
        ];
        
        const hasDashboardElements = dashboardElements.some(function(id) {
            return document.getElementById(id.substring(1)) !== null;
        });
        
        if (hasDashboardElements) {
            console.log('Dashboard elements detected - dashboard scripts should be loaded');
        }
    });

    // Función específica para manejar errores de Google Maps
    window.handleGoogleMapsError = function(mapElementId, errorMessage) {
        const mapElement = document.getElementById(mapElementId);
        if (mapElement) {
            mapElement.innerHTML = `
                <div class="alert alert-warning text-center p-4">
                    <h6><i class="fas fa-exclamation-triangle"></i> Error de Mapa</h6>
                    <p class="mb-0">${errorMessage || 'No se pudo cargar el mapa'}</p>
                </div>
            `;
        }
    };

    // Función específica para manejar errores de gráficos
    window.handleChartError = function(chartElementId, errorMessage) {
        const chartElement = document.getElementById(chartElementId);
        if (chartElement) {
            chartElement.innerHTML = `
                <div class="alert alert-info text-center p-4">
                    <h6><i class="fas fa-chart-bar"></i> Gráfico no disponible</h6>
                    <p class="mb-0">${errorMessage || 'No se pudieron cargar los datos del gráfico'}</p>
                </div>
            `;
        }
    };

    console.log('Global error handler initialized for MuniApp');
})();