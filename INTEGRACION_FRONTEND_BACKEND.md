# Integración Frontend - Backend

## Configuración Inicial

### 1. Actualizar ficheros JavaScript para consumir API

El frontend actual usa datos estáticos. Necesita ser actualizado para consumir la API REST:

**public/js/auth.js** - Actualizar login/registro:
```javascript
// Antes (estático)
const usuarios = JSON.parse(localStorage.getItem('usuarios')) || [];

// Después (con API)
async function registrar(datos) {
    const response = await fetch('http://localhost:8000/api/auth/registrar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            nombre: datos.nombre,
            email: datos.email,
            password: datos.password,
            tipo: datos.tipo,
            // ... otros campos
        })
    });
    
    const result = await response.json();
    if (result.success) {
        localStorage.setItem('usuario', JSON.stringify(result.usuario));
        localStorage.setItem('token', result.token);
        return result.usuario;
    }
    throw new Error(result.error);
}

async function iniciarSesion(email, password) {
    const response = await fetch('http://localhost:8000/api/auth/iniciar-sesion', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    });
    
    const result = await response.json();
    if (result.success) {
        localStorage.setItem('usuario', JSON.stringify(result.usuario));
        localStorage.setItem('token', result.token);
        return result.usuario;
    }
    throw new Error(result.error);
}
```

**public/js/cliente.js** - Integración de viajes:
```javascript
// Crear nuevo viaje
async function solicitarViaje(origen, destino, tarifaId) {
    const token = localStorage.getItem('token');
    const usuario = JSON.parse(localStorage.getItem('usuario'));
    
    const response = await fetch('http://localhost:8000/api/viajes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
            cliente_id: usuario.id,
            origen_lat: origen.lat,
            origen_lng: origen.lng,
            origen_direccion: origen.direccion,
            destino_lat: destino.lat,
            destino_lng: destino.lng,
            destino_direccion: destino.direccion,
            tarifa_id: tarifaId,
            ocupantes: 1,
            metodo_pago: 'tarjeta'
        })
    });
    
    const result = await response.json();
    if (result.success) {
        // Mostrar viaje creado
        mostrarViajeCreado(result.viaje);
        // Escuchar actualizaciones en tiempo real
        escucharActualizacionesViaje(result.viaje.id);
    }
}

// Obtener viajes del cliente
async function obtenerMisViajes() {
    const token = localStorage.getItem('token');
    const usuario = JSON.parse(localStorage.getItem('usuario'));
    
    const response = await fetch(
        `http://localhost:8000/api/viajes/cliente/${usuario.id}`,
        {
            headers: { 'Authorization': `Bearer ${token}` }
        }
    );
    
    const result = await response.json();
    if (result.success) {
        mostrarViajes(result.viajes);
    }
}

// Obtener taxistas disponibles cercanos
async function obtenerTaxistasCercanos(lat, lng) {
    const token = localStorage.getItem('token');
    
    const response = await fetch(
        `http://localhost:8000/api/viajes/taxistas-cercanos?lat=${lat}&lng=${lng}&radio=5`,
        {
            headers: { 'Authorization': `Bearer ${token}` }
        }
    );
    
    const result = await response.json();
    if (result.success) {
        mostrarTaxistasEnMapa(result.taxistas);
    }
}

// Pagar un viaje
async function pagarViaje(viajeId, monto) {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://localhost:8000/api/transacciones/pagar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
            viaje_id: viajeId,
            metodo: 'tarjeta',
            monto: monto
        })
    });
    
    const result = await response.json();
    if (result.success) {
        mostrarMensaje('Pago procesado correctamente');
    }
}

// Evaluar al taxista
async function evaluarTaxista(viajeId, taxistaId, calificacion) {
    const token = localStorage.getItem('token');
    const usuario = JSON.parse(localStorage.getItem('usuario'));
    
    const response = await fetch('http://localhost:8000/api/evaluaciones', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
            viaje_id: viajeId,
            evaluador_id: usuario.id,
            evaluado_id: taxistaId,
            calificacion: calificacion,
            comentario: document.getElementById('comentario').value,
            aspecto: 'trato'
        })
    });
    
    const result = await response.json();
    if (result.success) {
        mostrarMensaje('Evaluación registrada');
    }
}
```

**public/js/taxista.js** - Integración de gestión de viajes:
```javascript
// Obtener viajes solicitados
async function obtenerViajesSolicitados() {
    const token = localStorage.getItem('token');
    const usuario = JSON.parse(localStorage.getItem('usuario'));
    
    // Obtener taxista
    const response = await fetch(
        `http://localhost:8000/api/viajes/taxista/${usuario.id}/activos`,
        {
            headers: { 'Authorization': `Bearer ${token}` }
        }
    );
    
    const result = await response.json();
    if (result.success) {
        mostrarViajesDisponibles(result.viajes);
    }
}

