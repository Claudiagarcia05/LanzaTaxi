// Cliente JavaScript - Gestión de solicitudes de taxis y viajes
<<<<<<< HEAD
const API_URL = 'http://localhost:3000/api';
=======
// API_URL ya está definido en auth.js
>>>>>>> origin/master
let map, origenMarker, destinoMarker, routeLayer, taxistaMarker;
let socket;
let viajeActualId = null;
let origenCoords = null;
let destinoCoords = null;
let rutaCalculada = null;

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    // Comentado temporalmente - verificación de autenticación
    // const auth = verificarAutenticacion(['cliente']);
    // if (!auth) return;

    // Demo user
    const userName = document.getElementById('userName');
    if (userName) {
        userName.textContent = 'Pasajero Demo';
    }
    
<<<<<<< HEAD
    inicializarMapa();
    // conectarWebSocket();
    cargarViajes();
    cargarPerfil();
=======
    // No inicializar mapa aquí - se hace en el HTML inline
    // inicializarMapa();
    
    // conectarWebSocket();
    // cargarViajes();
    // cargarPerfil();
>>>>>>> origin/master
});

// Inicializar mapa con Leaflet
function inicializarMapa() {
    try {
<<<<<<< HEAD
        // Centro de Lanzarote
        map = L.map('map').setView([28.9636, -13.5477], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Click en el mapa para establecer origen/destino
        map.on('click', (e) => {
            if (!origenCoords) {
                establecerOrigen(e.latlng.lat, e.latlng.lng);
            } else if (!destinoCoords) {
                establecerDestino(e.latlng.lat, e.latlng.lng);
            }
        });
=======
        // Los mapas se inicializan en el HTML inline
        // Esta función se mantiene para compatibilidad
        console.log('Mapas inicializados desde HTML inline');
>>>>>>> origin/master
    } catch (error) {
        console.error('Error inicializando mapa:', error);
    }
}

// Conectar WebSocket (comentado - se activará con backend)
function conectarWebSocket() {
    // const auth = JSON.parse(localStorage.getItem('user'));
    // socket = io('http://localhost:3000');
    // socket.on('connect', () => console.log('✅ Conectado a WebSocket'));
}

// Usar ubicación actual
function usarUbicacionActual() {
    if (!navigator.geolocation) {
        LanzaTaxi.showNotification('Tu navegador no soporta geolocalización', 'error');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            establecerOrigen(lat, lng);
            if (map) map.setView([lat, lng], 13);
            LanzaTaxi.showNotification('Ubicación obtenida', 'success');
        },
        (error) => {
            LanzaTaxi.showNotification('No se pudo obtener tu ubicación: ' + error.message, 'error');
        }
    );
}

