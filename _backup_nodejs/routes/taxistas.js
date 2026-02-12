const express = require('express');
const router = express.Router();
const db = require('../database');
const { verifyToken } = require('./auth');

// Middleware para verificar rol de taxista
const verifyTaxista = (req, res, next) => {
  if (req.userRole !== 'taxista') {
    return res.status(403).json({ error: 'Acceso denegado' });
  }
  next();
};

// Obtener información del taxista actual
router.get('/mi-info', verifyToken, verifyTaxista, (req, res) => {
  try {
    const taxista = db.prepare(`
      SELECT t.*, u.nombre, u.email, u.telefono
      FROM taxistas t
      JOIN users u ON t.user_id = u.id
      WHERE t.user_id = ?
    `).get(req.userId);

    if (!taxista) {
      return res.status(404).json({ error: 'Taxista no encontrado' });
    }

    res.json(taxista);

  } catch (error) {
    console.error('Error al obtener info del taxista:', error);
    res.status(500).json({ error: 'Error al obtener información' });
  }
});

// Cambiar estado del taxista
router.post('/cambiar-estado', verifyToken, verifyTaxista, (req, res) => {
  try {
    const { estado } = req.body;

    if (!['libre', 'ocupado', 'en_servicio'].includes(estado)) {
      return res.status(400).json({ error: 'Estado inválido' });
    }

    const taxista = db.prepare('SELECT id FROM taxistas WHERE user_id = ?').get(req.userId);
    
    if (!taxista) {
      return res.status(404).json({ error: 'Taxista no encontrado' });
    }

    db.prepare('UPDATE taxistas SET estado = ? WHERE user_id = ?')
      .run(estado, req.userId);

    res.json({ message: 'Estado actualizado', estado });

  } catch (error) {
    console.error('Error al cambiar estado:', error);
    res.status(500).json({ error: 'Error al cambiar estado' });
  }
});

// Actualizar ubicación
router.post('/actualizar-ubicacion', verifyToken, verifyTaxista, (req, res) => {
  try {
    const { latitud, longitud } = req.body;

    db.prepare('UPDATE taxistas SET latitud = ?, longitud = ? WHERE user_id = ?')
      .run(latitud, longitud, req.userId);

    res.json({ message: 'Ubicación actualizada' });

  } catch (error) {
    console.error('Error al actualizar ubicación:', error);
    res.status(500).json({ error: 'Error al actualizar ubicación' });
  }
});

// Ver solicitudes pendientes
router.get('/solicitudes-pendientes', verifyToken, verifyTaxista, (req, res) => {
  try {
    const solicitudes = db.prepare(`
      SELECT v.*, u.nombre as cliente_nombre, u.telefono as cliente_telefono
      FROM viajes v
      JOIN users u ON v.cliente_id = u.id
      WHERE v.estado = 'pendiente'
      ORDER BY v.fecha_solicitud ASC
    `).all();

    res.json(solicitudes);

  } catch (error) {
    console.error('Error al obtener solicitudes:', error);
    res.status(500).json({ error: 'Error al obtener solicitudes' });
  }
});

// Aceptar viaje
router.post('/aceptar-viaje/:id', verifyToken, verifyTaxista, (req, res) => {
  try {
    const viajeId = req.params.id;
    
    // Obtener el taxista
    const taxista = db.prepare('SELECT id FROM taxistas WHERE user_id = ?').get(req.userId);
    
    if (!taxista) {
      return res.status(404).json({ error: 'Taxista no encontrado' });
    }

    // Verificar que el viaje está pendiente
    const viaje = db.prepare('SELECT * FROM viajes WHERE id = ? AND estado = ?').get(viajeId, 'pendiente');
    
    if (!viaje) {
      return res.status(404).json({ error: 'Viaje no disponible' });
    }

    // Aceptar el viaje
    db.prepare(`
      UPDATE viajes 
      SET taxista_id = ?, estado = 'aceptado', fecha_aceptacion = CURRENT_TIMESTAMP 
      WHERE id = ?
    `).run(taxista.id, viajeId);

    // Cambiar estado del taxista
    db.prepare('UPDATE taxistas SET estado = ? WHERE id = ?')
      .run('en_servicio', taxista.id);

    const viajeActualizado = db.prepare(`
      SELECT v.*, u.nombre as cliente_nombre, u.telefono as cliente_telefono
      FROM viajes v
      JOIN users u ON v.cliente_id = u.id
      WHERE v.id = ?
    `).get(viajeId);

    res.json({
      message: 'Viaje aceptado exitosamente',
      viaje: viajeActualizado
    });

  } catch (error) {
    console.error('Error al aceptar viaje:', error);
    res.status(500).json({ error: 'Error al aceptar viaje' });
  }
});