// Aceptar viaje
async function aceptarViaje(viajeId) {
    const token = localStorage.getItem('token');
    const usuario = JSON.parse(localStorage.getItem('usuario'));
    const taxistaId = usuario.id; // Debe obtenerse de relación Usuario->Taxista
    
    const response = await fetch(`http://localhost:8000/api/viajes/${viajeId}/aceptar`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ taxista_id: taxistaId })
    });
    
    const result = await response.json();
    if (result.success) {
        mostrarViajeAceptado(result.viaje);
        mostrarClienteEnMapa(result.viaje);
    }
}

// Actualizar ubicación del taxista
async function actualizarUbicacion(lat, lng) {
    const token = localStorage.getItem('token');
    
    // Nota: Esta ruta no está definida aún, sería bueno agregarla
    // Por ahora guardamos localmente
    localStorage.setItem('ubicacion', JSON.stringify({lat, lng}));
    
    // Emitir a WebSocket si está disponible
    if (window.viajeSocket) {
        window.viajeSocket.emit('taxista:ubicacion', {lat, lng});
    }
}

// Completar viaje
async function completarViaje(viajeId) {
    const token = localStorage.getItem('token');
    
    const response = await fetch(`http://localhost:8000/api/viajes/${viajeId}/completar`, {
        method: 'PUT',
        headers: { 'Authorization': `Bearer ${token}` }
    });
    
    const result = await response.json();
    if (result.success) {
        mostrarMensaje('Viaje completado');
        limpiarMapa();
    }
}

// Obtener ganancias
async function obtenerGanancias() {
    const token = localStorage.getItem('token');
    const usuario = JSON.parse(localStorage.getItem('usuario'));
    
    const response = await fetch(
        `http://localhost:8000/api/transacciones/usuario/${usuario.id}/ganancias?periodo=mes`,
        {
            headers: { 'Authorization': `Bearer ${token}` }
        }
    );
    
    const result = await response.json();
    if (result.success) {
        mostrarGanancias(result.ganancias);
    }
}

// Obtener calificación
async function obtenerCalificacion() {
    const token = localStorage.getItem('token');
    const usuario = JSON.parse(localStorage.getItem('usuario'));
    
    const response = await fetch(
        `http://localhost:8000/api/evaluaciones/usuario/${usuario.id}/calificacion`,
        {
            headers: { 'Authorization': `Bearer ${token}` }
        }
    );
    
    const result = await response.json();
    if (result.success) {
        mostrarCalificacion(result.calificacion_promedio);
    }
}
```

### 2. Crear módulo de utilidades para API

**public/js/api.js** - Centralizar llamadas API:
```javascript
const API_BASE = 'http://localhost:8000/api';

class ApiClient {
    constructor() {
        this.token = localStorage.getItem('token');
    }

