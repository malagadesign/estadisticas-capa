# 🏗️ ARQUITECTURA MODERNA - CAPA Encuestas v2.0

## 🎯 FILOSOFÍA DE DISEÑO

1. **Mobile-First** - Diseñar primero para mobile, luego desktop
2. **Progressive Enhancement** - Funcionalidad básica siempre, mejoras progresivas
3. **Separation of Concerns** - Lógica, presentación y datos separados
4. **DRY** - Don't Repeat Yourself
5. **KISS** - Keep It Simple, Stupid

---

## 📁 ESTRUCTURA DE CARPETAS (NUEVA)

```
/v2/  (nueva versión)
│
├── /public/                  # Acceso público
│   ├── index.php            # Entry point único
│   ├── /assets/             # Recursos estáticos
│   │   ├── /css/
│   │   │   ├── main.css     # Estilos principales
│   │   │   └── mobile.css   # Estilos mobile específicos
│   │   ├── /js/
│   │   │   ├── app.js       # JavaScript principal
│   │   │   └── modules/     # Módulos JS por funcionalidad
│   │   └── /img/
│   └── .htaccess            # Routing y seguridad
│
├── /app/                     # Lógica de aplicación
│   ├── /controllers/        # Controladores
│   │   ├── AuthController.php
│   │   ├── UsuariosController.php
│   │   ├── ConfigController.php
│   │   └── EncuestasController.php
│   │
│   ├── /models/             # Modelos (acceso a datos)
│   │   ├── Usuario.php
│   │   ├── Rubro.php
│   │   ├── Familia.php
│   │   ├── Articulo.php
│   │   ├── Mercado.php
│   │   ├── Encuesta.php
│   │   └── ArticuloMonto.php
│   │
│   ├── /views/              # Vistas (HTML + PHP)
│   │   ├── /layouts/
│   │   │   ├── base.php     # Layout principal
│   │   │   ├── auth.php     # Layout para login
│   │   │   └── /partials/
│   │   │       ├── header.php
│   │   │       ├── nav.php
│   │   │       ├── sidebar.php
│   │   │       └── footer.php
│   │   │
│   │   ├── /auth/
│   │   │   └── login.php
│   │   │
│   │   ├── /usuarios/
│   │   │   ├── index.php    # Lista
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   │
│   │   ├── /config/         # Configuración (rubros, familias, etc)
│   │   │   ├── rubros.php
│   │   │   ├── familias.php
│   │   │   ├── articulos.php
│   │   │   ├── mercados.php
│   │   │   └── encuestas.php
│   │   │
│   │   └── /encuestas/
│   │       ├── ultima.php   # Última encuesta
│   │       ├── anteriores.php
│   │       └── /components/
│   │           ├── tabla-precios.php
│   │           └── form-carga.php
│   │
│   ├── /middleware/         # Middlewares
│   │   ├── AuthMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   └── AdminMiddleware.php
│   │
│   └── /helpers/            # Funciones auxiliares
│       ├── functions.php
│       ├── validation.php
│       └── response.php
│
├── /core/                   # Core del framework ligero
│   ├── Router.php           # Sistema de routing
│   ├── Request.php          # Manejo de requests
│   ├── Response.php         # Manejo de responses
│   ├── View.php             # Render de vistas
│   ├── Database.php         # Conexión y queries
│   ├── Session.php          # Manejo de sesiones
│   └── Validator.php        # Validaciones
│
├── /config/                 # Configuración
│   ├── app.php             # Configuración general
│   ├── database.php        # Configuración BD
│   ├── routes.php          # Definición de rutas
│   └── middleware.php      # Configuración middlewares
│
├── /storage/                # Almacenamiento
│   ├── /logs/
│   ├── /uploads/           # Archivos Excel
│   └── /cache/
│
├── .env                     # Variables de entorno
├── .gitignore
└── composer.json            # Dependencias (opcional)
```

---

## 🗺️ SISTEMA DE ROUTING

### Archivo: `/config/routes.php`

```php
<?php
return [
    // Auth
    'GET /' => 'AuthController@showLogin',
    'POST /login' => 'AuthController@login',
    'GET /logout' => 'AuthController@logout',
    
    // Dashboard
    'GET /dashboard' => 'DashboardController@index',
    
    // Usuarios (solo admin)
    'GET /usuarios' => 'UsuariosController@index',
    'GET /usuarios/administrativos' => 'UsuariosController@administrativos',
    'GET /usuarios/socios' => 'UsuariosController@socios',
    'POST /usuarios/create' => 'UsuariosController@create',
    'POST /usuarios/update' => 'UsuariosController@update',
    'POST /usuarios/delete' => 'UsuariosController@delete',
    
    // Configuración (solo admin)
    'GET /config/rubros' => 'ConfigController@rubros',
    'GET /config/familias' => 'ConfigController@familias',
    'GET /config/articulos' => 'ConfigController@articulos',
    'GET /config/mercados' => 'ConfigController@mercados',
    'GET /config/encuestas' => 'ConfigController@encuestas',
    
    // Encuestas
    'GET /encuestas/ultima' => 'EncuestasController@ultima',
    'GET /encuestas/anteriores' => 'EncuestasController@anteriores',
    'POST /encuestas/guardar-precio' => 'EncuestasController@guardarPrecio',
    'POST /encuestas/upload-excel' => 'EncuestasController@uploadExcel',
    
    // API (AJAX)
    'POST /api/articulos/toggle' => 'ApiController@toggleArticulo',
    'GET /api/familias/:idRubro' => 'ApiController@familiasPorRubro',
    'GET /api/articulos/:idFamilia' => 'ApiController@articulosPorFamilia',
];
```

---

## 🔧 COMPONENTES CORE

### 1. Router (`/core/Router.php`)

