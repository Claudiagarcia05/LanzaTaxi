# LanzaTaxi - Backend Challenge DSW

## Visión General

El backend de LanzaTaxi implementa una arquitectura robusta siguiendo los estándares de **Desarrollo Web en Entorno Servidor (DSW)**. La solución incluye:

- **Base de datos relacional normalizada** (10 tablas)
- **ORM Eloquent** para acceso a datos
- **Servicios** para lógica de negocio
- **Controladores REST API** para HTTP
- **Inyección de dependencias** para desacoplamiento
- **Tests unitarios** con PHPUnit

## Arquitectura

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend (Cliente)                       │
│           HTML5 + Tailwind CSS + Vanilla JS                  │
└────────────────────────────┬────────────────────────────────┘
                             │
                    REST API (JSON)
                             │
┌────────────────────────────▼────────────────────────────────┐
│                    HTTP Controllers                          │
│     (laravel/controllers/Api/*)                              │
│  - AuthController                                            │
│  - ViajeController                                           │
│  - TransaccionController                                     │
│  - EvaluacionController                                      │
└────────────────────────────┬────────────────────────────────┘
                             │
                 Inyección de Dependencias
                             │
┌────────────────────────────▼────────────────────────────────┐
│                      Servicios (Lógica)                      │
│     (app/Services/*)                                         │
│  - ViajeService                                              │
│  - AuthService                                               │
│  - TransaccionService                                        │
│  - EvaluacionService                                         │
└────────────────────────────┬────────────────────────────────┘
                             │
┌────────────────────────────▼────────────────────────────────┐
│                    Modelos Eloquent                          │
│     (app/Models/*)                                           │
│  - Usuario (polimórfico)                                     │
│  - Cliente, Taxista, Admin                                   │
│  - Viaje, Vehiculo, Tarifa                                   │
│  - Evaluacion, Transaccion, Notificacion                     │
└────────────────────────────┬────────────────────────────────┘
                             │
┌────────────────────────────▼────────────────────────────────┐
│               Base de Datos Relacional                       │
│              (10 tablas normalizadas)                        │
│                    MySQL / PostgreSQL                        │
└─────────────────────────────────────────────────────────────┘
```

## Capas de la Aplicación

### 1. Capa de Presentación (Frontend)
- HTML en `public/` (index.html, cliente.html, taxista.html, admin.html)
- CSS con Tailwind en `public/css/`
- JavaScript vanilla en `public/js/`
- Mapas Leaflet integrados

### 2. Capa HTTP (Controladores)
**Archivo**: `app/Http/Controllers/Api/`

```php
// Los controladores reciben requests HTTP y retornan JSON
// Inyección automática de Servicios

class AuthController {
    public function __construct(AuthService $authService) {
        $this->authService = $authService; // Inyección
    }
    
    public function registrar(Request $request) {
        // Validar request
        // Delegar a servicio
        // Retornar JSON response
    }
}
```

### 3. Capa de Servicios (Lógica de Negocio)
**Archivo**: `app/Services/`

Contiene la lógica de negocio desacoplada de HTTP:

```php
class ViajeService {
    public function crearViaje(array $datos): Viaje {
        // Validar datos
        // Calcular distancia (Haversine)
        // Calcular precio según tarifa
        // Crear notificación
        // Retornar modelo Viaje
    }
    
    public function obtenerTaxistasCercanos(float $lat, float $lng): array {
        // Buscar taxistas disponibles en radio
        // Ordenar por distancia
        // Retornar array de taxistas
    }
}
```

### 4. Capa de Modelos (Acceso a Datos)
**Archivo**: `app/Models/`

Modelos Eloquent con relaciones y métodos de negocio:

```php
class Viaje extends Model {
    // Relaciones
    public function cliente() { return $this->belongsTo(Usuario::class); }
    public function taxista() { return $this->belongsTo(Usuario::class); }
    
    // Métodos de negocio
    public function aceptar($taxistaId) { /* ... */ }
    public function completar() { /* ... */ }
    public static function calcularDistancia($lat1, $lng1, $lat2, $lng2) { /* ... */ }
}
```

### 5. Capa de Base de Datos (Migraciones)
**Archivo**: `database/migrations/`

Define el esquema relacional:

```php
Schema::create('viajes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cliente_id')->constrained('usuarios');
    $table->foreignId('taxista_id')->nullable()->constrained('usuarios');
    $table->decimal('precio', 8, 2);
    $table->enum('estado', ['solicitado', 'aceptado', 'en_curso', 'completado', 'cancelado']);
    // ... más campos
    $table->timestamps();
});
```

## Endpoints API

### Autenticación
```
POST /api/auth/registrar
  {
    "nombre": "Juan Pérez",
    "email": "juan@example.com",
    "password": "securepass",
    "tipo": "cliente"
  }
  
