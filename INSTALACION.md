# 🎨 Guía de Instalación - LanzaTaxi

## Requisitos Previos
- Node.js 16+ instalado
- NPM o Yarn
- Git (opcional)

---

## 📦 Paso 1: Instalar Dependencias

Navega a la carpeta del proyecto y ejecuta:

```bash
cd C:\xampp\htdocs\LanzaTaxi
npm install
```

Esto instalará:
- ✅ `tailwindcss` - Framework CSS
- ✅ `postcss` - Procesador de CSS
- ✅ `autoprefixer` - Prefijos automáticos del navegador

---

## 🎨 Paso 2: Compilar Tailwind CSS

### Opción A: Modo Desarrollo (Con Watch)
Recompila automáticamente cuando cambies archivos:

```bash
npm run watch
```

O manualmente:
```bash
npx tailwindcss -i ./public/css/styles.css -o ./public/css/styles.css--watch
```

### Opción B: Compilación Única
Para producción:

```bash
npx tailwindcss -i ./public/css/styles.css -o ./public/css/styles.css
```

---

## 🚀 Paso 3: Iniciar el Servidor

En otra terminal (mantén el watch de Tailwind activo):

```bash
npm run dev
```

O sin nodemon:
```bash
npm start
```

El servidor estará disponible en:
- http://localhost:3000
- http://lanzataxi (después de configura Virtual Hosts)

---

## ✅ Verificación

### 1. Comprueba que Tailwind está compilado
```bash
# Debería existir el archivo compilado
ls public/css/styles.css
```

### 2. Abre el navegador
```
http://lanzataxi
```

### 3. Devtools - Inspecciona estilos
- Presiona `F12` → Elements → Inspecciona cualquier elemento
- Verifica que tiene clases como `text-blue-600`, `flex`, etc.

---

## 🛠️ Scripts de Utilidad

Añade estos scripts a tu `package.json` si no existen:

```json
"scripts": {
  "start": "node server.js",
  "dev": "nodemon server.js",
  "watch:css": "tailwindcss -i ./public/css/styles.css -o ./public/css/styles.css --watch",
  "build:css": "tailwindcss -i ./public/css/styles.css -o ./public/css/styles.css",
  "build": "npm run build:css && npm run build:js",
  "dev:all": "concurrently \"npm run watch:css\" \"npm run dev\""
}
```

---

## 🐛 Solución de Problemas

### Problema: Los estilos no se aplican
**Solución**:
1. Limpia el caché del navegador (`Ctrl + Shift + Delete`)
2. Asegúrate que `tailwindcss` compile: `npm run build:css`
3. Recarga la página (`Ctrl + F5`)

### Problema: Tailwind no encuentra archivos
**Solución**:
1. Verifica `tailwind.config.js`:
```javascript
content: [
  "./public/**/*.{html,js}",  // Debe incluir tus archivos
],
```

### Problema: Los cambios no se guardan en watch
**Solución**:
1. Detén el proceso: `Ctrl + C`
2. Reinicia: `npm run watch:css`
3. Node requiere crear/eliminar archivos, no solo editar

---

## 📚 Recursos Útiles

- [Documentación Tailwind CSS](https://tailwindcss.com/docs)
- [Guía PostCSS](https://postcss.org/)
- [WCAG 2.1 - Accesibilidad](https://www.w3.org/WAI/WCAG21/quickref/)

---

## 🌟 Buen a Saber

1. **Tailwind vs. CSS Personalizado**: Usa clases de Tailwind primero, solo añade CSS personalizado en `styles.css` cuando realmente lo necesites.

2. **PurgeCSS**: El build final eliminará automáticamente estilos no utilizados.

3. **Performance**: El CSS compilado a producción será mucho más pequeño.

4. **Extensibilidad**: Puedes personalizar colores, fuentes, etc., en `tailwind.config.js`

---

**¡Listo para diseñar! 🎨**
