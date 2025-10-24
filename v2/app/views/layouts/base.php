<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta name="theme-color" content="#001A4D">
    <title><?= $title ?? 'CAPA Encuestas' ?></title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/mobile.css') ?>">
    
    <!-- CSS adicional (opcional) -->
    <?php if (isset($css) && is_array($css)): ?>
        <?php foreach ($css as $stylesheet): ?>
            <link rel="stylesheet" href="<?= asset($stylesheet) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <?php View::partial('header'); ?>
    
    <!-- Navegación -->
    <?php View::partial('nav'); ?>
    
    <!-- Contenido Principal -->
    <main class="container-capa py-4">
        <?php if (Session::has('flash_message')): ?>
            <div class="alert alert-<?= Session::get('flash_type', 'success') ?> alert-dismissible fade show fade-in" role="alert">
                <?= Session::flash('flash_message') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <?php View::partial('footer'); ?>
    
    <!-- Bootstrap 5 Bundle (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- CSRF Token para AJAX -->
    <script>
        // Configurar CSRF token para todas las peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Fetch wrapper con CSRF
        window.fetchWithCsrf = function(url, options = {}) {
            options.headers = options.headers || {};
            options.headers['X-CSRF-Token'] = csrfToken;
            return fetch(url, options);
        };
    </script>
    
    <!-- App JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
    
    <!-- JS adicional (opcional) -->
    <?php if (isset($js) && is_array($js)): ?>
        <?php foreach ($js as $script): ?>
            <script src="<?= asset($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

