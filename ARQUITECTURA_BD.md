# 🗄️ DISEÑO DE BASE DE DATOS - LanzaTaxi

## Diagrama Relacional

```
┌─────────────────────────────────────────────────────────────┐
│                    USUARIOS (base user)                    │
├─────────────────────────────────────────────────────────────┤
│ id (PK)                                                     │
│ nombre                                                      │
│ email (UNIQUE)                                              │
│ password (hash)                                             │
│ teléfono                                                    │
│ tipo (cliente|taxista|admin)                               │
│ avatar_url                                                  │
│ estado (activo|inactivo|suspendido)                        │
│ verificado (0|1)                                            │
│ created_at, updated_at                                      │
└─────────────────────────────────────────────────────────────┘
                            │
                ┌───────────┼───────────┐
                │           │           │
                ▼           ▼           ▼
        ┌──────────────┐ ┌──────────────────┐ ┌──────────────┐
        │  CLIENTES    │ │   TAXISTAS       │ │    ADMINS    │
        ├──────────────┤ ├──────────────────┤ ├──────────────┤
        │ usuario_id   │ │ usuario_id (FK)  │ │ usuario_id   │
        │ direccion    │ │ licencia_nro     │ │ permisos     │
        │ ciudad       │ │ vehiculo_id (FK) │ │ activo       │
        │ pais         │ │ estado (available)│ │              │
        │ metodo_pago  │ │ ubicacion (GPS)  │ │              │
        └──────────────┘ │ calificacion     │ └──────────────┘
                         │ municipio        │
                         │ comisiones_pag   │
                         └──────────────────┘
                                 │
                                 ▼
                         ┌──────────────────┐
                         │   VEHICULOS      │
                         ├──────────────────┤
                         │ id (PK)          │
                         │ taxista_id (FK)  │
                         │ marca            │
                         │ modelo           │
                         │ matricula(UNIQUE)│
                         │ color            │
                         │ seats            │
                         │ licencia (num)   │
                         │ seguro_until     │
                         │ itv_until        │
                         └──────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                        VIAJES                               │
├─────────────────────────────────────────────────────────────┤
│ id (PK)                                                     │
│ cliente_id (FK → clientes)                                 │
│ taxista_id (FK → taxistas) [nullable]                      │
│ origen_lat, origen_lng                                      │
│ origen_direccion                                            │
│ destino_lat, destino_lng                                    │
│ destino_direccion                                           │
│ distancia_km (decimal)                                      │
│ tiempo_estimado (minutos)                                   │
│ precio (decimal)                                            │
│ tarifa_aplicada (1|2|suplemento)  [FK]                     │
│ estado (solicitado|aceptado|en_curso|completado|cancelado)│
│ ocupantes                                                   │
│ comentario_cliente                                          │
│ calificacion_cliente (1-5)                                  │
│ calificacion_taxista (1-5)                                  │
│ metodo_pago (efectivo|tarjeta|bizum)                       │
│ pagado (0|1)                                                │
│ created_at, updated_at                                      │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                       TARIFAS                               │
├─────────────────────────────────────────────────────────────┤
│ id (PK)                                                     │
│ nombre (Tarifa 1|Tarifa 2|etc)                             │
│ tipo (urbano|interurbano|especial)                         │
│ bajada_bandera (decimal)                                    │
│ precio_km (decimal)                                         │
│ precio_espera_hora (decimal)                                │
│ minimo_viaje (decimal)                                      │
│ activa (0|1)                                                │
│ vigente_desde, vigente_hasta                               │
│ municipios (JSON array)                                     │
│ created_at, updated_at                                      │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    SUPLEMENTOS                              │
├─────────────────────────────────────────────────────────────┤
│ id (PK)                                                     │
│ nombre (Aeropuerto|Puerto|Nocturno|Festivo)               │
│ tipo (fijo|variable)                                        │
│ valor (decimal)                                             │
│ descripcion                                                 │
│ activo (0|1)                                                │
│ created_at, updated_at                                      │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                   REVALUACIONES                             │
├─────────────────────────────────────────────────────────────┤
│ id (PK)                                                     │
│ viaje_id (FK → viajes)                                      │
│ evaluador_id (FK → usuarios)                               │
│ evaluado_id (FK → usuarios)                                │
│ calificacion (1-5)                                          │
│ comentario                                                  │
│ aspecto (puntualidad|limpieza|trato|seguridad)            │
│ created_at, updated_at                                      │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                   TRANSACCIONES                             │
├─────────────────────────────────────────────────────────────┤
│ id (PK)                                                     │
│ viaje_id (FK → viajes)                                      │
│ usuario_id (FK → usuarios)                                  │
│ tipo (pago|comisión|reembolso)                             │
│ monto (decimal)                                             │
│ metodo (efectivo|tarjeta|bizum|wallet)                     │
│ estado (pendiente|completado|fallido)                      │
│ referencia (transacción extern)                             │
│ created_at, updated_at                                      │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                 NOTIFICACIONES                              │
├─────────────────────────────────────────────────────────────┤
│ id (PK)                                                     │
│ usuario_id (FK → usuarios)                                  │
│ viaje_id (FK → viajes [nullable])                          │
│ tipo (viaje_aceptado|llegada|nuevo_viaje)                  │
│ titulo                                                      │
│ mensaje                                                     │
│ leida (0|1)                                                 │
│ created_at, updated_at                                      │
└─────────────────────────────────────────────────────────────┘
```

