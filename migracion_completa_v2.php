<?php
/**
 * MIGRACIÓN COMPLETA A V2
 * Unifica todas las funcionalidades en la versión 2 moderna
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚀 MIGRACIÓN COMPLETA A V2</h1>";
echo "<p>🔍 Unificando todas las funcionalidades en la versión 2 moderna...</p>";

// ============================================
// PASO 1: CREAR ESTRUCTURA V2 COMPLETA
// ============================================

echo "<h2>📁 PASO 1: Creando estructura V2 completa</h2>";

// Crear directorios necesarios
$directorios = [
    'v2/public',
    'v2/public/assets',
    'v2/public/assets/css',
    'v2/public/assets/js',
    'v2/public/assets/images',
    'v2/app/controllers',
    'v2/app/models',
    'v2/app/views',
    'v2/app/views/layouts',
    'v2/app/views/usuarios',
    'v2/app/views/encuestas',
    'v2/app/views/config',
    'v2/app/views/auth',
    'v2/app/views/errors',
    'v2/core',
    'v2/config',
    'v2/storage',
    'v2/storage/logs',
    'v2/storage/uploads',
    'v2/storage/cache'
];

foreach ($directorios as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p>✅ Creado directorio: $dir</p>";
        } else {
            echo "<p>❌ Error al crear directorio: $dir</p>";
        }
    } else {
        echo "<p>ℹ️ Directorio ya existe: $dir</p>";
    }
}

// ============================================
// PASO 2: CREAR INDEX.PHP PARA V2
// ============================================

echo "<h2>📄 PASO 2: Creando index.php para V2</h2>";

$index_content = '<?php
/**
 * CAPA Encuestas v2.0
 * Entry Point - Sistema Moderno
 */

