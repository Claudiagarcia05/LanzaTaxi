// Taxista Dashboard JavaScript
const API_URL = 'http://localhost:3000/api';
let map, miMarker;
let socket;

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    console.log('Panel de Taxista cargado');
    inicializarMapa();
    cargarDatosTaxista();
});

// Inicializar mapa
function inicializarMapa() {
    try {
        // Centro de Lanzarote
        map = L.map('map').setView([28.9636, -13.5477], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    } catch (error) {
        console.error('Error inicializando mapa:', error);
    }
}

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Obtener ubicación actual
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            map.setView([lat, lng], 13);
            
            miMarker = L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map);
            
            miMarker.bindPopup('<strong>Tu ubicación</strong>').openPopup();
            
            // Enviar ubicación al servidor
            enviarUbicacion(lat, lng);
        });
    }
}

// Conectar WebSocket
function conectarWebSocket() {
    const auth = JSON.parse(localStorage.getItem('user'));
    socket = io('http://localhost:3000');

    socket.on('connect', () => {
        console.log('✅ Conectado a WebSocket');
        socket.emit('register', {
            userId: auth.id,
            role: 'taxista',
            taxistaId: auth.taxista.id
        });
    });

    // Nueva solicitud disponible
    socket.on('solicitud_disponible', (solicitud) => {
        mostrarNotificacion('🔔 Nueva solicitud de viaje disponible', 'info');
        cargarSolicitudes();
    });

    // Viaje ya no disponible
    socket.on('viaje_no_disponible', (data) => {
        cargarSolicitudes();
    });
}

// Enviar ubicación al servidor
async function enviarUbicacion(lat, lng) {
    try {
        await fetchAuth(`${API_URL}/taxistas/actualizar-ubicacion`, {
            method: 'POST',
            body: JSON.stringify({ latitud: lat, longitud: lng })
        });

        // Emitir por WebSocket
        if (socket) {
            socket.emit('actualizar_ubicacion', {
                taxistaId: taxistaInfo.id,
                latitud: lat,
                longitud: lng
            });
        }

    } catch (error) {
        console.error('Error al enviar ubicación:', error);
    }
}

// Iniciar actualización de ubicación
function iniciarActualizacionUbicacion() {
    actualizacionUbicacionInterval = setInterval(() => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                if (miMarker) {
                    miMarker.setLatLng([lat, lng]);
                }
                
                enviarUbicacion(lat, lng);
            });
        }
    }, 10000); // Cada 10 segundos
}

// Cargar datos del taxista
async function cargarDatosTaxista() {
    try {
        const response = await fetchAuth(`${API_URL}/taxistas/mi-info`);
        taxistaInfo = await response.json();

        actualizarEstadoUI(taxistaInfo.estado);

    } catch (error) {
        console.error('Error al cargar info del taxista:', error);
    }
}

// Actualizar UI del estado
function actualizarEstadoUI(estado) {
    const estadoTexto = document.getElementById('estadoActual');
    
    switch(estado) {
        case 'libre':
            estadoTexto.innerHTML = '<i class="fas fa-check-circle" style="color: green;"></i> DISPONIBLE';
            estadoTexto.style.color = 'green';
            break;
        case 'ocupado':
            estadoTexto.innerHTML = '<i class="fas fa-times-circle" style="color: red;"></i> NO DISPONIBLE';
            estadoTexto.style.color = 'red';
            break;
        case 'en_servicio':
            estadoTexto.innerHTML = '<i class="fas fa-car" style="color: orange;"></i> EN SERVICIO';
            estadoTexto.style.color = 'orange';
            break;
    }
}

// Cambiar estado
async function cambiarEstado(nuevoEstado) {
    try {
        const response = await fetchAuth(`${API_URL}/taxistas/cambiar-estado`, {
            method: 'POST',
            body: JSON.stringify({ estado: nuevoEstado })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error);
        }

        actualizarEstadoUI(nuevoEstado);
        
        // Emitir por WebSocket
        if (socket) {
            socket.emit('cambiar_estado', {
                taxistaId: taxistaInfo.id,
                estado: nuevoEstado
            });
        }

        mostrarAlerta(`Estado cambiado a: ${nuevoEstado.toUpperCase()}`, 'success');

    } catch (error) {
        mostrarAlerta('Error al cambiar estado: ' + error.message, 'danger');
    }
}

