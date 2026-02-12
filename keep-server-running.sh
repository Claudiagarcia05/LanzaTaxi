#!/bin/bash

# Script para mantener el servidor Laravel corriendo en segundo plano
# Uso: ./keep-server-running.sh

# Detener servidores previos
pkill -f "php artisan serve" 2>/dev/null

# Crear directorio de logs si no existe
mkdir -p storage/logs

# Iniciar servidor en segundo plano
echo "🚕 Iniciando servidor Laravel en segundo plano..."
nohup php artisan serve --host=0.0.0.0 --port=8000 > storage/logs/artisan-serve.log 2>&1 &

# Guardar PID
echo $! > storage/logs/artisan-serve.pid

sleep 2

# Verificar que está corriendo
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/ | grep -q "200"; then
    echo "✅ Servidor iniciado correctamente"
    echo ""
    echo "Accede a:"
    echo "  → http://localhost:8000"
    echo "  → http://lanzataxi (vía Apache)"
    echo ""
    echo "Para detener el servidor:"
    echo "  kill \$(cat storage/logs/artisan-serve.pid)"
    echo ""
    echo "Ver logs:"
    echo "  tail -f storage/logs/artisan-serve.log"
else
    echo "❌ Error al iniciar el servidor"
    echo "Revisa los logs en: storage/logs/artisan-serve.log"
    exit 1
fi
