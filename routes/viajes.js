const express = require('express');
const router = express.Router();
const db = require('../database');
const { verifyToken } = require('./auth');
const PDFDocument = require('pdfkit');

// Calcular precio de un trayecto
router.post('/calcular-precio', verifyToken, (req, res) => {
  try {
    const { distancia, tipoTarifa = 'Tarifa 2', suplementos = [] } = req.body;

    // Obtener tarifa activa
    const tarifa = db.prepare('SELECT * FROM tarifas WHERE nombre = ? AND activa = 1').get(tipoTarifa);
    
    if (!tarifa) {
      return res.status(404).json({ error: 'Tarifa no encontrada' });
    }

    let precioTotal = tarifa.bajada_bandera + (distancia * tarifa.precio_km);

    // Aplicar suplementos
    let suplementosAplicados = [];
    if (suplementos.includes('aeropuerto')) {
      precioTotal += tarifa.suplemento_aeropuerto;
      suplementosAplicados.push({ nombre: 'Aeropuerto', precio: tarifa.suplemento_aeropuerto });
    }
    if (suplementos.includes('puerto')) {
      precioTotal += tarifa.suplemento_puerto;
      suplementosAplicados.push({ nombre: 'Puerto', precio: tarifa.suplemento_puerto });
    }
    if (suplementos.includes('nocturno')) {
      precioTotal += tarifa.suplemento_nocturno;
      suplementosAplicados.push({ nombre: 'Nocturno', precio: tarifa.suplemento_nocturno });
    }
    if (suplementos.includes('festivo')) {
      precioTotal += tarifa.suplemento_festivo;
      suplementosAplicados.push({ nombre: 'Festivo', precio: tarifa.suplemento_festivo });
    }

    res.json({
      distancia,
      tarifa: {
        nombre: tarifa.nombre,
        bajadaBandera: tarifa.bajada_bandera,
        precioKm: tarifa.precio_km
      },
      suplementos: suplementosAplicados,
      precioTotal: parseFloat(precioTotal.toFixed(2))
    });

  } catch (error) {
    console.error('Error al calcular precio:', error);
    res.status(500).json({ error: 'Error al calcular precio' });
  }
});

// Crear nueva solicitud de viaje
router.post('/solicitar', verifyToken, (req, res) => {
  try {
    const {
      origenLat,
      origenLng,
      origenDireccion,
      destinoLat,
      destinoLng,
      destinoDireccion,
      distancia,
      precioEstimado,
      tipoTarifa = 'Tarifa 2',
      suplementos = []
    } = req.body;

    const result = db.prepare(`
      INSERT INTO viajes (
        cliente_id, origen_lat, origen_lng, origen_direccion,
        destino_lat, destino_lng, destino_direccion,
        distancia, precio_estimado, tipo_tarifa, suplementos
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `).run(
      req.userId,
      origenLat,
      origenLng,
      origenDireccion,
      destinoLat,
      destinoLng,
      destinoDireccion,
      distancia,
      precioEstimado,
      tipoTarifa,
      JSON.stringify(suplementos)
    );

    const viaje = db.prepare('SELECT * FROM viajes WHERE id = ?').get(result.lastInsertRowid);

    res.json({
      message: 'Solicitud creada exitosamente',
      viaje
    });

  } catch (error) {
    console.error('Error al crear solicitud:', error);
    res.status(500).json({ error: 'Error al crear solicitud' });
  }
});

// Obtener viajes del cliente
router.get('/mis-viajes', verifyToken, (req, res) => {
  try {
    const viajes = db.prepare(`
      SELECT v.*, t.licencia, t.matricula, t.modelo_vehiculo, u.nombre as taxista_nombre
      FROM viajes v
      LEFT JOIN taxistas t ON v.taxista_id = t.id
      LEFT JOIN users u ON t.user_id = u.id
      WHERE v.cliente_id = ?
      ORDER BY v.fecha_solicitud DESC
    `).all(req.userId);

    res.json(viajes);

  } catch (error) {
    console.error('Error al obtener viajes:', error);
    res.status(500).json({ error: 'Error al obtener viajes' });
  }
});

// Obtener viaje específico
router.get('/:id', verifyToken, (req, res) => {
  try {
    const viaje = db.prepare(`
      SELECT v.*, 
             u.nombre as cliente_nombre, u.telefono as cliente_telefono,
             t.licencia, t.matricula, t.modelo_vehiculo, t.latitud as taxista_lat, t.longitud as taxista_lng,
             ut.nombre as taxista_nombre, ut.telefono as taxista_telefono
      FROM viajes v
      JOIN users u ON v.cliente_id = u.id
      LEFT JOIN taxistas t ON v.taxista_id = t.id
      LEFT JOIN users ut ON t.user_id = ut.id
      WHERE v.id = ?
    `).get(req.params.id);

    if (!viaje) {
      return res.status(404).json({ error: 'Viaje no encontrado' });
    }

    res.json(viaje);

  } catch (error) {
    console.error('Error al obtener viaje:', error);
    res.status(500).json({ error: 'Error al obtener viaje' });
  }
});

