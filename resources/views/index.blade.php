<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-url" content="{{ url('/api') }}">
    
    <!-- SEO -->
    <title>LanzaTaxi · Taxis en Lanzarote</title>
    <meta name="description" content="Sistema unificado de taxis en Lanzarote. Reserva tu taxi rápido, seguro y con tarifas oficiales.">
    <meta name="keywords" content="taxi Lanzarote, reserva taxi, aeropuerto Lanzarote, traslados">
    <meta name="author" content="LanzaTaxi">
    
    <!-- Theme Color para navegadores móviles -->
    <meta name="theme-color" content="#FFD700">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">
    
    <!-- Open Graph / Redes Sociales -->
    <meta property="og:title" content="LanzaTaxi · Taxis en Lanzarote">
    <meta property="og:description" content="Reserva tu taxi en Lanzarote de forma rápida y segura">
    <meta property="og:type" content="website">
    
    <!-- Tailwind CSS (compilado) + Estilos personalizados -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css?v=20260211') }}" rel="stylesheet">
    
    <style>
        /* Personalizaciones de Tailwind - Colores exactos de la imagen */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    
    <!-- SKIP LINK - WCAG ACCESIBILIDAD (navegación por teclado) -->
    <a href="#main-content" class="skip-link">
        Saltar al contenido principal
    </a>

    <!-- NAVBAR - Mobile First -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm nav-animate">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-[#244194] focus:ring-offset-2 rounded-lg" aria-label="LanzaTaxi inicio">
                    <img src="{{ asset('img/logo_sin_fondo.png') }}" alt="LanzaTaxi" class="w-8 h-8 md:w-10 md:h-10 object-contain">
                    <span class="text-lg md:text-xl font-bold text-[#1A1A1A]">LanzaTaxi</span>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="#como-funciona" class="text-gray-700 hover:text-[#0068CC] font-medium transition-colors">Cómo funciona</a>
                    <a href="#tarifas" class="text-gray-700 hover:text-[#0068CC] font-medium transition-colors">Tarifas</a>
                    <a href="#contacto" class="text-gray-700 hover:text-[#0068CC] font-medium transition-colors">Contacto</a>
                    <button onclick="mostrarModal('login')" class="btn btn-primary">
                        <i class="fas fa-user"></i>
                        Iniciar sesión
                    </button>
                </div>
                
                <!-- Mobile menu button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#0068CC]" aria-label="Abrir menú" aria-expanded="false">
                    <i class="fas fa-bars text-xl text-[#1A1A1A]"></i>
                </button>
            </div>
            
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-gray-200 animate-slideIn">
                <div class="flex flex-col gap-2">
                    <a href="#como-funciona" class="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Cómo funciona</a>
                    <a href="#tarifas" class="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Tarifas</a>
                    <a href="#contacto" class="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Contacto</a>
                    <button onclick="mostrarModal('login')" class="mt-2 btn btn-primary w-full">
                        <i class="fas fa-user"></i>
                        Iniciar sesión
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT - Skip link target -->
    <main id="main-content" class="flex-1">
        
        <!-- HERO SECTION - Basado en imagen 1.png -->
        <section class="bg-[#1A1A1A] text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 lg:py-24">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    
                    <!-- Hero Content -->
                    <div class="animate-slideIn">
                        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                            <i class="fas fa-check-circle text-[#FFD700]"></i>
                            <span class="text-sm font-medium">Servicio oficial en toda Lanzarote</span>
                        </div>
                        
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                            Tu taxi de confianza en <span class="text-[#FFD700]">Lanzarote</span>
                        </h1>
                        
                        <p class="text-lg md:text-xl text-gray-300 mb-8 leading-relaxed">
                            Reserva rápido, viaja seguro, paga justo. Conectamos pasajeros y taxistas de forma inteligente en toda la isla.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 mb-12">
                            <button onclick="mostrarModal('register')" class="btn btn-primary btn-lg">
                                <i class="fas fa-car"></i>
                                Pedir taxi ahora
                            </button>
                            <a href="#como-funciona" class="btn btn-secondary btn-lg bg-white/10 text-white border-white/20 hover:bg-white/20">
                                <i class="fas fa-play-circle"></i>
                                Cómo funciona
                            </a>
                        </div>
                        
                        <!-- Hero Stats -->
                        <div class="grid grid-cols-3 gap-4 md:gap-8">
                            <div>
                                <div class="text-2xl md:text-3xl font-bold text-[#FFD700]">2.5k+</div>
                                <div class="text-sm text-gray-400">Pasajeros</div>
                            </div>
                            <div>
                                <div class="text-2xl md:text-3xl font-bold text-[#FFD700]">450+</div>
                                <div class="text-sm text-gray-400">Taxistas</div>
                            </div>
                            <div>
                                <div class="text-2xl md:text-3xl font-bold text-[#FFD700]">15k+</div>
                                <div class="text-sm text-gray-400">Viajes/mes</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hero Image/Animation -->
                    <div class="hidden md:flex justify-center">
                        <div class="relative">
                            <div class="w-80 h-80 bg-[#FFD700] rounded-3xl rotate-6 opacity-10 absolute"></div>
                            <div class="relative bg-gradient-to-br from-[#FFD700] to-[#E6C200] p-8 rounded-3xl shadow-2xl">
                                <i class="fas fa-taxi text-[#1A1A1A] text-8xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CÓMO FUNCIONA - Features -->
        <section id="como-funciona" class="py-16 md:py-24 px-4">
            <div class="max-w-7xl mx-auto">
                
                <div class="text-center max-w-2xl mx-auto mb-12 md:mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-[#1A1A1A] mb-4">
                        ¿Cómo funciona LanzaTaxi?
                    </h2>
                    <p class="text-lg text-gray-600">
                        Reserva tu taxi en menos de 30 segundos, sin complicaciones
                    </p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    
                    <!-- Paso 1 -->
                    <div class="card text-center group hover:-translate-y-1 transition-transform duration-300">
                        <div class="w-16 h-16 bg-[#FFD700] bg-opacity-10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-map-marker-alt text-2xl text-[#0068CC]"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#1A1A1A] mb-3">1. Indica tu destino</h3>
                        <p class="text-gray-600">
                            Introduce dónde estás y a dónde quieres ir. Nuestro sistema calculará la ruta y el precio.
                        </p>
                    </div>
                    
                    <!-- Paso 2 -->
                    <div class="card text-center group hover:-translate-y-1 transition-transform duration-300">
                        <div class="w-16 h-16 bg-[#FFD700] bg-opacity-10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-taxi text-2xl text-[#0068CC]"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#1A1A1A] mb-3">2. Elige tu taxi</h3>
                        <p class="text-gray-600">
                            Te asignamos el taxista disponible más cercano. Puedes ver su ubicación en tiempo real.
                        </p>
                    </div>
                    
                    <!-- Paso 3 -->
                    <div class="card text-center group hover:-translate-y-1 transition-transform duration-300">
                        <div class="w-16 h-16 bg-[#FFD700] bg-opacity-10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-credit-card text-2xl text-[#0068CC]"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#1A1A1A] mb-3">3. Paga y viaja</h3>
                        <p class="text-gray-600">
                            Paga con tarjeta, efectivo o bizum. Recibe tu factura por email automáticamente.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TARIFAS OFICIALES - Basado en imagen 1.png -->
        <section id="tarifas" class="py-16 md:py-24 bg-gray-100 px-4">
            <div class="max-w-7xl mx-auto">
                
                <div class="text-center max-w-2xl mx-auto mb-12 md:mb-16">
                    <div class="inline-flex items-center gap-2 bg-[#FFD700] bg-opacity-20 px-4 py-2 rounded-full mb-4">
                        <i class="fas fa-tag text-[#0068CC]"></i>
                        <span class="text-sm font-semibold text-[#1A1A1A]">Tarifas oficiales Cabildo de Lanzarote</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-[#1A1A1A] mb-4">
                        Precios transparentes, sin sorpresas
                    </h2>
                    <p class="text-lg text-gray-600">
                        Conoce exactamente cuánto pagarás antes de confirmar tu viaje
                    </p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                    
                    <!-- Tarifa 1 - Urbano -->
                    <div class="card hover:scale-105 transition-transform duration-300">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-[#0068CC] bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-building text-2xl text-[#0068CC]"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#1A1A1A]">Tarifa 1</h3>
                            <p class="text-gray-600">Servicio urbano</p>
                        </div>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Bajada de bandera</span>
                                <span class="font-bold text-[#1A1A1A]">3,15€</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Por kilómetro</span>
                                <span class="font-bold text-[#1A1A1A]">0,60€</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Espera/hora</span>
                                <span class="font-bold text-[#1A1A1A]">18,00€</span>
                            </div>
                        </div>
                        
                        <div class="text-center text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                            Válido en cascos urbanos
                        </div>
                    </div>
                    
                    <!-- Tarifa 2 - Interurbano (Destacada) -->
                    <div class="card border-2 border-[#FFD700] hover:scale-105 transition-transform duration-300 relative">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#FFD700] px-4 py-1 rounded-full text-xs font-bold text-[#1A1A1A]">
                            MÁS UTILIZADA
                        </div>
                        
                        <div class="text-center mb-6 mt-2">
                            <div class="w-16 h-16 bg-[#FFD700] bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-road text-2xl text-[#1A1A1A]"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#1A1A1A]">Tarifa 2</h3>
                            <p class="text-gray-600">Servicio interurbano</p>
                        </div>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Bajada de bandera</span>
                                <span class="font-bold text-[#1A1A1A]">3,15€</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Por kilómetro</span>
                                <span class="font-bold text-[#0068CC] text-lg">0,75€</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Espera/hora</span>
                                <span class="font-bold text-[#1A1A1A]">18,00€</span>
                            </div>
                        </div>
                        
                        <div class="text-center text-sm bg-[#FFD700] bg-opacity-20 p-3 rounded-lg font-medium text-[#1A1A1A]">
                            Trayectos entre municipios
                        </div>
                    </div>
                    
                    <!-- Suplementos -->
                    <div class="card hover:scale-105 transition-transform duration-300">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-plus-circle text-2xl text-gray-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#1A1A1A]">Suplementos</h3>
                            <p class="text-gray-600">Cargos adicionales</p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Aeropuerto (ACE)</span>
                                <span class="font-bold text-[#1A1A1A]">3,50€</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Puerto</span>
                                <span class="font-bold text-[#1A1A1A]">2,00€</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Nocturno (22-6h)</span>
                                <span class="font-bold text-[#1A1A1A]">+0,20€/km</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Festivos</span>
                                <span class="font-bold text-[#1A1A1A]">+0,30€/km</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-10">
                    <p class="text-gray-600 mb-4">
                        <i class="fas fa-info-circle text-[#0068CC]"></i>
                        Tarifas oficiales según ordenanza municipal del Cabildo de Lanzarote
                    </p>
                    <button class="btn btn-outline">
                        <i class="fas fa-file-pdf"></i>
                        Descargar tarifario completo
                    </button>
                </div>
            </div>
        </section>

        <!-- MUNICIPIOS - Cobertura -->
        <section class="py-16 md:py-24 px-4">
            <div class="max-w-7xl mx-auto">
                
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-[#1A1A1A] mb-4">
                        Cobertura en toda la isla
                    </h2>
                    <p class="text-lg text-gray-600">
                        Operamos en los 7 municipios de Lanzarote
                    </p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
                    
                    <div class="card text-center p-4 hover:bg-[#FFD700] hover:text-[#1A1A1A] group transition-all duration-300">
                        <i class="fas fa-map-marker-alt text-2xl text-[#0068CC] group-hover:text-[#1A1A1A] mb-2"></i>
                        <span class="font-medium">Arrecife</span>
                    </div>
                    
                    <div class="card text-center p-4 hover:bg-[#FFD700] hover:text-[#1A1A1A] group transition-all duration-300">
                        <i class="fas fa-map-marker-alt text-2xl text-[#0068CC] group-hover:text-[#1A1A1A] mb-2"></i>
                        <span class="font-medium">Teguise</span>
                    </div>
                    
                    <div class="card text-center p-4 hover:bg-[#FFD700] hover:text-[#1A1A1A] group transition-all duration-300">
                        <i class="fas fa-map-marker-alt text-2xl text-[#0068CC] group-hover:text-[#1A1A1A] mb-2"></i>
                        <span class="font-medium">Tías</span>
                    </div>
                    
                    <div class="card text-center p-4 hover:bg-[#FFD700] hover:text-[#1A1A1A] group transition-all duration-300">
                        <i class="fas fa-map-marker-alt text-2xl text-[#0068CC] group-hover:text-[#1A1A1A] mb-2"></i>
                        <span class="font-medium">Yaiza</span>
                    </div>
                    
                    <div class="card text-center p-4 hover:bg-[#FFD700] hover:text-[#1A1A1A] group transition-all duration-300">
                        <i class="fas fa-map-marker-alt text-2xl text-[#0068CC] group-hover:text-[#1A1A1A] mb-2"></i>
                        <span class="font-medium">San Bartolomé</span>
                    </div>
                    
                    <div class="card text-center p-4 hover:bg-[#FFD700] hover:text-[#1A1A1A] group transition-all duration-300">
                        <i class="fas fa-map-marker-alt text-2xl text-[#0068CC] group-hover:text-[#1A1A1A] mb-2"></i>
                        <span class="font-medium">Tinajo</span>
                    </div>
                    
                    <div class="card text-center p-4 hover:bg-[#FFD700] hover:text-[#1A1A1A] group transition-all duration-300">
                        <i class="fas fa-map-marker-alt text-2xl text-[#0068CC] group-hover:text-[#1A1A1A] mb-2"></i>
                        <span class="font-medium">Haría</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="bg-[#1A1A1A] text-white py-16 md:py-24 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">
                    ¿Listo para tu próximo viaje?
                </h2>
                <p class="text-xl text-gray-300 mb-8">
                    Únete a miles de usuarios que confían en LanzaTaxi para sus desplazamientos
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="mostrarModal('register')" class="btn btn-primary btn-lg bg-[#FFD700] text-[#1A1A1A] hover:bg-[#E6C200]">
                        <i class="fas fa-user-plus"></i>
                        Crear cuenta gratis
                    </button>
                    <a href="#contacto" class="btn btn-lg border-2 border-white text-white hover:bg-white hover:text-[#1A1A1A]">
                        <i class="fas fa-headset"></i>
                        Contactar soporte
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-200 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                
                <!-- Col 1 - Logo -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-[#FFD700] rounded-lg flex items-center justify-center">
                            <i class="fas fa-taxi text-[#1A1A1A]"></i>
                        </div>
                        <span class="text-lg font-bold text-[#1A1A1A]">LanzaTaxi</span>
                    </div>
                    <p class="text-gray-600 text-sm">
                        Sistema unificado de gestión de taxis para Lanzarote. Conectando pasajeros y conductores de forma rápida y segura.
                    </p>
                    <p class="text-gray-500 text-xs mt-4">
                        © 2026 LanzaTaxi - Proyecto Final DAW
                    </p>
                </div>
                
                <!-- Col 2 - Pasajeros -->
                <div>
                    <h4 class="font-semibold text-[#1A1A1A] mb-4">Pasajeros</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-[#0068CC] text-sm transition-colors">Reservar taxi</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-[#0068CC] text-sm transition-colors">Cómo funciona</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-[#0068CC] text-sm transition-colors">Tarifas oficiales</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-[#0068CC] text-sm transition-colors">Preguntas frecuentes</a></li>
                    </ul>
                </div>
                
                <!-- Col 3 - Taxistas -->
                <div>
                    <h4 class="font-semibold text-[#1A1A1A] mb-4">Taxistas</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-[#0068CC] text-sm transition-colors">Trabaja con nosotros</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-[#0068CC] text-sm transition-colors">Requisitos</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-[#0068CC] text-sm transition-colors">Beneficios</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-[#0068CC] text-sm transition-colors">Soporte técnico</a></li>
                    </ul>
                </div>
                
                <!-- Col 4 - Contacto -->
                <div>
                    <h4 class="font-semibold text-[#1A1A1A] mb-4">Contacto</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-gray-600 text-sm">
                            <i class="fas fa-phone text-[#0068CC] w-4"></i>
                            928 123 456
                        </li>
                        <li class="flex items-center gap-2 text-gray-600 text-sm">
                            <i class="fas fa-envelope text-[#0068CC] w-4"></i>
                            info@lanzataxi.com
                        </li>
                        <li class="flex items-center gap-2 text-gray-600 text-sm">
                            <i class="fas fa-map-marker-alt text-[#0068CC] w-4"></i>
                            Lanzarote, Islas Canarias
                        </li>
                    </ul>
                    
                    <div class="flex gap-4 mt-6">
                        <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#FFD700] transition-colors" aria-label="Facebook">
                            <i class="fab fa-facebook-f text-gray-700"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#FFD700] transition-colors" aria-label="Twitter">
                            <i class="fab fa-twitter text-gray-700"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#FFD700] transition-colors" aria-label="Instagram">
                            <i class="fab fa-instagram text-gray-700"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-8 mt-8 text-center text-gray-500 text-xs">
                <p>Proyecto Final de Diseño de Interfaces Web - Segundo Curso DAW</p>
                <p class="mt-2">Diseño inclusivo, accesibilidad WCAG 2.1 y Mobile First</p>
            </div>
        </div>
    </footer>

    <!-- MODAL LOGIN/REGISTER - Accesible, navegación por teclado -->
    <div id="authModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto animate-slideIn">
            
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-center">
                <h2 id="modal-title" class="text-xl font-bold text-[#1A1A1A]">
                    <span id="modal-title-text">Iniciar sesión</span>
                </h2>
                <button onclick="cerrarModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-[#244194]" aria-label="Cerrar modal">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Tabs -->
                <div class="flex gap-2 mb-6 bg-gray-100 p-1 rounded-lg" role="tablist" aria-label="Autenticacion">
                    <button id="tab-login" role="tab" aria-selected="true" aria-controls="login-panel" tabindex="0" onclick="cambiarTab('login')" class="flex-1 py-2 px-4 rounded-lg font-medium transition-all duration-200">
                        Iniciar sesión
                    </button>
                    <button id="tab-register" role="tab" aria-selected="false" aria-controls="register-panel" tabindex="-1" onclick="cambiarTab('register')" class="flex-1 py-2 px-4 rounded-lg font-medium transition-all duration-200">
                        Registrarse
                    </button>
                </div>

                <div id="auth-feedback" class="form-feedback hidden" role="status" aria-live="polite"></div>
                
                <!-- Login Form -->
                <form id="login-panel" class="space-y-4" role="tabpanel" aria-labelledby="tab-login" aria-hidden="false" onsubmit="handleLogin(event)">
                    <div>
                        <label for="loginEmail" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="loginEmail" class="form-input" placeholder="tu@email.com" autocomplete="email" aria-describedby="auth-feedback" required>
                        </div>
                    </div>
                    <div>
                        <label for="loginPassword" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="loginPassword" class="form-input" placeholder="••••••••" autocomplete="current-password" aria-describedby="auth-feedback" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar sesión
                    </button>
                    
                    <div class="bg-gray-50 p-4 rounded-lg text-sm">
                        <p class="font-medium text-gray-700 mb-2">Usuarios de prueba:</p>
                        <p class="text-gray-600">👤 Cliente: cliente@test.com / 123456</p>
                        <p class="text-gray-600">🚕 Taxista: taxista@test.com / 123456</p>
                        <p class="text-gray-600">👑 Admin: admin@test.com / 123456</p>
                    </div>
                </form>
                
                <!-- Register Form -->
                <form id="register-panel" class="space-y-4 hidden" role="tabpanel" aria-labelledby="tab-register" aria-hidden="true" onsubmit="handleRegister(event)">
                    <div>
                        <label for="registerNombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="registerNombre" class="form-input" placeholder="Ej: María García" autocomplete="name" aria-describedby="auth-feedback" required>
                        </div>
                    </div>
                    <div>
                        <label for="registerEmail" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="registerEmail" class="form-input" placeholder="tu@email.com" autocomplete="email" aria-describedby="auth-feedback" required>
                        </div>
                    </div>
                    <div>
                        <label for="registerTelefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <div class="input-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="registerTelefono" class="form-input" placeholder="628 123 456" autocomplete="tel" aria-describedby="auth-feedback" required>
                        </div>
                    </div>
                    <div>
                        <label for="registerPassword" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="registerPassword" class="form-input" placeholder="Mínimo 6 caracteres" minlength="6" autocomplete="new-password" aria-describedby="auth-feedback" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-user-plus"></i>
                        Crear cuenta
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // MOBILE MENU TOGGLE
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            this.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
        });
        
        // MODAL FUNCTIONS
        function mostrarModal(tab = 'login') {
            document.getElementById('authModal').style.display = 'flex';
            cambiarTab(tab);
        }
        
        function cerrarModal() {
            document.getElementById('authModal').style.display = 'none';
        }
        
        function setAuthFeedback(message = '', type = 'info') {
            const feedback = document.getElementById('auth-feedback');
            if (!feedback) {
                return;
            }

            feedback.textContent = message;
            feedback.classList.remove('form-feedback--error', 'form-feedback--success');

            if (!message) {
                feedback.classList.add('hidden');
                return;
            }

            feedback.classList.remove('hidden');

            if (type === 'error') {
                feedback.classList.add('form-feedback--error');
            }

            if (type === 'success') {
                feedback.classList.add('form-feedback--success');
            }
        }

        function setFormInvalid(formId, isInvalid) {
            const form = document.getElementById(formId);
            if (!form) {
                return;
            }

            form.querySelectorAll('input').forEach((input) => {
                input.setAttribute('aria-invalid', isInvalid ? 'true' : 'false');
            });
        }

        function cambiarTab(tab) {
            const loginForm = document.getElementById('login-panel');
            const registerForm = document.getElementById('register-panel');
            const tabLogin = document.getElementById('tab-login');
            const tabRegister = document.getElementById('tab-register');
            const modalTitle = document.getElementById('modal-title-text');
            setAuthFeedback('');
            setFormInvalid('login-panel', false);
            setFormInvalid('register-panel', false);
            
            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                tabLogin.classList.add('bg-[#FFD700]', 'text-[#1A1A1A]');
                tabLogin.classList.remove('bg-transparent', 'text-gray-700');
                tabRegister.classList.remove('bg-[#FFD700]', 'text-[#1A1A1A]');
                tabRegister.classList.add('bg-transparent', 'text-gray-700');
                tabLogin.setAttribute('aria-selected', 'true');
                tabLogin.setAttribute('tabindex', '0');
                tabRegister.setAttribute('aria-selected', 'false');
                tabRegister.setAttribute('tabindex', '-1');
                loginForm.setAttribute('aria-hidden', 'false');
                registerForm.setAttribute('aria-hidden', 'true');
                modalTitle.textContent = 'Iniciar sesión';
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                tabRegister.classList.add('bg-[#FFD700]', 'text-[#1A1A1A]');
                tabRegister.classList.remove('bg-transparent', 'text-gray-700');
                tabLogin.classList.remove('bg-[#FFD700]', 'text-[#1A1A1A]');
                tabLogin.classList.add('bg-transparent', 'text-gray-700');
                tabRegister.setAttribute('aria-selected', 'true');
                tabRegister.setAttribute('tabindex', '0');
                tabLogin.setAttribute('aria-selected', 'false');
                tabLogin.setAttribute('tabindex', '-1');
                loginForm.setAttribute('aria-hidden', 'true');
                registerForm.setAttribute('aria-hidden', 'false');
                modalTitle.textContent = 'Crear cuenta';
            }
        }

        document.querySelector('[role="tablist"]')?.addEventListener('keydown', (event) => {
            const tabs = ['tab-login', 'tab-register'];
            const currentIndex = tabs.findIndex((id) => document.getElementById(id)?.getAttribute('aria-selected') === 'true');

            if (event.key === 'ArrowRight') {
                const nextIndex = (currentIndex + 1) % tabs.length;
                cambiarTab(nextIndex === 0 ? 'login' : 'register');
                document.getElementById(tabs[nextIndex])?.focus();
            }

            if (event.key === 'ArrowLeft') {
                const prevIndex = (currentIndex - 1 + tabs.length) % tabs.length;
                cambiarTab(prevIndex === 0 ? 'login' : 'register');
                document.getElementById(tabs[prevIndex])?.focus();
            }

            if (event.key === 'Home') {
                cambiarTab('login');
                document.getElementById('tab-login')?.focus();
            }

            if (event.key === 'End') {
                cambiarTab('register');
                document.getElementById('tab-register')?.focus();
            }
        });
        
        // Cerrar modal con ESC (Accesibilidad)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('authModal');
            if (event.target === modal) {
                cerrarModal();
            }
        };
        
        // Smooth scroll para anclas
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>

    <!-- Script de autenticación -->
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>