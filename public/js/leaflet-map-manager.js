/**
 * Leaflet Map Integration for MuniApp
 * Free alternative to Google Maps using OpenStreetMap
 */

class LeafletMapManager {
    constructor() {
        this.maps = new Map();
        this.defaultCenter = [-25.2637, -57.5759]; // Paraguay coordinates
        this.defaultZoom = 13;
    }

    // Inicializar mapa para mostrar ubicación (solo lectura)
    initDisplayMap(containerId, lat, lng, options = {}) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container ${containerId} not found`);
            return null;
        }

        try {
            // Verificar coordenadas válidas
            if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
                container.innerHTML = `
                    <div class="alert alert-warning text-center p-4">
                        <i class="fas fa-map-marker-alt"></i>
                        <h6>Ubicación no disponible</h6>
                        <p class="mb-0">No hay coordenadas válidas para mostrar</p>
                    </div>
                `;
                return null;
            }

            // Crear el mapa
            const map = L.map(containerId, {
                center: [lat, lng],
                zoom: options.zoom || this.defaultZoom,
                zoomControl: true,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                dragging: false,
                ...options
            });

            // Agregar capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Agregar marcador
            const marker = L.marker([lat, lng]).addTo(map);
            
            // Popup con información
            marker.bindPopup(`
                <div class="map-popup">
                    <h6><i class="fas fa-map-marker-alt"></i> Ubicación de la solicitud</h6>
                    <p><strong>Coordenadas:</strong><br>
                    Lat: ${lat}<br>
                    Lng: ${lng}</p>
                    <a href="https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}&zoom=15" target="_blank" class="btn btn-sm btn-primary">
                        Ver en OpenStreetMap
                    </a>
                </div>
            `).openPopup();

            this.maps.set(containerId, map);
            console.log(`Display map initialized for ${containerId}`);
            return map;

        } catch (error) {
            console.error(`Error initializing display map:`, error);
            container.innerHTML = `
                <div class="alert alert-danger text-center p-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h6>Error cargando mapa</h6>
                    <p class="mb-0">No se pudo inicializar el mapa</p>
                </div>
            `;
            return null;
        }
    }

    // Inicializar mapa para seleccionar ubicación (interactivo)
    initSelectMap(containerId, options = {}) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container ${containerId} not found`);
            return null;
        }

        try {
            // Crear el mapa
            const map = L.map(containerId, {
                center: options.center || this.defaultCenter,
                zoom: options.zoom || this.defaultZoom,
                zoomControl: true,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                dragging: true,
                ...options
            });

            // Agregar capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Marcador inicial
            let marker = L.marker(options.center || this.defaultCenter, {
                draggable: true
            }).addTo(map);

            // Actualizar campos de input cuando se mueve el marcador
            const updateInputs = (latlng) => {
                const latInput = document.getElementById(options.latInputId || 'latitud');
                const lngInput = document.getElementById(options.lngInputId || 'longitud');
                
                if (latInput) latInput.value = latlng.lat.toFixed(7);
                if (lngInput) lngInput.value = latlng.lng.toFixed(7);
            };

            // Event listener para arrastrar marcador
            marker.on('dragend', function(e) {
                const position = e.target.getLatLng();
                updateInputs(position);
                console.log('Marker moved to:', position);
            });

            // Event listener para clicks en el mapa
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateInputs(e.latlng);
                console.log('Map clicked at:', e.latlng);
            });

            // Establecer valores iniciales en los inputs
            updateInputs(marker.getLatLng());

            // Popup informativo
            marker.bindPopup(`
                <div class="map-popup">
                    <h6><i class="fas fa-map-marker-alt"></i> Seleccionar ubicación</h6>
                    <p>Arrastra el marcador o haz clic en el mapa para seleccionar la ubicación</p>
                </div>
            `);

            this.maps.set(containerId, { map, marker });
            console.log(`Select map initialized for ${containerId}`);
            return { map, marker };

        } catch (error) {
            console.error(`Error initializing select map:`, error);
            container.innerHTML = `
                <div class="alert alert-danger text-center p-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h6>Error cargando mapa</h6>
                    <p class="mb-0">No se pudo inicializar el mapa</p>
                </div>
            `;
            return null;
        }
    }

    // Buscar ubicación por dirección (usando Nominatim - gratuito)
    async searchLocation(query, mapId) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=py&limit=5`);
            const results = await response.json();
            
            if (results.length > 0) {
                const result = results[0];
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);
                
                const mapData = this.maps.get(mapId);
                if (mapData && mapData.map) {
                    mapData.map.setView([lat, lng], 15);
                    if (mapData.marker) {
                        mapData.marker.setLatLng([lat, lng]);
                        
                        // Actualizar inputs si existen
                        const latInput = document.getElementById('latitud');
                        const lngInput = document.getElementById('longitud');
                        if (latInput) latInput.value = lat.toFixed(7);
                        if (lngInput) lngInput.value = lng.toFixed(7);
                    }
                }
                
                return { lat, lng, address: result.display_name };
            } else {
                throw new Error('No se encontraron resultados');
            }
        } catch (error) {
            console.error('Error searching location:', error);
            throw error;
        }
    }

    // Obtener dirección de coordenadas (geocodificación inversa)
    async reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&countrycodes=py`);
            const result = await response.json();
            
            if (result && result.display_name) {
                return result.display_name;
            } else {
                return `Lat: ${lat}, Lng: ${lng}`;
            }
        } catch (error) {
            console.error('Error reverse geocoding:', error);
            return `Lat: ${lat}, Lng: ${lng}`;
        }
    }

    // Limpiar mapa
    destroyMap(containerId) {
        const mapData = this.maps.get(containerId);
        if (mapData) {
            if (mapData.map) {
                mapData.map.remove();
            }
            this.maps.delete(containerId);
            console.log(`Map ${containerId} destroyed`);
        }
    }

    // Redimensionar mapa (útil cuando el contenedor cambia de tamaño)
    resizeMap(containerId) {
        const mapData = this.maps.get(containerId);
        if (mapData && mapData.map) {
            setTimeout(() => {
                mapData.map.invalidateSize();
            }, 100);
        }
    }
}

// CSS para los mapas
const leafletMapCSS = `
.leaflet-popup-content {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.map-popup h6 {
    margin: 0 0 10px 0;
    color: #2563eb;
    font-weight: 600;
}

.map-popup p {
    margin: 8px 0;
    font-size: 13px;
    line-height: 1.4;
}

.map-popup .btn {
    padding: 4px 8px;
    font-size: 12px;
    text-decoration: none;
    border-radius: 4px;
    display: inline-block;
    margin-top: 8px;
}

.map-popup .btn-primary {
    background-color: #2563eb;
    color: white;
    border: 1px solid #2563eb;
}

.map-popup .btn-primary:hover {
    background-color: #1d4ed8;
}

.leaflet-container {
    border-radius: 8px;
    border: 1px solid #d1d5db;
}

.location-search-container {
    margin-bottom: 15px;
}

.location-search-input {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
}

.location-search-input input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}

.location-search-input button {
    padding: 8px 16px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
}

.location-search-input button:hover {
    background: #1d4ed8;
}

.location-search-input button:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.search-results {
    font-size: 12px;
    color: #6b7280;
    font-style: italic;
}
`;

// Insertar CSS si no existe
if (!document.getElementById('leaflet-map-styles')) {
    const style = document.createElement('style');
    style.id = 'leaflet-map-styles';
    style.textContent = leafletMapCSS;
    document.head.appendChild(style);
}

// Crear instancia global
window.LeafletMapManager = new LeafletMapManager();