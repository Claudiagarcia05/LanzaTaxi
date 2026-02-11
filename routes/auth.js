const express = require('express');
const router = express.Router();
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const db = require('../database');

// Middleware para verificar token
const verifyToken = (req, res, next) => {
  const token = req.headers['authorization']?.split(' ')[1];
  
  if (!token) {
    return res.status(403).json({ error: 'Token no proporcionado' });
  }

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    req.userId = decoded.id;
    req.userRole = decoded.role;
    next();
  } catch (error) {
    return res.status(401).json({ error: 'Token inválido' });
  }
};

// Registro
router.post('/register', (req, res) => {
  try {
    const { email, password, nombre, telefono, role = 'cliente' } = req.body;

    if (!email || !password || !nombre) {
      return res.status(400).json({ error: 'Faltan campos requeridos' });
    }

    // Verificar si el usuario ya existe
    const existingUser = db.prepare('SELECT * FROM users WHERE email = ?').get(email);
    if (existingUser) {
      return res.status(400).json({ error: 'El email ya está registrado' });
    }

    // Hash de la contraseña
    const hashedPassword = bcrypt.hashSync(password, 10);

    // Insertar usuario
    const result = db.prepare(`
      INSERT INTO users (email, password, nombre, telefono, role) 
      VALUES (?, ?, ?, ?, ?)
    `).run(email, hashedPassword, nombre, telefono, role);

    const userId = result.lastInsertRowid;

    // Generar token
    const token = jwt.sign(
      { id: userId, email, role },
      process.env.JWT_SECRET,
      { expiresIn: '7d' }
    );

    res.json({
      message: 'Usuario registrado exitosamente',
      token,
      user: { id: userId, email, nombre, role }
    });

  } catch (error) {
    console.error('Error en registro:', error);
    res.status(500).json({ error: 'Error al registrar usuario' });
  }
});

// Login
router.post('/login', (req, res) => {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ error: 'Email y contraseña requeridos' });
    }

    // Buscar usuario
    const user = db.prepare('SELECT * FROM users WHERE email = ?').get(email);
    
    if (!user) {
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }

    // Verificar contraseña
    const validPassword = bcrypt.compareSync(password, user.password);
    
    if (!validPassword) {
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }

    // Si es taxista, obtener información adicional
    let taxistaInfo = null;
    if (user.role === 'taxista') {
      taxistaInfo = db.prepare('SELECT * FROM taxistas WHERE user_id = ?').get(user.id);
    }

    // Generar token
    const token = jwt.sign(
      { id: user.id, email: user.email, role: user.role },
      process.env.JWT_SECRET,
      { expiresIn: '7d' }
    );

    res.json({
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
    });

  } catch (error) {
    console.error('Error en login:', error);
    res.status(500).json({ error: 'Error al iniciar sesión' });
  }
});

// Obtener perfil del usuario autenticado
router.get('/profile', verifyToken, (req, res) => {
  try {
    const user = db.prepare('SELECT id, email, nombre, telefono, role FROM users WHERE id = ?').get(req.userId);
    
    if (!user) {
      return res.status(404).json({ error: 'Usuario no encontrado' });
    }

    let taxistaInfo = null;
    if (user.role === 'taxista') {
      taxistaInfo = db.prepare('SELECT * FROM taxistas WHERE user_id = ?').get(user.id);
    }

    res.json({
      ...user,
      taxista: taxistaInfo
    });

  } catch (error) {
    console.error('Error al obtener perfil:', error);
    res.status(500).json({ error: 'Error al obtener perfil' });
  }
});

module.exports = router;
module.exports.verifyToken = verifyToken;