## Relaciones Clave

### 1. **USUARIOS** (Tabla base)
- Herencia polimórfica con CLIENTES, TAXISTAS, ADMINS
- Relaciones: viajes (cliente), viajes (taxista), evaluaciones

### 2. **CLIENTES** (1:N con VIAJES)
- Un cliente tiene MUCHOS viajes
- Relación con TRANSACCIONES (pagos)

### 3. **TAXISTAS** (1:N con VIAJES, 1:1 con VEHICULOS)
- Un taxista tiene MUCHOS viajes
- Un taxista tiene UN vehículo activo
- Relación con TRANSACCIONES (ingresos)

### 4. **VEHICULOS** (1:N con VIAJES)
- Un vehículo pertenece a UN taxista
- Registra histórico de viajes

### 5. **VIAJES** (Tabla central)
- Relaciona CLIENTES y TAXISTAS
- Relaciona TARIFAS (precio)
- Relaciona TRANSACCIONES (pagos)
- Relaciona EVALUACIONES (ratings)
- Relaciona NOTIFICACIONES (alertas)

### 6. **TARIFAS** (1:N con VIAJES)
- Tarifas oficiales del Cabildo
- Aplicadas en cada viaje

### 7. **SUPLEMENTOS** (Relación indirecta con VIAJES)
- Se aplican a viajes según criterios
- Aeropuerto, Puerto, Nocturno, Festivo

### 8. **EVALUACIONES** (1:N con VIAJES)
- Ratings de cliente a taxista
- Ratings de taxista a cliente

### 9. **TRANSACCIONES** (1:N con VIAJES)
- Registra pagos y comisiones
- Auditoría financiera completa

### 10. **NOTIFICACIONES** (1:N con USUARIOS y VIAJES)
- Push notifications
- Email alerts
- Historial de comunicaciones

## Índices Necesarios

```sql
-- Índices de búsqueda
INDEX (email) ON usuarios;
INDEX (tipo) ON usuarios;
INDEX (estado) ON usuarios;
INDEX (tipo, estado) ON usuarios;

-- Índices de relaciones
INDEX (cliente_id) ON viajes;
INDEX (taxista_id) ON viajes;
INDEX (vehiculo_id) ON vehiculos;
INDEX (usuario_id) ON clientes;
INDEX (usuario_id) ON taxistas;

-- Índices de búsqueda de viajes
INDEX (estado) ON viajes;
INDEX (cliente_id, created_at) ON viajes;
INDEX (taxista_id, created_at) ON viajes;
INDEX (created_at) ON viajes;

-- Índices de geolocalización
INDEX (origen_lat, origen_lng) ON viajes;
INDEX (destino_lat, destino_lng) ON viajes;
```

## Constraints y Validaciones

1. **Usuarios**: email UNIQUE, tipo válido (conjunto)
2. **Clientes**: usuario_id UNIQUE
3. **Taxistas**: usuario_id UNIQUE, licencia_nro UNIQUE, vehiculo_id UNIQUE
4. **Vehiculos**: matricula UNIQUE, licencia NOT NULL
5. **Viajes**: cliente_id NOT NULL, estado válido (conjunto)
6. **Tarifas**: nombre UNIQUE, bajada_bandera > 0, precio_km > 0
7. **Transacciones**: viaje_id NOT NULL, monto >= 0

## Normalizabilidad

✅ **1NF**: Todos los campos atómicos
✅ **2NF**: Dependencias funcionales completamente resueltas
✅ **3NF**: Sin dependencias transitivas
✅ **BCNF**: Todos los determinantes son claves candidatas
