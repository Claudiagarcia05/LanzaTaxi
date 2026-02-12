// Admin Dashboard JavaScript
<<<<<<< HEAD
const API_URL = 'http://localhost:3000/api';
=======
// API_URL ya está definido en auth.js
>>>>>>> origin/master
let map, taxistasMarkers = [];
let socket;

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    console.log('Panel Admin cargado');
    cargarDashboard();
    cargarTarifas();
});

<<<<<<< HEAD
// Mostrar sección
function mostrarSeccion(seccion) {
    document.querySelectorAll('section').forEach(s => s.classList.add('hidden'));
    document.querySelectorAll('button[onclick*="mostrarSeccion"]').forEach(btn => btn.classList.remove('active'));
    
    const section = document.getElementById(seccion);
    if (section) {
        section.classList.remove('hidden');
    }
    
    event.target.classList.add('active');
    
    const titles = {
        'dashboard': 'Panel Administrativo',
        'usuarios': 'Gestión de Usuarios',
        'taxistas': 'Gestión de Taxistas',
        'viajes': 'Gestión de Viajes',
        'tarifas': 'Gestión de Tarifas'
    };
    document.getElementById('headerTitle').textContent = titles[seccion] || 'Panel';
}

function cerrarSesion() {
    if (confirm('¿Está seguro de que desea cerrar sesión?')) {
        window.location.href = 'index.html';
    }
}

function abrirModalTaxista() {
    LanzaTaxi.showNotification('Funcionalidad de creación de taxistas en desarrollo', 'info');
}

function cargarTaxistas() {
    console.log('Cargando taxistas');
}

function cargarViajes() {
    console.log('Cargando viajes');
}

function cargarTarifas() {
    console.log('Cargas de tarifas');
}
    });
=======
function cerrarSesion() {
    if (confirm('¿Está seguro de que desea cerrar sesión?')) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/';
    }
}

function abrirModalTaxista() {
    alert('Funcionalidad de creación de taxistas en desarrollo');
>>>>>>> origin/master
}

// Cargar dashboard
async function cargarDashboard() {
    try {
        const response = await fetchAuth(`${API_URL}/admin/dashboard`);
        const data = await response.json();

        // Actualizar stats solo si los elementos existen
        const statViajesHoy = document.getElementById('statViajesHoy');
        const statIngresosHoy = document.getElementById('statIngresosHoy');
        const statTaxistasActivos = document.getElementById('statTaxistasActivos');
        const statTotalClientes = document.getElementById('statTotalClientes');

        if (statViajesHoy) {
            const viajesHoy = data.viajesHoy?.total || 0;
            statViajesHoy.textContent = viajesHoy;
        }
        
        if (statIngresosHoy) {
            const ingresosHoy = data.viajesHoy?.ingresos || 0;
            statIngresosHoy.textContent = `€${ingresosHoy.toFixed(2)}`;
        }
        
        if (statTaxistasActivos) {
            const taxistasLibres = data.taxistasEstado?.find(t => t.estado === 'libre')?.total || 0;
            statTaxistasActivos.textContent = taxistasLibres;
        }
        
        if (statTotalClientes) {
            const totalClientes = data.usuarios?.find(u => u.role === 'cliente')?.total || 0;
            statTotalClientes.textContent = totalClientes;
        }

        // Gráfico de viajes por hora
        // crearGraficoViajesPorHora(data.viajesPorHora);

        // Gráfico de viajes por municipio
        // crearGraficoViajesPorMunicipio(data.viajesPorMunicipio);

        // Top taxistas
        // mostrarTopTaxistas(data.topTaxistas);

    } catch (error) {
        console.error('Error al cargar dashboard:', error);
    }
}

