# Configuración Completada - LanzaTaxi

## ✅ Estado de la Instalación

La aplicación **LanzaTaxi** ha sido configurada correctamente en tu sistema Kubuntu.

### Pasos Completados

1. ✅ Archivo `.env` configurado con credenciales de base de datos
2. ✅ Dependencias de Composer instaladas (102 paquetes)
3. ✅ Clave de aplicación Laravel generada
4. ✅ Estructura de directorios `storage/` y `bootstrap/cache/` creada
5. ✅ Permisos ajustados para www-data
6. ✅ Migraciones ejecutadas exitosamente (10 tablas creadas)
7. ✅ Seeders ejecutados (datos de prueba cargados)
8. ✅ Tests unitarios verificados (26 tests pasando)

## 🚀 Iniciar el Servidor

Para arrancar la aplicación, ejecuta:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Luego accede a: **http://localhost:8000**

## 📱 Páginas Disponibles

- **Inicio**: http://localhost:8000/index.html
- **Dashboard Cliente**: http://localhost:8000/cliente.html
- **Dashboard Taxista**: http://localhost:8000/taxista.html
- **Dashboard Admin**: http://localhost:8000/admin.html

## 🗄️ Configuración de Base de Datos

- **Base de datos**: `lanzataxi`
- **Usuario**: `lanza`
- **Contraseña**: `lanza123`
- **Host**: `127.0.0.1`
- **Puerto**: `3306`

## 🔧 Comandos Útiles

### Gestión de Base de Datos
```bash
# Resetear BD y cargar datos de prueba
php artisan migrate:fresh --seed --seeder=TestDataSeeder

# Ver estado de migraciones
php artisan migrate:status

# Ejecutar solo seeders
php artisan db:seed --class=TestDataSeeder
```

### Testing
```bash
# Ejecutar todos los tests unitarios
vendor/bin/phpunit tests/Unit/

# Ejecutar tests con artisan
php artisan test

# Ejecutar un test específico
vendor/bin/phpunit tests/Unit/NombreTest.php
```

### Laravel
```bash
# Ver todas las rutas
php artisan route:list

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

### MySQL/MariaDB
```bash
# Reiniciar servicio MySQL
sudo systemctl restart mysql

# Acceder a MySQL como usuario lanza
mysql -u lanza -p lanzataxi

# Ver tablas
mysql -u lanza -p -e "USE lanzataxi; SHOW TABLES;"
```

## 📦 Estructura del Proyecto

```
LanzaTaxi/
├── app/
│   ├── Models/              # Modelos (Usuario, Cliente, Taxista, etc.)
│   ├── Http/Controllers/    # Controladores
│   └── Services/            # Servicios (Auth, Viaje, Transacción, etc.)
├── database/
│   ├── migrations/          # Migraciones de BD
│   └── seeders/             # Seeders de datos
├── public/
│   ├── index.html          # Página principal
│   ├── cliente.html        # Dashboard cliente
│   ├── taxista.html        # Dashboard taxista
│   ├── admin.html          # Dashboard admin
│   ├── css/                # Estilos
│   └── js/                 # JavaScript frontend
├── routes/
│   ├── api.php             # Rutas API REST
│   └── web.php             # Rutas web
├── storage/                # Logs y archivos generados
└── tests/                  # Tests unitarios y de funcionalidad
```

## 🐛 Solución de Problemas Comunes

### Error: "composer install fails"
```bash
# Asegurar extensiones PHP instaladas
sudo apt install php-zip php-mbstring php-xml php-curl

# Limpiar y reinstalar
rm -rf vendor composer.lock
composer install --no-interaction
```

### Error: "Access denied for user"
```bash
# Verificar configuración .env
cat .env | grep DB_

# Recrear usuario MySQL
sudo mysql -u root < setup_database.sql
```

### Error: "Permission denied" en storage
```bash
# Reajustar permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Dar permisos temporales para desarrollo
sudo chmod -R 777 storage bootstrap/cache
```

### Error en migraciones (tablas ya existen)
```bash
# Usar migrate:fresh para recrear todo
php artisan migrate:fresh --seed --seeder=TestDataSeeder
```

### El servidor no es accesible desde otras máquinas
```bash
# Asegúrate de usar --host=0.0.0.0
php artisan serve --host=0.0.0.0 --port=8000

# Verificar firewall
sudo ufw allow 8000/tcp
```

## 🌐 Configuración con Apache/Nginx (Producción)

### Apache
1. Crear VirtualHost:
```apache
<VirtualHost *:80>
    ServerName lanzataxi.local
    DocumentRoot /var/www/html/LanzaTaxi/public
    
    <Directory /var/www/html/LanzaTaxi/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/lanzataxi_error.log
    CustomLog ${APACHE_LOG_DIR}/lanzataxi_access.log combined
</VirtualHost>
```

2. Habilitar módulos y sitio:
```bash
sudo a2enmod rewrite
sudo a2ensite lanzataxi.conf
sudo systemctl restart apache2
```

### Nginx
```nginx
server {
    listen 80;
    server_name lanzataxi.local;
    root /var/www/html/LanzaTaxi/public;
    
    index index.html index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 📊 Usuarios de Prueba (desde TestDataSeeder)

Los seeders han creado usuarios de prueba. Consulta `database/seeders/TestDataSeeder.php` para ver las credenciales.

## 🔒 Seguridad

**IMPORTANTE**: Antes de desplegar en producción:
1. Cambiar `APP_DEBUG=false` en `.env`
2. Cambiar contraseñas de base de datos
3. Regenerar `JWT_SECRET`
4. Configurar HTTPS
5. Revisar permisos de archivos

## 📝 Notas Adicionales

- **PHP Version**: 8.2+ (actualmente usando 8.4.17)
- **Laravel Version**: 11.48.0
- **Node**: Opcional (solo para rebuildar assets frontend)

## ✨ Script de Instalación Automatizado

Si necesitas reinstalar desde cero, ejecuta:

```bash
./setup.sh
```

Este script automatiza todos los pasos de configuración.

---

**¡Listo para desarrollar! 🚕💨**

Si encuentras problemas, revisa los logs en `storage/logs/laravel.log`
