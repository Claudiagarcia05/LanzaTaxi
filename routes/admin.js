const express = require('express');
const router = express.Router();
const db = require('../database');
const { verifyToken } = require('./auth');
const bcrypt = require('bcryptjs');

// Middleware para verificar rol de admin
const verifyAdmin = (req, res, next) => {
  if (req.userRole !== 'admin') {
    return res.status(403).json({ error: 'Acceso denegado' });
  }
  next();
};

// Dashboard - Estadísticas generales
router.get('/dashboard', verifyToken, verifyAdmin, (req, res) => {
  try {
    // Total de usuarios por rol
    const usuarios = db.prepare(`
      SELECT role, COUNT(*) as total
      FROM users
      GROUP BY role
    `).all();

    // Total de viajes y estado
    const viajes = db.prepare(`
      SELECT estado, COUNT(*) as total, SUM(precio_final) as ingresos
      FROM viajes
      GROUP BY estado
    `).all();

    // Viajes del día
    const viajesHoy = db.prepare(`
      SELECT COUNT(*) as total, SUM(precio_final) as ingresos
      FROM viajes
      WHERE DATE(fecha_solicitud) = DATE('now')
    `).get();

    // Taxistas por estado
    const taxistasEstado = db.prepare(`
      SELECT estado, COUNT(*) as total
      FROM taxistas
      GROUP BY estado
    `).all();

    // Viajes por hora del día (últimas 24h)
    const viajesPorHora = db.prepare(`
      SELECT strftime('%H', fecha_solicitud) as hora, COUNT(*) as total
      FROM viajes
      WHERE fecha_solicitud >= datetime('now', '-24 hours')
      GROUP BY hora
      ORDER BY hora
    `).all();

    // Viajes por municipio (según taxista asignado)
    const viajesPorMunicipio = db.prepare(`
      SELECT t.municipio, COUNT(*) as total
      FROM viajes v
      JOIN taxistas t ON v.taxista_id = t.id
      WHERE v.estado = 'finalizado'
      GROUP BY t.municipio
    `).all();

    // Top 5 taxistas por número de viajes
    const topTaxistas = db.prepare(`
      SELECT u.nombre, t.licencia, COUNT(v.id) as total_viajes, 
             SUM(v.precio_final) as ingresos, t.valoracion_media
      FROM taxistas t
      JOIN users u ON t.user_id = u.id
      LEFT JOIN viajes v ON v.taxista_id = t.id AND v.estado = 'finalizado'
      GROUP BY t.id
      ORDER BY total_viajes DESC
      LIMIT 5
    `).all();

    res.json({
      usuarios,
      viajes,
      viajesHoy,
      taxistasEstado,
      viajesPorHora,
      viajesPorMunicipio,
      topTaxistas
    });

  } catch (error) {
    console.error('Error al obtener dashboard:', error);
    res.status(500).json({ error: 'Error al obtener estadísticas' });
  }
});

// Obtener todos los usuarios
router.get('/usuarios', verifyToken, verifyAdmin, (req, res) => {
  try {
    const { role } = req.query;
    
    let query = 'SELECT id, email, nombre, telefono, role, created_at FROM users';
    const params = [];
    
    if (role) {
      query += ' WHERE role = ?';
      params.push(role);
    }
    
    query += ' ORDER BY created_at DESC';
    
    const usuarios = db.prepare(query).all(...params);
    res.json(usuarios);

  } catch (error) {
    console.error('Error al obtener usuarios:', error);
    res.status(500).json({ error: 'Error al obtener usuarios' });
  }
});

// Obtener todos los taxistas con info completa
router.get('/taxistas', verifyToken, verifyAdmin, (req, res) => {
  try {
    const taxistas = db.prepare(`
      SELECT t.*, u.nombre, u.email, u.telefono, u.created_at
      FROM taxistas t
      JOIN users u ON t.user_id = u.id
      ORDER BY t.id DESC
    `).all();

    res.json(taxistas);

  } catch (error) {
    console.error('Error al obtener taxistas:', error);
    res.status(500).json({ error: 'Error al obtener taxistas' });
  }
});

// Crear nuevo taxista
router.post('/taxistas', verifyToken, verifyAdmin, (req, res) => {
  try {
    const { email, password, nombre, telefono, licencia, municipio, matricula, modeloVehiculo } = req.body;

    // Crear usuario
    const hashedPassword = bcrypt.hashSync(password, 10);
    const userResult = db.prepare(`
      INSERT INTO users (email, password, nombre, telefono, role) 
      VALUES (?, ?, ?, ?, 'taxista')
    `).run(email, hashedPassword, nombre, telefono);

    // Crear taxista
    db.prepare(`
      INSERT INTO taxistas (user_id, licencia, municipio, matricula, modelo_vehiculo) 
      VALUES (?, ?, ?, ?, ?)
    `).run(userResult.lastInsertRowid, licencia, municipio, matricula, modeloVehiculo);

    res.json({ message: 'Taxista creado exitosamente' });

  } catch (error) {
    console.error('Error al crear taxista:', error);
    res.status(500).json({ error: 'Error al crear taxista' });
  }
});

