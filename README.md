# 🚖 LanzaTaxi - Sistema de Gestión de Taxis de Lanzarote

Proyecto Final de DAW - Sistema completo de gestión de taxis para la isla de Lanzarote.

## 🚀 Características Principales

### 👤 Para Clientes
- ✅ Solicitud de taxi con ubicación automática
- ✅ Cálculo de precio estimado en tiempo real
- ✅ Seguimiento del taxi en el mapa
- ✅ Historial de viajes y facturas descargables
- ✅ Sistema de valoraciones

### 🚕 Para Taxistas
- ✅ Control de disponibilidad (Libre/Ocupado)
- ✅ Notificaciones de nuevas solicitudes
- ✅ Navegación integrada con mapas
- ✅ Historial de ingresos
- ✅ Gestión de servicios

### 👨‍💼 Para Administradores
- ✅ Dashboard con estadísticas en tiempo real
- ✅ Gestión de usuarios y taxistas
- ✅ Configuración de tarifas
- ✅ Mapa de calor de demanda
- ✅ Gestión de incidencias

## 📋 Tecnologías Utilizadas

- **Backend**: Node.js + Express
- **Base de Datos**: SQLite
- **Frontend**: HTML5, CSS3, JavaScript
- **Mapas**: Leaflet + OpenStreetMap
- **Tiempo Real**: Socket.IO (WebSockets)
- **Autenticación**: JWT
- **Gráficas**: Chart.js

## 🛠️ Instalación

```bash
# Instalar dependencias
npm install

# Iniciar servidor de desarrollo
npm run dev

# Iniciar servidor de producción
npm start
```

## 🌐 Acceso

- **URL**: http://localhost:3000
- **Usuarios de prueba**:
  - Cliente: `cliente@test.com` / `123456`
  - Taxista: `taxista@test.com` / `123456`
  - Admin: `admin@test.com` / `123456`

## 📱 Funcionalidades Implementadas

### Sistema de Tarifas de Lanzarote
- Tarifa 1 (Urbana): €0.60/km
- Tarifa 2 (Interurbana): €0.75/km
- Suplementos: Aeropuerto, Puerto, Nocturno, Festivo

### Geolocalización
- Detección automática de ubicación
- Autocompletado de direcciones
- Cálculo de distancias y rutas
- Mapas interactivos en tiempo real

### Comunicación en Tiempo Real
- Actualización de posición del taxi
- Notificaciones instantáneas
- Chat entre taxista y cliente

## 📊 Estructura del Proyecto

```
LanzaTaxi/
├── server.js           # Servidor principal
├── database.js         # Configuración BD
├── routes/            # API REST
├── public/            # Frontend
│   ├── index.html     # Landing page
│   ├── cliente.html   # Panel cliente
│   ├── taxista.html   # Panel taxista
│   ├── admin.html     # Panel admin
│   ├── css/          # Estilos
│   └── js/           # JavaScript
└── utils/            # Utilidades
```

## 👨‍🎓 Proyecto Final DAW

Este proyecto cumple con los requisitos de:
- Desarrollo Web en Entorno Cliente
- Desarrollo Web en Entorno Servidor
- Diseño de Interfaces Web
- Despliegue de Aplicaciones Web

---

**Autor**: Proyecto Final DAW 2º
**Fecha**: 2026
