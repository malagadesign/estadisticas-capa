/**
 * CAPA Encuestas v2.0 - JavaScript Principal
 */

(function() {
    'use strict';
    
    // ==========================================
    // AUTO-CERRAR ALERTS DESPUÉS DE 5 SEGUNDOS
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
    
    // ==========================================
    // CONFIRMACIÓN ANTES DE ELIMINAR/DESHABILITAR
    // ==========================================
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-confirm]') || e.target.closest('[data-confirm]')) {
            const element = e.target.matches('[data-confirm]') ? e.target : e.target.closest('[data-confirm]');
            const message = element.dataset.confirm || '¿Está seguro de realizar esta acción?';
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // ==========================================
    // LOADING SPINNER PARA FORMULARIOS
    // ==========================================
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const submitBtn = form.querySelector('[type="submit"]');
        
        if (submitBtn && !submitBtn.classList.contains('no-loading')) {
            // Deshabilitar botón y mostrar spinner
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
            
            // Restaurar después de 10 segundos (por si falla la submit)
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000);
        }
    });
    
    // ==========================================
    // TOOLTIPS DE BOOTSTRAP
    // ==========================================
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // ==========================================
    // MEJORAR DROPDOWNS EN MOBILE
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (!navbarCollapse) return;
        
        const dropdownToggles = navbarCollapse.querySelectorAll('.dropdown-toggle');
        
        // Función para habilitar/deshabilitar Bootstrap dropdowns
        function toggleBootstrapDropdowns() {
            dropdownToggles.forEach(function(toggle) {
                if (window.innerWidth < 992) {
                    // Mobile: remover data-bs-toggle para desactivar Bootstrap
                    toggle.removeAttribute('data-bs-toggle');
                } else {
                    // Desktop: agregar data-bs-toggle para activar Bootstrap
                    toggle.setAttribute('data-bs-toggle', 'dropdown');
                }
            });
        }
        
        // Ejecutar al cargar
        toggleBootstrapDropdowns();
        
        // Ejecutar al redimensionar
        window.addEventListener('resize', toggleBootstrapDropdowns);
        
        // Handler manual para mobile
        dropdownToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                if (window.innerWidth < 992) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const dropdown = this.nextElementSibling;
                    
                    if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                        const isOpen = dropdown.classList.contains('show');
                        
                        // Cerrar todos los demás dropdowns
                        document.querySelectorAll('.navbar .dropdown-menu.show').forEach(function(menu) {
                            menu.classList.remove('show');
                        });
                        
                        // Toggle este dropdown
                        if (!isOpen) {
                            dropdown.classList.add('show');
                        }
                    }
                    
                    return false;
                }
            });
        });
        
        // Cerrar dropdown al hacer click en un item (solo mobile)
        const dropdownItems = navbarCollapse.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(function(item) {
            item.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    // Cerrar todos los dropdowns
                    document.querySelectorAll('.navbar .dropdown-menu.show').forEach(function(menu) {
                        menu.classList.remove('show');
                    });
                    
                    // Cerrar el navbar completo
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });
        });
    });
    
    // ==========================================
    // FORMATEAR NÚMEROS EN INPUTS
    // ==========================================
    window.formatearNumero = function(input) {
        let valor = input.value.replace(/\D/g, '');
        if (valor) {
            valor = parseInt(valor, 10).toLocaleString('es-AR');
        }
        input.value = valor;
    };
    
    // ==========================================
    // HELPER: FETCH CON CSRF TOKEN
    // ==========================================
    window.fetchCapa = async function(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        options.headers = options.headers || {};
        options.headers['X-CSRF-Token'] = csrfToken;
        options.headers['Content-Type'] = options.headers['Content-Type'] || 'application/json';
        
        try {
            const response = await fetch(url, options);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    };
    
    // ==========================================
    // HELPER: MOSTRAR TOAST
    // ==========================================
    window.showToast = function(message, type = 'success') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0 show" role="alert" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        const toastContainer = document.createElement('div');
        toastContainer.innerHTML = toastHtml;
        document.body.appendChild(toastContainer);
        
        setTimeout(function() {
            toastContainer.remove();
        }, 5000);
    };
    
    // ==========================================
    // DEBUG INFO (solo development)
    // ==========================================
    console.log('CAPA Encuestas v2.0 loaded successfully');
})();

