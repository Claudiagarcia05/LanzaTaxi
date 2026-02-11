const Database = require('better-sqlite3');
const bcrypt = require('bcryptjs');
const path = require('path');

const db = new Database(path.join(__dirname, 'lanzataxi.db'));

// Crear tablas
function initDatabase() {
  // Tabla de usuarios
  db.exec(`
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      email TEXT UNIQUE NOT NULL,
      password TEXT NOT NULL,
      role TEXT NOT NULL CHECK(role IN ('cliente', 'taxista', 'admin')),
      nombre TEXT NOT NULL,
      telefono TEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Tabla de taxistas (información adicional)
  db.exec(`
    CREATE TABLE IF NOT EXISTS taxistas (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      user_id INTEGER UNIQUE NOT NULL,
      licencia TEXT UNIQUE NOT NULL,
      municipio TEXT NOT NULL,
      matricula TEXT NOT NULL,
      modelo_vehiculo TEXT,
      estado TEXT DEFAULT 'ocupado' CHECK(estado IN ('libre', 'ocupado', 'en_servicio')),
      latitud REAL,
      longitud REAL,
      valoracion_media REAL DEFAULT 0,
      num_valoraciones INTEGER DEFAULT 0,
      FOREIGN KEY (user_id) REFERENCES users(id)
    )
  `);

  // Tabla de viajes
  db.exec(`
    CREATE TABLE IF NOT EXISTS viajes (
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
      estado TEXT DEFAULT 'pendiente' CHECK(estado IN ('pendiente', 'aceptado', 'en_curso', 'finalizado', 'cancelado')),
      tipo_tarifa TEXT NOT NULL,
      suplementos TEXT,
      fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
      fecha_aceptacion DATETIME,
      fecha_inicio DATETIME,
      fecha_fin DATETIME,
      valoracion INTEGER,
      comentario TEXT,
      FOREIGN KEY (cliente_id) REFERENCES users(id),
      FOREIGN KEY (taxista_id) REFERENCES taxistas(id)
    )
  `);

  // Tabla de tarifas
  db.exec(`
    CREATE TABLE IF NOT EXISTS tarifas (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      nombre TEXT NOT NULL,
      bajada_bandera REAL NOT NULL,
      precio_km REAL NOT NULL,
      suplemento_aeropuerto REAL DEFAULT 0,
      suplemento_puerto REAL DEFAULT 0,
      suplemento_nocturno REAL DEFAULT 0,
      suplemento_festivo REAL DEFAULT 0,
      activa INTEGER DEFAULT 1,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Tabla de incidencias
  db.exec(`
    CREATE TABLE IF NOT EXISTS incidencias (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      viaje_id INTEGER,
      user_id INTEGER NOT NULL,
      tipo TEXT NOT NULL,
      descripcion TEXT NOT NULL,
      estado TEXT DEFAULT 'pendiente' CHECK(estado IN ('pendiente', 'en_revision', 'resuelta')),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (viaje_id) REFERENCES viajes(id),
      FOREIGN KEY (user_id) REFERENCES users(id)
    )
  `);

  console.log('✅ Base de datos inicializada correctamente');
  
  // Insertar datos de prueba
  insertTestData();
}

function insertTestData() {
  // Verificar si ya existen datos
  const userCount = db.prepare('SELECT COUNT(*) as count FROM users').get();
  if (userCount.count > 0) {
    console.log('✅ Datos de prueba ya existen');
    return;
  }

  const hashedPassword = bcrypt.hashSync('123456', 10);

  // Usuarios de prueba
  const insertUser = db.prepare(`
    INSERT INTO users (email, password, role, nombre, telefono) 
    VALUES (?, ?, ?, ?, ?)
  `);

  insertUser.run('admin@test.com', hashedPassword, 'admin', 'Administrador Principal', '928123456');
  insertUser.run('cliente@test.com', hashedPassword, 'cliente', 'María García', '628111222');
  insertUser.run('cliente2@test.com', hashedPassword, 'cliente', 'John Smith', '628222333');
  
  insertUser.run('taxista@test.com', hashedPassword, 'taxista', 'Carlos Rodríguez', '628333444');
  insertUser.run('taxista2@test.com', hashedPassword, 'taxista', 'Pedro Martínez', '628444555');
  insertUser.run('taxista3@test.com', hashedPassword, 'taxista', 'Ana López', '628555666');

  // Taxistas
  const insertTaxista = db.prepare(`
    INSERT INTO taxistas (user_id, licencia, municipio, matricula, modelo_vehiculo, estado, latitud, longitud) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  `);

  // Taxista 1 - Arrecife (Cerca del aeropuerto)
  insertTaxista.run(4, 'LZ-001', 'Arrecife', '1234-ABC', 'Toyota Prius', 'libre', 28.945, -13.605);
  
  // Taxista 2 - Teguise
  insertTaxista.run(5, 'LZ-002', 'Teguise', '5678-DEF', 'Mercedes E-Class', 'libre', 29.060, -13.562);
  
  // Taxista 3 - Puerto del Carmen
  insertTaxista.run(6, 'LZ-003', 'Tías', '9012-GHI', 'Seat Toledo', 'ocupado', 28.927, -13.664);

  // Tarifas oficiales de Lanzarote
  const insertTarifa = db.prepare(`
    INSERT INTO tarifas (nombre, bajada_bandera, precio_km, suplemento_aeropuerto, suplemento_puerto, suplemento_nocturno, suplemento_festivo) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
  `);

  insertTarifa.run('Tarifa 1 - Urbana', 3.15, 0.60, 3.50, 2.00, 0.20, 0.30);
  insertTarifa.run('Tarifa 2 - Interurbana', 3.15, 0.75, 3.50, 2.00, 0.20, 0.30);

  // Viajes de ejemplo
  const insertViaje = db.prepare(`
    INSERT INTO viajes (cliente_id, taxista_id, origen_lat, origen_lng, origen_direccion, destino_lat, destino_lng, destino_direccion, distancia, precio_estimado, precio_final, estado, tipo_tarifa, valoracion, comentario, fecha_fin) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now', '-2 hours'))
  `);

  insertViaje.run(
    2, 1, 
    28.945, -13.605, 'Aeropuerto de Lanzarote',
    28.963, -13.547, 'Arrecife, Calle León y Castillo',
    8.5, 12.60, 12.60, 'finalizado', 'Tarifa 2',
    5, 'Muy puntual y amable'
  );

  console.log('✅ Datos de prueba insertados correctamente');
}

// Inicializar la base de datos
initDatabase();

module.exports = db;
