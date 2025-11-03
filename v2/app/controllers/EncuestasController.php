<?php
/**
 * EncuestasController - Módulo de Encuestas
 */

// Cargar modelos
require_once __DIR__ . '/../models/Encuesta.php';
require_once __DIR__ . '/../models/Rubro.php';
require_once __DIR__ . '/../models/Familia.php';
require_once __DIR__ . '/../models/Articulo.php';
require_once __DIR__ . '/../models/Mercado.php';

class EncuestasController {
    
    /**
     * Ver última encuesta
     */
    public function ultima() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::redirect('/', 'Debe iniciar sesión', 'warning');
        }
        
        $userId = Session::userId();
        $userType = Session::userType();
        $isAdmin = Session::isAdmin();
        
        // Obtener última encuesta
        $encuestaModel = new Encuesta();
        $encuesta = $encuestaModel->getUltima();
        
        if (!$encuesta) {
            View::render('encuestas/no-activa', [
                'title' => 'Última Encuesta - CAPA',
                'message' => 'No hay encuestas activas en este momento'
            ]);
            return;
        }
        
        // Verificar si es editable
        $esEditable = $encuestaModel->isEditable($encuesta['did']);
        
        // Cargar rubros, familias, mercados
        $rubroModel = new Rubro();
        $familiaModel = new Familia();
        $mercadoModel = new Mercado();
        
        $rubros = $rubroModel->getAll();
        $familias = $familiaModel->getAll();
        $mercados = $mercadoModel->getAll();
        
        // Si es admin, cargar TODOS los artículos para el consolidado
        $articuloModel = null;
        if (Session::isAdmin()) {
            $articuloModel = new Articulo();
            // Precargar artículos solo para admin
        }
        // Para socios se cargarán por demanda via AJAX
        
        // Debug: Log de carga
        error_log("DEBUG carga inicial - Rubros: " . count($rubros) . ", Familias: " . count($familias) . ", Mercados: " . count($mercados));
        
        // Organizar datos por jerarquía
        $rubrosArray = [];
        foreach ($rubros as $rubro) {
            $rubrosArray[$rubro['did']] = $rubro['nombre'];
        }
        
        $familiasArray = [];
        $familiasPorRubro = [];
        foreach ($familias as $familia) {
            $familiasArray[$familia['did']] = [
                'nombre' => $familia['nombre'],
                'rubroDid' => $familia['didRubro']
            ];
            $familiasPorRubro[$familia['didRubro']][] = $familia;
        }
        
        // Precargar artículos solo para admin (para consolidado)
        $articulosArray = [];
        $articulosPorFamilia = [];
        if ($articuloModel) {
            $startTime = microtime(true);
            $articulos = $articuloModel->getAll();
            foreach ($articulos as $articulo) {
                $articulosArray[$articulo['did']] = [
                    'nombre' => $articulo['nombre'],
                    'familiaDid' => $articulo['didFamilia']
                ];
                $articulosPorFamilia[$articulo['didFamilia']][] = $articulo;
            }
            $elapsedTime = round((microtime(true) - $startTime) * 1000, 2);
            error_log("DEBUG admin - getAll() articulos: Tardó {$elapsedTime}ms, cantidad: " . count($articulos));
        }
        
        $mercadosArray = [];
        foreach ($mercados as $mercado) {
            $mercadosArray[$mercado['did']] = $mercado['nombre'];
        }
        
        // Datos específicos según rol
        $articulosDeshabilitados = [];
        $montosYaCargados = [];
        $articulosNoIncluidos = [];
        
        if ($isAdmin) {
            // Admin: cargar consolidado con count de socios
            $consolidado = $encuestaModel->getConsolidadoAdmin($encuesta['did']);
            
            // Admin: contar socios que cargaron datos
            $db = Database::getInstance();
            $sociosCargaron = $db->fetchOne(
                "SELECT COUNT(DISTINCT didUsuario) as total FROM articulosMontos 
                 WHERE didEncuesta = ? AND superado = 0 AND elim = 0 AND monto > 0",
                ['i', $encuesta['did']]
            )['total'];
            
            $totalSocios = $db->fetchOne(
                "SELECT COUNT(*) as total FROM usuarios 
                 WHERE TRIM(tipo) = 'socio' AND superado = 0 AND elim = 0 AND habilitado = 1"
            )['total'];
            
            $sociosFaltan = $totalSocios - $sociosCargaron;
        } else {
            // Socio: cargar sus datos
            $articulosDeshabilitados = $encuestaModel->getArticulosDeshabilitadosPorSocio($userId);
            $montosYaCargados = $encuestaModel->getMontosYaCargados($encuesta['did'], $userId);
            $consolidado = [];
            $sociosCargaron = 0;
            $sociosFaltan = 0;
        }
        
        View::render('encuestas/ultima', [
            'title' => 'Última Encuesta - CAPA',
            'encuesta' => $encuesta,
            'esEditable' => $esEditable,
            'rubros' => $rubrosArray,
            'familias' => $familiasArray,
            'familiasPorRubro' => $familiasPorRubro,
            'articulos' => $articulosArray,
            'articulosPorFamilia' => $articulosPorFamilia,
            'mercados' => $isAdmin ? $mercados : $mercadosArray, // Admin: array completo, Socio: solo nombres
            'articulosDeshabilitados' => $articulosDeshabilitados,
            'montosYaCargados' => $montosYaCargados,
            'articulosNoIncluidos' => $articulosNoIncluidos,
            'consolidado' => $consolidado,
            'sociosCargaron' => $sociosCargaron ?? 0,
            'sociosFaltan' => $sociosFaltan ?? 0,
            'isAdmin' => $isAdmin
        ]);
    }
    
    /**
     * Guardar precio (AJAX)
     */
    public function guardarPrecio() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        // Solo socios pueden cargar precios
        if (Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'Los administradores no pueden cargar precios'], 403);
        }
        
        // Verificar CSRF
        $csrfToken = Request::post('csrf_token');
        if (!csrf_verify($csrfToken)) {
            View::json(['success' => false, 'message' => 'Token inválido'], 403);
        }
        
        // Obtener datos
        $encuestaDid = Request::post('encuestaDid');
        $articuloDid = Request::post('articuloDid');
        $mercadoDid = Request::post('mercadoDid');
        $tipo = Request::post('tipo', 'venta');
        $monto = Request::post('monto', 0);
        
        // Validar
        if (!$encuestaDid || !$articuloDid || !$mercadoDid) {
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        // Guardar
        try {
            $encuestaModel = new Encuesta();
            $encuestaModel->saveMonto(
                $encuestaDid,
                Session::userId(),
                $articuloDid,
                $mercadoDid,
                $tipo,
                $monto
            );
            
            View::json(['success' => true, 'message' => 'Precio guardado correctamente']);
        } catch (Exception $e) {
            error_log("Error guardando precio: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al guardar'], 500);
        }
    }
    
    /**
     * Guardar dato (cantidad o valor) - Tab 2 grilla (AJAX)
     */
    public function guardarDato() {
        error_log("DEBUG guardarDato - Inicio");
        
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            error_log("DEBUG guardarDato - No autenticado");
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        // Solo socios pueden cargar datos
        if (Session::isAdmin()) {
            error_log("DEBUG guardarDato - Admin no puede cargar");
            View::json(['success' => false, 'message' => 'Los administradores no pueden cargar datos'], 403);
        }
        
        // Verificar CSRF
        $csrfToken = Request::post('csrf_token');
        if (!csrf_verify($csrfToken)) {
            error_log("DEBUG guardarDato - Token inválido");
            View::json(['success' => false, 'message' => 'Token inválido'], 403);
        }
        
        // Obtener datos
        $encuestaDid = Request::post('encuestaDid');
        $articuloDid = Request::post('articuloDid');
        $canalDid = Request::post('canalDid'); // Mercado/Canal
        $tipoTexto = Request::post('tipo'); // 'cantidad' o 'valor'
        $monto = Request::post('monto', 0);
        
        error_log("DEBUG guardarDato - encuestaDid=$encuestaDid, articuloDid=$articuloDid, canalDid=$canalDid, tipoTexto=$tipoTexto, monto=$monto");
        
        // Validar
        if (!$encuestaDid || !$articuloDid || !$canalDid || !$tipoTexto) {
            error_log("DEBUG guardarDato - Datos incompletos");
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        // Convertir tipo texto a número: cantidad=1, valor=2
        $tipoNum = ($tipoTexto === 'cantidad' || $tipoTexto === '1') ? 1 : 2;
        error_log("DEBUG guardarDato - tipo convertido: $tipoNum");
        
        // Guardar
        try {
            $encuestaModel = new Encuesta();
            $encuestaModel->saveMonto(
                $encuestaDid,
                Session::userId(),
                $articuloDid,
                $canalDid,
                $tipoNum,
                $monto
            );
            
            error_log("DEBUG guardarDato - OK");
            View::json(['success' => true, 'message' => 'Dato guardado correctamente']);
        } catch (Exception $e) {
            error_log("Error guardando dato: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Toggle artículo (AJAX)
     */
    public function toggleArticulo() {
        error_log("DEBUG toggleArticulo - Inicio");
        
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            error_log("DEBUG toggleArticulo - No autenticado");
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        // Solo socios pueden configurar artículos
        if (Session::isAdmin()) {
            error_log("DEBUG toggleArticulo - Admin no puede configurar");
            View::json(['success' => false, 'message' => 'Los administradores no pueden configurar artículos'], 403);
        }
        
        // Verificar CSRF
        $csrfToken = Request::post('csrf_token');
        error_log("DEBUG toggleArticulo - CSRF token recibido: " . substr($csrfToken, 0, 20) . "...");
        if (!csrf_verify($csrfToken)) {
            error_log("DEBUG toggleArticulo - Token inválido");
            View::json(['success' => false, 'message' => 'Token inválido'], 403);
        }
        
        // Obtener datos
        $articuloDid = Request::post('articuloDid');
        $userId = Session::userId();
        error_log("DEBUG toggleArticulo - ArticuloDid: $articuloDid, UserId: $userId");
        
        if (!$articuloDid) {
            error_log("DEBUG toggleArticulo - Artículo no especificado");
            View::json(['success' => false, 'message' => 'Artículo no especificado'], 400);
        }
        
        // Toggle
        try {
            $encuestaModel = new Encuesta();
            error_log("DEBUG toggleArticulo - Llamando a toggleArticuloSocio");
            $nuevoEstado = $encuestaModel->toggleArticuloSocio($userId, $articuloDid);
            error_log("DEBUG toggleArticulo - Nuevo estado: $nuevoEstado");
            
            View::json([
                'success' => true,
                'habilitado' => $nuevoEstado,
                'message' => $nuevoEstado ? 'Artículo habilitado' : 'Artículo deshabilitado'
            ]);
        } catch (Exception $e) {
            error_log("ERROR toggle artículo: " . $e->getMessage());
            error_log("ERROR stack trace: " . $e->getTraceAsString());
            View::json(['success' => false, 'message' => 'Error al actualizar'], 500);
        }
    }
    
    /**
     * Encuestas anteriores
     */
    public function anteriores() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::redirect('/', 'Debe iniciar sesión', 'warning');
        }
        
        $encuestaModel = new Encuesta();
        $encuestas = $encuestaModel->getAll();
        
        View::render('encuestas/anteriores', [
            'title' => 'Encuestas Anteriores - CAPA',
            'encuestas' => $encuestas
        ]);
    }
    
    /**
     * Upload Excel (TODO: implementar)
     */
    public function uploadExcel() {
        View::json(['success' => false, 'message' => 'Funcionalidad en desarrollo'], 501);
    }
    
    /**
     * Cargar artículos por familia (Carga diferida - AJAX)
     */
    public function getArticulosPorFamilia() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        $familiaDid = Request::get('familiaDid');
        
        if (!$familiaDid) {
            View::json(['success' => false, 'message' => 'Familia no especificada'], 400);
        }
        
        try {
            $articuloModel = new Articulo();
            $articulos = $articuloModel->getByFamilia($familiaDid);
            
            // Si es socio, obtener también los artículos deshabilitados
            $articulosDeshabilitados = [];
            if (!Session::isAdmin()) {
                $encuestaModel = new Encuesta();
                $articulosDeshabilitados = $encuestaModel->getArticulosDeshabilitadosPorSocio(Session::userId());
            }
            
            // Formatear respuesta
            $response = [];
            foreach ($articulos as $articulo) {
                $deshabilitado = isset($articulosDeshabilitados[$articulo['did']]);
                $response[] = [
                    'did' => $articulo['did'],
                    'nombre' => $articulo['nombre'],
                    'didFamilia' => $articulo['didFamilia'],
                    'deshabilitado' => $deshabilitado
                ];
            }
            
            View::json(['success' => true, 'articulos' => $response]);
        } catch (Exception $e) {
            error_log("Error cargando artículos por familia: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al cargar artículos'], 500);
        }
    }
    
    /**
     * Obtener seguimiento de socios (AJAX)
     */
    public function seguimiento() {
        // Verificar autenticación
        if (!Session::isLoggedIn() || !Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        // Obtener última encuesta
        $encuestaModel = new Encuesta();
        $encuesta = $encuestaModel->getUltima();
        
        if (!$encuesta) {
            View::json(['success' => false, 'message' => 'No hay encuestas activas'], 404);
        }
        
        $db = Database::getInstance();
        
        // Obtener socios que cargaron datos
        $completaron = $db->fetchAll(
            "SELECT DISTINCT u.did, u.usuario 
             FROM usuarios u
             INNER JOIN articulosMontos am ON u.did = am.didUsuario
             WHERE am.didEncuesta = ? 
             AND am.superado = 0 
             AND am.elim = 0 
             AND am.monto > 0
             AND TRIM(u.tipo) = 'socio' 
             AND u.superado = 0 
             AND u.elim = 0 
             AND u.habilitado = 1
             ORDER BY u.usuario ASC",
            ['i', $encuesta['did']]
        );
        
        // Obtener todos los socios
        $todosLosSocios = $db->fetchAll(
            "SELECT did, usuario 
             FROM usuarios 
             WHERE TRIM(tipo) = 'socio' 
             AND superado = 0 
             AND elim = 0 
             AND habilitado = 1
             ORDER BY usuario ASC"
        );
        
        // Obtener IDs de los que completaron
        $completaronIds = array_column($completaron, 'did');
        
        // Los que faltan son los que no están en completaron
        $faltan = array_filter($todosLosSocios, function($socio) use ($completaronIds) {
            return !in_array($socio['did'], $completaronIds);
        });
        
        View::json([
            'success' => true,
            'completaron' => array_values($completaron),
            'faltan' => array_values($faltan)
        ]);
    }
    
    /**
     * Enviar recordatorios a socios que no completaron la encuesta
     */
    public function enviarRecordatorios() {
        // Verificar autenticación
        if (!Session::isLoggedIn() || !Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        
        if (empty($did)) {
            View::json(['success' => false, 'message' => 'ID de encuesta requerido'], 400);
        }
        
        require_once __DIR__ . '/../../core/MailHelper.php';
        
        try {
            $db = Database::getInstance();
            
            // Obtener datos de la encuesta
            $encuesta = $db->fetchOne(
                "SELECT * FROM encuestas WHERE did = ? AND elim = 0",
                ['i', $did]
            );
            
            if (!$encuesta) {
                View::json(['success' => false, 'message' => 'Encuesta no encontrada'], 404);
            }
            
            // Obtener socios que cargaron datos
            $completaron = $db->fetchAll(
                "SELECT DISTINCT u.did 
                 FROM usuarios u
                 INNER JOIN articulosMontos am ON u.did = am.didUsuario
                 WHERE am.didEncuesta = ? 
                 AND am.superado = 0 
                 AND am.elim = 0 
                 AND am.monto > 0
                 AND TRIM(u.tipo) = 'socio' 
                 AND u.superado = 0 
                 AND u.elim = 0 
                 AND u.habilitado = 1",
                ['i', $did]
            );
            
            // Obtener todos los socios activos con su hash
            $todosLosSocios = $db->fetchAll(
                "SELECT did, usuario, mail, hash 
                 FROM usuarios 
                 WHERE TRIM(tipo) = 'socio' 
                 AND superado = 0 
                 AND elim = 0 
                 AND habilitado = 1"
            );
            
            // Obtener IDs de los que completaron
            $completaronIds = array_column($completaron, 'did');
            
            // Los que faltan son los que no están en completaron
            $sociosFaltan = array_filter($todosLosSocios, function($socio) use ($completaronIds) {
                return !in_array($socio['did'], $completaronIds);
            });
            
            if (empty($sociosFaltan)) {
                View::json(['success' => false, 'message' => 'Todos los socios han completado la encuesta'], 400);
            }
            
            // Obtener plantilla de email
            $plantilla = $db->fetchOne(
                "SELECT * FROM emailsPlantillas WHERE tipo = 'recordatorio' AND habilitado = 1 AND elim = 0"
            );
            
            if (!$plantilla) {
                View::json(['success' => false, 'message' => 'Plantilla de email no encontrada'], 404);
            }
            
            // Enviar email a cada socio que falta
            $enviados = 0;
            $errores = 0;
            $emailPrueba = 'micaela@malaga-design.com.ar';
            $appUrl = env('APP_URL', 'https://estadistica-capa.org.ar');
            
            foreach ($sociosFaltan as $socio) {
                // MODO PRUEBA: Solo enviar a email de prueba
                // TODO: Quitar este filtro cuando se confirme que funciona correctamente
                if ($socio['mail'] !== $emailPrueba) {
                    continue;
                }
                
                // Verificar que el socio tenga hash
                if (empty($socio['hash'])) {
                    error_log("Socio {$socio['usuario']} no tiene hash, saltando");
                    continue;
                }
                
                // Generar link de acceso personalizado
                $linkAcceso = $appUrl . "/v2/log?h=" . $socio['hash'];
                
                // Procesar variables dinámicas para este socio
                $asunto = MailHelper::procesarPlantillaPublic($plantilla['asunto'], [
                    'nombre_encuesta' => $encuesta['nombre'],
                    'fecha_vencimiento' => $encuesta['hastaText']
                ]);
                
                $cuerpoHtml = MailHelper::procesarPlantillaPublic($plantilla['cuerpo_html'], [
                    'nombre_encuesta' => $encuesta['nombre'],
                    'fecha_vencimiento' => $encuesta['hastaText'],
                    'link_sistema' => $appUrl,
                    'link_acceso' => $linkAcceso
                ]);
                
                try {
                    MailHelper::enviarEmail($socio['mail'], $asunto, $cuerpoHtml);
                    $enviados++;
                    error_log("Recordatorio enviado a: {$socio['mail']}");
                } catch (Exception $e) {
                    $errores++;
                    error_log("Error enviando recordatorio a {$socio['mail']}: " . $e->getMessage());
                }
            }
            
            View::json([
                'success' => true, 
                'message' => "Recordatorios enviados: {$enviados} exitosos" . ($errores > 0 ? ", {$errores} errores" : "")
            ]);
        } catch (Exception $e) {
            error_log("Error enviando recordatorios: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al enviar recordatorios'], 500);
        }
    }
}

