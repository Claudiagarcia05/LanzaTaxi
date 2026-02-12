/**
 * Modelo de Taxista - Patrón Active Record
 * Representa la entidad Taxista con métodos de acceso a datos
 */

class TaxistaModel {
  constructor(db) {
    this.db = db;
  }

  /**
   * Crear nuevo taxista
   * @param {Object} taxistaData - Datos del taxista
   * @returns {number} ID del taxista creado
   */
  create(taxistaData) {
    const {
      user_id,
      licencia,
      municipio,
      matricula,
      modelo_vehiculo,
      estado = 'ocupado'
    } = taxistaData;

    try {
      const result = this.db.prepare(`
        INSERT INTO taxistas (user_id, licencia, municipio, matricula, modelo_vehiculo, estado) 
        VALUES (?, ?, ?, ?, ?, ?)
      `).run(user_id, licencia, municipio, matricula, modelo_vehiculo, estado);

      return result.lastInsertRowid;
    } catch (error) {
      throw new Error(`Error al crear taxista: ${error.message}`);
    }
  }

  /**
   * Buscar taxista por ID
   * @param {number} id - ID del taxista
   * @returns {Object|null} Taxista encontrado o null
   */
  findById(id) {
    try {
      return this.db.prepare(`
        SELECT t.*, u.nombre, u.email, u.telefono
        FROM taxistas t
        INNER JOIN users u ON t.user_id = u.id
        WHERE t.id = ?
      `).get(id);
    } catch (error) {
      throw new Error(`Error al buscar taxista: ${error.message}`);
    }
  }

  /**
   * Buscar taxista por user_id
   * @param {number} userId - ID del usuario
   * @returns {Object|null} Taxista encontrado o null
   */
  findByUserId(userId) {
    try {
      return this.db.prepare('SELECT * FROM taxistas WHERE user_id = ?').get(userId);
    } catch (error) {
      throw new Error(`Error al buscar taxista por user_id: ${error.message}`);
    }
  }

  /**
   * Obtener todos los taxistas
   * @returns {Array} Lista de taxistas
   */
  findAll() {
    try {
      return this.db.prepare(`
        SELECT t.*, u.nombre, u.email, u.telefono
        FROM taxistas t
        INNER JOIN users u ON t.user_id = u.id
        ORDER BY u.nombre ASC
      `).all();
    } catch (error) {
      throw new Error(`Error al obtener taxistas: ${error.message}`);
    }
  }

  /**
   * Obtener taxistas disponibles (libres)
   * @returns {Array} Lista de taxistas libres
   */
  findDisponibles() {
    try {
      return this.db.prepare(`
        SELECT t.*, u.nombre, u.email, u.telefono
        FROM taxistas t
        INNER JOIN users u ON t.user_id = u.id
        WHERE t.estado = 'libre'
        ORDER BY u.nombre ASC
      `).all();
    } catch (error) {
      throw new Error(`Error al obtener taxistas disponibles: ${error.message}`);
    }
  }

  /**
   * Obtener taxistas cercanos a una ubicación
   * @param {number} lat - Latitud
   * @param {number} lng - Longitud
   * @param {number} radio - Radio de búsqueda en km (default: 10)
   * @returns {Array} Lista de taxistas cercanos
   */
  findCercanos(lat, lng, radio = 10) {
    try {
      const taxistas = this.findDisponibles();
      
      // Filtrar por distancia
      return taxistas
        .map(taxista => {
          if (!taxista.latitud || !taxista.longitud) {
            return null;
          }

          const distancia = this.calcularDistancia(
            lat, lng, 
            taxista.latitud, taxista.longitud
          );

          return {
            ...taxista,
            distancia
          };
        })
        .filter(taxista => taxista !== null && taxista.distancia <= radio)
        .sort((a, b) => a.distancia - b.distancia);
    } catch (error) {
      throw new Error(`Error al buscar taxistas cercanos: ${error.message}`);
    }
  }

  /**
   * Actualizar estado del taxista
   * @param {number} id - ID del taxista
   * @param {string} estado - Nuevo estado (libre, ocupado, en_servicio)
   * @returns {boolean} True si se actualizó correctamente
   */
  updateEstado(id, estado) {
    const estadosValidos = ['libre', 'ocupado', 'en_servicio'];
    
    if (!estadosValidos.includes(estado)) {
      throw new Error(`Estado inválido: ${estado}`);
    }

    try {
      const result = this.db.prepare(`
        UPDATE taxistas 
        SET estado = ? 
        WHERE id = ?
      `).run(estado, id);

      return result.changes > 0;
    } catch (error) {
      throw new Error(`Error al actualizar estado: ${error.message}`);
    }
  }

  /**
   * Actualizar ubicación del taxista
   * @param {number} id - ID del taxista
   * @param {number} latitud - Nueva latitud
   * @param {number} longitud - Nueva longitud
   * @returns {boolean} True si se actualizó correctamente
   */
  updateUbicacion(id, latitud, longitud) {
    try {
      const result = this.db.prepare(`
        UPDATE taxistas 
        SET latitud = ?, longitud = ? 
        WHERE id = ?
      `).run(latitud, longitud, id);

      return result.changes > 0;
    } catch (error) {
      throw new Error(`Error al actualizar ubicación: ${error.message}`);
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
  calcularDistancia(lat1, lng1, lat2, lng2) {
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
   * Contar taxistas por estado
   * @param {string} estado - Estado del taxista (opcional)
   * @returns {number} Cantidad de taxistas
   */
  count(estado = null) {
    try {
      if (estado) {
        return this.db.prepare('SELECT COUNT(*) as count FROM taxistas WHERE estado = ?').get(estado).count;
      }
      return this.db.prepare('SELECT COUNT(*) as count FROM taxistas').get().count;
    } catch (error) {
      throw new Error(`Error al contar taxistas: ${error.message}`);
    }
  }
}

module.exports = TaxistaModel;