// Iniciar viaje (cuando llega al punto de recogida)
router.post('/iniciar-viaje/:id', verifyToken, verifyTaxista, (req, res) => {
  try {
    const viajeId = req.params.id;
    const taxista = db.prepare('SELECT id FROM taxistas WHERE user_id = ?').get(req.userId);

    const viaje = db.prepare('SELECT * FROM viajes WHERE id = ? AND taxista_id = ?').get(viajeId, taxista.id);
    
    if (!viaje) {
      return res.status(404).json({ error: 'Viaje no encontrado' });
    }

    db.prepare(`
      UPDATE viajes 
      SET estado = 'en_curso', fecha_inicio = CURRENT_TIMESTAMP 
      WHERE id = ?
    `).run(viajeId);

    res.json({ message: 'Viaje iniciado' });

  } catch (error) {
    console.error('Error al iniciar viaje:', error);
    res.status(500).json({ error: 'Error al iniciar viaje' });
  }
});

// Finalizar viaje
router.post('/finalizar-viaje/:id', verifyToken, verifyTaxista, (req, res) => {
  try {
    const viajeId = req.params.id;
    const { precioFinal } = req.body;
    const taxista = db.prepare('SELECT id FROM taxistas WHERE user_id = ?').get(req.userId);

    const viaje = db.prepare('SELECT * FROM viajes WHERE id = ? AND taxista_id = ?').get(viajeId, taxista.id);
    
    if (!viaje) {
      return res.status(404).json({ error: 'Viaje no encontrado' });
    }

    db.prepare(`
      UPDATE viajes 
      SET estado = 'finalizado', fecha_fin = CURRENT_TIMESTAMP, precio_final = ?
      WHERE id = ?
    `).run(precioFinal || viaje.precio_estimado, viajeId);

    // Cambiar estado del taxista a libre
    db.prepare('UPDATE taxistas SET estado = ? WHERE id = ?')
      .run('libre', taxista.id);

    res.json({ message: 'Viaje finalizado' });

  } catch (error) {
    console.error('Error al finalizar viaje:', error);
    res.status(500).json({ error: 'Error al finalizar viaje' });
  }
});

// Historial de viajes del taxista
router.get('/mis-viajes', verifyToken, verifyTaxista, (req, res) => {
  try {
    const taxista = db.prepare('SELECT id FROM taxistas WHERE user_id = ?').get(req.userId);
    
    const viajes = db.prepare(`
      SELECT v.*, u.nombre as cliente_nombre
      FROM viajes v
      JOIN users u ON v.cliente_id = u.id
      WHERE v.taxista_id = ?
      ORDER BY v.fecha_solicitud DESC
    `).all(taxista.id);

    res.json(viajes);

  } catch (error) {
    console.error('Error al obtener viajes:', error);
    res.status(500).json({ error: 'Error al obtener viajes' });
  }
});

// Estadísticas del taxista
router.get('/estadisticas', verifyToken, verifyTaxista, (req, res) => {
  try {
    const taxista = db.prepare('SELECT id FROM taxistas WHERE user_id = ?').get(req.userId);
    
    const stats = db.prepare(`
      SELECT 
        COUNT(*) as total_viajes,
        SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) as viajes_completados,
        SUM(CASE WHEN estado = 'finalizado' THEN precio_final ELSE 0 END) as ingresos_totales,
        AVG(CASE WHEN estado = 'finalizado' THEN precio_final ELSE NULL END) as ingreso_promedio
      FROM viajes
      WHERE taxista_id = ?
    `).get(taxista.id);

    // Viajes de hoy
    const hoy = db.prepare(`
      SELECT COUNT(*) as viajes_hoy, SUM(precio_final) as ingresos_hoy
      FROM viajes
      WHERE taxista_id = ? 
      AND DATE(fecha_fin) = DATE('now')
      AND estado = 'finalizado'
    `).get(taxista.id);

    res.json({
      ...stats,
      ...hoy
    });

  } catch (error) {
    console.error('Error al obtener estadísticas:', error);
    res.status(500).json({ error: 'Error al obtener estadísticas' });
  }
});

module.exports = router;