// Valorar viaje
router.post('/:id/valorar', verifyToken, (req, res) => {
  try {
    const { valoracion, comentario } = req.body;
    const viajeId = req.params.id;

    // Verificar que el viaje existe y pertenece al cliente
    const viaje = db.prepare('SELECT * FROM viajes WHERE id = ? AND cliente_id = ?').get(viajeId, req.userId);
    
    if (!viaje) {
      return res.status(404).json({ error: 'Viaje no encontrado' });
    }

    if (viaje.estado !== 'finalizado') {
      return res.status(400).json({ error: 'Solo puedes valorar viajes finalizados' });
    }

    // Actualizar valoración del viaje
    db.prepare('UPDATE viajes SET valoracion = ?, comentario = ? WHERE id = ?')
      .run(valoracion, comentario, viajeId);

    // Actualizar valoración media del taxista
    if (viaje.taxista_id) {
      const taxista = db.prepare('SELECT * FROM taxistas WHERE id = ?').get(viaje.taxista_id);
      const nuevaMedia = ((taxista.valoracion_media * taxista.num_valoraciones) + valoracion) / (taxista.num_valoraciones + 1);
      
      db.prepare('UPDATE taxistas SET valoracion_media = ?, num_valoraciones = ? WHERE id = ?')
        .run(nuevaMedia, taxista.num_valoraciones + 1, viaje.taxista_id);
    }

    res.json({ message: 'Valoración registrada exitosamente' });

  } catch (error) {
    console.error('Error al valorar viaje:', error);
    res.status(500).json({ error: 'Error al valorar viaje' });
  }
});

// Generar factura PDF
router.get('/:id/factura', verifyToken, (req, res) => {
  try {
    const viaje = db.prepare(`
      SELECT v.*, 
             u.nombre as cliente_nombre, u.telefono as cliente_telefono, u.email as cliente_email,
             ut.nombre as taxista_nombre, t.licencia, t.matricula
      FROM viajes v
      JOIN users u ON v.cliente_id = u.id
      LEFT JOIN taxistas t ON v.taxista_id = t.id
      LEFT JOIN users ut ON t.user_id = ut.id
      WHERE v.id = ? AND v.cliente_id = ?
    `).get(req.params.id, req.userId);

    if (!viaje) {
      return res.status(404).json({ error: 'Viaje no encontrado' });
    }

    if (viaje.estado !== 'finalizado') {
      return res.status(400).json({ error: 'Solo puedes descargar facturas de viajes finalizados' });
    }

    // Crear PDF
    const doc = new PDFDocument({ margin: 50 });
    
    res.setHeader('Content-Type', 'application/pdf');
    res.setHeader('Content-Disposition', `attachment; filename=factura-${viaje.id}.pdf`);
    
    doc.pipe(res);

    // Encabezado
    doc.fontSize(20).text('LANZATAXI', { align: 'center' });
    doc.fontSize(10).text('Servicio de Taxi - Lanzarote', { align: 'center' });
    doc.moveDown();
    doc.fontSize(16).text(`FACTURA #${viaje.id}`, { align: 'center' });
    doc.moveDown(2);

    // Datos del cliente
    doc.fontSize(12).text('DATOS DEL CLIENTE:', { underline: true });
    doc.fontSize(10).text(`Nombre: ${viaje.cliente_nombre}`);
    doc.text(`Email: ${viaje.cliente_email}`);
    doc.text(`Teléfono: ${viaje.cliente_telefono}`);
    doc.moveDown();

    // Datos del viaje
    doc.fontSize(12).text('DETALLES DEL VIAJE:', { underline: true });
    doc.fontSize(10).text(`Fecha: ${new Date(viaje.fecha_fin).toLocaleString('es-ES')}`);
    doc.text(`Origen: ${viaje.origen_direccion}`);
    doc.text(`Destino: ${viaje.destino_direccion}`);
    doc.text(`Distancia: ${viaje.distancia} km`);
    doc.text(`Taxista: ${viaje.taxista_nombre || 'N/A'}`);
    doc.text(`Matrícula: ${viaje.matricula || 'N/A'}`);
    doc.text(`Licencia: ${viaje.licencia || 'N/A'}`);
    doc.moveDown();

    // Desglose del precio
    doc.fontSize(12).text('DESGLOSE:', { underline: true });
    doc.fontSize(10).text(`Tarifa: ${viaje.tipo_tarifa}`);
    
    const suplementos = viaje.suplementos ? JSON.parse(viaje.suplementos) : [];
    if (suplementos.length > 0) {
      doc.text(`Suplementos: ${suplementos.join(', ')}`);
    }
    
    doc.moveDown();
    doc.fontSize(14).text(`TOTAL: €${viaje.precio_final || viaje.precio_estimado}`, { bold: true });

    doc.end();

  } catch (error) {
    console.error('Error al generar factura:', error);
    res.status(500).json({ error: 'Error al generar factura' });
  }
});

module.exports = router;
