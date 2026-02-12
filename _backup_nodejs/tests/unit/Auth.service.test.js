/**
 * Tests Unitarios para AuthService
 * Pruebas de autenticación y gestión de usuarios
 */

const Database = require('better-sqlite3');
const UserModel = require('../../src/models/User.model');
const TaxistaModel = require('../../src/models/Taxista.model');
const AuthService = require('../../src/services/Auth.service');

describe('AuthService - Tests Unitarios', () => {
  let db;
  let userModel;
  let taxistaModel;
  let authService;

  beforeEach(() => {
    db = new Database(':memory:');
    
    // Crear tablas
    db.exec(`
      CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT NOT NULL,
        nombre TEXT NOT NULL,
        telefono TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
      )
    `);

    db.exec(`
      CREATE TABLE taxistas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER UNIQUE NOT NULL,
        licencia TEXT UNIQUE NOT NULL,
        municipio TEXT NOT NULL,
        matricula TEXT NOT NULL,
        modelo_vehiculo TEXT,
        estado TEXT DEFAULT 'ocupado',
        latitud REAL,
        longitud REAL,
        FOREIGN KEY (user_id) REFERENCES users(id)
      )
    `);

    userModel = new UserModel(db);
    taxistaModel = new TaxistaModel(db);
    authService = new AuthService(userModel, taxistaModel);
  });

  afterEach(() => {
    db.close();
  });

  describe('register()', () => {
    test('debe registrar un nuevo usuario correctamente', async () => {
      const userData = {
        email: 'test@example.com',
        password: 'password123',
        nombre: 'Juan Pérez',
        telefono: '628111222',
        role: 'cliente'
      };

      const result = await authService.register(userData);

      expect(result).toBeDefined();
      expect(result.token).toBeDefined();
      expect(result.user.email).toBe('test@example.com');
      expect(result.user.nombre).toBe('Juan Pérez');
      expect(result.user.role).toBe('cliente');
    });

    test('debe lanzar error si falta el email', async () => {
      const userData = {
        password: 'password123',
        nombre: 'Juan Pérez'
      };

      await expect(authService.register(userData)).rejects.toThrow('Faltan campos requeridos');
    });

    test('debe lanzar error si el email es inválido', async () => {
      const userData = {
        email: 'invalid-email',
        password: 'password123',
        nombre: 'Juan Pérez'
      };

      await expect(authService.register(userData)).rejects.toThrow('Formato de email inválido');
    });

    test('debe lanzar error si la contraseña es muy corta', async () => {
      const userData = {
        email: 'test@example.com',
        password: '123',
        nombre: 'Juan Pérez'
      };

      await expect(authService.register(userData)).rejects.toThrow('al menos 6 caracteres');
    });

    test('debe lanzar error si el rol es inválido', async () => {
      const userData = {
        email: 'test@example.com',
        password: 'password123',
        nombre: 'Juan Pérez',
        role: 'superadmin'
      };

      await expect(authService.register(userData)).rejects.toThrow('Rol inválido');
    });

    test('debe lanzar error si el email ya está registrado', async () => {
      const userData = {
        email: 'test@example.com',
        password: 'password123',
        nombre: 'Juan Pérez'
      };

      await authService.register(userData);
      
      await expect(authService.register(userData)).rejects.toThrow('ya está registrado');
    });

    test('debe hashear la contraseña', async () => {
      const userData = {
        email: 'test@example.com',
        password: 'password123',
        nombre: 'Juan Pérez'
      };

      await authService.register(userData);

      const user = userModel.findByEmail('test@example.com');
      expect(user.password).not.toBe('password123');
      expect(user.password).toMatch(/^\$2[aby]\$/); // bcrypt hash format
    });
  });

  describe('login()', () => {
    beforeEach(async () => {
      // Registrar un usuario para las pruebas
      await authService.register({
        email: 'test@example.com',
        password: 'password123',
        nombre: 'Juan Pérez',
        role: 'cliente'
      });
    });

    test('debe hacer login correctamente con credenciales válidas', async () => {
      const result = await authService.login('test@example.com', 'password123');

      expect(result).toBeDefined();
      expect(result.token).toBeDefined();
      expect(result.user.email).toBe('test@example.com');
      expect(result.user.password).toBeUndefined(); // No debe retornar password
    });

    test('debe lanzar error con email incorrecto', async () => {
      await expect(
        authService.login('wrong@example.com', 'password123')
      ).rejects.toThrow('Credenciales inválidas');
    });

    test('debe lanzar error con contraseña incorrecta', async () => {
      await expect(
        authService.login('test@example.com', 'wrongpassword')
      ).rejects.toThrow('Credenciales inválidas');
    });

    test('debe lanzar error si falta email o contraseña', async () => {
      await expect(authService.login('', 'password')).rejects.toThrow('requeridos');
      await expect(authService.login('email@test.com', '')).rejects.toThrow('requeridos');
    });

    test('debe retornar información de taxista si el usuario es taxista', async () => {
      // Registrar taxista
      const taxistaRegister = await authService.register({
        email: 'taxista@example.com',
        password: 'password123',
        nombre: 'Taxista Test',
        role: 'taxista'
      });

      // Crear info de taxista
      taxistaModel.create({
        user_id: taxistaRegister.user.id,
        licencia: 'LZ-001',
        municipio: 'Arrecife',
        matricula: '1234-ABC',
        modelo_vehiculo: 'Toyota Prius'
      });

      // Login
      const result = await authService.login('taxista@example.com', 'password123');

      expect(result.user.taxista).toBeDefined();
      expect(result.user.taxista.licencia).toBe('LZ-001');
    });
  });

  describe('getProfile()', () => {
    test('debe obtener perfil de usuario existente', async () => {
      const registered = await authService.register({
        email: 'test@example.com',
        password: 'password123',
        nombre: 'Juan Pérez'
      });

      const profile = await authService.getProfile(registered.user.id);

      expect(profile).toBeDefined();
      expect(profile.email).toBe('test@example.com');
      expect(profile.nombre).toBe('Juan Pérez');
      expect(profile.password).toBeUndefined(); // No debe incluir password
    });

    test('debe lanzar error para usuario inexistente', async () => {
      await expect(authService.getProfile(9999)).rejects.toThrow('no encontrado');
    });
  });

  describe('changePassword()', () => {
    let userId;

    beforeEach(async () => {
      const registered = await authService.register({
        email: 'test@example.com',
        password: 'oldpassword',
        nombre: 'Juan Pérez'
      });
      userId = registered.user.id;
    });

    test('debe cambiar contraseña correctamente', async () => {
      const success = await authService.changePassword(userId, 'oldpassword', 'newpassword123');

      expect(success).toBe(true);

      // Verificar que puede hacer login con la nueva contraseña
      const result = await authService.login('test@example.com', 'newpassword123');
      expect(result.token).toBeDefined();
    });

    test('debe lanzar error si la contraseña actual es incorrecta', async () => {
      await expect(
        authService.changePassword(userId, 'wrongpassword', 'newpassword123')
      ).rejects.toThrow('incorrecta');
    });

    test('debe lanzar error si la nueva contraseña es muy corta', async () => {
      await expect(
        authService.changePassword(userId, 'oldpassword', '123')
      ).rejects.toThrow('al menos 6 caracteres');
    });
  });

  describe('generateToken() y verifyToken()', () => {
    test('debe generar y verificar token válido', () => {
      const payload = { id: 1, email: 'test@example.com', role: 'cliente' };
      
      const token = authService.generateToken(payload);
      expect(token).toBeDefined();
      expect(typeof token).toBe('string');

      const decoded = authService.verifyToken(token);
      expect(decoded.id).toBe(1);
      expect(decoded.email).toBe('test@example.com');
      expect(decoded.role).toBe('cliente');
    });

    test('debe lanzar error con token inválido', () => {
      expect(() => authService.verifyToken('invalid_token')).toThrow('inválido');
    });
  });
});
