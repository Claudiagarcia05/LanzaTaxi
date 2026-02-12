/**
 * Servicio de Viajes - Lógica de Negocio
 * Maneja la creación, gestión y cálculo de viajes
 */

const ViajeModel = require('../models/Viaje.model');

class ViajeService {
  constructor(viajeModel, tarifaModel) {
    this.viajeModel = viajeModel;
    this.tarifaModel = tarifaModel;
  }

  /**
   * Crear nuevo viaje
   * @param {Object} viajeData - Datos del viaje
   * @returns {Object} Viaje creado
   */
  async crearViaje(viajeData) {
    const {
      cliente_id,
      origen_lat,
      origen_lng,
      origen_direccion,
      destino_lat,
      destino_lng,
      destino_direccion,
      tipo_tarifa = 'Tarifa 1'
    } = viajeData;

    // Validar campos requeridos
    if (!cliente_id || !origen_lat || !origen_lng || !destino_lat || !destino_lng) {
      throw new Error('Faltan campos requeridos para crear el viaje');
    }

    // Calcular distancia
    const distancia = ViajeModel.calcularDistancia(
      origen_lat, origen_lng,
      destino_lat, destino_lng
    );

    if (distancia < 0.5) {
      throw new Error('La distancia mínima del viaje debe ser de 500 metros');
    }

    // Calcular precio estimado
    const precio_estimado = this.calcularPrecio(distancia, tipo_tarifa);

    // Crear viaje
    const viajeId = this.viajeModel.create({
      cliente_id,
      origen_lat,
      origen_lng,
      origen_direccion: origen_direccion || `${origen_lat},${origen_lng}`,
      destino_lat,
      destino_lng,
      destino_direccion: destino_direccion || `${destino_lat},${destino_lng}`,
      distancia,
      precio_estimado,
      tipo_tarifa
    });

    return this.viajeModel.findById(viajeId);
  }

  /**
   * Calcular precio del viaje según tarifa
   * @param {number} distancia - Distancia en km
   * @param {string} tipoTarifa - Tipo de tarifa
   * @param {Array} suplementos - Suplementos aplicables (opcional)
   * @returns {number} Precio calculado
   */
  calcularPrecio(distancia, tipoTarifa = 'Tarifa 1', suplementos = []) {
    // Tarifas base de Lanzarote (según normativa)
    const tarifas = {
      'Tarifa 1': {
        bajada_bandera: 3.15,
        precio_km: 0.60
      },
      'Tarifa 2': {
        bajada_bandera: 3.15,
        precio_km: 0.75
      }
    };

    const tarifa = tarifas[tipoTarifa] || tarifas['Tarifa 1'];
    
    let precio = tarifa.bajada_bandera + (distancia * tarifa.precio_km);

    // Aplicar suplementos
    const suplementosValores = {
      'aeropuerto': 3.50,
      'puerto': 2.00,
      'nocturno': 0.20 * precio, // 20% adicional
      'festivo': 0.30 * precio   // 30% adicional
    };

    if (suplementos && suplementos.length > 0) {
      suplementos.forEach(sup => {
        if (suplementosValores[sup]) {
          precio += suplementosValores[sup];
        }
      });
    }

    return Math.round(precio * 100) / 100; // Redondear a 2 decimales
  }

  /**
   * Aceptar viaje
   * @param {number} viajeId - ID del viaje
   * @param {number} taxistaId - ID del taxista
   * @returns {Object} Viaje actualizado
   */
  async aceptarViaje(viajeId, taxistaId) {
    // Verificar que el viaje existe y está pendiente
    const viaje = this.viajeModel.findById(viajeId);
    
    if (!viaje) {
      throw new Error('Viaje no encontrado');
    }

    if (viaje.estado !== 'pendiente') {
      throw new Error(`No se puede aceptar un viaje en estado: ${viaje.estado}`);
    }

    // Aceptar viaje
    const success = this.viajeModel.aceptar(viajeId, taxistaId);
    
    if (!success) {
      throw new Error('No se pudo aceptar el viaje');
    }

    return this.viajeModel.findById(viajeId);
  }

  /**
   * Iniciar viaje
   * @param {number} viajeId - ID del viaje
   * @returns {Object} Viaje actualizado
   */
  async iniciarViaje(viajeId) {
    const viaje = this.viajeModel.findById(viajeId);
    
    if (!viaje) {
      throw new Error('Viaje no encontrado');
    }

    if (viaje.estado !== 'aceptado') {
      throw new Error(`No se puede iniciar un viaje en estado: ${viaje.estado}`);
    }

    const success = this.viajeModel.iniciar(viajeId);
    
    if (!success) {
      throw new Error('No se pudo iniciar el viaje');
    }

    return this.viajeModel.findById(viajeId);
  }

  /**
   * Finalizar viaje
   * @param {number} viajeId - ID del viaje
   * @returns {Object} Viaje actualizado
   */
  async finalizarViaje(viajeId) {
    const viaje = this.viajeModel.findById(viajeId);
    
    if (!viaje) {
      throw new Error('Viaje no encontrado');
    }

    if (viaje.estado !== 'en_curso') {
      throw new Error(`No se puede finalizar un viaje en estado: ${viaje.estado}`);
    }

    // Usar precio estimado como precio final si no se especifica otro
    const precioFinal = viaje.precio_final || viaje.precio_estimado;

    const success = this.viajeModel.finalizar(viajeId, precioFinal);
    
    if (!success) {
      throw new Error('No se pudo finalizar el viaje');
    }

    return this.viajeModel.findById(viajeId);
  }

  /**
   * Cancelar viaje
   * @param {number} viajeId - ID del viaje
   * @returns {Object} Viaje actualizado
   */
  async cancelarViaje(viajeId) {
    const viaje = this.viajeModel.findById(viajeId);
    
    if (!viaje) {
      throw new Error('Viaje no encontrado');
    }

    if (!['pendiente', 'aceptado'].includes(viaje.estado)) {
      throw new Error(`No se puede cancelar un viaje en estado: ${viaje.estado}`);
    }

    const success = this.viajeModel.cancelar(viajeId);
    
    if (!success) {
      throw new Error('No se pudo cancelar el viaje');
    }

    return this.viajeModel.findById(viajeId);
  }

  /**
   * Obtener viajes de un cliente
   * @param {number} clienteId - ID del cliente
   * @returns {Array} Lista de viajes
   */
  async obtenerViajesCliente(clienteId) {
    return this.viajeModel.findByCliente(clienteId);
  }

  /**
   * Obtener viajes de un taxista
   * @param {number} taxistaId - ID del taxista
   * @returns {Array} Lista de viajes
   */
  async obtenerViajesTaxista(taxistaId) {
    return this.viajeModel.findByTaxista(taxistaId);
  }

  /**
   * Obtener viajes pendientes
   * @returns {Array} Lista de viajes pendientes
   */
  async obtenerViajesPendientes() {
    return this.viajeModel.findPendientes();
  }

  /**
   * Obtener estadísticas de viajes
   * @returns {Object} Estadísticas
   */
  async obtenerEstadisticas() {
    return this.viajeModel.getEstadisticas();
  }
}

module.exports = ViajeService;
