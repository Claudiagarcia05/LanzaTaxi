#!/bin/bash

# Script para detener el servidor Laravel
# Uso: ./stop-server.sh

echo "🛑 Deteniendo servidor Laravel..."

# Intentar detener usando el PID guardado
if [ -f storage/logs/artisan-serve.pid ]; then
    PID=$(cat storage/logs/artisan-serve.pid)
    if kill $PID 2>/dev/null; then
        echo "✅ Servidor detenido (PID: $PID)"
        rm storage/logs/artisan-serve.pid
    else
        echo "⚠️  No se encontró proceso con PID: $PID"
    fi
fi

# Detener todos los procesos de artisan serve
if pkill -f "php artisan serve" 2>/dev/null; then
    echo "✅ Todos los servidores Laravel detenidos"
else
    echo "ℹ️  No hay servidores Laravel corriendo"
fi

echo ""
echo "Estado actual:"
if pgrep -f "php artisan serve" > /dev/null; then
    echo "  ⚠️  Aún hay procesos corriendo:"
    ps aux | grep "php artisan serve" | grep -v grep
else
    echo "  ✅ No hay servidores Laravel corriendo"
fi