    async fetch(endpoint, opciones = {}) {
        const url = `${API_BASE}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
            ...opciones.headers
        };

        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        const response = await fetch(url, {
            ...opciones,
            headers
        });

        if (response.status === 401) {
            // Token expirado
            localStorage.removeItem('token');
            localStorage.removeItem('usuario');
            window.location.href = '/index.html';
        }

        return response.json();
    }

    async post(endpoint, datos) {
        return this.fetch(endpoint, {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    }

    async put(endpoint, datos) {
        return this.fetch(endpoint, {
            method: 'PUT',
            body: JSON.stringify(datos)
        });
    }

    async get(endpoint) {
        return this.fetch(endpoint, {
            method: 'GET'
        });
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem('token', token);
    }
}

const api = new ApiClient();
```

### 3. Actualizar HTML para usar API

**public/index.html** - Formulario de login:
```html
<form id="loginForm">
    <input type="email" id="email" placeholder="Email" required>
    <input type="password" id="password" placeholder="Contraseña" required>
    <button type="submit">Iniciar Sesión</button>
</form>

<script src="js/api.js"></script>
<script src="js/auth.js"></script>
<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            const resultado = await api.post('/auth/iniciar-sesion', {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            });
            
            if (resultado.success) {
                api.setToken(resultado.token);
                localStorage.setItem('usuario', JSON.stringify(resultado.usuario));
                
                // Redirigir según tipo
                if (resultado.usuario.tipo === 'cliente') {
                    window.location.href = '/cliente.html';
                } else if (resultado.usuario.tipo === 'taxista') {
                    window.location.href = '/taxista.html';
                } else {
                    window.location.href = '/admin.html';
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al iniciar sesión');
        }
    });
</script>
```

### 4. Instalación de Dependencias

**Backend - Laravel**:
```bash
cd c:\xampp\htdocs\LanzaTaxi

# Instalar dependencias
composer install

# Generar APP_KEY
php artisan key:generate

# Crear base de datos
# Editar .env con datos de DB
# Luego:
php artisan migrate

# Ejecutar tests
php artisan test
```

**Frontend - No requiere instalación**:
El frontend es vanilla JavaScript, solo necesita un servidor HTTP:
```bash
# Opción 1: PHP built-in server
php -S localhost:8000

# Opción 2: Python
python -m http.server 8000

# Opción 3: XAMPP (ya configurado)
# http://localhost/LanzaTaxi
```

### 5. CORS - Permitir requests desde Frontend

**app/Http/Middleware/Cors.php**:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://localhost')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
```

Registrar en `app/Http/Kernel.php`:
```php
protected $middleware = [
    // ...
    \App\Http\Middleware\Cors::class,
];
```

### 6. Variables de Entorno - .env

```env
APP_NAME="LanzaTaxi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lanzataxi
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=your_secret_key_here

FRONTEND_URL=http://localhost
```

## Flujo de Autenticación

### Con localStorage + JWT
```javascript
// 1. Usuario inicia sesión
POST /api/auth/iniciar-sesion
← { token: "eyJhbGci..." }

// 2. Guardar token
localStorage.setItem('token', token);

// 3. En requests posteriores, incluir token
fetch('/api/viajes', {
    headers: { 'Authorization': 'Bearer ' + token }
})

// 4. Backend valida token
// Si inválido → 401 → redirigir a login
```

## Actualización en Tiempo Real

### Opción 1: Polling (Consultar cada X segundos)
```javascript
setInterval(async () => {
    const viajes = await api.get(`/viajes/cliente/${usuarioId}`);
    actualizarUI(viajes);
}, 5000); // Cada 5 segundos
```

### Opción 2: WebSockets (En tiempo real)
Requiere Laravel Echo + Broadcasting:
```javascript
// Escuchar cambios en un viaje
Echo.channel(`viaje.${viajeId}`)
    .listen('ViajeActualizado', (e) => {
        mostrarEstadoActual(e.viaje);
    });
```

## Listado de Cambios Necesarios

- [ ] `public/js/auth.js` - Integrar API autenticación
- [ ] `public/js/cliente.js` - Integrar API viajes
- [ ] `public/js/taxista.js` - Integrar API taxista
- [ ] `public/js/api.js` - Crear (nuevo archivo)
- [ ] `public/index.html` - Actualizar formularios
- [ ] `public/cliente.html` - Integrar mapas + API
- [ ] `public/taxista.html` - Integrar mapas + API
- [ ] `.env` - Crear base de datos
- [ ] `php artisan migrate` - Crear tablas
- [ ] Habilitar CORS en Laravel
- [ ] Tests E2E - Probar flujos completos

## URLs de Referencia

**Documentación Laravel**:
- https://laravel.com/docs/11/eloquent
- https://laravel.com/docs/11/sanctum
- https://laravel.com/docs/11/testing

**Documentación Leaflet**:
- https://leafletjs.com/examples/quick-start/

**Estándares REST**:
- https://restfulapi.net/

## Soporte

Para preguntas sobre la integración, consultar:
1. `ARQUITECTURA_BACKEND.md` - Estructura general
2. `ARQUITECTURA_BD.md` - Esquema base de datos
3. Código en `app/Services/` - Lógica disponible
