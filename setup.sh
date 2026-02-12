#!/bin/bash

# Script de configuración rápida para LanzaTaxi en Kubuntu
# Ejecutar con: bash setup.sh

set -e

echo "=== Configuración de LanzaTaxi ==="
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # Sin color

# Paso 1: Crear base de datos
echo -e "${YELLOW}[1/5] Creando base de datos y usuario MySQL...${NC}"
sudo mysql -u root << 'EOF'
CREATE DATABASE IF NOT EXISTS lanzataxi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'lanza'@'localhost' IDENTIFIED BY 'lanza123';
GRANT ALL PRIVILEGES ON lanzataxi.* TO 'lanza'@'localhost';
FLUSH PRIVILEGES;
EOF
echo -e "${GREEN}✓ Base de datos creada${NC}"
echo ""

# Paso 2: Ejecutar migraciones
echo -e "${YELLOW}[2/5] Ejecutando migraciones y seeders...${NC}"
php artisan migrate:fresh --seed --seeder=TestDataSeeder --force
echo -e "${GREEN}✓ Migraciones completadas${NC}"
echo ""

# Paso 3: Verificar tests (opcional)
echo -e "${YELLOW}[3/5] Ejecutando tests unitarios...${NC}"
vendor/bin/phpunit tests/Unit/ --stop-on-failure || echo -e "${YELLOW}⚠ Algunos tests fallaron (esto es opcional)${NC}"
echo ""

# Paso 4: Configurar permisos adicionales
echo -e "${YELLOW}[4/5] Verificando permisos...${NC}"
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permisos configurados${NC}"
echo ""

# Paso 5: Mostrar información
echo -e "${YELLOW}[5/5] Configuración completada${NC}"
echo ""
echo -e "${GREEN}=== ✓ Instalación completada ===${NC}"
echo ""
echo "Para arrancar el servidor de desarrollo:"
echo "  php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo "Luego accede a: http://localhost:8000"
echo ""
echo "Archivos disponibles:"
echo "  - http://localhost:8000/index.html (Página principal)"
echo "  - http://localhost:8000/cliente.html (Dashboard cliente)"
echo "  - http://localhost:8000/taxista.html (Dashboard taxista)"
echo "  - http://localhost:8000/admin.html (Dashboard admin)"
echo ""
echo "Comandos útiles:"
echo "  php artisan migrate:fresh --seed  # Resetear BD"
echo "  php artisan route:list            # Ver rutas"
echo "  tail -f storage/logs/laravel.log  # Ver logs"
echo ""
