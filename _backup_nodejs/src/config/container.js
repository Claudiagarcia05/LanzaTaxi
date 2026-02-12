/**
 * Contenedor de Inyección de Dependencias
 * Gestiona la creación y resolución de dependencias del sistema
 * Patrón: Service Locator + Dependency Injection
 */

const db = require('../../database');

// Modelos
const UserModel = require('../models/User.model');
const ViajeModel = require('../models/Viaje.model');
const TaxistaModel = require('../models/Taxista.model');

// Servicios
const AuthService = require('../services/Auth.service');
const ViajeService = require('../services/Viaje.service');

class Container {
  constructor() {
    this.services = new Map();
    this.singletons = new Map();
    this.registerDefaults();
  }

  /**
   * Registrar servicios por defecto
   */
  registerDefaults() {
    // Registrar base de datos como singleton
    this.singleton('database', () => db);

    // Registrar Modelos como factories
    this.register('UserModel', () => new UserModel(this.resolve('database')));
    this.register('ViajeModel', () => new ViajeModel(this.resolve('database')));
    this.register('TaxistaModel', () => new TaxistaModel(this.resolve('database')));

    // Registrar Servicios como singletons
    this.singleton('AuthService', () => new AuthService(
      this.resolve('UserModel'),
      this.resolve('TaxistaModel')
    ));

    this.singleton('ViajeService', () => new ViajeService(
      this.resolve('ViajeModel'),
      null // tarifaModel - pendiente de implementar
    ));
  }

  /**
   * Registrar un servicio (factory - nueva instancia cada vez)
   * @param {string} name - Nombre del servicio
   * @param {Function} resolver - Función que retorna la instancia
   */
  register(name, resolver) {
    this.services.set(name, { type: 'factory', resolver });
  }

  /**
   * Registrar un singleton (una sola instancia compartida)
   * @param {string} name - Nombre del servicio
   * @param {Function} resolver - Función que retorna la instancia
   */
  singleton(name, resolver) {
    this.services.set(name, { type: 'singleton', resolver });
  }

  /**
   * Resolver/obtener un servicio
   * @param {string} name - Nombre del servicio
   * @returns {*} Instancia del servicio
   */
  resolve(name) {
    const service = this.services.get(name);

    if (!service) {
      throw new Error(`Servicio '${name}' no registrado en el contenedor`);
    }

    // Si es singleton y ya fue creado, retornar la instancia existente
    if (service.type === 'singleton' && this.singletons.has(name)) {
      return this.singletons.get(name);
    }

    // Crear nueva instancia
    const instance = service.resolver(this);

    // Si es singleton, guardar la instancia
    if (service.type === 'singleton') {
      this.singletons.set(name, instance);
    }

    return instance;
  }

  /**
   * Verificar si un servicio está registrado
   * @param {string} name - Nombre del servicio
   * @returns {boolean}
   */
  has(name) {
    return this.services.has(name);
  }

  /**
   * Obtener todos los servicios registrados
   * @returns {Array} Lista de nombres de servicios
   */
  getRegisteredServices() {
    return Array.from(this.services.keys());
  }

  /**
   * Limpiar singletons (útil para testing)
   */
  clearSingletons() {
    this.singletons.clear();
  }

  /**
   * Reset completo del contenedor
   */
  reset() {
    this.services.clear();
    this.singletons.clear();
    this.registerDefaults();
  }
}

// Crear instancia global del contenedor
const container = new Container();

module.exports = container;
