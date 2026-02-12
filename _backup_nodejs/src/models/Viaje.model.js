/**
 * Modelo de Viaje - Patrón Active Record
 * Representa la entidad Viaje con métodos de acceso a datos
 */

class ViajeModel {
  constructor(db) {
    this.db = db;
  }

  /**
   * Crear nuevo viaje
   * @param {Object} viajeData - Datos del viaje
   * @returns {number} ID del viaje creado
   */
  create(viajeData) {
    const {
      cliente_id,
      origen_lat,
      origen_lng,
      origen_direccion,
      destino_lat,
      destino_lng,
      destino_direccion,
      distancia,
      precio_estimado,
      tipo_tarifa,
      suplementos = null
    } = viajeData;

    try {
      const result = this.db.prepare(`
        INSERT INTO viajes (
          cliente_id, origen_lat, origen_lng, origen_direccion,
          destino_lat, destino_lng, destino_direccion,
          distancia, precio_estimado, tipo_tarifa, suplementos
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `).run(
        cliente_id, origen_lat, origen_lng, origen_direccion,
        destino_lat, destino_lng, destino_direccion,
        distancia, precio_estimado, tipo_tarifa, suplementos
      );

      return result.lastInsertRowid;
    } catch (error) {
      throw new Error(`Error al crear viaje: ${error.message}`);
    }
  }

  /**
   * Buscar viaje por ID
   * @param {number} id - ID del viaje
   * @returns {Object|null} Viaje encontrado o null
   */
  findById(id) {
    try {
      return this.db.prepare('SELECT * FROM viajes WHERE id = ?').get(id);
    } catch (error) {
      throw new Error(`Error al buscar viaje: ${error.message}`);
    }
  }

  /**
   * Obtener viajes de un cliente
   * @param {number} clienteId - ID del cliente
   * @returns {Array} Lista de viajes
   */
  findByCliente(clienteId) {
    try {
      return this.db.prepare(`
        SELECT v.*, 
               t.licencia as taxista_licencia,
               u.nombre as taxista_nombre
        FROM viajes v
        LEFT JOIN taxistas t ON v.taxista_id = t.id
        LEFT JOIN users u ON t.user_id = u.id
        WHERE v.cliente_id = ?
        ORDER BY v.fecha_solicitud DESC
      `).all(clienteId);
    } catch (error) {
      throw new Error(`Error al obtener viajes del cliente: ${error.message}`);
    }
  }

  /**
   * Obtener viajes de un taxista
   * @param {number} taxistaId - ID del taxista
   * @returns {Array} Lista de viajes
   */
  findByTaxista(taxistaId) {
    try {
      return this.db.prepare(`
        SELECT v.*, 
               u.nombre as cliente_nombre,
               u.telefono as cliente_telefono
        FROM viajes v
        LEFT JOIN users u ON v.cliente_id = u.id
        WHERE v.taxista_id = ?
        ORDER BY v.fecha_solicitud DESC
      `).all(taxistaId);
    } catch (error) {
      throw new Error(`Error al obtener viajes del taxista: ${error.message}`);
    }
  }

  /**
   * Obtener viajes pendientes (sin asignar)
   * @returns {Array} Lista de viajes pendientes
   */
  findPendientes() {
    try {
      return this.db.prepare(`
        SELECT v.*, 
               u.nombre as cliente_nombre,
               u.telefono as cliente_telefono
        FROM viajes v
        LEFT JOIN users u ON v.cliente_id = u.id
        WHERE v.estado = 'pendiente'
        ORDER BY v.fecha_solicitud ASC
      `).all();
    } catch (error) {
      throw new Error(`Error al obtener viajes pendientes: ${error.message}`);
    }
  }

  /**
   * Aceptar viaje (asignar taxista)
   * @param {number} viajeId - ID del viaje
   * @param {number} taxistaId - ID del taxista
   * @returns {boolean} True si se actualizó correctamente
   */
  aceptar(viajeId, taxistaId) {
    try {
      const result = this.db.prepare(`
        UPDATE viajes 
        SET taxista_id = ?, 
            estado = 'aceptado',
            fecha_aceptacion = datetime('now')
        WHERE id = ? AND estado = 'pendiente'
      `).run(taxistaId, viajeId);

      return result.changes > 0;
    } catch (error) {
      throw new Error(`Error al aceptar viaje: ${error.message}`);
    }
  }

  /**
   * Iniciar viaje
   * @param {number} viajeId - ID del viaje
   * @returns {boolean} True si se actualizó correctamente
   */
  iniciar(viajeId) {
    try {
      const result = this.db.prepare(`
        UPDATE viajes 
        SET estado = 'en_curso',
            fecha_inicio = datetime('now')
        WHERE id = ? AND estado = 'aceptado'
      `).run(viajeId);

      return result.changes > 0;
    } catch (error) {
      throw new Error(`Error al iniciar viaje: ${error.message}`);
    }
  }

  /**
   * Finalizar viaje
   * @param {number} viajeId - ID del viaje
   * @param {number} precioFinal - Precio final del viaje
   * @returns {boolean} True si se actualizó correctamente
   */
  finalizar(viajeId, precioFinal) {
    try {
      const result = this.db.prepare(`
        UPDATE viajes 
        SET estado = 'finalizado',
            precio_final = ?,
            fecha_fin = datetime('now')
        WHERE id = ? AND estado = 'en_curso'
      `).run(precioFinal, viajeId);

      return result.changes > 0;
    } catch (error) {
      throw new Error(`Error al finalizar viaje: ${error.message}`);
    }
  }

  /**
   * Cancelar viaje
   * @param {number} viajeId - ID del viaje
   * @returns {boolean} True si se actualizó correctamente
   */
  cancelar(viajeId) {
    try {
      const result = this.db.prepare(`
        UPDATE viajes 
        SET estado = 'cancelado'
        WHERE id = ? AND estado IN ('pendiente', 'aceptado')
      `).run(viajeId);

      return result.changes > 0;
    } catch (error) {
      throw new Error(`Error al cancelar viaje: ${error.message}`);
    }
  }

  /**
   * Calcular distancia entre dos puntos (fórmula de Haversine)
   * @param {number} lat1 - Latitud origen
   * @param {number} lng1 - Longitud origen
   * @param {number} lat2 - Latitud destino
   * @param {number} lng2 - Longitud destino
   * @returns {number} Distancia en kilómetros
   */
  static calcularDistancia(lat1, lng1, lat2, lng2) {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    
    const a = 
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLng / 2) * Math.sin(dLng / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distancia = R * c;
    
    return Math.round(distancia * 100) / 100; // Redondear a 2 decimales
  }

  /**
   * Contar viajes por estado
   * @param {string} estado - Estado del viaje (opcional)
   * @returns {number} Cantidad de viajes
   */
  count(estado = null) {
    try {
      if (estado) {
        return this.db.prepare('SELECT COUNT(*) as count FROM viajes WHERE estado = ?').get(estado).count;
      }
      return this.db.prepare('SELECT COUNT(*) as count FROM viajes').get().count;
    } catch (error) {
      throw new Error(`Error al contar viajes: ${error.message}`);
    }
  }

  /**
   * Obtener estadísticas de viajes
   * @returns {Object} Estadísticas
   */
  getEstadisticas() {
    try {
      return {
        total: this.count(),
        pendientes: this.count('pendiente'),
        en_curso: this.count('en_curso'),
        finalizados: this.count('finalizado'),
        cancelados: this.count('cancelado')
      };
    } catch (error) {
      throw new Error(`Error al obtener estadísticas: ${error.message}`);
    }
  }
}

module.exports = ViajeModel;