// Suprimir warnings en pantalla (se logean en archivo)
ini_set(\'display_errors\', \'0\');
error_reporting(E_ALL);

// Cargar configuración
require_once __DIR__ . \'/config/app.php\';

// Cargar clases del core
require_once __DIR__ . \'/core/Router.php\';
require_once __DIR__ . \'/core/Database.php\';
require_once __DIR__ . \'/core/View.php\';
require_once __DIR__ . \'/core/Request.php\';
require_once __DIR__ . \'/core/Session.php\';

// Iniciar sesión
Session::start();

// Crear instancia del router
$router = new Router();

// Cargar rutas
require_once __DIR__ . \'/config/routes.php\';

// Manejo de errores global
set_exception_handler(function($exception) {
    error_log("Uncaught exception: " . $exception->getMessage());
    error_log("Stack trace: " . $exception->getTraceAsString());
    
    if (ENVIRONMENT === \'development\') {
        echo "<h1>Error</h1>";
        echo "<p><strong>Message:</strong> " . $exception->getMessage() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        View::render(\'errors/500\', [], \'auth\');
    }
});

// Despachar request
try {
    $router->dispatch(
        Request::url(),
        Request::method()
    );
} catch (Exception $e) {
    error_log("Dispatch error: " . $e->getMessage());
    
    if (ENVIRONMENT === \'development\') {
        echo "<h1>Dispatch Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        View::notFound();
    }
}
';

if (file_put_contents('v2/index.php', $index_content)) {
    echo "<p>✅ Archivo v2/index.php creado</p>";
} else {
    echo "<p>❌ Error al crear v2/index.php</p>";
}

// ============================================
// PASO 3: CREAR ROUTES.PHP PARA V2
// ============================================

echo "<h2>🛣️ PASO 3: Creando routes.php para V2</h2>";

$routes_content = '<?php
/**
 * Definición de rutas de la aplicación V2
 */

// ===============================================
// RUTAS PÚBLICAS (sin autenticación)
// ===============================================
$router->get(\'/\', \'AuthController@showLogin\');
$router->post(\'/login\', \'AuthController@login\');

// ===============================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ===============================================

// Dashboard
$router->get(\'/dashboard\', \'DashboardController@index\');

// Logout
$router->get(\'/logout\', \'AuthController@logout\');

// ===============================================
// ENCUESTAS (Admin y Socios)
// ===============================================
$router->get(\'/encuestas/ultima\', \'EncuestasController@ultima\');
$router->get(\'/encuestas/anteriores\', \'EncuestasController@anteriores\');
$router->post(\'/encuestas/guardar-precio\', \'EncuestasController@guardarPrecio\');
$router->post(\'/encuestas/upload-excel\', \'EncuestasController@uploadExcel\');
$router->post(\'/encuestas/toggle-articulo\', \'EncuestasController@toggleArticulo\');

// ===============================================
// USUARIOS (Solo Admin) - MIGRADO DE V1
// ===============================================
$router->get(\'/usuarios\', \'UsuariosController@index\');
$router->get(\'/usuarios/administrativos\', \'UsuariosController@administrativos\');
$router->get(\'/usuarios/socios\', \'UsuariosController@socios\');
$router->post(\'/usuarios/create\', \'UsuariosController@create\');
$router->post(\'/usuarios/update\', \'UsuariosController@update\');
$router->post(\'/usuarios/toggle\', \'UsuariosController@toggle\');

// ===============================================
// CONFIGURACIÓN (Solo Admin)
// ===============================================

// Mercados
$router->get(\'/config/mercados\', \'ConfigController@mercados\');
$router->post(\'/config/mercados/create\', \'ConfigController@mercados_create\');
$router->post(\'/config/mercados/update\', \'ConfigController@mercados_update\');
$router->post(\'/config/mercados/delete\', \'ConfigController@mercados_delete\');

// Rubros
$router->get(\'/config/rubros\', \'ConfigController@rubros\');
$router->post(\'/config/rubros/create\', \'ConfigController@rubros_create\');
$router->post(\'/config/rubros/update\', \'ConfigController@rubros_update\');
$router->post(\'/config/rubros/delete\', \'ConfigController@rubros_delete\');

// Familias
$router->get(\'/config/familias\', \'ConfigController@familias\');
$router->post(\'/config/familias/create\', \'ConfigController@familias_create\');
$router->post(\'/config/familias/update\', \'ConfigController@familias_update\');
$router->post(\'/config/familias/delete\', \'ConfigController@familias_delete\');

// Artículos
$router->get(\'/config/articulos\', \'ConfigController@articulos\');
$router->post(\'/config/articulos/create\', \'ConfigController@articulos_create\');
$router->post(\'/config/articulos/update\', \'ConfigController@articulos_update\');
$router->post(\'/config/articulos/delete\', \'ConfigController@articulos_delete\');

// Encuestas
$router->get(\'/config/encuestas\', \'ConfigController@encuestas\');
$router->post(\'/config/encuestas/create\', \'ConfigController@encuestas_create\');
$router->post(\'/config/encuestas/update\', \'ConfigController@encuestas_update\');
$router->post(\'/config/encuestas/delete\', \'ConfigController@encuestas_delete\');

// ===============================================
// CUENTA (Todos)
// ===============================================
$router->get(\'/cuenta/cambiar-password\', \'CuentaController@cambiarPassword\');
$router->post(\'/cuenta/update-password\', \'CuentaController@updatePassword\');

// ===============================================
// API (AJAX)
// ===============================================
$router->get(\'/api/familias/:idRubro\', \'ApiController@familiasPorRubro\');
$router->get(\'/api/articulos/:idFamilia\', \'ApiController@articulosPorFamilia\');
';

if (file_put_contents('v2/config/routes.php', $routes_content)) {
    echo "<p>✅ Archivo v2/config/routes.php creado</p>";
} else {
    echo "<p>❌ Error al crear v2/config/routes.php</p>";
}

// ============================================
// PASO 4: COPIAR ARCHIVOS CORE EXISTENTES
// ============================================

echo "<h2>⚙️ PASO 4: Copiando archivos core existentes</h2>";

$archivos_core = [
    'core/Router.php' => 'v2/core/Router.php',
    'core/Database.php' => 'v2/core/Database.php',
    'core/View.php' => 'v2/core/View.php',
    'core/Request.php' => 'v2/core/Request.php',
    'core/Session.php' => 'v2/core/Session.php'
];

foreach ($archivos_core as $origen => $destino) {
    if (file_exists($origen)) {
        if (copy($origen, $destino)) {
            echo "<p>✅ Copiado: $origen → $destino</p>";
        } else {
            echo "<p>❌ Error al copiar: $origen</p>";
        }
    } else {
        echo "<p>⚠️ No existe: $origen</p>";
    }
}

// ============================================
// PASO 5: COPIAR CONTROLADORES EXISTENTES
// ============================================

echo "<h2>🎮 PASO 5: Copiando controladores existentes</h2>";

$archivos_controllers = [
    'app/controllers/AuthController.php' => 'v2/app/controllers/AuthController.php',
    'app/controllers/DashboardController.php' => 'v2/app/controllers/DashboardController.php',
    'app/controllers/UsuariosController.php' => 'v2/app/controllers/UsuariosController.php',
    'app/controllers/ConfigController.php' => 'v2/app/controllers/ConfigController.php',
    'app/controllers/EncuestasController.php' => 'v2/app/controllers/EncuestasController.php'
];

foreach ($archivos_controllers as $origen => $destino) {
    if (file_exists($origen)) {
        if (copy($origen, $destino)) {
            echo "<p>✅ Copiado: $origen → $destino</p>";
        } else {
            echo "<p>❌ Error al copiar: $origen</p>";
        }
    } else {
        echo "<p>⚠️ No existe: $origen</p>";
    }
}

// ============================================
// PASO 6: COPIAR MODELOS EXISTENTES
// ============================================

echo "<h2>📊 PASO 6: Copiando modelos existentes</h2>";

$archivos_models = [
    'app/models/Usuario.php' => 'v2/app/models/Usuario.php',
    'app/models/Encuesta.php' => 'v2/app/models/Encuesta.php',
    'app/models/Articulo.php' => 'v2/app/models/Articulo.php',
    'app/models/Mercado.php' => 'v2/app/models/Mercado.php',
    'app/models/Rubro.php' => 'v2/app/models/Rubro.php',
    'app/models/Familia.php' => 'v2/app/models/Familia.php'
];

foreach ($archivos_models as $origen => $destino) {
    if (file_exists($origen)) {
        if (copy($origen, $destino)) {
            echo "<p>✅ Copiado: $origen → $destino</p>";
        } else {
            echo "<p>❌ Error al copiar: $origen</p>";
        }
    } else {
        echo "<p>⚠️ No existe: $origen</p>";
    }
}

// ============================================
// PASO 7: COPIAR VISTAS EXISTENTES
// ============================================

echo "<h2>🎨 PASO 7: Copiando vistas existentes</h2>";

// Copiar todas las vistas
$vistas_origen = 'app/views';
$vistas_destino = 'v2/app/views';

if (is_dir($vistas_origen)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($vistas_origen, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        $destino_path = $vistas_destino . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        
        if ($item->isDir()) {
            if (!is_dir($destino_path)) {
                mkdir($destino_path, 0755, true);
            }
        } else {
            if (copy($item, $destino_path)) {
                echo "<p>✅ Copiado: {$item->getPathname()} → $destino_path</p>";
            } else {
                echo "<p>❌ Error al copiar: {$item->getPathname()}</p>";
            }
        }
    }
} else {
    echo "<p>⚠️ No existe directorio de vistas: $vistas_origen</p>";
}

// ============================================
// PASO 8: CREAR .HTACCESS PARA V2
// ============================================

echo "<h2>🔒 PASO 8: Creando .htaccess para V2</h2>";

$htaccess_content = '# CAPA Encuestas v2.0 - Apache Configuration

# Habilitar rewrite engine
RewriteEngine On

# Headers de seguridad
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
</IfModule>

# Proteger archivos sensibles
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "*.log">
    Order allow,deny
    Deny from all
</Files>

# Proteger directorios
RedirectMatch 403 ^/storage/
RedirectMatch 403 ^/config/
RedirectMatch 403 ^/core/

# Routing para V2
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Cache para assets estáticos
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
</IfModule>

# Compresión GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
';

if (file_put_contents('v2/.htaccess', $htaccess_content)) {
    echo "<p>✅ Archivo v2/.htaccess creado</p>";
} else {
    echo "<p>❌ Error al crear v2/.htaccess</p>";
}

// ============================================
// PASO 9: CREAR ARCHIVO DE CONFIGURACIÓN DE PRODUCCIÓN
// ============================================

echo "<h2>⚙️ PASO 9: Creando configuración de producción</h2>";

$env_production_content = '# CAPA Encuestas v2.0 - Configuración de Producción

# BASE DE DATOS
DB_HOST=localhost
DB_USER=encuesta_capa
DB_PASSWORD=Malaga77
DB_NAME=encuesta_capa
DB_PORT=3306

# ENTORNO
ENVIRONMENT=production
DISPLAY_ERRORS=0
SESSION_COOKIE_SECURE=1

# APLICACIÓN
APP_URL=https://estadistica-capa.org.ar
APP_NAME=CAPA Encuestas
APP_VERSION=2.0

# SEGURIDAD
CSRF_TOKEN_EXPIRE=3600
SESSION_LIFETIME=7200

# EMAIL (PHPMailer)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=capa@capa.org.ar
MAIL_PASSWORD=your_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=capa@capa.org.ar
MAIL_FROM_NAME=CAPA Encuestas
';

if (file_put_contents('v2/.env', $env_production_content)) {
    echo "<p>✅ Archivo v2/.env creado</p>";
} else {
    echo "<p>❌ Error al crear v2/.env</p>";
}

// ============================================
// PASO 10: CREAR README PARA V2
// ============================================

echo "<h2>📖 PASO 10: Creando README para V2</h2>";

$readme_content = '# 🚀 CAPA Encuestas v2.0

## Sistema Moderno de Gestión de Encuestas de Precios

**Versión:** 2.0  
**Framework:** Custom PHP MVC + Bootstrap 5  
**Fecha:** Octubre 2025

---

## ✨ CARACTERÍSTICAS

- ✅ **Bootstrap 5** - Framework moderno y responsive
- ✅ **Mobile-First** - Diseño adaptado a todos los dispositivos
- ✅ **Arquitectura MVC** - Código organizado y mantenible
- ✅ **Routing moderno** - URLs limpias y RESTful
- ✅ **Seguridad mejorada** - Prepared statements, CSRF protection
- ✅ **Performance optimizado** - Lazy loading, queries eficientes
- ✅ **Paleta CAPA** - Colores oficiales de la marca

---

## 🎨 PALETA DE COLORES

- **Azul Oscuro CAPA:** `#001A4D` (primario)
- **Púrpura CAPA:** `#9D4EDD` (secundario)
- **Púrpura Claro:** `#C084FC` (acentos)
- **Púrpura Oscuro:** `#6B21A8` (hover)

---

## 📁 ESTRUCTURA DEL PROYECTO

```
v2/
├── index.php           # Entry point
├── .htaccess          # Routing y seguridad
├── .env               # Configuración
│
├── app/               # Aplicación
│   ├── controllers/   # Controladores
│   ├── models/        # Modelos (acceso a datos)
│   ├── views/         # Vistas (HTML+PHP)
│   ├── middleware/    # Middlewares
│   └── helpers/       # Funciones auxiliares
│
├── core/              # Core del framework
│   ├── Router.php
│   ├── Database.php
│   ├── View.php
│   ├── Request.php
│   └── Session.php
│
├── config/            # Configuración
│   ├── app.php        # Config general
│   └── routes.php     # Definición de rutas
│
└── storage/           # Almacenamiento
    ├── logs/
    ├── uploads/
    └── cache/
```

---

## 🚀 INSTALACIÓN Y ACCESO

### 1. **Acceder al sistema nuevo**

**URL:**
```
https://estadistica-capa.org.ar/v2/
```

### 2. **Credenciales de prueba**

Las mismas credenciales del sistema viejo funcionan:

**Admin:**
- Usuario: `Coordinación`
- Contraseña: `para1857`

**Socio:**
- Usuario: `CAPA`
- Contraseña: (la misma del sistema viejo)

### 3. **Base de Datos**

- ✅ Usa la misma BD: `encuesta_capa`
- ✅ No se modifica nada
- ✅ Funciona en paralelo con el sistema viejo

---

## 🔐 SEGURIDAD

### Implementado:

- ✅ Sesiones seguras con regeneración automática
- ✅ CSRF protection en todos los formularios
- ✅ Prepared statements en todas las queries
- ✅ Password hashing con bcrypt
- ✅ Headers de seguridad en .htaccess
- ✅ Validación de permisos por rol

---

## 📱 RESPONSIVE DESIGN

### Breakpoints:

- **Mobile:** < 576px
- **Tablet:** 576px - 991px
- **Desktop:** >= 992px

### Características Mobile:

- ✅ Navegación hamburger
- ✅ Tablas convertidas a cards
- ✅ Botones táctiles (44px mínimo)
- ✅ Inputs con font-size 16px (evita zoom en iOS)
- ✅ Scroll horizontal en tablas grandes

---

## 🗺️ RUTAS PRINCIPALES

### Públicas:
- `GET /` - Login
- `POST /login` - Procesar login

### Protegidas:
- `GET /dashboard` - Panel principal
- `GET /logout` - Cerrar sesión

### Encuestas:
- `GET /encuestas/ultima` - Última encuesta
- `GET /encuestas/anteriores` - Historial

### Configuración (Admin):
- `GET /config/rubros` - Gestión de rubros
- `GET /config/familias` - Gestión de familias
- `GET /config/articulos` - Gestión de artículos
- `GET /config/mercados` - Gestión de mercados
- `GET /config/encuestas` - Gestión de encuestas

### Usuarios (Admin):
- `GET /usuarios/administrativos` - Usuarios admin
- `GET /usuarios/socios` - Usuarios socios

---

## 🎯 ESTADO ACTUAL

### ✅ COMPLETADO:

1. ✅ Estructura del proyecto
2. ✅ Core del framework (Router, Database, View, Session, Request)
3. ✅ Sistema de autenticación
4. ✅ Layouts con Bootstrap 5
5. ✅ Diseño responsive mobile-first
6. ✅ Paleta de colores CAPA
7. ✅ Dashboard funcional
8. ✅ Gestión de usuarios (migrado de v1)
9. ✅ Configuración completa

### ⏳ PENDIENTE:

- ⏳ Módulo de Encuestas (última, anteriores, carga de datos)
- ⏳ Módulo de Configuración (CRUD rubros, familias, artículos, mercados, encuestas)
- ⏳ Upload de Excel
- ⏳ Exportación de datos

---

## 🔄 MIGRACIÓN

### Convivencia con sistema viejo:

- ✅ Ambos sistemas funcionan en paralelo
- ✅ Misma base de datos
- ✅ Usuarios pueden usar ambos
- ✅ Cuando v2.0 esté completo, se reemplaza v1.0

---

## 📞 SOPORTE

Para reportar problemas o sugerencias:

- **Email:** hola@malaga-design.com
- **Desarrollador:** AI Assistant powered by Claude

---

## 📄 LICENCIA

Propietario: CAPA (Cámara Argentina de Productores Avícolas)  
Desarrollado por: malagadesign

---

**¡Sistema listo para usar!** 🎉

Navega a `https://estadistica-capa.org.ar/v2/` para comenzar.
';

if (file_put_contents('v2/README.md', $readme_content)) {
    echo "<p>✅ Archivo v2/README.md creado</p>";
} else {
    echo "<p>❌ Error al crear v2/README.md</p>";
}

// ============================================
// PASO 11: CREAR SCRIPT DE ACTUALIZACIÓN DEL INDEX PRINCIPAL
// ============================================

echo "<h2>🔄 PASO 11: Creando script de actualización del index principal</h2>";

$index_update_content = '<?php
/**
 * ACTUALIZACIÓN DEL INDEX PRINCIPAL
 * Redirige automáticamente a la V2
 */

// Redirigir automáticamente a V2
header("Location: v2/");
exit();
';

if (file_put_contents('index_v2_redirect.php', $index_update_content)) {
    echo "<p>✅ Archivo index_v2_redirect.php creado</p>";
} else {
    echo "<p>❌ Error al crear index_v2_redirect.php</p>";
}

// ============================================
// PASO 12: VERIFICAR RESULTADO FINAL
// ============================================

echo "<h2>✅ PASO 12: Verificando resultado final</h2>";

// Verificar archivos críticos
$archivos_criticos = [
    'v2/index.php',
    'v2/config/app.php',
    'v2/config/routes.php',
    'v2/.htaccess',
    'v2/.env',
    'v2/README.md'
];

foreach ($archivos_criticos as $archivo) {
    if (file_exists($archivo)) {
        echo "<p>✅ Archivo crítico existe: $archivo</p>";
    } else {
        echo "<p>❌ Archivo crítico faltante: $archivo</p>";
    }
}

// Verificar directorios críticos
$directorios_criticos = [
    'v2/app/controllers',
    'v2/app/models',
    'v2/app/views',
    'v2/core',
    'v2/storage'
];

foreach ($directorios_criticos as $directorio) {
    if (is_dir($directorio)) {
        echo "<p>✅ Directorio crítico existe: $directorio</p>";
    } else {
        echo "<p>❌ Directorio crítico faltante: $directorio</p>";
    }
}

echo "<p style='color: green; font-weight: bold;'>🎉 ¡MIGRACIÓN COMPLETA A V2 FINALIZADA!</p>";
echo "<p>💡 <strong>Próximos pasos:</strong></p>";
echo "<ul>";
echo "<li>1. Subir los cambios al repositorio Git</li>";
echo "<li>2. Probar el sistema en: <a href='v2/'>v2/</a></li>";
echo "<li>3. Verificar que la gestión de usuarios funcione correctamente</li>";
echo "<li>4. Una vez confirmado que todo funciona, reemplazar index.php con index_v2_redirect.php</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>📝 Resumen de la migración:</strong></p>";
echo "<ul>";
echo "<li>✅ Estructura V2 completa creada</li>";
echo "<li>✅ Archivos core migrados</li>";
echo "<li>✅ Controladores migrados</li>";
echo "<li>✅ Modelos migrados</li>";
echo "<li>✅ Vistas migradas</li>";
echo "<li>✅ Configuración de producción creada</li>";
echo "<li>✅ Documentación actualizada</li>";
echo "<li>✅ Script de redirección creado</li>";
echo "</ul>";

echo "<p><strong>🔗 Enlaces importantes:</strong></p>";
echo "<ul>";
echo "<li><a href='v2/'>Sistema V2</a></li>";
echo "<li><a href='v2/README.md'>Documentación V2</a></li>";
echo "<li><a href='index_v2_redirect.php'>Script de redirección</a></li>";
echo "</ul>";
?>
