/**
 * Servicio de Autenticación - Lógica de Negocio
 * Maneja registro, login y autenticación de usuarios
 */

const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

class AuthService {
  constructor(userModel, taxistaModel) {
    this.userModel = userModel;
    this.taxistaModel = taxistaModel;
    this.jwtSecret = process.env.JWT_SECRET || 'lanzataxi_secret_key_2026';
  }

  /**
   * Registrar nuevo usuario
   * @param {Object} userData - Datos del usuario
   * @returns {Object} Usuario creado y token
   */
  async register(userData) {
    const { email, password, nombre, telefono, role = 'cliente' } = userData;

    // Validar campos requeridos
    if (!email || !password || !nombre) {
      throw new Error('Faltan campos requeridos: email, password, nombre');
    }

    // Validar formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      throw new Error('Formato de email inválido');
    }

    // Validar longitud de contraseña
    if (password.length < 6) {
      throw new Error('La contraseña debe tener al menos 6 caracteres');
    }

    // Validar rol
    const rolesValidos = ['cliente', 'taxista', 'admin'];
    if (!rolesValidos.includes(role)) {
      throw new Error(`Rol inválido. Debe ser: ${rolesValidos.join(', ')}`);
    }

    // Verificar si el email ya existe
    const existingUser = this.userModel.findByEmail(email);
    if (existingUser) {
      throw new Error('El email ya está registrado');
    }

    // Hash de la contraseña
    const hashedPassword = bcrypt.hashSync(password, 10);

    // Crear usuario
    const userId = this.userModel.create({
      email,
      password: hashedPassword,
      nombre,
      telefono,
      role
    });

    // Generar token
    const token = this.generateToken({ id: userId, email, role });

    return {
      message: 'Usuario registrado exitosamente',
      token,
      user: { id: userId, email, nombre, role }
    };
  }

  /**
   * Iniciar sesión
   * @param {string} email - Email del usuario
   * @param {string} password - Contraseña
   * @returns {Object} Usuario y token
   */
  async login(email, password) {
    // Validar campos requeridos
    if (!email || !password) {
      throw new Error('Email y contraseña son requeridos');
    }

    // Buscar usuario
    const user = this.userModel.findByEmail(email);
    if (!user) {
      throw new Error('Credenciales inválidas');
    }

    // Verificar contraseña
    const validPassword = bcrypt.compareSync(password, user.password);
    if (!validPassword) {
      throw new Error('Credenciales inválidas');
    }

    // Si es taxista, obtener información adicional
    let taxistaInfo = null;
    if (user.role === 'taxista') {
      taxistaInfo = this.taxistaModel.findByUserId(user.id);
    }

    // Generar token
    const token = this.generateToken({ 
      id: user.id, 
      email: user.email, 
      role: user.role 
    });

    return {
      message: 'Login exitoso',
      token,
      user: {
        id: user.id,
        email: user.email,
        nombre: user.nombre,
        telefono: user.telefono,
        role: user.role,
        taxista: taxistaInfo
      }
    };
  }

  /**
   * Obtener perfil de usuario
   * @param {number} userId - ID del usuario
   * @returns {Object} Datos del usuario
   */
  async getProfile(userId) {
    const user = this.userModel.findById(userId);
    
    if (!user) {
      throw new Error('Usuario no encontrado');
    }

    // Eliminar password del resultado
    delete user.password;

    // Si es taxista, agregar información adicional
    if (user.role === 'taxista') {
      user.taxista = this.taxistaModel.findByUserId(user.id);
    }

    return user;
  }

  /**
   * Cambiar contraseña
   * @param {number} userId - ID del usuario
   * @param {string} oldPassword - Contraseña actual
   * @param {string} newPassword - Nueva contraseña
   * @returns {boolean} True si se cambió correctamente
   */
  async changePassword(userId, oldPassword, newPassword) {
    const user = this.userModel.findById(userId);
    
    if (!user) {
      throw new Error('Usuario no encontrado');
    }

    // Verificar contraseña actual
    const validPassword = bcrypt.compareSync(oldPassword, user.password);
    if (!validPassword) {
      throw new Error('Contraseña actual incorrecta');
    }

    // Validar nueva contraseña
    if (newPassword.length < 6) {
      throw new Error('La nueva contraseña debe tener al menos 6 caracteres');
    }

    // Hash de la nueva contraseña
    const hashedPassword = bcrypt.hashSync(newPassword, 10);

    // Actualizar contraseña
    return this.userModel.update(userId, { password: hashedPassword });
  }

  /**
   * Generar token JWT
   * @param {Object} payload - Datos a incluir en el token
   * @returns {string} Token JWT
   */
  generateToken(payload) {
    return jwt.sign(payload, this.jwtSecret, { expiresIn: '7d' });
  }

  /**
   * Verificar token JWT
   * @param {string} token - Token a verificar
   * @returns {Object} Payload del token
   */
  verifyToken(token) {
    try {
      return jwt.verify(token, this.jwtSecret);
    } catch (error) {
      throw new Error('Token inválido o expirado');
    }
  }
}

module.exports = AuthService;
