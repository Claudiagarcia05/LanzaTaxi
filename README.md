# 🚖 LanzaTaxi - Sistema de Gestión de Taxis de Lanzarote

Proyecto Final de DAW - Sistema completo de gestión de taxis para la isla de Lanzarote con **interfaz moderna, accesible e inclusiva**.

## ✨ Asignatura DOR - Diseño de Interfaces Web - COMPLETADO

### 📋 Características Completadas

#### 🎨 Diseño & UX
- ✅ **HTML5 Semántico**: Estructura correcta con etiquetas semánticas
- ✅ **Tailwind CSS**: Framework utility-first para diseño moderno
- ✅ **Mobile First Responsive**: Perfecto en móvil, tablet y desktop
- ✅ **Animaciones CSS**: Transiciones suaves y micro-interacciones
- ✅ **Sistema de Componentes**: Botones, tarjetas, formularios, alertas reutilizables

#### ♿ Accesibilidad WCAG 2.1 AA+
- ✅ **Contraste de Colores**: Ratios WCAG AA verificados (4.5:1 y superiores)
- ✅ **Navegación por Teclado**: Tab, Shift+Tab, Enter, Escape completamente funcionales
- ✅ **Lectores de Pantalla**: Aria labels, live regions, roles semánticos
- ✅ **Formularios Accesibles**: Labels asociados, validación clara, mensajes de error
- ✅ **Focus Visible**: Todos los elementos enfocables tienen indicadores claros

#### 🚀 Características Bonus
- ✅ Skip link "Ir al contenido principal"
- ✅ Menú móvil con control aria-expanded
- ✅ Notificaciones dinámicas con aria-live
- ✅ Iconografía mixta (emoji + descripciones)
- ✅ Indicadores de carga y estados

### 🚀 Características Principales

#### 👤 Para Clientes
- ✅ Interfaz para solicitar taxi
- ✅ Cálculo de precio estimado
- ✅ Integración con mapas (Leaflet)
- ✅ Historial de viajes
- ✅ Panel de perfil

#### 🚕 Para Taxistas
- ✅ Panel de solicitudes disponibles
- ✅ Control de estado (en línea/ocupado)
- ✅ Historial de viajes y ganancias
- ✅ Sistema de valoraciones
- ✅ Información del perfil

#### 👨‍💼 Para Administradores
- ✅ Dashboard con estadísticas
- ✅ Gestión de usuarios y taxistas
- ✅ Monitoreo de viajes en tiempo real
- ✅ Control de tarifas
- ✅ Análisis de ingresos

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
