/**
 * Controlador de Viajes
 * Maneja las peticiones HTTP relacionadas con viajes
 * Patrón MVC - Controlador
 */

class ViajeController {
  constructor(viajeService) {
    this.viajeService = viajeService;
  }

  /**
   * Crear nuevo viaje
   * POST /api/viajes
   */
  async crear(req, res) {
    try {
      const viaje = await this.viajeService.crearViaje(req.body);
      res.status(201).json(viaje);
    } catch (error) {
      console.error('Error al crear viaje:', error.message);
      res.status(400).json({ error: error.message });
    }
  }

  /**
   * Obtener viaje por ID
   * GET /api/viajes/:id
   */
  async obtener(req, res) {
    try {
      const viajeId = parseInt(req.params.id);
      const viaje = await this.viajeService.viajeModel.findById(viajeId);
      
      if (!viaje) {
        return res.status(404).json({ error: 'Viaje no encontrado' });
      }

      res.json(viaje);
    } catch (error) {
      console.error('Error al obtener viaje:', error.message);
      res.status(500).json({ error: error.message });
    }
  }

  /**
   * Aceptar viaje (taxista)
   * PUT /api/viajes/:id/aceptar
   */
  async aceptar(req, res) {
    try {
      const viajeId = parseInt(req.params.id);
      const { taxistaId } = req.body;

      if (!taxistaId) {
        return res.status(400).json({ error: 'taxistaId es requerido' });
      }

      const viaje = await this.viajeService.aceptarViaje(viajeId, taxistaId);
      res.json(viaje);
    } catch (error) {
      console.error('Error al aceptar viaje:', error.message);
      res.status(400).json({ error: error.message });
    }
  }

  /**
   * Iniciar viaje
   * PUT /api/viajes/:id/iniciar
   */
  async iniciar(req, res) {
    try {
      const viajeId = parseInt(req.params.id);
      const viaje = await this.viajeService.iniciarViaje(viajeId);
      res.json(viaje);
    } catch (error) {
      console.error('Error al iniciar viaje:', error.message);
      res.status(400).json({ error: error.message });
    }
  }

  /**
   * Finalizar viaje
   * PUT /api/viajes/:id/finalizar
   */
  async finalizar(req, res) {
    try {
      const viajeId = parseInt(req.params.id);
      const viaje = await this.viajeService.finalizarViaje(viajeId);
      res.json(viaje);
    } catch (error) {
      console.error('Error al finalizar viaje:', error.message);
      res.status(400).json({ error: error.message });
    }
  }

  /**
   * Cancelar viaje
   * PUT /api/viajes/:id/cancelar
   */
  async cancelar(req, res) {
    try {
      const viajeId = parseInt(req.params.id);
      const viaje = await this.viajeService.cancelarViaje(viajeId);
      res.json(viaje);
    } catch (error) {
      console.error('Error al cancelar viaje:', error.message);
      res.status(400).json({ error: error.message });
    }
  }

  /**
   * Obtener viajes del cliente autenticado
   * GET /api/viajes/mis-viajes
   */
  async misViajes(req, res) {
    try {
      const clienteId = req.userId; // Viene del middleware de autenticación
      const viajes = await this.viajeService.obtenerViajesCliente(clienteId);
      res.json(viajes);
    } catch (error) {
      console.error('Error al obtener viajes:', error.message);
      res.status(500).json({ error: error.message });
    }
  }

  /**
   * Obtener viajes pendientes
   * GET /api/viajes/pendientes
   */
  async pendientes(req, res) {
    try {
      const viajes = await this.viajeService.obtenerViajesPendientes();
      res.json(viajes);
    } catch (error) {
      console.error('Error al obtener viajes pendientes:', error.message);
      res.status(500).json({ error: error.message });
    }
  }

  /**
   * Obtener estadísticas de viajes
   * GET /api/viajes/estadisticas
   */
  async estadisticas(req, res) {
    try {
      const stats = await this.viajeService.obtenerEstadisticas();
      res.json(stats);
    } catch (error) {
      console.error('Error al obtener estadísticas:', error.message);
      res.status(500).json({ error: error.message });
    }
  }
}

module.exports = ViajeController;
