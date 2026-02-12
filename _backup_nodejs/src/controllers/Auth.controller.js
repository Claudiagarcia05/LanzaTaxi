/**
 * Controlador de Autenticación
 * Maneja las peticiones HTTP relacionadas con autenticación
 * Patrón MVC - Controlador
 */

class AuthController {
  constructor(authService) {
    this.authService = authService;
  }

  /**
   * Registrar nuevo usuario
   * POST /api/auth/register
   */
  async register(req, res) {
    try {
      const result = await this.authService.register(req.body);
      res.status(201).json(result);
    } catch (error) {
      console.error('Error en registro:', error.message);
      res.status(400).json({ error: error.message });
    }
  }

  /**
   * Iniciar sesión
   * POST /api/auth/login
   */
  async login(req, res) {
    try {
      const result = await this.authService.login(req.body.email, req.body.password);
      res.json(result);
    } catch (error) {
      console.error('Error en login:', error.message);
      
      // Si es error de credenciales, retornar 401
      if (error.message.includes('Credenciales')) {
        return res.status(401).json({ error: error.message });
      }
      
      res.status(400).json({ error: error.message });
    }
  }

  /**
   * Obtener perfil del usuario autenticado
   * GET /api/auth/profile
   */
  async getProfile(req, res) {
    try {
      const userId = req.userId; // Viene del middleware de autenticación
      const profile = await this.authService.getProfile(userId);
      res.json(profile);
    } catch (error) {
      console.error('Error al obtener perfil:', error.message);
      res.status(404).json({ error: error.message });
    }
  }

  /**
   * Cambiar contraseña
   * POST /api/auth/change-password
   */
  async changePassword(req, res) {
    try {
      const userId = req.userId;
      const { oldPassword, newPassword } = req.body;

      const success = await this.authService.changePassword(userId, oldPassword, newPassword);
      
      if (success) {
        res.json({ message: 'Contraseña cambiada exitosamente' });
      } else {
        res.status(400).json({ error: 'No se pudo cambiar la contraseña' });
      }
    } catch (error) {
      console.error('Error al cambiar contraseña:', error.message);
      res.status(400).json({ error: error.message });
    }
  }

  /**
   * Cerrar sesión (opcional - el cliente elimina el token)
   * POST /api/auth/logout
   */
  async logout(req, res) {
    // En JWT no hay estado en servidor, el cliente elimina el token
    res.json({ message: 'Sesión cerrada exitosamente' });
  }
}

module.exports = AuthController;