// Crear gráfico de viajes por hora
function crearGraficoViajesPorHora(datos) {
    const ctx = document.getElementById('chartViajesPorHora');
    
    // Destruir gráfico anterior si existe
    if (charts.viajesPorHora) {
        charts.viajesPorHora.destroy();
    }

    // Preparar datos
    const horas = Array.from({ length: 24 }, (_, i) => i);
    const valores = horas.map(hora => {
        const dato = datos.find(d => parseInt(d.hora) === hora);
        return dato ? dato.total : 0;
    });

    charts.viajesPorHora = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: horas.map(h => `${h}:00`),
            datasets: [{
                label: 'Número de Viajes',
                data: valores,
                backgroundColor: 'rgba(243, 156, 18, 0.6)',
                borderColor: 'rgba(243, 156, 18, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Crear gráfico de viajes por municipio
function crearGraficoViajesPorMunicipio(datos) {
    const ctx = document.getElementById('chartViajesPorMunicipio');
    
    if (charts.viajesPorMunicipio) {
        charts.viajesPorMunicipio.destroy();
    }

    if (!datos || datos.length === 0) {
        return;
    }

    const colores = [
        'rgba(255, 99, 132, 0.6)',
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 206, 86, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(153, 102, 255, 0.6)',
        'rgba(255, 159, 64, 0.6)',
        'rgba(201, 203, 207, 0.6)'
    ];

    charts.viajesPorMunicipio = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: datos.map(d => d.municipio),
            datasets: [{
                data: datos.map(d => d.total),
                backgroundColor: colores.slice(0, datos.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Mostrar top taxistas
function mostrarTopTaxistas(taxistas) {
    const contenedor = document.getElementById('topTaxistas');
    
    if (!taxistas || taxistas.length === 0) {
        contenedor.innerHTML = '<div class="empty-state"><p>No hay datos disponibles</p></div>';
        return;
    }

    contenedor.innerHTML = `
        <table class="tabla-viajes">
            <thead>
                <tr>
                    <th>Posición</th>
                    <th>Nombre</th>
                    <th>Licencia</th>
                    <th>Viajes</th>
                    <th>Ingresos</th>
                    <th>Valoración</th>
                </tr>
            </thead>
            <tbody>
                ${taxistas.map((t, index) => {
                    const ingresosValor = Number.parseFloat(t.ingresos || 0);
                    const ingresos = Number.isFinite(ingresosValor) ? ingresosValor.toFixed(2) : '0.00';
                    const valoracionValor = Number.parseFloat(t.valoracion_media);
                    const valoracion = Number.isFinite(valoracionValor) ? valoracionValor.toFixed(1) : '-';

                    return `
                    <tr>
                        <td>
                            ${index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : index + 1}
                        </td>
                        <td>${t.nombre}</td>
                        <td>${t.licencia}</td>
                        <td>${t.total_viajes}</td>
                        <td>€${ingresos}</td>
                        <td>
                            <i class="fas fa-star" style="color: #f39c12;"></i>
                            ${valoracion}
                        </td>
                    </tr>
                `;
                }).join('')}
            </tbody>
        </table>
    `;
}

// Cargar viajes
async function cargarViajes() {
    try {
        const filtroEstado = document.getElementById('filtroEstado');
        const estado = filtroEstado ? filtroEstado.value : '';
        const url = estado ? `${API_URL}/admin/viajes?estado=${estado}` : `${API_URL}/admin/viajes`;
        
        const response = await fetchAuth(url);
        const viajes = await response.json();

        const contenedor = document.getElementById('listaViajes');
        
        if (!contenedor) {
            console.warn('Contenedor listaViajes no encontrado');
            return;
        }
        
        if (viajes.length === 0) {
            contenedor.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No hay viajes</p></div>';
            return;
        }

        contenedor.innerHTML = viajes.map(viaje => {
            const distanciaValor = Number.parseFloat(viaje.distancia);
            const distancia = Number.isFinite(distanciaValor) ? distanciaValor.toFixed(2) + ' km' : 'N/A';
            const precioValor = Number.parseFloat(viaje.precio_final ?? viaje.precio_estimado ?? 0);
            const precio = Number.isFinite(precioValor) ? precioValor.toFixed(2) : '0.00';

            return `
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
                        <label>Taxista</label>
                        <strong>${viaje.taxista_nombre || 'Sin asignar'}</strong>
                    </div>
                    <div class="info-item">
                        <label>Distancia</label>
                        <strong>${distancia}</strong>
                    </div>
                    <div class="info-item">
                        <label>Precio</label>
                        <strong>€${precio}</strong>
                    </div>
                </div>

                ${viaje.estado === 'pendiente' || viaje.estado === 'aceptado' ? `
                    <div class="viaje-acciones">
                        <button class="btn btn-sm btn-danger" onclick="cancelarViaje(${viaje.id})">
                            <i class="fas fa-times"></i> Cancelar Viaje
                        </button>
                    </div>
                ` : ''}
            </div>
        `;
        }).join('');

    } catch (error) {
        console.error('Error al cargar viajes:', error);
    }
}

// Cancelar viaje
async function cancelarViaje(viajeId) {
    if (!confirm('¿Estás seguro de cancelar este viaje?')) {
        return;
    }

    try {
        const response = await fetchAuth(`${API_URL}/admin/viajes/${viajeId}/cancelar`, {
            method: 'POST'
        });

        mostrarAlerta('Viaje cancelado', 'success');
        cargarViajes();
        cargarDashboard();

    } catch (error) {
        mostrarAlerta('Error al cancelar viaje: ' + error.message, 'danger');
    }
}

// Cargar taxistas
async function cargarTaxistas() {
    try {
        const response = await fetchAuth(`${API_URL}/admin/taxistas`);
        const taxistas = await response.json();

        const contenedor = document.getElementById('listaTaxistas');
        
        if (!contenedor) {
            console.warn('Contenedor listaTaxistas no encontrado');
            return;
        }
        
        if (taxistas.length === 0) {
            contenedor.innerHTML = '<div class="empty-state"><p>No hay taxistas registrados</p></div>';
            return;
        }

        contenedor.innerHTML = `
            <table class="tabla-viajes">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Licencia</th>
                        <th>Municipio</th>
                        <th>Matrícula</th>
                        <th>Estado</th>
                        <th>Valoración</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${taxistas.map(t => {
                        const valoracionValor = Number.parseFloat(t.valoracion_media);
                        const valoracion = Number.isFinite(valoracionValor) ? valoracionValor.toFixed(1) : '-';

                        return `
                        <tr>
                            <td>${t.nombre}</td>
                            <td>${t.email}</td>
                            <td>${t.licencia}</td>
                            <td>${t.municipio}</td>
                            <td>${t.matricula}</td>
                            <td><span class="badge badge-${t.estado === 'libre' ? 'success' : t.estado === 'ocupado' ? 'danger' : 'warning'}">${t.estado.toUpperCase()}</span></td>
                            <td>
                                <i class="fas fa-star" style="color: #f39c12;"></i>
                                ${valoracion}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(${t.user_id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    }).join('')}
                </tbody>
            </table>
        `;

    } catch (error) {
        console.error('Error al cargar taxistas:', error);
    }
}

// Cargar clientes
async function cargarClientes() {
    try {
        const response = await fetchAuth(`${API_URL}/admin/usuarios?role=cliente`);
        const clientes = await response.json();

        const contenedor = document.getElementById('listaClientes');
        
        if (clientes.length === 0) {
            contenedor.innerHTML = '<div class="empty-state"><p>No hay clientes registrados</p></div>';
            return;
        }

        contenedor.innerHTML = `
            <table class="tabla-viajes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${clientes.map(c => `
                        <tr>
                            <td>#${c.id}</td>
                            <td>${c.nombre}</td>
                            <td>${c.email}</td>
                            <td>${c.telefono || 'N/A'}</td>
                            <td>${new Date(c.created_at).toLocaleDateString('es-ES')}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(${c.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

    } catch (error) {
        console.error('Error al cargar clientes:', error);
    }
}

// Eliminar usuario
async function eliminarUsuario(userId) {
    if (!confirm('¿Estás seguro de eliminar este usuario?')) {
        return;
    }

    try {
        const response = await fetchAuth(`${API_URL}/admin/usuarios/${userId}`, {
            method: 'DELETE'
        });

        mostrarAlerta('Usuario eliminado', 'success');
        cargarTaxistas();
        cargarClientes();
        cargarDashboard();

    } catch (error) {
        mostrarAlerta('Error al eliminar usuario: ' + error.message, 'danger');
    }
}

// Cargar tarifas
async function cargarTarifas() {
    try {
        const response = await fetchAuth(`${API_URL}/tarifas`);
        const tarifas = await response.json();

        const tarifa1 = tarifas.find(t => t.nombre === 'Tarifa 1 - Urbana');
        const tarifa2 = tarifas.find(t => t.nombre === 'Tarifa 2 - Interurbana');

        const tarifaUrbanaEl = document.getElementById('tarifaUrbana');
        const tarifaInterurbanaEl = document.getElementById('tarifaInterurbana');

        if (tarifa1 && tarifaUrbanaEl) {
            tarifaUrbanaEl.innerHTML = renderTarifa(tarifa1);
        }
        if (tarifa2 && tarifaInterurbanaEl) {
            tarifaInterurbanaEl.innerHTML = renderTarifa(tarifa2);
        }

    } catch (error) {
        console.error('Error al cargar tarifas:', error);
    }
}

// Render tarifa
function renderTarifa(tarifa) {
    const toMoney = (value) => {
        const numero = Number.parseFloat(value);
        return Number.isFinite(numero) ? numero.toFixed(2) : '0.00';
    };

    return `
        <div style="padding: 2rem;">
            <div class="perfil-info">
                <div class="perfil-campo">
                    <label>Bajada de Bandera</label>
                    <strong>€${toMoney(tarifa.bajada_bandera)}</strong>
                </div>
                <div class="perfil-campo">
                    <label>Precio por km</label>
                    <strong>€${toMoney(tarifa.precio_km)}</strong>
                </div>
                <div class="perfil-campo">
                    <label>Supl. Aeropuerto</label>
                    <strong>€${toMoney(tarifa.suplemento_aeropuerto)}</strong>
                </div>
                <div class="perfil-campo">
                    <label>Supl. Puerto</label>
                    <strong>€${toMoney(tarifa.suplemento_puerto)}</strong>
                </div>
                <div class="perfil-campo">
                    <label>Supl. Nocturno</label>
                    <strong>€${toMoney(tarifa.suplemento_nocturno)}</strong>
                </div>
                <div class="perfil-campo">
                    <label>Supl. Festivo</label>
                    <strong>€${toMoney(tarifa.suplemento_festivo)}</strong>
                </div>
            </div>
            <button class="btn btn-primary mt-2" onclick="editarTarifa(${tarifa.id})">
                <i class="fas fa-edit"></i> Editar Tarifa
            </button>
        </div>
    `;
}

// Editar tarifa
function editarTarifa(tarifaId) {
    mostrarAlerta('Funcionalidad de edición de tarifas disponible en versión completa', 'info');
}

// Inicializar mapa
function inicializarMapa() {
    map = L.map('map').setView([28.9636, -13.5477], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    cargarUbicacionesTaxistas();
}

// Cargar ubicaciones de taxistas
async function cargarUbicacionesTaxistas() {
    try {
        const response = await fetchAuth(`${API_URL}/admin/taxistas/ubicaciones`);
        const ubicaciones = await response.json();

        // Limpiar marcadores anteriores
        taxistasMarkers.forEach(marker => map.removeLayer(marker));
        taxistasMarkers = [];

        // Agregar nuevos marcadores
        ubicaciones.forEach(taxista => {
            const color = taxista.estado === 'libre' ? 'green' : taxista.estado === 'ocupado' ? 'red' : 'yellow';
            
            const marker = L.marker([taxista.latitud, taxista.longitud], {
                icon: L.icon({
                    iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map);

            marker.bindPopup(`
                <strong>${taxista.nombre}</strong><br>
                <strong>Licencia:</strong> ${taxista.licencia}<br>
                <strong>Estado:</strong> ${taxista.estado.toUpperCase()}<br>
                <strong>Matrícula:</strong> ${taxista.matricula}
            `);

            taxistasMarkers.push({ id: taxista.id, marker });
        });

    } catch (error) {
        console.error('Error al cargar ubicaciones:', error);
    }
}

// Actualizar marcador de taxista en tiempo real
function actualizarMarcadorTaxista(data) {
    const taxistaMarker = taxistasMarkers.find(m => m.id === data.taxistaId);
    
    if (taxistaMarker) {
        taxistaMarker.marker.setLatLng([data.latitud, data.longitud]);
    } else {
        // Si no existe, recargar todos
        cargarUbicacionesTaxistas();
    }
}

// Modal taxista
function abrirModalTaxista() {
    document.getElementById('modalTaxista').style.display = 'block';
}

function cerrarModalTaxista() {
    document.getElementById('modalTaxista').style.display = 'none';
}

// Crear taxista
async function crearTaxista(event) {
    event.preventDefault();

    const datos = {
        nombre: document.getElementById('taxistaNombre').value,
        email: document.getElementById('taxistaEmail').value,
        telefono: document.getElementById('taxistaTelefono').value,
        password: document.getElementById('taxistaPassword').value,
        licencia: document.getElementById('taxistaLicencia').value,
        municipio: document.getElementById('taxistaMunicipio').value,
        matricula: document.getElementById('taxistaMatricula').value,
        modeloVehiculo: document.getElementById('taxistaModelo').value
    };

    try {
        const response = await fetchAuth(`${API_URL}/admin/taxistas`, {
            method: 'POST',
            body: JSON.stringify(datos)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error);
        }

        mostrarAlerta('✅ Taxista creado exitosamente', 'success');
        cerrarModalTaxista();
        cargarTaxistas();
        cargarDashboard();

    } catch (error) {
        mostrarAlerta('Error: ' + error.message, 'danger');
    }
}

// Mostrar sección
function mostrarSeccion(seccion) {
    // Ocultar todas las secciones
    const secciones = ['dashboard', 'usuarios', 'taxistas', 'tarifas'];
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

    // Actualizar título
    const titulos = {
        'dashboard': 'Panel Administrativo',
        'usuarios': 'Gestión de Usuarios',
        'taxistas': 'Gestión de Taxistas',
        'tarifas': 'Gestión de Tarifas'
    };
    
    const headerTitle = document.getElementById('headerTitle');
    if (headerTitle) {
        headerTitle.textContent = titulos[seccion] || 'Panel Admin';
    }

    // Cargar datos según la sección
    if (seccion === 'taxistas') {
        cargarTaxistas();
    } else if (seccion === 'usuarios') {
        // cargarUsuarios();
    } else if (seccion === 'dashboard') {
        cargarDashboard();
    } else if (seccion === 'tarifas') {
        cargarTarifas();
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
}