// Eliminar usuario
router.delete('/usuarios/:id', verifyToken, verifyAdmin, (req, res) => {
  try {
    const userId = req.params.id;

    // Verificar si es taxista y eliminar su registro
    const taxista = db.prepare('SELECT id FROM taxistas WHERE user_id = ?').get(userId);
    if (taxista) {
      db.prepare('DELETE FROM taxistas WHERE user_id = ?').run(userId);
    }

    // Eliminar usuario
    db.prepare('DELETE FROM users WHERE id = ?').run(userId);

    res.json({ message: 'Usuario eliminado exitosamente' });

  } catch (error) {
    console.error('Error al eliminar usuario:', error);
    res.status(500).json({ error: 'Error al eliminar usuario' });
  }
});

// Obtener todos los viajes
router.get('/viajes', verifyToken, verifyAdmin, (req, res) => {
  try {
    const { estado, fecha } = req.query;
    
    let query = `
      SELECT v.*, 
             uc.nombre as cliente_nombre,
             ut.nombre as taxista_nombre, t.licencia, t.matricula
      FROM viajes v
      JOIN users uc ON v.cliente_id = uc.id
      LEFT JOIN taxistas t ON v.taxista_id = t.id
      LEFT JOIN users ut ON t.user_id = ut.id
      WHERE 1=1
    `;
    const params = [];
    
    if (estado) {
      query += ' AND v.estado = ?';
      params.push(estado);
    }
    
    if (fecha) {
      query += ' AND DATE(v.fecha_solicitud) = DATE(?)';
      params.push(fecha);
    }
    
    query += ' ORDER BY v.fecha_solicitud DESC LIMIT 100';
    
    const viajes = db.prepare(query).all(...params);
    res.json(viajes);

  } catch (error) {
    console.error('Error al obtener viajes:', error);
    res.status(500).json({ error: 'Error al obtener viajes' });
  }
});

// Cancelar viaje (admin)
router.post('/viajes/:id/cancelar', verifyToken, verifyAdmin, (req, res) => {
  try {
    const viajeId = req.params.id;

    db.prepare('UPDATE viajes SET estado = ? WHERE id = ?')
      .run('cancelado', viajeId);

    res.json({ message: 'Viaje cancelado' });

  } catch (error) {
    console.error('Error al cancelar viaje:', error);
    res.status(500).json({ error: 'Error al cancelar viaje' });
  }
});

// Obtener ubicaciones de todos los taxistas (para mapa en tiempo real)
router.get('/taxistas/ubicaciones', verifyToken, verifyAdmin, (req, res) => {
  try {
    const ubicaciones = db.prepare(`
      SELECT t.id, t.latitud, t.longitud, t.estado, t.licencia, t.matricula,
             u.nombre
      FROM taxistas t
      JOIN users u ON t.user_id = u.id
      WHERE t.latitud IS NOT NULL AND t.longitud IS NOT NULL
    `).all();

    res.json(ubicaciones);

  } catch (error) {
    console.error('Error al obtener ubicaciones:', error);
    res.status(500).json({ error: 'Error al obtener ubicaciones' });
  }
});

// Obtener incidencias
router.get('/incidencias', verifyToken, verifyAdmin, (req, res) => {
  try {
    const incidencias = db.prepare(`
      SELECT i.*, u.nombre as usuario_nombre, u.email
      FROM incidencias i
      JOIN users u ON i.user_id = u.id
      ORDER BY i.created_at DESC
      LIMIT 50
    `).all();

    res.json(incidencias);

  } catch (error) {
    console.error('Error al obtener incidencias:', error);
    res.status(500).json({ error: 'Error al obtener incidencias' });
  }
});

// Actualizar estado de incidencia
router.put('/incidencias/:id', verifyToken, verifyAdmin, (req, res) => {
  try {
    const { estado } = req.body;
    const incidenciaId = req.params.id;

    db.prepare('UPDATE incidencias SET estado = ? WHERE id = ?')
      .run(estado, incidenciaId);

    res.json({ message: 'Incidencia actualizada' });

  } catch (error) {
    console.error('Error al actualizar incidencia:', error);
    res.status(500).json({ error: 'Error al actualizar incidencia' });
  }
});

module.exports = router;