// Cargar estadísticas
async function cargarEstadisticas() {
    try {
        const response = await fetchAuth(`${API_URL}/taxistas/estadisticas`);
        const stats = await response.json();

        document.getElementById('statViajesHoy').textContent = stats.viajes_hoy || 0;
        document.getElementById('statIngresosHoy').textContent = 
            stats.ingresos_hoy ? `€${stats.ingresos_hoy.toFixed(2)}` : '€0.00';
        document.getElementById('statTotalViajes').textContent = stats.total_viajes || 0;
        document.getElementById('statValoracion').textContent = 
            stats.valoracion_media ? stats.valoracion_media.toFixed(1) : '-';

        // Mostrar estadísticas detalladas en sección
        document.getElementById('contenedorEstadisticas').innerHTML = `
            <div class="grid-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Viajes Completados</div>
                        <div class="stat-value">${stats.viajes_completados || 0}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Ingresos Totales</div>
                        <div class="stat-value">€${(stats.ingresos_totales || 0).toFixed(2)}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Ingreso Promedio</div>
                        <div class="stat-value">€${(stats.ingreso_promedio || 0).toFixed(2)}</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">📊 Resumen de Actividad</div>
                <div style="padding: 2rem;">
                    <div class="perfil-info">
                        <div class="perfil-campo">
                            <label>Total de Viajes</label>
                            <strong>${stats.total_viajes || 0}</strong>
                        </div>
                        <div class="perfil-campo">
                            <label>Viajes Hoy</label>
                            <strong>${stats.viajes_hoy || 0}</strong>
                        </div>
                        <div class="perfil-campo">
                            <label>Ingresos Hoy</label>
                            <strong>€${(stats.ingresos_hoy || 0).toFixed(2)}</strong>
                        </div>
                        <div class="perfil-campo">
                            <label>Valoración Media</label>
                            <strong>${stats.valoracion_media ? stats.valoracion_media.toFixed(1) : '-'} / 5 
                                <i class="fas fa-star" style="color: #f39c12;"></i>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        `;

    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
}

// Cargar solicitudes pendientes
async function cargarSolicitudes() {
    try {
        const response = await fetchAuth(`${API_URL}/taxistas/solicitudes-pendientes`);
        const solicitudes = await response.json();

        const contenedor = document.getElementById('listaSolicitudes');
        
        if (solicitudes.length === 0) {
            contenedor.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No hay solicitudes pendientes</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = solicitudes.map(sol => `
            <div class="solicitud-card">
                <div class="solicitud-header">
                    <div>
                        <strong>Solicitud #${sol.id}</strong>
                        <span class="badge badge-warning">PENDIENTE</span>
                    </div>
                    <div class="solicitud-precio">€${sol.precio_estimado.toFixed(2)}</div>
                </div>
                
                <div style="margin: 1rem 0;">
                    <div class="ruta-punto">
                        <i class="fas fa-circle" style="color: #27ae60;"></i>
                        <div>
                            <strong>Recogida</strong><br>
                            <span class="text-muted">${sol.origen_direccion}</span>
                        </div>
                    </div>
                    <div class="ruta-punto">
                        <i class="fas fa-circle" style="color: #e74c3c;"></i>
                        <div>
                            <strong>Destino</strong><br>
                            <span class="text-muted">${sol.destino_direccion}</span>
                        </div>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-row">
                        <span><i class="fas fa-user"></i> Cliente:</span>
                        <strong>${sol.cliente_nombre}</strong>
                    </div>
                    <div class="info-row">
                        <span><i class="fas fa-road"></i> Distancia:</span>
                        <strong>${sol.distancia.toFixed(2)} km</strong>
                    </div>
                    <div class="info-row">
                        <span><i class="fas fa-clock"></i> Solicitado:</span>
                        <strong>${new Date(sol.fecha_solicitud).toLocaleTimeString('es-ES')}</strong>
                    </div>
                </div>

                <div class="solicitud-acciones">
                    <button class="btn btn-success" onclick="aceptarViaje(${sol.id}, ${sol.cliente_id})">
                        <i class="fas fa-check"></i> Aceptar
                    </button>
                    <button class="btn btn-secondary" onclick="verEnMapa(${sol.origen_lat}, ${sol.origen_lng})">
                        <i class="fas fa-map"></i> Ver en Mapa
                    </button>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error al cargar solicitudes:', error);
    }
}

// Aceptar viaje
async function aceptarViaje(viajeId, clienteId) {
    try {
        const response = await fetchAuth(`${API_URL}/taxistas/aceptar-viaje/${viajeId}`, {
            method: 'POST'
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error);
        }

        mostrarAlerta('✅ Viaje aceptado. Cliente notificado.', 'success');
        
        viajeActivo = data.viaje;
        
        // Emitir evento por WebSocket
        socket.emit('aceptar_viaje', {
            viajeId,
            taxistaId: taxistaInfo.id,
            clienteId
        });

        // Mostrar viaje activo
        mostrarViajeActivo(data.viaje);
        
        // Recargar solicitudes y estadísticas
        cargarSolicitudes();
        cargarEstadisticas();

    } catch (error) {
        mostrarAlerta('❌ Error al aceptar viaje: ' + error.message, 'danger');
    }
}

// Mostrar viaje activo
function mostrarViajeActivo(viaje) {
    document.getElementById('viajeActivoCard').style.display = 'block';
    document.getElementById('viajeActivoContenido').innerHTML = `
        <div style="padding: 2rem;">
            <h3>Viaje #${viaje.id}</h3>
            <div class="viaje-ruta">
                <div class="ruta-punto">
                    <i class="fas fa-circle" style="color: #27ae60;"></i>
                    <div>
                        <strong>Recogida</strong><br>
                        <span class="text-muted">${viaje.origen_direccion}</span>
                    </div>
                </div>
                <div class="ruta-punto">
                    <i class="fas fa-circle" style="color: #e74c3c;"></i>
                    <div>
                        <strong>Destino</strong><br>
                        <span class="text-muted">${viaje.destino_direccion}</span>
                    </div>
                </div>
            </div>
            
            <div class="info-box mt-2">
                <div class="info-row">
                    <span><i class="fas fa-user"></i> Cliente:</span>
                    <strong>${viaje.cliente_nombre}</strong>
                </div>
                <div class="info-row">
                    <span><i class="fas fa-phone"></i> Teléfono:</span>
                    <strong>${viaje.cliente_telefono}</strong>
                </div>
                <div class="info-row">
                    <span><i class="fas fa-euro-sign"></i> Precio estimado:</span>
                    <strong>€${viaje.precio_estimado.toFixed(2)}</strong>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                <button class="btn btn-primary" onclick="iniciarViaje(${viaje.id})">
                    <i class="fas fa-play"></i> Iniciar Viaje
                </button>
                <button class="btn btn-success" onclick="finalizarViaje(${viaje.id})">
                    <i class="fas fa-flag-checkered"></i> Finalizar
                </button>
                <button class="btn btn-info" onclick="abrirNavegacion(${viaje.origen_lat}, ${viaje.origen_lng})">
                    <i class="fas fa-directions"></i> Navegación
                </button>
            </div>
        </div>
    `;
}

// Iniciar viaje
async function iniciarViaje(viajeId) {
    try {
        const response = await fetchAuth(`${API_URL}/taxistas/iniciar-viaje/${viajeId}`, {
            method: 'POST'
        });

        mostrarAlerta('✅ Viaje iniciado', 'success');

    } catch (error) {
        mostrarAlerta('Error: ' + error.message, 'danger');
    }
}

// Finalizar viaje
async function finalizarViaje(viajeId) {
    const precioFinal = prompt('Ingresa el precio final del viaje:');
    
    if (!precioFinal) return;

    try {
        const response = await fetchAuth(`${API_URL}/taxistas/finalizar-viaje/${viajeId}`, {
            method: 'POST',
            body: JSON.stringify({ precioFinal: parseFloat(precioFinal) })
        });

        mostrarAlerta('✅ Viaje finalizado exitosamente', 'success');
        
        // Notificar al cliente
        socket.emit('finalizar_viaje', {
            viajeId,
            clienteId: viajeActivo.cliente_id
        });

        document.getElementById('viajeActivoCard').style.display = 'none';
        viajeActivo = null;
        
        cargarEstadisticas();
        cargarViajes();

    } catch (error) {
        mostrarAlerta('Error: ' + error.message, 'danger');
    }
}

// Ver en mapa
function verEnMapa(lat, lng) {
    map.setView([lat, lng], 15);
    mostrarSeccion('inicio');
}

// Abrir navegación externa
function abrirNavegacion(lat, lng) {
    const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
    window.open(url, '_blank');
}

// Cargar viajes
async function cargarViajes() {
    try {
        const response = await fetchAuth(`${API_URL}/taxistas/mis-viajes`);
        const viajes = await response.json();

        const contenedor = document.getElementById('listaViajes');
        
        if (viajes.length === 0) {
            contenedor.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-car"></i>
                    <p>No tienes viajes registrados</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = viajes.map(viaje => `
            <div class="viaje-card">
                <div class="viaje-header">
                    <div>
                        <span class="viaje-id">#${viaje.id}</span>
                        <span class="badge badge-${viaje.estado}">${viaje.estado.toUpperCase()}</span>
                    </div>
                    <span class="viaje-fecha">${new Date(viaje.fecha_solicitud).toLocaleString('es-ES')}</span>
                </div>
                
                <div class="viaje-ruta">
                    <div class="ruta-punto">
                        <i class="fas fa-circle" style="color: #27ae60;"></i>
                        <div>
                            <strong>Origen</strong><br>
                            <span class="text-muted">${viaje.origen_direccion}</span>
                        </div>
                    </div>
                    <div class="ruta-punto">
                        <i class="fas fa-circle" style="color: #e74c3c;"></i>
                        <div>
                            <strong>Destino</strong><br>
                            <span class="text-muted">${viaje.destino_direccion}</span>
                        </div>
                    </div>
                </div>

                <div class="viaje-info">
                    <div class="info-item">
                        <label>Cliente</label>
                        <strong>${viaje.cliente_nombre}</strong>
                    </div>
                    <div class="info-item">
                        <label>Distancia</label>
                        <strong>${viaje.distancia.toFixed(2)} km</strong>
                    </div>
                    <div class="info-item">
                        <label>Ingreso</label>
                        <strong>€${(viaje.precio_final || viaje.precio_estimado).toFixed(2)}</strong>
                    </div>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error al cargar viajes:', error);
    }
}

// Mostrar sección
function mostrarSeccion(seccion) {
    document.querySelectorAll('.seccion').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));

    document.getElementById(`seccion-${seccion}`).classList.add('active');
    event.target.closest('.nav-item').classList.add('active');

    const titulos = {
        'inicio': 'Panel de Control',
        'solicitudes': 'Solicitudes Pendientes',
        'viajes': 'Mis Viajes',
        'estadisticas': 'Estadísticas'
    };
    document.getElementById('headerTitle').textContent = titulos[seccion] || 'Panel';

    if (seccion === 'solicitudes') {
        cargarSolicitudes();
    } else if (seccion === 'viajes') {
        cargarViajes();
    } else if (seccion === 'estadisticas') {
        cargarEstadisticas();
    }
}

// Mostrar notificación
function mostrarNotificacion(mensaje, tipo = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${tipo}`;
    notification.innerHTML = `
        <i class="fas fa-bell"></i>
        <span>${mensaje}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
    
    // Reproducir sonido (opcional)
    try {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZUQ8PWKzn77BdGAg+ltryxnMpBSuBzvLZizcIGmi78OegNwkGZ7vy3JVLDg==');
        audio.play();
    } catch (e) {}
}
