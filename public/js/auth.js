// Obtener API_URL desde meta tag (dinámico) o usar fallback
const API_URL = document.querySelector('meta[name="api-url"]')?.content || 'http://localhost:8000/api';

// Manejar login
async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    try {
        const response = await fetch(`${API_URL}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Error al iniciar sesión');
        }

        // Guardar token y datos del usuario
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));

        // Mostrar mensaje de éxito
        alert('✅ ¡Bienvenido ' + data.user.nombre + '!');

        // Redirigir según el rol
        switch (data.user.role) {
            case 'cliente':
                window.location.href = '/cliente';
                break;
            case 'taxista':
                window.location.href = '/taxista';
                break;
            case 'admin':
                window.location.href = '/admin';
                break;
            default:
                throw new Error('Rol no válido');
        }

    } catch (error) {
        alert('❌ Error: ' + error.message);
    }
}

// Manejar registro
async function handleRegister(event) {
    event.preventDefault();
    
    const nombre = document.getElementById('registerNombre').value;
    const email = document.getElementById('registerEmail').value;
    const telefono = document.getElementById('registerTelefono').value;
    const password = document.getElementById('registerPassword').value;

    if (password.length < 6) {
        alert('❌ La contraseña debe tener al menos 6 caracteres');
        return;
    }

    try {
        const response = await fetch(`${API_URL}/auth/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nombre,
                email,
                telefono,
                password,
                role: 'cliente'
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Error al registrarse');
        }

        // Guardar token y datos del usuario
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));

        alert('✅ ¡Cuenta creada exitosamente!');
        window.location.href = '/cliente';

    } catch (error) {
        alert('❌ Error: ' + error.message);
    }
}

// Verificar autenticación
function verificarAutenticacion(rolesPermitidos = []) {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    if (!token) {
        window.location.href = '/';
        return null;
    }

    if (rolesPermitidos.length > 0 && !rolesPermitidos.includes(user.role)) {
        alert('❌ No tienes permisos para acceder a esta página');
        cerrarSesion();
        return null;
    }

    return { token, user };
}

// Cerrar sesión
function cerrarSesion() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/';
}

// Headers de autenticación
function getAuthHeaders() {
    const token = localStorage.getItem('token');
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };
}

// Hacer petición autenticada
async function fetchAuth(url, options = {}) {
    const token = localStorage.getItem('token');
    
    const response = await fetch(url, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...options.headers
        }
    });

    if (response.status === 401 || response.status === 403) {
        alert('❌ Sesión expirada. Por favor, inicia sesión nuevamente.');
        cerrarSesion();
        throw new Error('Sesión expirada');
    }

    return response;
}

// Mostrar alertas
function mostrarAlerta(mensaje, tipo = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo}`;
    alertDiv.textContent = mensaje;
    
    const contenedor = document.querySelector('.container') || document.body;
    contenedor.insertBefore(alertDiv, contenedor.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
