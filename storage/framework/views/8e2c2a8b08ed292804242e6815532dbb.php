<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="api-url" content="<?php echo e(url('/api')); ?>">
    <title>LanzaTaxi · Panel Taxista</title>
    <meta name="description" content="Gestiona tus servicios y disponibilidad en tiempo real">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.ico')); ?>">
    
    <!-- Tailwind CSS (compilado) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/styles.css?v=20260211')); ?>" rel="stylesheet">
    
    <!-- Leaflet para mapas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50">
    
    <a href="#main-content" class="skip-link">
        Saltar al contenido principal
    </a>

    <!-- SIDEBAR - Panel taxista -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="<?php echo e(asset('img/logo_sin_fondo.png')); ?>" alt="LanzaTaxi" class="w-10 h-10 object-contain">
            <span class="text-xl font-bold text-[#1A1A1A]">LanzaTaxi</span>
        </div>
        
        <nav class="sidebar-nav">
            <a href="<?php echo e(route('home')); ?>" class="sidebar-nav-item mb-4 text-sm">
                <i class="fas fa-arrow-left"></i>
                Volver al inicio
            </a>
            
            <div class="divider"></div>
            
            <button onclick="mostrarSeccion('dashboard')" class="sidebar-nav-item active" id="nav-dashboard">
                <i class="fas fa-chart-pie"></i>
                Dashboard
            </button>
            
            <button onclick="mostrarSeccion('viajes')" class="sidebar-nav-item" id="nav-viajes">
                <i class="fas fa-route"></i>
                Mis Viajes
            </button>
            
            <button onclick="mostrarSeccion('ganancias')" class="sidebar-nav-item" id="nav-ganancias">
                <i class="fas fa-euro-sign"></i>
                Ganancias
            </button>
            
            <div class="divider"></div>
            
            <button onclick="cerrarSesion()" class="sidebar-nav-item text-red-600 hover:bg-red-50">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </button>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main id="main-content" class="main-content">
        
        <div class="panel-header">
            <h1 class="text-3xl font-bold text-[#1A1A1A]">Panel del Taxista</h1>
            <p class="text-gray-600">Gestión eficiente de servicios y disponibilidad</p>
        </div>

        <!-- SECCIÓN: DASHBOARD - BASADO EXACTAMENTE EN IMAGEN 3.png -->
        <section id="dashboard" class="grid lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Columna izquierda - Perfil y estado -->
            <div class="lg:col-span-1">
                <div class="card">
                    <!-- Perfil del taxista -->
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-[#FFD700] rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-[#1A1A1A]">JG</span>
                        </div>
                        <h2 class="text-2xl font-bold text-[#1A1A1A]">Juan García</h2>
                        <p class="text-sm text-gray-600">Taxi #4721 · Arrecife</p>
                    </div>
                    
                    <!-- Estado actual - EXACTO imagen 3.png -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Estado actual</span>
                            <span class="badge badge-available">
                                Disponible
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle text-[#0068CC]"></i>
                            Estás visible para recibir nuevos servicios
                        </p>
                    </div>
                    
                    <!-- Estadísticas rápidas -->
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600">Servicios hoy</p>
                            <p class="text-xl font-bold text-[#1A1A1A]">8</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600">Ingresos hoy</p>
                            <p class="text-xl font-bold text-[#10B981]">156€</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600">Valoración</p>
                            <p class="text-xl font-bold text-[#F59E0B]">4.8⭐</p>
                        </div>
                    </div>
                    
                    <!-- Cambiar estado - EXACTO imagen 3.png -->
                    <div class="mb-6">
                        <label class="form-label">Cambiar estado</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button onclick="cambiarEstado('disponible')" class="btn btn-success btn-sm !px-2">
                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                Disponible
                            </button>
                            <button onclick="cambiarEstado('ocupado')" class="btn btn-danger btn-sm !px-2">
                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                Ocupado
                            </button>
                            <button onclick="cambiarEstado('fuera')" class="btn btn-secondary btn-sm !px-2">
                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                Fuera
                            </button>
                        </div>
                    </div>
                    
                    <!-- Recordatorio - EXACTO imagen 3.png -->
                    <div class="bg-[#FFD700] bg-opacity-10 p-4 rounded-lg border border-[#FFD700] border-opacity-20">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-lightbulb text-[#F59E0B] mt-1"></i>
                            <div>
                                <p class="text-xs font-medium text-[#1A1A1A]">Recordatorio</p>
                                <p class="text-xs text-gray-600">
                                    Actualiza tu estado cuando termines un servicio para recibir nuevas solicitudes.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información del vehículo -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="font-semibold text-[#1A1A1A] mb-3">Mi vehículo</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-car text-gray-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-medium text-[#1A1A1A]">Toyota Prius</p>
                                <p class="text-sm text-gray-600">Matrícula: TF-2024-AR</p>
                                <p class="text-xs text-gray-500">Blanco · 4 plazas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna derecha - Cola de Servicios (EXACTO imagen 3.png) -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock text-[#0068CC] mr-2"></i>
                            Cola de Servicios
                        </h3>
                        <span class="badge badge-pending">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            2 pendientes
                        </span>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Servicio 1 - NUEVO (como en imagen) -->
                        <div class="service-item new">
                            <div class="service-header">
                                <div class="service-id">
                                    <strong>Servicio #2847</strong>
                                    <span>NUEVO</span>
                                    <span class="text-xs text-gray-500">Solicitado hace 2 min</span>
                                </div>
                                <div class="service-price">18.50€</div>
                            </div>
                            
                            <div class="service-route">
                                <div class="route-point">
                                    <i class="fas fa-circle text-[10px] text-[#10B981] mt-1.5"></i>
                                    <span>Calle Real 45, Arrecife</span>
                                </div>
                                <div class="route-point">
                                    <i class="fas fa-flag-checkered text-[#EF4444] mt-1.5"></i>
                                    <span>Hotel Fariones, Puerto del Carmen</span>
                                </div>
                            </div>
                            
                            <div class="route-details">
                                <span><i class="fas fa-route mr-1"></i>12.5 km</span>
                                <span><i class="fas fa-clock mr-1"></i>18 min</span>
                                <span><i class="fas fa-user mr-1"></i>Cliente: 4.8⭐</span>
                            </div>
                            
                            <div class="service-actions">
                                <button class="btn btn-success flex-1" onclick="aceptarServicio()">
                                    <i class="fas fa-check-circle"></i>
                                    Aceptar
                                </button>
                                <button class="btn btn-danger flex-1">
                                    <i class="fas fa-times-circle"></i>
                                    Rechazar
                                </button>
                            </div>
                        </div>
                        
                        <!-- Servicio 2 - PROGRAMADO (como en imagen) -->
                        <div class="service-item scheduled">
                            <div class="service-header">
                                <div class="service-id">
                                    <strong>Servicio #2848</strong>
                                    <span class="bg-blue-100 text-[#0068CC]">PROGRAMADO</span>
                                    <span class="text-xs text-gray-500">Hoy a las 16:30</span>
                                </div>
                                <div class="service-price">24.00€</div>
                            </div>
                            
                            <div class="service-route">
                                <div class="route-point">
                                    <i class="fas fa-circle text-[10px] text-[#10B981] mt-1.5"></i>
                                    <span>Aeropuerto de Lanzarote (ACE)</span>
                                </div>
                                <div class="route-point">
                                    <i class="fas fa-flag-checkered text-[#EF4444] mt-1.5"></i>
                                    <span>Hotel Volcán, Playa Blanca</span>
                                </div>
                            </div>
                            
                            <div class="route-details">
                                <span><i class="fas fa-route mr-1"></i>14.2 km</span>
                                <span><i class="fas fa-clock mr-1"></i>22 min</span>
                                <span><i class="fas fa-suitcase mr-1"></i>Cliente con equipaje</span>
                            </div>
                            
                            <div class="service-actions">
                                <button class="btn btn-outline flex-1">
                                    <i class="fas fa-map"></i>
                                    Ver en Mapa
                                </button>
                                <button class="btn btn-secondary flex-1">
                                    <i class="fas fa-calendar-check"></i>
                                    Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <a href="#" class="text-sm text-[#0068CC] hover:underline flex items-center justify-center gap-1">
                            Ver historial completo
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Mapa en tiempo real -->
                <div class="card mt-6" id="mapaClienteCard">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-location-dot text-[#0068CC] mr-2"></i>
                            Ubicación del cliente
                        </h3>
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-sync-alt animate-pulse-subtle mr-1"></i>
                            Tiempo real
                        </span>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-lg text-center mb-4 hidden" id="mapaSinClienteMsg">
                        <i class="fas fa-map text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">Sin cliente en ruta</p>
                        <p class="text-xs text-gray-400 mt-1">Cuando aceptes un servicio, verás la ubicación del cliente aquí</p>
                    </div>
                    <div id="mapaCliente" class="map-container h-[300px] hidden"></div>
                    <div class="flex justify-between items-center mt-4">
                        <div class="flex gap-4">
                            <span class="flex items-center gap-1 text-xs">
                                <span class="w-3 h-3 bg-[#10B981] rounded-full"></span>
                                Taxis disponibles
                            </span>
                            <span class="flex items-center gap-1 text-xs">
                                <span class="w-3 h-3 bg-[#F59E0B] rounded-full"></span>
                                Ocupados
                            </span>
                        </div>
                        <button class="btn btn-sm btn-outline">
                            <i class="fas fa-location-dot"></i>
                            Mi ubicación
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: MIS VIAJES -->
        <section id="viajes" class="hidden">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Historial de viajes realizados</h3>
                    <div class="flex gap-2">
                        <label class="sr-only" for="filtroViajes">Filtrar viajes</label>
                        <select id="filtroViajes" class="form-input !py-2 !w-auto">
                            <option>Hoy</option>
                            <option>Esta semana</option>
                            <option>Este mes</option>
                            <option>Personalizado</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha/Hora</th>
                                <th>Cliente</th>
                                <th>Origen → Destino</th>
                                <th>Distancia</th>
                                <th>Precio</th>
                                <th>Valoración</th>
                            </tr>
                        </thead>
                        <tbody id="tablaViajes">
                            <tr>
                                <td class="font-medium">#2845</td>
                                <td>15/01/2025 14:30</td>
                                <td>María López</td>
                                <td>Aeropuerto → Hotel Fariones</td>
                                <td>12.5 km</td>
                                <td class="font-bold">18.50€</td>
                                <td>
                                    <span class="flex items-center gap-1">
                                        5.0 <i class="fas fa-star text-[#FFD700]"></i>
                                    </span>
                                </td>
                            </tr>
                            <!-- Más filas... -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: GANANCIAS -->
        <section id="ganancias" class="hidden">
            <div class="grid md:grid-cols-4 gap-6 mb-6">
                <div class="stat-card">
                    <div class="stat-label">Ingresos hoy</div>
                    <div class="stat-value">156€</div>
                    <div class="stat-trend">↑ +12% vs ayer</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Ingresos semana</div>
                    <div class="stat-value">842€</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Ingresos mes</div>
                    <div class="stat-value">2,450€</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Comisión LanzaTaxi</div>
                    <div class="stat-value">245€</div>
                </div>
            </div>
            
            <div class="card">
                <h3 class="card-title mb-4">Desglose de ganancias</h3>
                <!-- Aquí iría un gráfico -->
                <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-4xl text-gray-300 mb-2"></i>
                        <p>Gráfico de ganancias - Próximamente</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function mostrarSeccion(seccion) {
            document.querySelectorAll('section').forEach(s => s.classList.add('hidden'));
            document.getElementById(seccion).classList.remove('hidden');
            
            document.querySelectorAll('.sidebar-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            document.getElementById(`nav-${seccion}`).classList.add('active');
        }
        
        function cambiarEstado(estado) {
            const estados = {
                'disponible': 'badge-available',
                'ocupado': 'badge-occupied',
                'fuera': 'badge-offline'
            };
            
            alert(`Estado cambiado a: ${estado}`);
            // Aquí iría la lógica real
        }
        
        function cerrarSesion() {
            window.location.href = 'index.html';
        }
        
        // Variable global para mapa del cliente
        let mapaClienteActivo = null;
        
        // Función para inicializar mapa cuando hay un cliente en ruta
        function iniciarMapaCliente() {
            // Verificar que Leaflet esté disponible
            if (typeof L === 'undefined') {
                console.error('Leaflet no está cargado todavía');
                return;
            }
            
            if (document.getElementById('mapaCliente') && !mapaClienteActivo) {
                document.getElementById('mapaCliente').classList.remove('hidden');
                document.getElementById('mapaSinClienteMsg').classList.add('hidden');
                
                mapaClienteActivo = L.map('mapaCliente').setView([28.963, -13.547], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(mapaClienteActivo);
                
                // Ubicación del taxista (verde)
                L.marker([28.963, -13.547], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(mapaClienteActivo)
                    .bindPopup('🚕 Tu ubicación')
                    .openPopup();
                
                // Ubicación del cliente (rojo)
                L.marker([28.960, -13.545], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(mapaClienteActivo)
                    .bindPopup('📍 Cliente: Juan Díaz')
                    .openPopup();
                
                // Línea de ruta
                const ruta = L.polyline([
                    [28.963, -13.547],
                    [28.960, -13.545]
                ], {
                    color: '#0068CC',
                    weight: 2,
                    opacity: 0.8,
                    dashArray: '5, 5'
                }).addTo(mapaClienteActivo);
            }
        }
        
        // Función para ocultar mapa cuando no hay cliente
        function ocultarMapaCliente() {
            if (mapaClienteActivo) {
                mapaClienteActivo.remove();
                mapaClienteActivo = null;
            }
            document.getElementById('mapaCliente').classList.add('hidden');
            document.getElementById('mapaSinClienteMsg').classList.remove('hidden');
        }
        
        // Mostrar mapa cuando se acepta un servicio
        function aceptarServicio() {
            alert('✅ Servicio aceptado. Cliente ubicado en el mapa.');
            iniciarMapaCliente();
        }
        
        // Inicializar - mostrar mensaje sin cliente
        window.addEventListener('load', function() {
            document.getElementById('mapaSinClienteMsg').classList.remove('hidden');
            document.getElementById('mapaCliente').classList.add('hidden');
        });
    </script>
    
    <!-- Script de autenticación -->
    <script src="<?php echo e(asset('js/auth.js')); ?>"></script>
    <script src="<?php echo e(asset('js/taxista.js')); ?>"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\LanzaTaxi\resources\views/taxista.blade.php ENDPATH**/ ?>