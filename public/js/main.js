// Main JavaScript para LanzaTaxi
// Inicialización y utilidades globales

class LanzaTaxi {
    constructor() {
        this.init();
    }

    init() {
        this.setupAccessibility();
        this.setupFormValidation();
        this.setupAutoclose();
    }

    // Accesibilidad mejorada
    setupAccessibility() {
        // Asegurar navegación por tabindex
        const focusableElements = document.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        // Focus trap para modales (cuando existan)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                this.handleTabKey(e, focusableElements);
            }
        });

        // Announce live updates to screen readers
        this.setupLiveRegions();
    }

    handleTabKey(e, elements) {
        const focusableArray = Array.from(elements);
        const currentFocus = document.activeElement;
        const focusedIndex = focusableArray.indexOf(currentFocus);

        if (e.shiftKey) {
            if (focusedIndex === 0) {
                e.preventDefault();
                focusableArray[focusableArray.length - 1].focus();
            }
        } else {
            if (focusedIndex === focusableArray.length - 1) {
                e.preventDefault();
                focusableArray[0].focus();
            }
        }
    }

    setupLiveRegions() {
        // Crear regiones live para anuncios de pantalla
        const liveRegion = document.createElement('div');
        liveRegion.id = 'live-region';
        liveRegion.className = 'sr-only';
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        document.body.appendChild(liveRegion);
    }

    // Validación de formularios accesible
    setupFormValidation() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.announceError('Por favor completa los campos requeridos correctamente');
                }
            });

            // Validación en tiempo real
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;

        // Limpiar mensajes de error previos
        const errorMsg = field.parentElement.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }

        if (!value && field.hasAttribute('required')) {
            isValid = false;
        }

        if (field.type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
        }

        if (field.type === 'tel' && value && !this.isValidPhone(value)) {
            isValid = false;
        }

        if (!isValid && field.hasAttribute('required')) {
            field.setAttribute('aria-invalid', 'true');
            const errorMsg = document.createElement('p');
            errorMsg.className = 'error-message text-red-600 text-sm mt-1';
            errorMsg.id = `error-${field.id}`;
            errorMsg.textContent = this.getErrorMessage(field);
            field.setAttribute('aria-describedby', errorMsg.id);
            field.parentElement.appendChild(errorMsg);
        } else {
            field.setAttribute('aria-invalid', 'false');
        }

        return isValid;
    }

    isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    isValidPhone(phone) {
        const regex = /^[\d\s\-\+\(\)]{9,}$/;
        return regex.test(phone);
    }

    getErrorMessage(field) {
        const type = field.type;
        const required = field.hasAttribute('required');

        if (!field.value.trim() && required) {
            return 'Este campo es requerido';
        }
        if (type === 'email') {
            return 'Por favor ingresa un email válido';
        }
        if (type === 'tel') {
            return 'Por favor ingresa un teléfono válido';
        }
        return 'Por favor revisa este campo';
    }

    // Auto-cerrar alertas
    setupAutoclose() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    }

    // Anunciar mensajes al lector de pantalla
    announceError(message) {
        const liveRegion = document.getElementById('live-region');
        if (liveRegion) {
            liveRegion.textContent = `Error: ${message}`;
        }
    }

    announceSuccess(message) {
        const liveRegion = document.getElementById('live-region');
        if (liveRegion) {
            liveRegion.textContent = `Éxito: ${message}`;
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new LanzaTaxi();
});

// Utilidades globales
window.LanzaTaxi = {
    showNotification(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-error',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} fixed top-4 right-4 max-w-md z-50 animate-slideIn`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            <div class="flex-1">
                <p class="font-semibold">${message}</p>
            </div>
            <button class="btn-icon ml-4" onclick="this.closest('[role=alert]').remove()" aria-label="Cerrar alerta">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        
        document.body.appendChild(alert);

        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    }
};
