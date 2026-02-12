<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-url" content="{{ url('/api') }}">
    <title>LanzaTaxi · Panel Cliente</title>
    <meta name="description" content="Solicita tu taxi en Lanzarote - Seguimiento en tiempo real">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">
    
    <!-- Tailwind CSS (compilado) -->
    <link href="{{ asset('css/tailwind.css?v=20260212') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css?v=20260211') }}" rel="stylesheet">
    
    <!-- Leaflet para mapas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- SKIP LINK - Accesibilidad -->
    <a href="#main-content" class="skip-link">
        Saltar al contenido principal
    </a>

    <!-- SIDEBAR - Igual que en imagen 2.png -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="w-10 h-10 bg-[#FFD700] rounded-lg flex items-center justify-center">
                <i class="fas fa-taxi text-[#1A1A1A] text-xl"></i>
            </div>
            <span class="text-xl font-bold text-[#1A1A1A]">LanzaTaxi</span>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('home') }}" class="sidebar-nav-item mb-4 text-sm">
                <i class="fas fa-arrow-left"></i>
                Volver al inicio
            </a>
            
            <div class="divider"></div>
            
            <button onclick="mostrarSeccion('solicitar')" class="sidebar-nav-item active" id="nav-solicitar">
                <i class="fas fa-taxi"></i>
                Solicitar Taxi
            </button>
            
            <button onclick="mostrarSeccion('historial')" class="sidebar-nav-item" id="nav-historial">
                <i class="fas fa-history"></i>
                Historial de Viajes
            </button>
            
            <button onclick="mostrarSeccion('perfil')" class="sidebar-nav-item" id="nav-perfil">
                <i class="fas fa-user"></i>
                Mi Perfil
            </button>
            
            <div class="divider"></div>
            
            <button onclick="cerrarSesion()" class="sidebar-nav-item text-red-600 hover:bg-red-50">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </button>
        </nav>
        
        <!-- Información del usuario (sidebar footer) -->
        <div class="absolute bottom-6 left-6 right-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#FFD700] rounded-full flex items-center justify-center">
                        <span class="font-bold text-[#1A1A1A]">JD</span>
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-[#1A1A1A]">Juan Díaz</p>
                        <p class="text-xs text-gray-600">Cliente desde 2025</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main id="main-content" class="main-content">
        
        <!-- HEADER -->
        <div class="panel-header">
            <h1 class="text-3xl font-bold text-[#1A1A1A]">Panel del Cliente</h1>
            <p class="text-gray-600">Interfaz intuitiva para solicitar taxis y gestionar viajes</p>
        </div>

        <!-- SECCIÓN: SOLICITAR TAXI (Basado EXACTAMENTE en imagen 2.png) -->
        <section id="solicitar" class="grid lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Columna izquierda - Formulario de solicitud -->
            <div class="lg:col-span-1">
                <div class="card">
                    <!-- Logo y título -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-[#FFD700] rounded-xl flex items-center justify-center">
                            <i class="fas fa-taxi text-[#1A1A1A] text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-[#1A1A1A]">LanzaTaxi</h2>
                            <p class="text-xs text-gray-500">¿A dónde vamos?</p>
                        </div>
                    </div>
                    
                    <!-- Información del usuario -->
                    <div class="bg-gray-50 p-3 rounded-lg mb-6 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-600">Pasajero</p>
                            <p class="font-semibold text-[#1A1A1A]" id="userName">Juan Díaz</p>
                        </div>
                        <div class="badge badge-available">
                            Verificado
                        </div>
                    </div>
                    
                    <!-- Formulario -->
                    <form id="formSolicitud" class="space-y-4">
                        <!-- Origen - Ubicación actual -->
                        <div>
                            <label class="form-label">Origen - Tu ubicación actual</label>
                            <div class="input-group">
                                <div class="input-icon flex-1">
                                    <i class="fas fa-map-pin"></i>
                                    <input type="text" id="origenInput" class="form-input" 
                                           placeholder="Calle Real 45, Arrecife" 
                                           value="Calle Real 45, Arrecife">
                                </div>
                                <button type="button" class="btn btn-secondary !px-3" onclick="usarUbicacionActual()" title="Usar mi ubicación actual">
                                    <i class="fas fa-location-dot"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Destino -->
                        <div>
                            <label class="form-label">Destino - ¿A dónde vas?</label>
                            <div class="input-icon">
                                <i class="fas fa-flag-checkered"></i>
                                <input type="text" id="destinoInput" class="form-input" 
                                       placeholder="Ej: Aeropuerto, Puerto del Carmen...">
                            </div>
                        </div>
                        
                        <!-- Información del viaje - EXACTO de imagen 2.png -->
                        <div class="bg-blue-50 bg-opacity-50 border border-blue-100 rounded-lg p-5 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Distancia estimada</span>
                                <span class="font-semibold text-[#1A1A1A]" id="distancia">12.5 km</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tiempo estimado</span>
                                <span class="font-semibold text-[#1A1A1A]" id="tiempo">18 min</span>
                            </div>
                            <div class="divider !my-2"></div>
                            <div class="flex justify-between text-lg">
                                <span class="font-bold text-[#1A1A1A]">Precio estimado</span>
                                <span class="font-bold text-[#0068CC] text-2xl" id="precio">18.50€</span>
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-taxi"></i>
                            Pedir Ahora
                        </button>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" class="btn btn-secondary">
                                <i class="fas fa-wheelchair"></i>
                                Accesible
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="programarViaje()">
                                <i class="fas fa-calendar"></i>
                                Programar
                            </button>
                        </div>
                        
                        <p class="text-xs text-center text-gray-500 mt-4">
                            <i class="fas fa-shield-alt text-[#0068CC]"></i>
                            Pago seguro. Tarifa oficial del Cabildo de Lanzarote.
                        </p>
                    </form>
                </div>
                
                <!-- Mapa de taxistas disponibles -->
                <div id="taxistasDisponiblesCard" class="card mt-6">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map text-[#0068CC] mr-2"></i>
                            Taxistas disponibles en tu zona
                        </h3>
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-sync-alt animate-pulse-subtle mr-1"></i>
                            Tiempo real
                        </span>
                    </div>
                    <div id="mapaTaxistasDisponibles" class="h-64 rounded-lg mb-4"></div>
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div class="bg-green-50 p-3 rounded-lg">
                            <div class="flex justify-center mb-1">
                                <span class="w-4 h-4 bg-[#10B981] rounded-full"></span>
                            </div>
                            <p class="font-bold text-[#1A1A1A]">12</p>
                            <p class="text-gray-600 text-xs">Disponibles</p>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <div class="flex justify-center mb-1">
                                <span class="w-4 h-4 bg-[#F59E0B] rounded-full"></span>
                            </div>
                            <p class="font-bold text-[#1A1A1A]">8</p>
                            <p class="text-gray-600 text-xs">Ocupados</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <div class="flex justify-center mb-1">
                                <span class="w-4 h-4 bg-[#0068CC] rounded-full"></span>
                            </div>
                            <p class="font-bold text-[#1A1A1A]">3.2 km</p>
                            <p class="text-gray-600 text-xs">Más cercano</p>
                        </div>
                    </div>
                </div>
                
                <!-- Mapa de seguimiento (aparece cuando hay viaje activo) -->
                <div id="viajeActivoCard" class="card mt-6 hidden">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-car text-[#10B981] mr-2"></i>
                            Tu taxi está en camino
                        </h3>
                        <span class="badge badge-available">Llega en 3 min</span>
                    </div>
                    <div id="mapaSeguimiento" class="h-48 rounded-lg mb-4"></div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-[#1A1A1A]">Juan García</p>
                            <p class="text-sm text-gray-600">Toyota Prius · TF-2024-AR</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Matrícula</p>
                            <p class="font-bold text-[#1A1A1A]">4721 JKX</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna derecha - Historial de viajes (EXACTO de imagen 2.png) -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history text-[#0068CC] mr-2"></i>
                            Historial de Viajes
                        </h3>
                        <a href="#" class="text-sm text-[#0068CC] hover:underline flex items-center gap-1">
                            Ver todo
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Viaje 1 - EXACTO como en la imagen -->
                        <div class="trip-item">
                            <div class="trip-info">
                                <h4 class="flex items-center gap-2">
                                    <span class="font-semibold">Aeropuerto</span>
                                    <i class="fas fa-arrow-right text-gray-400 text-sm"></i>
                                    <span class="font-semibold">Hotel Fariones</span>
                                </h4>
                                <div class="trip-meta">
                                    <span><i class="far fa-calendar mr-1"></i>15 Ene 2025</span>
                                    <span><i class="far fa-clock mr-1"></i>14:30</span>
                                    <span class="badge !py-0.5 bg-gray-100 text-gray-700">Taxi #4721</span>
                                    <span class="text-[#0068CC]">Juan García</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="trip-price">24.50€</span>
                                <button class="btn btn-sm btn-outline">
                                    <i class="fas fa-file-pdf"></i>
                                    PDF
                                </button>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-rotate-right"></i>
                                    Repetir
                                </button>
                            </div>
                        </div>
                        
                        <!-- Viaje 2 -->
                        <div class="trip-item">
                            <div class="trip-info">
                                <h4 class="flex items-center gap-2">
                                    <span class="font-semibold">Puerto del Carmen</span>
                                    <i class="fas fa-arrow-right text-gray-400 text-sm"></i>
                                    <span class="font-semibold">Arrecife Centro</span>
                                </h4>
                                <div class="trip-meta">
                                    <span><i class="far fa-calendar mr-1"></i>12 Ene 2025</span>
                                    <span><i class="far fa-clock mr-1"></i>09:15</span>
                                    <span class="badge !py-0.5 bg-gray-100 text-gray-700">Taxi #3892</span>
                                    <span class="text-[#0068CC]">María López</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="trip-price">16.80€</span>
                                <button class="btn btn-sm btn-outline">
                                    <i class="fas fa-file-pdf"></i>
                                    PDF
                                </button>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-rotate-right"></i>
                                    Repetir
                                </button>
                            </div>
                        </div>
                        
                        <!-- Viaje 3 -->
                        <div class="trip-item">
                            <div class="trip-info">
                                <h4 class="flex items-center gap-2">
                                    <span class="font-semibold">Teguise</span>
                                    <i class="fas fa-arrow-right text-gray-400 text-sm"></i>
                                    <span class="font-semibold">Playa Blanca</span>
                                </h4>
                                <div class="trip-meta">
                                    <span><i class="far fa-calendar mr-1"></i>08 Ene 2025</span>
                                    <span><i class="far fa-clock mr-1"></i>16:45</span>
                                    <span class="badge !py-0.5 bg-gray-100 text-gray-700">Taxi #5614</span>
                                    <span class="text-[#0068CC]">Pedro Martín</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="trip-price">32.00€</span>
                                <button class="btn btn-sm btn-outline">
                                    <i class="fas fa-file-pdf"></i>
                                    PDF
                                </button>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-rotate-right"></i>
                                    Repetir
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resumen mensual -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Total gastado este mes</p>
                                <p class="text-2xl font-bold text-[#1A1A1A]">73.30€</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Viajes realizados</p>
                                <p class="text-2xl font-bold text-[#0068CC]">3</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: HISTORIAL COMPLETO -->
        <section id="historial" class="hidden">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Todos tus viajes</h3>
                    <div class="flex gap-2">
                        <div class="input-icon">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-input !py-2 !px-8 !w-64" placeholder="Buscar viaje...">
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Ruta</th>
                                <th>Taxista</th>
                                <th>Distancia</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaHistorialViajes">
                            <tr>
                                <td>15/01/2025</td>
                                <td>Aeropuerto → Hotel Fariones</td>
                                <td>Juan García #4721</td>
                                <td>12.5 km</td>
                                <td class="font-bold">24.50€</td>
                                <td><span class="badge badge-completed">Completado</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline !py-1 !px-2">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </td>
                            </tr>
                            <!-- Más filas... -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: PERFIL -->
        <section id="perfil" class="hidden">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <div class="card text-center">
                        <div class="w-24 h-24 bg-[#FFD700] rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl font-bold text-[#1A1A1A]">JD</span>
                        </div>
                        <h3 class="text-xl font-bold text-[#1A1A1A]">Juan Díaz</h3>
                        <p class="text-gray-600 mb-4">juan.diaz@email.com</p>
                        <span class="badge badge-available mx-auto">Verificado</span>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <div class="card">
                        <h3 class="card-title mb-4">Información personal</h3>
                        <div id="datosPerfil">
                            <!-- Los datos se cargarán dinámicamente desde la API -->
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                                <p>Cargando información...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Configuración de secciones
        function mostrarSeccion(seccion) {
            // Ocultar todas las secciones
            document.querySelectorAll('section').forEach(s => s.classList.add('hidden'));
            document.getElementById(seccion).classList.remove('hidden');
            
            // Actualizar active en sidebar
            document.querySelectorAll('.sidebar-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            document.getElementById(`nav-${seccion}`).classList.add('active');
        }
        
        // Geolocalización
        function usarUbicacionActual() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    alert(`Ubicación obtenida: ${position.coords.latitude}, ${position.coords.longitude}`);
                    // Aquí iría la lógica para reverse geocoding
                });
            } else {
                alert('Tu navegador no soporta geolocalización');
            }
        }
        
        // Programar viaje
        function programarViaje() {
            alert('Función de programación de viajes - Próximamente');
        }
        
        // Cerrar sesión
        function cerrarSesion() {
            window.location.href = '/';
        }
        
        // Variable global para mapa
        let mapaTaxistas = null;
        let mapaSeguimiento = null;
        
        // Inicializar mapa de taxistas disponibles
        function iniciarMapaTaxistasDisponibles() {
            // Verificar que Leaflet esté disponible
            if (typeof L === 'undefined') {
                console.error('Leaflet no está cargado todavía');
                return;
            }
            
            if (document.getElementById('mapaTaxistasDisponibles') && !mapaTaxistas) {
                mapaTaxistas = L.map('mapaTaxistasDisponibles').setView([28.963, -13.547], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(mapaTaxistas);
                
                // Ubicación del cliente
                L.marker([28.963, -13.547], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(mapaTaxistas)
                    .bindPopup('📍 Tu ubicación')
                    .openPopup();
                
                // Taxistas disponibles (simulados)
                const taxistasDisp = [
                    { lat: 28.965, lng: -13.545, nombre: 'Juan García - #4721', estado: 'available' },
                    { lat: 28.960, lng: -13.550, nombre: 'María López - #3892', estado: 'available' },
                    { lat: 28.958, lng: -13.540, nombre: 'Pedro Martín - #5614', estado: 'available' },
                    { lat: 28.970, lng: -13.548, nombre: 'Carlos Rivera - #2847', estado: 'available' },
                    { lat: 28.955, lng: -13.545, nombre: 'Ana García - #3421', estado: 'occupied' },
                    { lat: 28.968, lng: -13.540, nombre: 'Luis Fernández - #4156', estado: 'occupied' }
                ];
                
                taxistasDisp.forEach(taxi => {
                    const color = taxi.estado === 'available' 
                        ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png'
                        : 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png';
                    
                    L.marker([taxi.lat, taxi.lng], {
                        icon: L.icon({
                            iconUrl: color,
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(mapaTaxistas)
                        .bindPopup(`🚕 ${taxi.nombre}<br/>${taxi.estado === 'available' ? 'Disponible' : 'Ocupado'}`);
                });
            }
        }
        
        // Inicializar mapa cuando hay viaje activo
        function iniciarMapaSeguimiento() {
            // Verificar que Leaflet esté disponible
            if (typeof L === 'undefined') {
                console.error('Leaflet no está cargado todavía');
                return;
            }
            
            if (document.getElementById('mapaSeguimiento') && !mapaSeguimiento) {
                mapaSeguimiento = L.map('mapaSeguimiento').setView([28.963, -13.547], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(mapaSeguimiento);
                
                // Marcador del taxi asignado
                L.marker([28.963, -13.547], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(mapaSeguimiento)
                    .bindPopup('🚕 Tu taxi en camino')
                    .openPopup();
            }
        }
        
        // Inicializar mapas cuando carga la página
        window.addEventListener('load', iniciarMapaTaxistasDisponibles);
    </script>
    
    <!-- Script de autenticación -->
    <script src="{{ asset('js/auth.js') }}"></script>
    <script src="{{ asset('js/cliente.js') }}"></script>
</body>
</html>