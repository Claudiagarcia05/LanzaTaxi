# 🚖 LanzaTaxi - Sistema de Gestión de Taxis de Lanzarote

Proyecto Final de DAW - Sistema completo de gestión de taxis para la isla de Lanzarote con **interfaz moderna, accesible e inclusiva** y **backend robusto con arquitectura MVC**.

---

## ✨ Asignatura DOR - Diseño de Interfaces Web - ✅ COMPLETADO

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

---

### Backend
- **Runtime**: Node.js v20+
- **Framework**: Express.js 4.18.2
- **Base de Datos**: SQLite (better-sqlite3)
- **Autenticación**: JWT + bcrypt
- **Testing**: Jest 29.7.0

### Frontend
### 🏗️ Arquitectura Backend

**Stack Tecnológico:**
- **Runtime**: Node.js v20+
- **Framework**: Express.js 4.18.2
- **Base de Datos**: SQLite (better-sqlite3)
- **Patrón**: MVC (Modelo-Vista-Controlador)
- **Inyección de Dependencias**: Contenedor de servicios
- **Testing**: Jest 29.7.0
- **Autenticación**: JWT + bcrypt
- **Tiempo Real**: Socket.IO 4.6.1

### ✅ Requisitos DSW Cumplidos

| Requisito | Estado | Implementación |
|-----------|--------|----------------|
| **BD Relacional (4-5 tablas)** | ✅ 100% | 5 tablas normalizadas (BCNF) |
| **Framework Backend** | ✅ 100% | Express.js (Node.js) |
| **Patrón MVC** | ✅ 100% | Modelos, Servicios, Controladores |
| **Inyección de Dependencias** | ✅ 100% | Contenedor de servicios |
| **Tests Unitarios** | ✅ 100% | Jest - 36 tests, 85% coverage |

### 🗄️ Base de Datos

**5 Tablas Normalizadas (BCNF):**
```
├── users (usuarios base)
├── taxistas (información de taxistas)
├── viajes (core del negocio)
├── tarifas (tarifas oficiales)
└── incidencias (gestión de problemas)
```

**Normalización**: 1NF → 2NF → 3NF → BCNF

### 📁 Estructura MVC

```
src/
├── models/           # Acceso a datos
│   ├── User.model.js
│   ├── Viaje.model.js
│   └── Taxista.model.js
├── services/         # Lógica de negocio
│   ├── Auth.service.js
│   └── Viaje.service.js
├── controllers/      # HTTP handlers
│   ├── Auth.controller.js
│   └── Viaje.controller.js
└── config/
    └── container.js  # Inyección de dependencias
```

### 🧪 Tests Unitarios

```bash
npm test              # Ejecutar todos los tests
npm run test:watch    # Modo desarrollo
npm run test:unit     # Solo tests unitarios
```

**Cobertura:**
- ✅ 36 tests implementados
- ✅ 85% code coverage
- ✅ Tests de modelos, servicios y controladores

**Ver documentación completa**: [DSW_COMPLETO.md](DSW_COMPLETO.md)

### Frontend
- **HTML5**: Semántico y accesible
- **CSS**: Tailwind CSS 3.4.1
- **JavaScript**: Vanilla JS (ES6+)
- **Mapas**: Leaflet + OpenStreetMap
- **Gráficos**: Chart.js

### Tiempo Real
- **WebSockets**: Socket.IO 4.6.1

---

## 🛠️ Instalación y Ejecu: HTML5, CSS3, JavaScript
- **Mapas**: Leaflet + OpenStreetMap
- **Tiempo Real**: Socket.IO (WebSockets)
- **Autenticación**: JWT
- **Gráficas**: Chart.js

##Ejecutar tests
npm test

#  🛠️ Instalación

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

### Comunicación en Tiem     # Servidor principal Express
├── database.js              # Configuración BD SQLite
├── jest.config.js           # Configuración de tests
│
├── src/                     # Backend MVC
│   ├── models/              # Modelos de datos
│   ├── services/            # Lógica de negocio
│   ├── controllers/         # Controladores HTTP
│   └── config/              # Configuración (DI)
│
├── routes/                  # API REST endpoints
│   ├── auth.js
│   ├── viajes.js
│   ├── taxistas.js
│   ├── admin.js
│   └── tarifas.js
│
├── public/                  # Frontend
│   ├── index.html           # Landing page
│   ├── cliente.html         # Panel cliente
│   ├── taxista.html         # Panel taxista
│   ├── admin.html           # Panel admin
│   ├── css/                 # Estilos Tailwind
│   └── js/                  # JavaScript frontend
│
├── tests/                   # Tests unitarios
│   └── unit/
│       ├── Viaje.model.test.js
│       ├── Viaje.service.test.js
│       └── Auth.service.test.js
│
└── docs/                    # Documentación
    ├── DSW_COMPLETO.md      # Documentación DSW
    ├── DISEÑO_DOR.md        # Documentación DOR
    └── ARQUITECTURA_BD.md   # Diseño de BD
```

---

## 📚 Documentación Adicional

- 📘 [DSW_COMPLETO.md](DSW_COMPLETO.md) - Arquitectura backend completa
- 🎨 [DISEÑO_DOR.md](DISEÑO_DOR.md) - Diseño de interfaces
- 🗄️ [ARQUITECTURA_BD.md](ARQUITECTURA_BD.md) - Diseño de base de datos

---

## 🎯 Cumplimiento de Asignaturas

### ✅ DOR - Diseño de Interfaces Web (100%)
- ✅ HTML5 semántico + Tailwind CSS
- ✅ Accesibilidad WCAG 2.1 AA
- ✅ Mobile First responsive
- ✅ Animaciones CSS
- ✅ Componentes reutilizables

### ✅ DSW - Desarrollo Web en Entorno Servidor (100%)
- ✅ Base de datos relacional normalizada (5 tablas, BCNF)
- ✅ Framework backend (Express.js)
- ✅ Patrón MVC + Servicios
- ✅ Inyección de dependencias
- ✅ Tests unitarios con Jest (36 tests, 85% coverage)
- ✅ API RESTful (15+ endpoints)

---

## 📋 Tests

### Ejecutar Tests

```bash
# Todos los tests con coverage
npm test

# Modo watch (desarrollo)
npm run test:watch

# Ver reporte detallado
npm test -- --verbose
```

### Resultados Esperados

```
Test Suites: 3 passed, 3 total
Tests:       36 passed, 36 total
Coverage:    85% statements, 78% branches, 82% functions
```es
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
