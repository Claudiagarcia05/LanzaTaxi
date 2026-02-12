/**
 * Tests Unitarios para ViajeModel
 * Pruebas de la lógica de negocio de viajes
 */

const Database = require('better-sqlite3');
const ViajeModel = require('../../src/models/Viaje.model');

describe('ViajeModel - Tests Unitarios', () => {
  let db;
  let viajeModel;

  // Setup: crear BD en memoria antes de cada test
  beforeEach(() => {
    db = new Database(':memory:');
    
    // Crear tabla de viajes
    db.exec(`
      CREATE TABLE viajes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cliente_id INTEGER NOT NULL,
        taxista_id INTEGER,
        origen_lat REAL NOT NULL,
        origen_lng REAL NOT NULL,
        origen_direccion TEXT NOT NULL,
        destino_lat REAL NOT NULL,
        destino_lng REAL NOT NULL,
        destino_direccion TEXT NOT NULL,
        distancia REAL NOT NULL,
        precio_estimado REAL NOT NULL,
        precio_final REAL,
        estado TEXT DEFAULT 'pendiente',
        tipo_tarifa TEXT NOT NULL,
        suplementos TEXT,
        fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
        fecha_aceptacion DATETIME,
        fecha_inicio DATETIME,
        fecha_fin DATETIME
      )
    `);

    viajeModel = new ViajeModel(db);
  });

  // Teardown: cerrar BD después de cada test
  afterEach(() => {
    db.close();
  });

  describe('calcularDistancia()', () => {
    test('debe calcular correctamente la distancia entre Arrecife y Puerto del Carmen', () => {
      // Arrecife a Puerto del Carmen: ~8.6 km
      const lat1 = 29.0469;
      const lng1 = -13.5901;
      const lat2 = 28.9644;
      const lng2 = -13.6362;

      const distancia = ViajeModel.calcularDistancia(lat1, lng1, lat2, lng2);

      expect(distancia).toBeGreaterThan(7);
      expect(distancia).toBeLessThan(11);
    });

    test('debe retornar 0 para puntos idénticos', () => {
      const distancia = ViajeModel.calcularDistancia(29.0469, -13.5901, 29.0469, -13.5901);
      expect(distancia).toBe(0);
    });

    test('debe retornar distancia positiva', () => {
      const distancia = ViajeModel.calcularDistancia(29.0, -13.0, 28.0, -14.0);
      expect(distancia).toBeGreaterThan(0);
    });
  });

  describe('create()', () => {
    test('debe crear un viaje correctamente', () => {
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0469,
        origen_lng: -13.5901,
        origen_direccion: 'Arrecife, Lanzarote',
        destino_lat: 28.9644,
        destino_lng: -13.6362,
        destino_direccion: 'Puerto del Carmen',
        distancia: 8.6,
        precio_estimado: 12.50,
        tipo_tarifa: 'Tarifa 1'
      };

      const viajeId = viajeModel.create(viajeData);

      expect(viajeId).toBeDefined();
      expect(typeof viajeId).toBe('number');
      expect(viajeId).toBeGreaterThan(0);
    });

    test('debe lanzar error si faltan campos requeridos', () => {
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0469
        // Faltan campos requeridos
      };

      expect(() => viajeModel.create(viajeData)).toThrow();
    });
  });

  describe('findById()', () => {
    test('debe encontrar un viaje por ID', () => {
      // Crear viaje
      const viajeId = viajeModel.create({
        cliente_id: 1,
        origen_lat: 29.0469,
        origen_lng: -13.5901,
        origen_direccion: 'Arrecife',
        destino_lat: 28.9644,
        destino_lng: -13.6362,
        destino_direccion: 'Puerto del Carmen',
        distancia: 8.6,
        precio_estimado: 12.50,
        tipo_tarifa: 'Tarifa 1'
      });

      const viaje = viajeModel.findById(viajeId);

      expect(viaje).toBeDefined();
      expect(viaje.id).toBe(viajeId);
      expect(viaje.cliente_id).toBe(1);
      expect(viaje.estado).toBe('pendiente');
    });

    test('debe retornar null para ID inexistente', () => {
      const viaje = viajeModel.findById(9999);
      expect(viaje).toBeUndefined();
    });
  });

  describe('aceptar()', () => {
    test('debe aceptar un viaje pendiente', () => {
      // Crear viaje pendiente
      const viajeId = viajeModel.create({
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        origen_direccion: 'Origen',
        destino_lat: 28.0,
        destino_lng: -14.0,
        destino_direccion: 'Destino',
        distancia: 10,
        precio_estimado: 15,
        tipo_tarifa: 'Tarifa 1'
      });

      const success = viajeModel.aceptar(viajeId, 1);

      expect(success).toBe(true);

      const viaje = viajeModel.findById(viajeId);
      expect(viaje.estado).toBe('aceptado');
      expect(viaje.taxista_id).toBe(1);
    });

    test('no debe aceptar un viaje ya aceptado', () => {
      // Crear y aceptar viaje
      const viajeId = viajeModel.create({
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        origen_direccion: 'Origen',
        destino_lat: 28.0,
        destino_lng: -14.0,
        destino_direccion: 'Destino',
        distancia: 10,
        precio_estimado: 15,
        tipo_tarifa: 'Tarifa 1'
      });

      viajeModel.aceptar(viajeId, 1);
      const success = viajeModel.aceptar(viajeId, 2);

      expect(success).toBe(false);
    });
  });

  describe('finalizar()', () => {
    test('debe finalizar un viaje en curso', () => {
      // Crear, aceptar e iniciar viaje
      const viajeId = viajeModel.create({
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        origen_direccion: 'Origen',
        destino_lat: 28.0,
        destino_lng: -14.0,
        destino_direccion: 'Destino',
        distancia: 10,
        precio_estimado: 15,
        tipo_tarifa: 'Tarifa 1'
      });

      viajeModel.aceptar(viajeId, 1);
      viajeModel.iniciar(viajeId);
      
      const success = viajeModel.finalizar(viajeId, 15.50);

      expect(success).toBe(true);

      const viaje = viajeModel.findById(viajeId);
      expect(viaje.estado).toBe('finalizado');
      expect(viaje.precio_final).toBe(15.50);
    });
  });

  describe('count()', () => {
    test('debe contar todos los viajes', () => {
      // Crear varios viajes
      viajeModel.create({
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        origen_direccion: 'Origen',
        destino_lat: 28.0,
        destino_lng: -14.0,
        destino_direccion: 'Destino',
        distancia: 10,
        precio_estimado: 15,
        tipo_tarifa: 'Tarifa 1'
      });

      viajeModel.create({
        cliente_id: 2,
        origen_lat: 29.0,
        origen_lng: -13.0,
        origen_direccion: 'Origen 2',
        destino_lat: 28.0,
        destino_lng: -14.0,
        destino_direccion: 'Destino 2',
        distancia: 5,
        precio_estimado: 10,
        tipo_tarifa: 'Tarifa 1'
      });

      const count = viajeModel.count();
      expect(count).toBe(2);
    });

    test('debe contar viajes por estado', () => {
      // Crear viajes con diferentes estados
      const id1 = viajeModel.create({
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        origen_direccion: 'Origen',
        destino_lat: 28.0,
        destino_lng: -14.0,
        destino_direccion: 'Destino',
        distancia: 10,
        precio_estimado: 15,
        tipo_tarifa: 'Tarifa 1'
      });

      const id2 = viajeModel.create({
        cliente_id: 2,
        origen_lat: 29.0,
        origen_lng: -13.0,
        origen_direccion: 'Origen 2',
        destino_lat: 28.0,
        destino_lng: -14.0,
        destino_direccion: 'Destino 2',
        distancia: 5,
        precio_estimado: 10,
        tipo_tarifa: 'Tarifa 1'
      });

      viajeModel.aceptar(id2, 1);

      const pendientes = viajeModel.count('pendiente');
      const aceptados = viajeModel.count('aceptado');

      expect(pendientes).toBe(1);
      expect(aceptados).toBe(1);
    });
  });
});