POST /api/auth/iniciar-sesion
  {
    "email": "juan@example.com",
    "password": "securepass"
  }
  
GET /api/auth/perfil (requiere token)
POST /api/auth/cambiar-password (requiere token)
POST /api/auth/logout (requiere token)
```

### Viajes
```
POST /api/viajes (crear nuevo viaje)
  {
    "cliente_id": 1,
    "origen_lat": 29.0469,
    "origen_lng": -13.5901,
    "origen_direccion": "Calle Principal, Arrecife",
    "destino_lat": 28.9644,
    "destino_lng": -13.6362,
    "destino_direccion": "Centro Comercial",
    "tarifa_id": 1
  }
  
GET /api/viajes/{id} (obtener viaje)
PUT /api/viajes/{id}/aceptar (taxista acepta viaje)
PUT /api/viajes/{id}/completar (completar viaje)
PUT /api/viajes/{id}/cancelar (cancelar viaje)
GET /api/viajes/cliente/{clienteId} (viajes del cliente)
GET /api/viajes/taxista/{taxistaId}/activos (viajes activos)
GET /api/viajes/taxistas-cercanos?lat=29.0469&lng=-13.5901&radio=5
```

### Transacciones
```
POST /api/transacciones/pagar (procesar pago)
  {
    "viaje_id": 1,
    "metodo": "tarjeta",
    "monto": 15.50
  }
  
GET /api/transacciones/usuario/{usuarioId}
GET /api/transacciones/usuario/{usuarioId}/balance
GET /api/transacciones/usuario/{usuarioId}/ganancias?periodo=mes
```

### Evaluaciones
```
POST /api/evaluaciones (crear evaluación)
  {
    "viaje_id": 1,
    "evaluador_id": 1,
    "evaluado_id": 2,
    "calificacion": 5,
    "comentario": "Muy buena experiencia",
    "aspecto": "trato"
  }
  
GET /api/evaluaciones/usuario/{usuarioId}
GET /api/evaluaciones/usuario/{usuarioId}/calificacion
```

## Inyección de Dependencias

La aplicación usa el contenedor de servicios de Laravel registrado en `AppServiceProvider`:

```php
// AppServiceProvider.php
class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton(ViajeService::class, function ($app) {
            return new ViajeService();
        });
    }
}

// En controllers - resolución automática
class ViajeController {
    public function __construct(ViajeService $viajeService) {
        // Laravel automáticamente crea una instancia de ViajeService
        $this->viajeService = $viajeService;
    }
}
```

**Ventajas**:
- Desacoplamiento de servicios
- Fácil testear (mock services)
- Configuración centralizada
- Cambios sin modificar controllers

## Tests Unitarios

Ubicados en `tests/Unit/`:

### Test de Modelos
```php
// ViajeTest.php
public function test_calcular_distancia() {
    $distancia = Viaje::calcularDistancia(29.0469, -13.5901, 28.9644, -13.6362);
    $this->assertGreaterThan(7, $distancia);
    $this->assertLessThan(10, $distancia);
}

public function test_estados_validos() {
    $estados = Viaje::estadosValidos();
    $this->assertContains('solicitado', $estados);
}
```

### Test de Servicios
```php
// ViajeServiceTest.php
public function test_crear_viaje_valida() {
    $datos = [...]; // datos válidos
    // $viaje = $service->crearViaje($datos);
    // $this->assertInstanceOf(Viaje::class, $viaje);
}
```

**Ejecutar tests**:
```bash
# Todos los tests
php artisan test

# Solo tests de modelos
php artisan test tests/Unit/Models

