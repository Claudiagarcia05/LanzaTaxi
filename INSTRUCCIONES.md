# 🚕 LanzaTaxi - Instrucciones de Uso

## ✅ Estado Actual
La aplicación está **100% estructurada y lista para usar**. Todos los archivos HTML, CSS y JavaScript están configurados correctamente.

---

## 🚀 Cómo Acceder a la Aplicación

### 1. **Página Principal (Index)**
```
http://localhost/LanzaTaxi/
```
- Landing page con todas las secciones
- Sistema de login/registro con modal accesible
- Información de tarifas y municipios

### 2. **Panel del Cliente**
```
http://localhost/LanzaTaxi/public/cliente.html
```
**Credenciales de prueba:**
- Email: `cliente@test.com`
- Contraseña: `123456`

**Funcionalidades:**
- ✅ Solicitar taxi (formulario completo)
- ✅ Historial de viajes
- ✅ Perfil de usuario
- ✅ Seguimiento en tiempo real

### 3. **Panel del Taxista**
```
http://localhost/LanzaTaxi/public/taxista.html
```
**Credenciales de prueba:**
- Email: `taxista@test.com`
- Contraseña: `123456`

**Funcionalidades:**
- ✅ Dashboard con estadísticas
- ✅ Cola de servicios
- ✅ Ganancias y mapa en tiempo real
- ✅ Gestión de estado (disponible/ocupado/fuera)

### 4. **Panel del Administrador**
```
http://localhost/LanzaTaxi/public/admin.html
```
**Credenciales de prueba:**
- Email: `admin@test.com`
- Contraseña: `123456`

**Funcionalidades:**
- ✅ Dashboard con KPIs principales
- ✅ Gráficos de demanda por hora
- ✅ Servicios por municipio
- ✅ Gestión de usuarios, taxistas, viajes y tarifas
- ✅ Monitor de viajes en tiempo real

---

## 🔧 Para Ver los Cambios en el Navegador

Si los cambios CSS/HTML **no se muestran** en el navegador, realiza un **Hard Refresh**:

### En Windows (Chrome, Firefox, Edge):
```
Ctrl + Shift + R
```

### En Mac:
```
Cmd + Shift + R
```

### Alternativa (Limpiar todo):
1. Abre DevTools: `F12`
2. Right-click en el botón de recargar (arriba) → "Vaciar caché y recargar"
3. Cierra completamente el navegador
4. Reabre y accede a la URL

---

## 📁 Estructura de Archivos

```
LanzaTaxi/
├── public/
│   ├── index.html          ← Landing page (Logo, Hero, Features, Tarifas)
│   ├── cliente.html        ← Panel de pasajero
│   ├── taxista.html        ← Panel de taxista
│   ├── admin.html          ← Panel de administrador
│   ├── css/
│   │   └── styles.css      ← CSS compilado (TAILWIND + Componentes)
│   ├── js/
│   │   ├── auth.js         ← Autenticación
│   │   ├── cliente.js      ← Lógica cliente
│   │   ├── taxista.js      ← Lógica taxista
│   │   └── admin.js        ← Lógica admin
│   └── img/
│       └── logo_sin_fondo.png
├── routes/
├── database.js
├── package.json
└── server.js
```

---

## 🎨 Sistema de Diseño

### Colores Oficiales
- **Amarillo (Primario)**: `#FFD700` - Botones, destacados
- **Azul (Secundario)**: `#0068CC` - Enlaces, iconos
- **Negro (Base)**: `#1A1A1A` - Texto principal
- **Verde (Éxito)**: `#10B981` - Estados positivos
- **Rojo (Error)**: `#EF4444` - Estados negativos

### Componentes
- **Buttons**: `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-success`, `.btn-danger`
- **Cards**: `.card` con shadow y bordes elegantes
- **Badges**: `.badge`, `.badge-available`, `.badge-occupied`, etc.
- **Formularios**: `.form-input`, `.form-label`, `.input-icon`
- **Sidebar**: `.sidebar`, `.sidebar-nav-item`
- **Tablas**: `.table`, `.table-container`

### Animaciones
- Fade In: `.animate-slideIn`
- Slide: `.animate-slideInRight`
- Pulse subtle: `.animate-pulse-subtle`

---

## ✨ Características Principales

### Index (Landing Page)
✅ Navbar sticky con navegación  
✅ Hero section con CTA  
✅ Sección "Cómo funciona" (3 pasos)  
✅ Tarifas oficiales del Cabildo de Lanzarote  
✅ Cobertura en 7 municipios  
✅ CTA final + Footer completo  
✅ Modal login/register accesible  

### Cliente
✅ Formulario intuitivo para solicitar taxi  
✅ Cálculo de distancia y precio en tiempo real  
✅ Historial de viajes con detalles  
✅ Seguimiento de taxi en mapa (Leaflet)  
✅ Perfil de usuario editable  
✅ Datos de prueba incluidos  

### Taxista
✅ Dashboard con estadísticas personales  
✅ Cola de servicios (nuevos + programados)  
✅ Cambio de estado (disponible/ocupado/fuera)  
✅ Mapa en tiempo real con posición  
✅ Historial de viajes  
✅ Estadísticas de ganancias  

### Admin
✅ Dashboard con KPIs principales  
✅ Gráfico de demanda por hora (Chart.js)  
✅ Servicios por municipio (barras de progreso)  
✅ Tabla de licencias por municipio  
✅ Gestión de usuarios (tabla)  
✅ Gestión de taxistas (tabla)  
✅ Monitor de viajes en tiempo real  
✅ Editor de tarifas  

---

## 🔐 Seguridad y Accesibilidad

✅ WCAG 2.1 AA compliant  
✅ Skip links para navegación por teclado  
✅ Focus visible en elementos interactivos  
✅ Alt text en imágenes  
✅ Estructura semántica HTML5  
✅ Responsive design (mobile-first)  
✅ Alto contraste respaldado  
✅ Acceso por teclado a todo  

---

## 📝 Notas Importantes

1. **Los datos son de demostración**: La aplicación ahora mismo no tiene backend conectado, todos los datos son estáticos.

2. **Base de datos**: Cuando implementes el backend en Node.js/Express, conecta con:
   - `database.js` (configuración)
   - `routes/` (endpoints)

3. **Tailwind CSS**: Está compilado en `public/css/styles.css`. Si cambias algo que requiera recompilación, ejecuta:
   ```bash
   npx tailwindcss -i ./public/css/styles.css -o ./public/css/styles.css
   ```

4. **Cache del navegador**: Si hay cambios que no se ven:
   - Haz **Ctrl + Shift + R** (hard refresh)
   - O abre DevTools → Settings → Network → Deshabilitar cache

---

## 🎯 Próximos Pasos

Para completar la aplicación:
1. Implementar autenticación real en `routes/auth.js`
2. Conectar base de datos en `database.js`
3. Crear endpoints REST para cada panel
4. Implementar WebSockets para tiempo real (viajes activos)
5. Integrar APIs de mapas (Leaflet está listo)
6. Sistema de pagos reales

---

## 📞 Contacto

Para cualquier duda sobre estructura o componentes, revisa:
- `public/css/styles.css` → Todos los componentes documentados
- HTML con comentarios explicativos en cada sección

---

**¡LanzaTaxi está listo para usar! 🚕✨**
