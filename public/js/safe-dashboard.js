/**
 * Safe Dashboard Script for MuniApp
 * Verifica la existencia de elementos antes de inicializar gráficos
 */

(function() {
    'use strict';

    // Función para inicializar charts de manera segura
    function safeChartInit(chartId, chartConfig, chartName) {
        const element = document.getElementById(chartId);
        if (!element) {
            console.log(`Element #${chartId} not found, skipping ${chartName} chart`);
            return null;
        }

        try {
            const chart = new ApexCharts(element, chartConfig);
            chart.render();
            console.log(`${chartName} chart initialized successfully`);
            return chart;
        } catch (error) {
            console.error(`Error initializing ${chartName} chart:`, error);
            // Mostrar mensaje de error en el elemento
            element.innerHTML = `
                <div class="alert alert-info text-center p-4">
                    <h6><i class="fas fa-chart-bar"></i> ${chartName}</h6>
                    <p class="mb-0">Gráfico no disponible temporalmente</p>
                </div>
            `;
            return null;
        }
    }

    // Función para detectar tema actual
    function getCurrentTheme() {
        try {
            const themeObject = localStorage.getItem("theme");
            const parsedObject = JSON.parse(themeObject);
            return parsedObject?.settings?.layout?.darkMode ? 'dark' : 'light';
        } catch (error) {
            console.warn('Error detecting theme, using light theme');
            return 'light';
        }
    }

    // Configuraciones base para charts
    function getBaseChartConfig(theme) {
        return {
            tooltip: {
                theme: theme
            },
            chart: {
                fontFamily: 'Nunito, sans-serif',
                foreColor: theme === 'dark' ? '#bbb' : '#373d3f',
                background: 'transparent'
            },
            colors: ['#1b55e2', '#e7515a', '#e2a03f', '#00ab55', '#6c5ffc'],
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: theme === 'dark' ? '#191e3a' : '#eee',
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false,
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            }
        };
    }

    // Inicializar dashboard cuando esté listo
    function initDashboard() {
        // Verificar que ApexCharts esté disponible
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts not loaded');
            return;
        }

        const theme = getCurrentTheme();
        const baseConfig = getBaseChartConfig(theme);

        // Configurar tema global de Apex
        if (typeof Apex !== 'undefined') {
            Apex.tooltip = baseConfig.tooltip;
        }

        // Chart 1 - Unique Visits (si existe)
        if (document.getElementById('chart1')) {
            const spark1Config = {
                chart: {
                    id: 'unique-visits',
                    group: 'sparks2',
                    type: 'line',
                    height: 80,
                    sparkline: {
                        enabled: true
                    },
                    dropShadow: {
                        enabled: true,
                        top: 1,
                        left: 1,
                        blur: 2,
                        color: '#e2a03f',
                        opacity: 0.7,
                    }
                },
                series: [{
                    data: [21, 9, 36, 12, 44, 25, 59, 41, 66, 25]
                }],
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                markers: {
                    size: 0
                },
                colors: ['#e2a03f'],
                tooltip: {
                    ...baseConfig.tooltip,
                    x: {
                        show: false,
                    },
                    y: {
                        title: {
                            formatter: function formatter(val) {
                                return '';
                            }
                        }
                    }
                },
                responsive: [{
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 95,
                        },
                    },
                }]
            };
            safeChartInit('chart1', spark1Config, 'Unique Visits');
        }

        // Chart 2 - Paid Visits (si existe)
        if (document.getElementById('chart2')) {
            const spark2Config = {
                chart: {
                    id: 'paid-visits',
                    group: 'sparks1',
                    type: 'line',
                    height: 80,
                    sparkline: {
                        enabled: true
                    },
                    dropShadow: {
                        enabled: true,
                        top: 1,
                        left: 1,
                        blur: 2,
                        color: '#1b55e2',
                        opacity: 0.7,
                    }
                },
                series: [{
                    data: [22, 19, 30, 47, 32, 44, 34, 55, 41, 69]
                }],
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                markers: {
                    size: 0
                },
                colors: ['#1b55e2'],
                tooltip: {
                    ...baseConfig.tooltip,
                    x: {
                        show: false,
                    },
                    y: {
                        title: {
                            formatter: function formatter(val) {
                                return '';
                            }
                        }
                    }
                },
                responsive: [{
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 95,
                        },
                    },
                }]
            };
            safeChartInit('chart2', spark2Config, 'Paid Visits');
        }

        // Chart 3 - Revenue Chart (si existe)
        if (document.getElementById('chart3')) {
            const revenueConfig = {
                chart: {
                    fontFamily: 'Nunito, sans-serif',
                    height: 365,
                    type: 'area',
                    zoom: {
                        enabled: false
                    },
                    dropShadow: {
                        enabled: true,
                        opacity: 0.3,
                        blur: 5,
                        left: -7,
                        top: 22
                    },
                    toolbar: {
                        show: false
                    },
                    events: {
                        mounted: function(chart, options) {
                            chart.windowResizeHandler();
                        }
                    }
                },
                ...baseConfig,
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    curve: 'smooth',
                    width: 2,
                    lineCap: 'square'
                },
                series: [{
                    name: 'Ingresos',
                    data: [16800, 16800, 15500, 17800, 15500, 17000, 19000, 16000, 15000, 17000, 14000, 17000]
                }],
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                xaxis: {
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    crosshairs: {
                        show: true
                    },
                    labels: {
                        offsetX: 0,
                        offsetY: 5,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Nunito, sans-serif',
                            cssClass: 'apexcharts-xaxis-title',
                        },
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value, index) {
                            return (value / 1000) + 'K';
                        },
                        offsetX: -22,
                        offsetY: 0,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Nunito, sans-serif',
                            cssClass: 'apexcharts-yaxis-title',
                        },
                    }
                }
            };
            safeChartInit('chart3', revenueConfig, 'Revenue Chart');
        }

        // Agregar más charts según sea necesario...
        console.log('Dashboard initialization completed');
    }

    // Inicializar cuando el DOM esté listo y ApexCharts esté disponible
    function checkAndInit() {
        if (typeof ApexCharts !== 'undefined') {
            initDashboard();
        } else {
            console.log('ApexCharts not yet available, retrying in 100ms...');
            setTimeout(checkAndInit, 100);
        }
    }

    // Verificar si el documento ya está listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAndInit);
    } else {
        checkAndInit();
    }

    // Exponer función para re-inicializar si es necesario
    window.reinitDashboard = initDashboard;

})();