// Establecer origen
function establecerOrigen(lat, lng) {
    origenCoords = { lat, lng };
    
    if (origenMarker) {
        map.removeLayer(origenMarker);
    }

    origenMarker = L.marker([lat, lng], {
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(map);

    origenMarker.bindPopup('<strong>Punto de Recogida</strong>').openPopup();
    
    document.getElementById('origenInput').value = `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
    document.getElementById('origenCoords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
}

// Establecer destino
function establecerDestino(lat, lng) {
    destinoCoords = { lat, lng };
    
    if (destinoMarker) {
        map.removeLayer(destinoMarker);
    }

    destinoMarker = L.marker([lat, lng], {
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(map);

    destinoMarker.bindPopup('<strong>Destino</strong>').openPopup();
    
    document.getElementById('destinoInput').value = `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
    document.getElementById('destinoCoords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
}

// Calcular ruta y precio
async function calcularRuta() {
    if (!origenCoords || !destinoCoords) {
        mostrarAlerta('❌ Debes seleccionar origen y destino (haz clic en el mapa)', 'warning');
        return;
    }

    // Calcular distancia (fórmula de Haversine)
    const distancia = calcularDistancia(
        origenCoords.lat, origenCoords.lng,
        destinoCoords.lat, destinoCoords.lng
    );

    // Dibujar línea en el mapa
    if (routeLayer) {
        map.removeLayer(routeLayer);
    }

    routeLayer = L.polyline([
        [origenCoords.lat, origenCoords.lng],
        [destinoCoords.lat, destinoCoords.lng]
    ], {
        color: '#f39c12',
        weight: 4,
        opacity: 0.7
    }).addTo(map);

    map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });

    // Calcular precio
    await calcularPrecio(distancia);
}

// Calcular distancia con fórmula de Haversine
function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Calcular precio
async function calcularPrecio(distanciaParam = null) {
    const distancia = distanciaParam || rutaCalculada?.distancia;
    
    if (!distancia) {
        mostrarAlerta('❌ Primero debes calcular la ruta', 'warning');
        return;
    }

    const tipoTarifa = document.getElementById('tipoTarifa').value;
    const checkboxes = document.querySelectorAll('.checkbox-group input:checked');
    const suplementos = Array.from(checkboxes).map(cb => cb.value);

    try {
        const response = await fetchAuth(`${API_URL}/viajes/calcular-precio`, {
            method: 'POST',
            body: JSON.stringify({ distancia, tipoTarifa, suplementos })
        });

        const data = await response.json();

        // Guardar información del precio calculado
        rutaCalculada = {
            distancia: distancia,
            precio: data.precioTotal,
            tarifa: tipoTarifa,
            suplementos: suplementos
        };

        // Mostrar información
        document.getElementById('infoViaje').style.display = 'block';
        document.getElementById('infoDistancia').textContent = `${distancia.toFixed(2)} km`;
        document.getElementById('infoTiempo').textContent = `${Math.ceil(distancia * 1.5)} min`;
        document.getElementById('infoPrecio').textContent = `€${data.precioTotal.toFixed(2)}`;
        document.getElementById('btnSolicitar').disabled = false;

        mostrarAlerta('✅ Precio calculado correctamente', 'success');

    } catch (error) {
        mostrarAlerta('❌ Error al calcular precio: ' + error.message, 'danger');
    }
}

// Solicitar taxi
async function solicitarTaxi(event) {
    event.preventDefault();

    if (!rutaCalculada) {
        mostrarAlerta('❌ Debes calcular el precio primero', 'warning');
        return;
    }

    try {
        const origenDireccion = document.getElementById('origenInput').value;
        const destinoDireccion = document.getElementById('destinoInput').value;

        const response = await fetchAuth(`${API_URL}/viajes/solicitar`, {
            method: 'POST',
            body: JSON.stringify({
                origenLat: origenCoords.lat,
                origenLng: origenCoords.lng,
                origenDireccion,
                destinoLat: destinoCoords.lat,
                destinoLng: destinoCoords.lng,
                destinoDireccion,
                distancia: rutaCalculada.distancia,
                precioEstimado: rutaCalculada.precio,
                tipoTarifa: rutaCalculada.tarifa,
                suplementos: rutaCalculada.suplementos
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error);
        }

        viajeActualId = data.viaje.id;

        // Notify taxistas via WebSocket
        socket.emit('nueva_solicitud', {
            id: data.viaje.id,
            ...data.viaje,
            clienteNombre: JSON.parse(localStorage.getItem('user')).nombre
        });

        // Mostrar estado del viaje
        document.getElementById('viajeActivo').style.display = 'block';
        document.getElementById('statusTexto').textContent = 'Buscando taxi disponible...';
        document.getElementById('formSolicitud').reset();
        document.getElementById('infoViaje').style.display = 'none';
        document.getElementById('btnSolicitar').disabled = true;

        // Limpiar mapa
        origenCoords = null;
        destinoCoords = null;
        rutaCalculada = null;

        mostrarAlerta('✅ ¡Solicitud enviada! Buscando taxi disponible...', 'success');

    } catch (error) {
        mostrarAlerta('❌ Error al solicitar taxi: ' + error.message, 'danger');
    }
}

// Cargar información del taxista asignado
async function cargarInfoTaxista(taxistaId) {
    try {
        const response = await fetchAuth(`${API_URL}/viajes/${viajeActualId}`);
        const viaje = await response.json();

        document.getElementById('taxistaNombre').textContent = viaje.taxista_nombre || 'N/A';
        document.getElementById('taxistaVehiculo').textContent = 
            `${viaje.modelo_vehiculo || 'N/A'}`;
        document.getElementById('taxistaMatricula').textContent = viaje.matricula || 'N/A';
        document.getElementById('taxistaLicencia').textContent = viaje.licencia || 'N/A';
        document.getElementById('taxistaValoracion').textContent = 
            (viaje.valoracion_media || 0).toFixed(1);

        document.getElementById('taxistaInfo').style.display = 'block';

        // Agregar marcador del taxista en el mapa
        if (viaje.taxista_lat && viaje.taxista_lng) {
            if (taxistaMarker) {
                map.removeLayer(taxistaMarker);
            }

            taxistaMarker = L.marker([viaje.taxista_lat, viaje.taxista_lng], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map);

            taxistaMarker.bindPopup(`<strong>Tu Taxi</strong><br>${viaje.taxista_nombre}`).openPopup();
        }

    } catch (error) {
        console.error('Error al cargar info del taxista:', error);
    }
}

// Actualizar ubicación del taxista en tiempo real
function actualizarUbicacionTaxista(data) {
    if (taxistaMarker) {
        taxistaMarker.setLatLng([data.latitud, data.longitud]);
    }
}

// Cancelar viaje
async function cancelarViaje() {
    if (!confirm('¿Estás seguro de cancelar este viaje?')) {
        return;
    }

    // TODO: Implementar cancelación en backend
    document.getElementById('viajeActivo').style.display = 'none';
    viajeActualId = null;
    
    if (taxistaMarker) {
        map.removeLayer(taxistaMarker);
        taxistaMarker = null;
    }

    mostrarAlerta('Viaje cancelado', 'info');
}

// Cargar historial de viajes
async function cargarViajes() {
    try {
        const contenedor = document.getElementById('tablaHistorialViajes');
        
        if (!contenedor) {
            console.warn('Contenedor tablaHistorialViajes no encontrado');
            return;
        }

        const response = await fetchAuth(`${API_URL}/viajes/mis-viajes`);
        const data = await response.json();
        const viajes = data.viajes || [];
        
        if (viajes.length === 0) {
            contenedor.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">
                        <i class="fas fa-car text-4xl mb-2 block"></i>
                        <p>No tienes viajes registrados</p>
                    </td>
                </tr>
            `;
            return;
        }

        contenedor.innerHTML = viajes.map(viaje => {
            const fecha = new Date(viaje.fecha_solicitud).toLocaleDateString('es-ES');
            const origen = viaje.origen_direccion || 'N/A';
            const destino = viaje.destino_direccion || 'N/A';
            const taxista = viaje.taxista_nombre || 'Sin asignar';
            const distanciaValor = Number.parseFloat(viaje.distancia);
            const distancia = Number.isFinite(distanciaValor) ? distanciaValor.toFixed(2) + ' km' : 'N/A';
            const precioValor = Number.parseFloat(viaje.precio_final ?? viaje.precio_estimado ?? 0);
            const precio = Number.isFinite(precioValor) ? precioValor.toFixed(2) : '0.00';
            const estadoClass = viaje.estado === 'finalizado' ? 'completed' : viaje.estado === 'en_curso' ? 'available' : 'pending';
            const estadoTexto = viaje.estado === 'finalizado' ? 'Completado' : viaje.estado === 'en_curso' ? 'En curso' : 'Pendiente';
            
            return `
                <tr>
                    <td>${fecha}</td>
                    <td>${origen} → ${destino}</td>
                    <td>${taxista}</td>
                    <td>${distancia}</td>
                    <td class="font-bold">${precio}€</td>
                    <td><span class="badge badge-${estadoClass}">${estadoTexto}</span></td>
                    <td>
                        ${viaje.estado === 'finalizado' ? `
                            <button class="btn btn-sm btn-outline !py-1 !px-2" onclick="descargarFactura(${viaje.id})" title="Descargar factura">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        ` : ''}
                        ${viaje.estado === 'finalizado' && !viaje.valoracion ? `
                            <button class="btn btn-sm btn-primary !py-1 !px-2 ml-1" onclick="abrirModalValoracion(${viaje.id})" title="Valorar">
                                <i class="fas fa-star"></i>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');

    } catch (error) {
        console.error('Error al cargar viajes:', error);
        const contenedor = document.getElementById('tablaHistorialViajes');
        if (contenedor) {
            contenedor.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-red-600">
                        <i class="fas fa-exclamation-triangle"></i> Error al cargar el historial de viajes
                    </td>
                </tr>
            `;
        }
    }
}

// Abrir modal de valoración
function abrirModalValoracion(viajeId) {
    document.getElementById('viajeIdValoracion').value = viajeId;
    document.getElementById('modalValoracion').style.display = 'block';
    seleccionarEstrellas(5); // Seleccionar 5 estrellas por defecto
}

// Cerrar modal de valoración
function cerrarModalValoracion() {
    document.getElementById('modalValoracion').style.display = 'none';
}

// Seleccionar estrellas
function seleccionarEstrellas(rating) {
    document.getElementById('valoracionEstrellas').value = rating;
    
    const estrellas = document.querySelectorAll('.rating i');
    estrellas.forEach((estrella, index) => {
        if (index < rating) {
            estrella.classList.add('active');
        } else {
            estrella.classList.remove('active');
        }
    });
}

// Enviar valoración
async function enviarValoracion(event) {
    event.preventDefault();

    const viajeId = document.getElementById('viajeIdValoracion').value;
    const valoracion = parseInt(document.getElementById('valoracionEstrellas').value);
    const comentario = document.getElementById('valoracionComentario').value;

    try {
        const response = await fetchAuth(`${API_URL}/viajes/${viajeId}/valorar`, {
            method: 'POST',
            body: JSON.stringify({ valoracion, comentario })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error);
        }

        mostrarAlerta('✅ ¡Gracias por tu valoración!', 'success');
        cerrarModalValoracion();
        cargarViajes(); // Recargar viajes

    } catch (error) {
        mostrarAlerta('❌ Error al enviar valoración: ' + error.message, 'danger');
    }
}

// Descargar factura
async function descargarFactura(viajeId) {
    try {
        const token = localStorage.getItem('token');
        const url = `${API_URL}/viajes/${viajeId}/factura`;
        
        window.open(url + '?token=' + token, '_blank');

    } catch (error) {
        mostrarAlerta('❌ Error al descargar factura: ' + error.message, 'danger');
    }
}

// Cargar perfil
async function cargarPerfil() {
    try {
        const response = await fetchAuth(`${API_URL}/auth/profile`);
        const data = await response.json();
        const usuario = data.user || data;

        const contenedor = document.getElementById('datosPerfil');
        
        if (!contenedor) {
            console.warn('Contenedor datosPerfil no encontrado');
            return;
        }

        const roleLabel = usuario.role ? usuario.role.toUpperCase() : 'N/A';

        contenedor.innerHTML = `
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nombre completo</label>
                    <input type="text" class="form-input" value="${usuario.nombre}" readonly>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="${usuario.email}" readonly>
                </div>
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-input" value="${usuario.telefono || 'No especificado'}" readonly>
                </div>
                <div>
                    <label class="form-label">Tipo de cuenta</label>
                    <input type="text" class="form-input" value="${roleLabel}" readonly>
                </div>
            </div>
        `;

    } catch (error) {
        console.error('Error al cargar perfil:', error);
    }
}

// Mostrar sección
function mostrarSeccion(seccion) {
    // Ocultar todas las secciones
    const secciones = ['solicitar', 'historial', 'perfil'];
    secciones.forEach(s => {
        const elemento = document.getElementById(s);
        if (elemento) {
            if (s === seccion) {
                elemento.classList.remove('hidden');
            } else {
                elemento.classList.add('hidden');
            }
        }
    });

    // Actualizar estado activo de los botones de navegación
    document.querySelectorAll('.sidebar-nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const navButton = document.getElementById(`nav-${seccion}`);
    if (navButton) {
        navButton.classList.add('active');
    }

    // Cargar datos según la sección
    if (seccion === 'historial') {
        cargarViajes();
    } else if (seccion === 'perfil') {
        cargarPerfil();
    }
}