# Un test específico
php artisan test tests/Unit/Models/ViajeTest::test_calcular_distancia
```

## Normalización de Base de Datos

La base de datos está normalizada hasta BCNF (Boyce-Codd Normal Form):

### 1NF (Primera Forma Normal)
- Cada columna contiene valores atómicos
- No hay arrays anidados (excepto JSON)
- Cada fila es única

### 2NF (Segunda Forma Normal)
- Cumple 1NF
- Todos los atributos no clave dependen completamente de la clave primaria
- No hay dependencias parciales

### 3NF (Tercera Forma Normal)
- Cumple 2NF
- No hay dependencias transitivas
- Atributos no clave solo dependen de la clave primaria

### BCNF (Boyce-Codd Normal Form)
- Cumple 3NF
- Todas las determinantes son claves candidatas
- Máxima normalización posible

**Ejemplo**:
```
✗ No normalizado:
clientes (id, nombre, viaje_origen, viaje_destino, viaje_precio)
// Un cliente con múltiples viajes = duplicación

✓ Normalizado:
usuarios (id, nombre)
clientes (id, usuario_id)
viajes (id, cliente_id, origen, destino, precio)
```

## Workflow del Sistema

### 1. Cliente solicita un viaje
```
Cliente hace POST /api/viajes con:
  - Coordenadas origen
  - Coordenadas destino
  - Tarifa elegida

ViajeController.crear():
  - Valida request
  - Delega a ViajeService.crearViaje()
  
ViajeService.crearViaje():
  - Calcula distancia (Haversine)
  - Calcula tiempo estimado
  - Obtiene tarifa
  - Calcula precio
  - Crea Viaje en BD
  - Retorna viaje creado
  
Respuesta: JSON con Viaje creado (estado: solicitado)
```

### 2. Taxista acepta el viaje
```
Taxista hace PUT /api/viajes/{id}/aceptar con:
  - taxista_id

ViajeController.aceptar():
  - Valida request
  - Delega a ViajeService.aceptarViaje()

ViajeService.aceptarViaje():
  - Obtiene viaje
  - Llama Viaje.aceptar(taxista_id)
  - Crea notificación para cliente
  - Retorna viaje actualizado

Respuesta: JSON con Viaje aceptado (estado: aceptado)
```

### 3. Taxista completa el viaje
```
Taxista hace PUT /api/viajes/{id}/completar

ViajeService.completarViaje():
  - Obtiene viaje
  - Cambia estado a "completado"
  
Usuario puede hacer POST /api/evaluaciones
  - Evalúa al taxista
  
Usuario hace POST /api/transacciones/pagar
  - Procesa pago
  - Calcula comisión (15%)
  - Acredita dinero al taxista
```

## Configuración Necesaria

### 1. Variables de entorno (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lanzataxi
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

### 2. Ejecutar migraciones
```bash
php artisan migrate
```

### 3. Ejecutar seeders (datos de prueba)
```bash
php artisan db:seed
```

## Seguridad

### Validación
Todos los endpoints validan inputs:
```php
$validado = $request->validate([
    'email' => 'required|email',
    'password' => 'required|string|min:6',
    'tipo' => 'required|in:cliente,taxista,admin',
]);
```

### Autenticación
- Sanctum para tokens API
- Hashing de passwords
- CSRF protection

### Autorización
- Solo usuarios autenticados pueden crear viajes
- Solo taxistas pueden aceptar viajes
- Solo participantes pueden evaluar

## Escalabilidad

El diseño permite:
- **Microservicios**: Servicios pueden convertirse en APIs independientes
- **Cache**: Datos de tarifas y taxistas disponibles en Redis
- **Queue**: Notificaciones y emails en background jobs
- **Event listeners**: Viajes completados disparan eventos
- **Webhooks**: Integraciones externas en tiempo real

## Próximos Pasos

1. **Integración Frontend**
   - Cambiar URLs en js/ por endpoints API
   - Manejar autenticación token
   - Mostrar datos en tiempo real

2. **Características Avanzadas**
   - WebSockets para ubicación en tiempo real
   - Push notifications
   - Chat cliente-taxista
   - Historial de viajes

3. **Mejoras de Performance**
   - Índices en base de datos
   - Paginación de resultados
   - Caching de consultas frecuentes
   - Compresión de responses

4. **Monitoreo**
   - Logs de errores
   - Métricas de performance
   - Alertas de disponibilidad
