/**
 * Modelo de Usuario - Patrón Active Record
 * Representa la entidad Usuario con sus métodos de acceso a datos
 */

class UserModel {
  constructor(db) {
    this.db = db;
  }

  /**
   * Buscar usuario por email
   * @param {string} email - Email del usuario
   * @returns {Object|null} Usuario encontrado o null
   */
  findByEmail(email) {
    try {
      return this.db.prepare('SELECT * FROM users WHERE email = ?').get(email);
    } catch (error) {
      throw new Error(`Error al buscar usuario por email: ${error.message}`);
    }
  }

  /**
   * Buscar usuario por ID
   * @param {number} id - ID del usuario
   * @returns {Object|null} Usuario encontrado o null
   */
  findById(id) {
    try {
      return this.db.prepare('SELECT * FROM users WHERE id = ?').get(id);
    } catch (error) {
      throw new Error(`Error al buscar usuario por ID: ${error.message}`);
    }
  }

  /**
   * Crear nuevo usuario
   * @param {Object} userData - Datos del usuario
   * @returns {number} ID del usuario creado
   */
  create(userData) {
    const { email, password, nombre, telefono, role = 'cliente' } = userData;

    try {
      const result = this.db.prepare(`
        INSERT INTO users (email, password, nombre, telefono, role) 
        VALUES (?, ?, ?, ?, ?)
      `).run(email, password, nombre, telefono, role);

      return result.lastInsertRowid;
    } catch (error) {
      throw new Error(`Error al crear usuario: ${error.message}`);
    }
  }

  /**
   * Obtener todos los usuarios con paginación
   * @param {number} limit - Límite de resultados
   * @param {number} offset - Offset para paginación
   * @returns {Array} Lista de usuarios
   */
  findAll(limit = 100, offset = 0) {
    try {
      return this.db.prepare(`
        SELECT id, email, nombre, telefono, role, created_at 
        FROM users 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
      `).all(limit, offset);
    } catch (error) {
      throw new Error(`Error al obtener usuarios: ${error.message}`);
    }
  }

  /**
   * Obtener usuarios por rol
   * @param {string} role - Rol del usuario (cliente, taxista, admin)
   * @returns {Array} Lista de usuarios
   */
  findByRole(role) {
    try {
      return this.db.prepare(`
        SELECT id, email, nombre, telefono, role, created_at 
        FROM users 
        WHERE role = ?
        ORDER BY created_at DESC
      `).all(role);
    } catch (error) {
      throw new Error(`Error al obtener usuarios por rol: ${error.message}`);
    }
  }

  /**
   * Actualizar usuario
   * @param {number} id - ID del usuario
   * @param {Object} userData - Datos a actualizar
   * @returns {boolean} True si se actualizó correctamente
   */
  update(id, userData) {
    const fields = [];
    const values = [];

    if (userData.nombre !== undefined) {
      fields.push('nombre = ?');
      values.push(userData.nombre);
    }
    if (userData.telefono !== undefined) {
      fields.push('telefono = ?');
      values.push(userData.telefono);
    }
    if (userData.password !== undefined) {
      fields.push('password = ?');
      values.push(userData.password);
    }

    if (fields.length === 0) {
      return false;
    }

    values.push(id);

    try {
      const result = this.db.prepare(`
        UPDATE users 
        SET ${fields.join(', ')} 
        WHERE id = ?
      `).run(...values);

      return result.changes > 0;
    } catch (error) {
      throw new Error(`Error al actualizar usuario: ${error.message}`);
    }
  }

  /**
   * Eliminar usuario
   * @param {number} id - ID del usuario
   * @returns {boolean} True si se eliminó correctamente
   */
  delete(id) {
    try {
      const result = this.db.prepare('DELETE FROM users WHERE id = ?').run(id);
      return result.changes > 0;
    } catch (error) {
      throw new Error(`Error al eliminar usuario: ${error.message}`);
    }
  }

  /**
   * Contar usuarios por rol
   * @param {string} role - Rol a contar (opcional)
   * @returns {number} Cantidad de usuarios
   */
  count(role = null) {
    try {
      if (role) {
        return this.db.prepare('SELECT COUNT(*) as count FROM users WHERE role = ?').get(role).count;
      }
      return this.db.prepare('SELECT COUNT(*) as count FROM users').get().count;
    } catch (error) {
      throw new Error(`Error al contar usuarios: ${error.message}`);
    }
  }
}

module.exports = UserModel;