```php
<?php
class Router {
    private $routes = [];
    private $params = [];
    
    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->formatPath($path),
            'handler' => $handler
        ];
    }
    
    public function dispatch($url, $method) {
        foreach ($this->routes as $route) {
            if ($this->match($route, $url, $method)) {
                return $this->execute($route);
            }
        }
        http_response_code(404);
        require __DIR__ . '/../app/views/errors/404.php';
    }
    
    // ... más métodos
}
```

### 2. Database (`/core/Database.php`)

```php
<?php
class Database {
    private static $instance = null;
    private $mysqli;
    
    private function __construct() {
        $this->mysqli = new mysqli(
            env('DB_HOST'),
            env('DB_USER'),
            env('DB_PASSWORD'),
            env('DB_NAME'),
            env('DB_PORT', 3306)
        );
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->mysqli->prepare($sql);
        if ($params) {
            $stmt->bind_param(...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // ... más métodos
}
```

### 3. View (`/core/View.php`)

```php
<?php
class View {
    public static function render($view, $data = [], $layout = 'base') {
        // Convertir array a variables
        extract($data);
        
        // Iniciar buffer
        ob_start();
        
        // Incluir vista
        require __DIR__ . "/../app/views/{$view}.php";
        
        // Capturar contenido
        $content = ob_get_clean();
        
        // Incluir layout
        require __DIR__ . "/../app/views/layouts/{$layout}.php";
    }
    
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
```

---

## 🎨 LAYOUT BASE (Bootstrap 5)

### `/app/views/layouts/base.php`

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= $title ?? 'CAPA Encuestas' ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="/assets/css/main.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/assets/css/mobile.css?v=<?= time() ?>">
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <main class="container-fluid py-4">
        <?= $content ?>
    </main>
    
    <?php include __DIR__ . '/../partials/footer.php'; ?>
    
    <!-- Bootstrap 5 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- App JS -->
    <script src="/assets/js/app.js"></script>
    
    <?php if (isset($js)): ?>
        <?php foreach ($js as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
```

---

## 🎨 PALETA DE COLORES CAPA

```css
:root {
    /* Colores principales */
    --capa-azul-oscuro: #001A4D;
    --capa-purpura: #9D4EDD;
    --capa-purpura-claro: #C084FC;
    --capa-purpura-oscuro: #6B21A8;
    
    /* Grises */
    --gris-claro: #F8F9FA;
    --gris-medio: #6C757D;
    --gris-oscuro: #343A40;
    
    /* Semánticos */
    --color-primario: var(--capa-azul-oscuro);
    --color-secundario: var(--capa-purpura);
    --color-acento: var(--capa-purpura-claro);
    
    /* Tipografía */
    --font-principal: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
```

---

## 📱 COMPONENTES MOBILE-FIRST

### Navegación Responsiva

```html
<nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--capa-azul-oscuro);">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard">
            <img src="/assets/img/logo-capa.png" alt="CAPA" height="40">
        </a>
        
        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menú -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Links dinámicos según rol -->
            </ul>
        </div>
    </div>
</nav>
```

### Tabla Responsiva (Card en Mobile)

```html
<div class="table-responsive d-none d-md-block">
    <!-- Tabla normal en desktop -->
</div>

<div class="d-md-none">
    <!-- Cards en mobile -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?= $item['nombre'] ?></h5>
            <p class="card-text">...</p>
        </div>
    </div>
</div>
```

---

## 🔐 SEGURIDAD

### Middlewares

1. **AuthMiddleware** - Verifica autenticación
2. **CsrfMiddleware** - Valida tokens CSRF en POST
3. **AdminMiddleware** - Verifica rol admin

### Prepared Statements (todos los queries)

```php
$db = Database::getInstance();
$result = $db->query(
    "SELECT * FROM usuarios WHERE usuario = ? AND habilitado = 1",
    ['s', $usuario]
);
```

---

## 📊 PERFORMANCE

### Lazy Loading
- Cargar JS solo cuando se necesita
- Usar Bootstrap 5 Tree Shaking

### Queries Optimizadas
- Selects con LIMIT
- Índices en BD
- Cache de queries frecuentes

### Assets
- Minificación CSS/JS
- Uso de CDN para Bootstrap

---

## 🚀 PLAN DE IMPLEMENTACIÓN

### Fase 1: Core (2 horas)
- ✅ Estructura de carpetas
- ✅ Router básico
- ✅ Database wrapper
- ✅ View system

### Fase 2: Autenticación (1 hora)
- ✅ Login/Logout
- ✅ Session management
- ✅ Middlewares

### Fase 3: Módulos (3 horas)
- ✅ Encuestas (última, anteriores)
- ✅ Configuración (CRUD)
- ✅ Usuarios (CRUD)

### Fase 4: UI/UX (1 hora)
- ✅ Diseño responsive
- ✅ Paleta CAPA
- ✅ Toasts y feedback

### Fase 5: Testing (30 min)
- ✅ Pruebas funcionales
- ✅ Ajustes finales

**Total estimado: 7.5 horas**

---

## ✅ VENTAJAS DE LA NUEVA ARQUITECTURA

1. **Mantenibilidad** - Código organizado y modular
2. **Escalabilidad** - Fácil agregar funcionalidades
3. **Performance** - Optimizado desde el inicio
4. **Seguridad** - Mejor control de acceso
5. **UX** - Mobile-first real
6. **Modernidad** - Bootstrap 5, PHP 8+

---

## 🔄 MIGRACIÓN DE DATOS

- ❌ **NO se modifica la base de datos actual**
- ✅ Misma estructura de tablas
- ✅ Mismos datos
- ✅ Solo cambia el código PHP/HTML/CSS

---

¿Listo para implementar? 🚀

