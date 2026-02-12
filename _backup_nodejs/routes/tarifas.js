const express = require('express');
const router = express.Router();
const db = require('../database');
const { verifyToken } = require('./auth');

// Obtener todas las tarifas
router.get('/', (req, res) => {
  try {
    const tarifas = db.prepare('SELECT * FROM tarifas ORDER BY created_at DESC').all();
    res.json(tarifas);

  } catch (error) {
    console.error('Error al obtener tarifas:', error);
    res.status(500).json({ error: 'Error al obtener tarifas' });
  }
});

// Obtener tarifa activa
router.get('/activa/:nombre', (req, res) => {
  try {
    const tarifa = db.prepare('SELECT * FROM tarifas WHERE nombre = ? AND activa = 1').get(req.params.nombre);
    
    if (!tarifa) {
      return res.status(404).json({ error: 'Tarifa no encontrada' });
    }

    res.json(tarifa);

  } catch (error) {
    console.error('Error al obtener tarifa:', error);
    res.status(500).json({ error: 'Error al obtener tarifa' });
  }
});

// Actualizar tarifa (admin)
router.put('/:id', verifyToken, (req, res) => {
  try {
    if (req.userRole !== 'admin') {
      return res.status(403).json({ error: 'Acceso denegado' });
    }

    const {
      bajadaBandera,
      precioKm,
      suplementoAeropuerto,
      suplementoPuerto,
      suplementoNocturno,
      suplementoFestivo
    } = req.body;

    db.prepare(`
      UPDATE tarifas 
      SET bajada_bandera = ?, precio_km = ?, 
          suplemento_aeropuerto = ?, suplemento_puerto = ?,
          suplemento_nocturno = ?, suplemento_festivo = ?
      WHERE id = ?
    `).run(
      bajadaBandera,
      precioKm,
      suplementoAeropuerto,
      suplementoPuerto,
      suplementoNocturno,
      suplementoFestivo,
      req.params.id
    );

    res.json({ message: 'Tarifa actualizada exitosamente' });

  } catch (error) {
    console.error('Error al actualizar tarifa:', error);
    res.status(500).json({ error: 'Error al actualizar tarifa' });
  }
});

// Crear nueva tarifa (admin)
router.post('/', verifyToken, (req, res) => {
  try {
    if (req.userRole !== 'admin') {
      return res.status(403).json({ error: 'Acceso denegado' });
    }

    const {
      nombre,
      bajadaBandera,
      precioKm,
      suplementoAeropuerto,
      suplementoPuerto,
      suplementoNocturno,
      suplementoFestivo
    } = req.body;

    db.prepare(`
      INSERT INTO tarifas (
        nombre, bajada_bandera, precio_km,
        suplemento_aeropuerto, suplemento_puerto,
        suplemento_nocturno, suplemento_festivo
      ) VALUES (?, ?, ?, ?, ?, ?, ?)
    `).run(
      nombre,
      bajadaBandera,
      precioKm,
      suplementoAeropuerto || 0,
      suplementoPuerto || 0,
      suplementoNocturno || 0,
      suplementoFestivo || 0
    );

    res.json({ message: 'Tarifa creada exitosamente' });

  } catch (error) {
    console.error('Error al crear tarifa:', error);
    res.status(500).json({ error: 'Error al crear tarifa' });
  }
});

module.exports = router;
