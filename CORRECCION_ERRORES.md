# Corrección de Errores - LanzaTaxi

## 🔧 Problemas Identificados y Solucionados

### Problema 1: HTTP ERROR 500 en localhost:8000
**Causa**: Faltaban archivos críticos de Laravel en el directorio `public/`
- No existía `public/index.php` (punto de entrada de Laravel)
- No existía `public/.htaccess` (reglas de reescritura)

**Solución**:
✅ Creado `public/index.php` con el bootstrap de Laravel 11
✅ Creado `public/.htaccess` con reglas de mod_rewrite

### Problema 2: Service Unavailable en http://lanzataxi/
**Causa**: Configuración incorrecta de Apache
- VirtualHost configurado como proxy a Node.js (puerto 3000)
- No había servidor Node.js corriendo
- DocumentRoot apuntaba a un servidor inexistente

**Solución**:
✅ Actualizada configuración de Apache para apuntar a Laravel
✅ DocumentRoot ahora es `/var/www/html/LanzaTaxi/public`
✅ Deshabilitados VirtualHosts obsoletos que causaban warnings
✅ Apache reiniciado con nueva configuración

## ✅ Estado Actual

### Servidores Funcionando

1. **Servidor de Desarrollo Laravel** (http://localhost:8000)
   - Status: ✅ Activo
   - Comando: `php artisan serve --host=0.0.0.0 --port=8000`
   - HTTP Response: 200 OK

2. **Apache con mod_php** (http://lanzataxi)
   - Status: ✅ Activo
   - DocumentRoot: `/var/www/html/LanzaTaxi/public`
   - HTTP Response: 200 OK

### Páginas Accesibles

Todas las páginas responden correctamente con código 200:

| Página | URL | Status |
|--------|-----|--------|
| Principal | http://localhost:8000/index.html | ✅ 200 |
| Dashboard Cliente | http://localhost:8000/cliente.html | ✅ 200 |
| Dashboard Taxista | http://localhost:8000/taxista.html | ✅ 200 |
| Dashboard Admin | http://localhost:8000/admin.html | ✅ 200 |

También accesibles vía Apache: http://lanzataxi/[página].html

## 📝 Archivos Creados/Modificados

### Archivos Nuevos
```
public/index.php              - Bootstrap de Laravel
public/.htaccess             - Reglas de reescritura
storage/logs/.gitignore      - Ignorar logs en git
storage/framework/*/.gitignore - Ignorar archivos temporales
```

### Archivos Modificados
```
/etc/apache2/sites-available/lanzataxi.conf - Nueva configuración Apache
start-server.sh              - Script mejorado para iniciar servidor
```

### Archivos Deshabilitados
```
/etc/apache2/sites-enabled/lanzataxi-backend.conf - Removido (obsoleto)
/etc/apache2/sites-enabled/lanzataxi-unificado.conf - Removido (obsoleto)
```

## 🚀 Cómo Usar

### Opción 1: Servidor de Desarrollo (Recomendado para desarrollo)
```bash
./start-server.sh
```
O manualmente:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Opción 2: Apache (Producción local)
Ya está configurado y corriendo. Solo accede a: http://lanzataxi

## 🔍 Verificación

Para verificar que todo funciona:

```bash
# Verificar Laravel dev server
curl -I http://localhost:8000/index.html

# Verificar Apache
curl -I http://lanzataxi/index.html

# Ver logs de Laravel (si hay errores)
tail -f storage/logs/laravel.log

# Ver logs de Apache (si hay errores)
sudo tail -f /var/log/apache2/lanzataxi-error.log
```

## ⚙️ Configuración Técnica

### Apache VirtualHost
```apache
<VirtualHost *:80>
    ServerName lanzataxi
    ServerAlias www.lanzataxi
    DocumentRoot /var/www/html/LanzaTaxi/public
    
    <Directory /var/www/html/LanzaTaxi/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/lanzataxi-error.log
    CustomLog ${APACHE_LOG_DIR}/lanzataxi-access.log combined
</VirtualHost>
```

### Módulos Apache Requeridos
- ✅ mod_rewrite (habilitado)
- ✅ mod_php (habilitado)

### Bootstrap Laravel (public/index.php)
```php
<?php
use Illuminate\Http\Request;
define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
```

## 🐛 Solución de Problemas

### Si localhost:8000 no funciona:
```bash
# Verificar que no haya otro proceso en el puerto 8000
sudo lsof -i :8000

# Detener procesos previos y reiniciar
pkill -f "php artisan serve"
./start-server.sh
```

### Si lanzataxi no funciona:
```bash
# Verificar configuración de Apache
sudo apache2ctl configtest

# Ver logs de Apache
sudo tail -f /var/log/apache2/lanzataxi-error.log

# Reiniciar Apache
sudo systemctl restart apache2
```

### Si aparece error de permisos:
```bash
# Reajustar permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 📊 Comparación: Antes vs Después

| Aspecto | Antes ❌ | Después ✅ |
|---------|---------|-----------|
| localhost:8000 | HTTP 500 | HTTP 200 |
| http://lanzataxi | Service Unavailable | HTTP 200 |
| index.php | ❌ No existía | ✅ Creado |
| .htaccess | ❌ No existía | ✅ Creado |
| Apache config | ❌ Proxy a Node.js inexistente | ✅ Apunta a Laravel |
| Páginas HTML | ❌ Inaccesibles | ✅ Todas funcionan |

## 🎯 Próximos Pasos (Opcional)

Si deseas usar el backend Node.js además de Laravel:

1. Instalar dependencias Node.js:
   ```bash
   npm install
   ```

2. Iniciar servidor Node.js:
   ```bash
   node server.js
   ```
   (Correrá en puerto 3000)

3. Configurar un VirtualHost adicional para Node.js en otro puerto/dominio

## 📚 Referencias

- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Apache mod_rewrite](https://httpd.apache.org/docs/current/mod/mod_rewrite.html)
- Configuración local en: `/etc/apache2/sites-available/lanzataxi.conf`

---

**✅ Todos los errores han sido corregidos y el sistema está funcionando correctamente.**

Última actualización: 12 de febrero de 2026
