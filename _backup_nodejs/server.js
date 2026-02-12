require('dotenv').config();
const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');
const path = require('path');
const db = require('./database');
const authRoutes = require('./routes/auth');
const viajesRoutes = require('./routes/viajes');
const taxistasRoutes = require('./routes/taxistas');
const adminRoutes = require('./routes/admin');
const tarifasRoutes = require('./routes/tarifas');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

// Rutas API
app.use('/api/auth', authRoutes);
app.use('/api/viajes', viajesRoutes);
app.use('/api/taxistas', taxistasRoutes);
app.use('/api/admin', adminRoutes);
app.use('/api/tarifas', tarifasRoutes);

// WebSocket para tiempo real
const connectedUsers = new Map(); // userId -> socketId
const connectedTaxistas = new Map(); // taxistaId -> socketId

io.on('connection', (socket) => {
  console.log('✅ Cliente conectado:', socket.id);

  // Registro de usuario
  socket.on('register', (data) => {
    const { userId, role, taxistaId } = data;
    connectedUsers.set(userId, socket.id);
    
    if (role === 'taxista' && taxistaId) {
      connectedTaxistas.set(taxistaId, socket.id);
      socket.join('taxistas');
      console.log(`🚕 Taxista ${taxistaId} conectado`);
    }
    
    if (role === 'admin') {
      socket.join('admins');
      console.log('👨‍💼 Admin conectado');
    }

    socket.userId = userId;
    socket.role = role;
    socket.taxistaId = taxistaId;
  });

  // Nueva solicitud de viaje
  socket.on('nueva_solicitud', (viajeData) => {
    console.log('🚖 Nueva solicitud de viaje:', viajeData.id);
    
    // Notificar a todos los taxistas libres del municipio
    io.to('taxistas').emit('solicitud_disponible', viajeData);
    
    // Notificar a admins
    io.to('admins').emit('nueva_solicitud', viajeData);
  });

  // Taxista acepta viaje
  socket.on('aceptar_viaje', (data) => {
    const { viajeId, taxistaId, clienteId } = data;
    
    // Notificar al cliente
    const clienteSocketId = connectedUsers.get(clienteId);
    if (clienteSocketId) {
      io.to(clienteSocketId).emit('viaje_aceptado', data);
    }
    
    // Notificar a otros taxistas que el viaje ya no está disponible
    io.to('taxistas').emit('viaje_no_disponible', { viajeId });
    
    // Notificar a admins
    io.to('admins').emit('viaje_aceptado', data);
  });

  // Actualización de ubicación del taxista
  socket.on('actualizar_ubicacion', (data) => {
    const { taxistaId, latitud, longitud } = data;
    
    // Actualizar en BD
    db.prepare('UPDATE taxistas SET latitud = ?, longitud = ? WHERE id = ?')
      .run(latitud, longitud, taxistaId);
    
    // Broadcast a clientes que están esperando este taxi
    socket.broadcast.emit('ubicacion_taxista', data);
    
    // Notificar a admins para el mapa en tiempo real
    io.to('admins').emit('ubicacion_taxista', data);
  });

  // Cambio de estado del taxista
  socket.on('cambiar_estado', (data) => {
    const { taxistaId, estado } = data;
    
    db.prepare('UPDATE taxistas SET estado = ? WHERE id = ?')
      .run(estado, taxistaId);
    
    io.to('admins').emit('taxista_cambio_estado', data);
    console.log(`🚕 Taxista ${taxistaId} cambió a: ${estado}`);
  });

  // Viaje finalizado
  socket.on('finalizar_viaje', (data) => {
    const { viajeId, clienteId } = data;
    
    const clienteSocketId = connectedUsers.get(clienteId);
    if (clienteSocketId) {
      io.to(clienteSocketId).emit('viaje_finalizado', data);
    }
    
    io.to('admins').emit('viaje_finalizado', data);
  });

  // Desconexión
  socket.on('disconnect', () => {
    if (socket.userId) {
      connectedUsers.delete(socket.userId);
    }
    if (socket.taxistaId) {
      connectedTaxistas.delete(socket.taxistaId);
      console.log(`🚕 Taxista ${socket.taxistaId} desconectado`);
    }
    console.log('❌ Cliente desconectado:', socket.id);
  });
});

// Hacer io disponible globalmente
app.set('io', io);

// Ruta principal
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Iniciar servidor
server.listen(PORT, () => {
  console.log('');
  console.log('🚖 ========================================');
  console.log('   LANZATAXI - Sistema de Gestión de Taxis');
  console.log('   ========================================');
  console.log('');
  console.log(`   🌐 Servidor: http://localhost:${PORT}`);
  console.log('   📊 Estado: Activo');
  console.log('');
  console.log('   👤 Usuarios de prueba:');
  console.log('   ├─ Cliente:  cliente@test.com  / 123456');
  console.log('   ├─ Taxista:  taxista@test.com  / 123456');
  console.log('   └─ Admin:    admin@test.com    / 123456');
  console.log('');
  console.log('🚖 ========================================');
  console.log('');
});

module.exports = { app, io };
