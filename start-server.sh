#!/bin/bash

# Script para iniciar el servidor de desarrollo LanzaTaxi
# Uso: ./start-server.sh

# Detener servidores Node.js previos en puerto 3000
pkill -f "node server.js" 2>/dev/null || true
lsof -ti:3000 | xargs kill -9 2>/dev/null || true

echo "🚕 Iniciando servidor LanzaTaxi..."
echo ""

# Verificar si existen las dependencias
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias..."
    npm install
    echo ""
fi

echo "🚖 ========================================"
echo "   LANZATAXI - Sistema de Gestión de Taxis"
echo "   ========================================"
echo ""
echo "   🌐 Servidor: http://localhost:3000"
echo "   📊 Estado: Iniciando..."
echo ""
echo "   👤 Usuarios de prueba:"
echo "   ├─ Cliente:  cliente@test.com  / 123456"
echo "   ├─ Taxista:  taxista@test.com  / 123456"
echo "   └─ Admin:    admin@test.com    / 123456"
echo ""
echo "   📄 Páginas disponibles:"
echo "   ├─ http://localhost:3000            (Landing page)"
echo "   ├─ http://localhost:3000/cliente.html   (Panel cliente)"
echo "   ├─ http://localhost:3000/taxista.html   (Panel taxista)"
echo "   └─ http://localhost:3000/admin.html     (Panel admin)"
echo ""
echo "   Presiona Ctrl+C para detener el servidor"
echo "🚖 ========================================"
echo ""

# Iniciar servidor Node.js
node server.js
