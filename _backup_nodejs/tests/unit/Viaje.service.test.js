/**
 * Tests Unitarios para ViajeService
 * Pruebas de la lógica de negocio de servicios de viaje
 */

const Database = require('better-sqlite3');
const ViajeModel = require('../../src/models/Viaje.model');
const ViajeService = require('../../src/services/Viaje.service');

describe('ViajeService - Tests Unitarios', () => {
  let db;
  let viajeModel;
  let viajeService;

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
    viajeService = new ViajeService(viajeModel, null);
  });

  afterEach(() => {
    db.close();
  });

  describe('calcularPrecio()', () => {
    test('debe calcular precio correctamente con Tarifa 1', () => {
      const distancia = 10; // km
      const precio = viajeService.calcularPrecio(distancia, 'Tarifa 1');

      // 3.15 (bajada) + (10 * 0.60) = 9.15
      expect(precio).toBe(9.15);
    });

    test('debe calcular precio correctamente con Tarifa 2', () => {
      const distancia = 10; // km
      const precio = viajeService.calcularPrecio(distancia, 'Tarifa 2');

      // 3.15 (bajada) + (10 * 0.75) = 10.65
      expect(precio).toBe(10.65);
    });

    test('debe aplicar suplemento de aeropuerto', () => {
      const distancia = 10;
      const precio = viajeService.calcularPrecio(distancia, 'Tarifa 1', ['aeropuerto']);

      // 9.15 + 3.50 = 12.65
      expect(precio).toBe(12.65);
    });

    test('debe aplicar múltiples suplementos', () => {
      const distancia = 10;
      const precio = viajeService.calcularPrecio(distancia, 'Tarifa 1', ['aeropuerto', 'puerto']);

      // 9.15 + 3.50 + 2.00 = 14.65
      expect(precio).toBe(14.65);
    });

    test('debe usar Tarifa 1 por defecto si no se especifica', () => {
      const distancia = 10;
      const precio = viajeService.calcularPrecio(distancia);

      expect(precio).toBe(9.15);
    });
  });

  describe('crearViaje()', () => {
    test('debe crear un viaje completo con distancia y precio calculados', async () => {
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0469,
        origen_lng: -13.5901,
        origen_direccion: 'Arrecife',
        destino_lat: 28.9644,
        destino_lng: -13.6362,
        destino_direccion: 'Puerto del Carmen',
        tipo_tarifa: 'Tarifa 1'
      };

      const viaje = await viajeService.crearViaje(viajeData);

      expect(viaje).toBeDefined();
      expect(viaje.id).toBeDefined();
      expect(viaje.distancia).toBeGreaterThan(7);
      expect(viaje.distancia).toBeLessThan(11);
      expect(viaje.precio_estimado).toBeGreaterThan(0);
      expect(viaje.estado).toBe('pendiente');
    });

    test('debe lanzar error si faltan campos requeridos', async () => {
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0469
        // Faltan campos
      };

      await expect(viajeService.crearViaje(viajeData)).rejects.toThrow();
    });

    test('debe lanzar error si la distancia es menor a 500 metros', async () => {
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0469,
        origen_lng: -13.5901,
        destino_lat: 29.0470, // Muy cerca
        destino_lng: -13.5902,
        tipo_tarifa: 'Tarifa 1'
      };

      await expect(viajeService.crearViaje(viajeData)).rejects.toThrow('distancia mínima');
    });
  });

  describe('aceptarViaje()', () => {
    test('debe aceptar un viaje pendiente correctamente', async () => {
      // Crear viaje
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        destino_lat: 28.0,
        destino_lng: -14.0,
        tipo_tarifa: 'Tarifa 1'
      };

      const viajeCreado = await viajeService.crearViaje(viajeData);
      
      // Aceptar viaje
      const viajeAceptado = await viajeService.aceptarViaje(viajeCreado.id, 1);

      expect(viajeAceptado.estado).toBe('aceptado');
      expect(viajeAceptado.taxista_id).toBe(1);
    });

    test('debe lanzar error si el viaje no existe', async () => {
      await expect(viajeService.aceptarViaje(9999, 1)).rejects.toThrow('no encontrado');
    });

    test('debe lanzar error si el viaje no está pendiente', async () => {
      // Crear y aceptar viaje
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        destino_lat: 28.0,
        destino_lng: -14.0,
        tipo_tarifa: 'Tarifa 1'
      };

      const viaje = await viajeService.crearViaje(viajeData);
      await viajeService.aceptarViaje(viaje.id, 1);

      // Intentar aceptar de nuevo
      await expect(viajeService.aceptarViaje(viaje.id, 2)).rejects.toThrow();
    });
  });

  describe('finalizarViaje()', () => {
    test('debe finalizar un viaje en curso', async () => {
      // Crear, aceptar e iniciar viaje
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        destino_lat: 28.0,
        destino_lng: -14.0,
        tipo_tarifa: 'Tarifa 1'
      };

      const viaje = await viajeService.crearViaje(viajeData);
      await viajeService.aceptarViaje(viaje.id, 1);
      await viajeService.iniciarViaje(viaje.id);

      const viajeFinalizado = await viajeService.finalizarViaje(viaje.id);

      expect(viajeFinalizado.estado).toBe('finalizado');
      expect(viajeFinalizado.precio_final).toBeDefined();
    });

    test('debe lanzar error si el viaje no está en curso', async () => {
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        destino_lat: 28.0,
        destino_lng: -14.0,
        tipo_tarifa: 'Tarifa 1'
      };

      const viaje = await viajeService.crearViaje(viajeData);

      await expect(viajeService.finalizarViaje(viaje.id)).rejects.toThrow();
    });
  });

  describe('cancelarViaje()', () => {
    test('debe cancelar un viaje pendiente', async () => {
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        destino_lat: 28.0,
        destino_lng: -14.0,
        tipo_tarifa: 'Tarifa 1'
      };

      const viaje = await viajeService.crearViaje(viajeData);
      const viajeCancelado = await viajeService.cancelarViaje(viaje.id);

      expect(viajeCancelado.estado).toBe('cancelado');
    });

    test('no debe cancelar un viaje finalizado', async () => {
      const viajeData = {
        cliente_id: 1,
        origen_lat: 29.0,
        origen_lng: -13.0,
        destino_lat: 28.0,
        destino_lng: -14.0,
        tipo_tarifa: 'Tarifa 1'
      };

      const viaje = await viajeService.crearViaje(viajeData);
      await viajeService.aceptarViaje(viaje.id, 1);
      await viajeService.iniciarViaje(viaje.id);
      await viajeService.finalizarViaje(viaje.id);

      await expect(viajeService.cancelarViaje(viaje.id)).rejects.toThrow();
    });
  });
});
