# INFORME DE SEGURIDAD - Sistema de Encuestas CAPA
**Fecha:** 8 de Octubre, 2025  
**Analista:** Auditoría de Seguridad

---

## RESUMEN EJECUTIVO

Se ha realizado un análisis exhaustivo del sistema de encuestas. **NO se ha detectado código malicioso**, pero se han identificado **múltiples vulnerabilidades de seguridad críticas** que deben ser corregidas de inmediato.

**Estado General:** ⚠️ **VULNERABLE** - Requiere acciones correctivas urgentes

---

## 1. CÓDIGO MALICIOSO

### ✅ RESULTADO: NO SE DETECTÓ CÓDIGO MALICIOSO

- No se encontraron funciones sospechosas como `eval()`, `base64_decode()` en contextos maliciosos
- Los usos de `exec()`, `popen()` están limitados a la librería PHPMailer (código legítimo)
- No hay backdoors o shells ocultas
- No hay archivos ofuscados o con código encriptado sospechoso
- No hay conexiones a dominios externos sospechosos

---

## 2. VULNERABILIDADES DE SEGURIDAD CRÍTICAS

### 🔴 CRÍTICO: Credenciales Expuestas en Código

**Archivos Afectados:**
- `conector.php` (líneas 39-42, 160-182)
- `conector_viejo.php` (líneas 39-42, 160-184)
- `index.php` (líneas 200-204)

**Credenciales Expuestas:**

```php
// Base de datos
$DB_USER = "mlgcapa_enc";
$DB_PASSWORD = "7CAmlgPA7";

// Email SMTP
$GlobalMailUser = 'estadisticas@capa.org.ar';
$GlobalMailPasw = 'Capa1932$';

// Comentario en index.php
//Pass: @Mercado2024
```

**Impacto:** Un atacante con acceso al código fuente puede:
- Acceder completamente a la base de datos
- Enviar correos desde la cuenta corporativa
- Robar o modificar información sensible

**Recomendación:**
- Mover todas las credenciales a un archivo `.env` fuera del directorio web
- Usar variables de entorno
- Cambiar INMEDIATAMENTE todas las contraseñas expuestas
- Revisar logs de acceso para detectar posibles compromisos

---

### 🔴 CRÍTICO: Contraseñas Almacenadas en Texto Plano

**Archivos Afectados:**
- `conector.php` (línea 92-108)
- `login-register.php` (líneas 1-11)
- `cuenta/ADM.php` (línea 94)
- `usuarios/ADM.php` (línea 196)

**Problema:**
Las contraseñas de usuarios se almacenan en texto plano en la base de datos y se comparan directamente sin hashing.

```php
// Comparación directa - INSEGURO
if ($indiceUsuario == "{$usuario}-{$psw}-{$tipo}-{$did}"){
    $Glogeado = true;
}
```

**Impacto:**
- Si la base de datos es comprometida, todas las contraseñas quedan expuestas
- Los administradores pueden ver las contraseñas de los usuarios
- No hay protección contra dumps de base de datos

**Recomendación:**
- Implementar `password_hash()` con algoritmo bcrypt o argon2
- Usar `password_verify()` para validación
- Forzar cambio de contraseña de todos los usuarios
- Implementar política de contraseñas fuertes

---

### 🔴 CRÍTICO: Inyección SQL

**Archivos Afectados:**
- `adm/ADM.php` (líneas 46, 79, 112, 138, 188)
- `cuenta/ADM.php` (línea 94)
- `usuarios/ADM.php` (línea 196)
- `ver/ultimoADMmontos.php` (línea 79)

**Problema:**
Aunque se usa la función `Flimpiar()` para sanitizar, las consultas SQL concatenan valores directamente sin usar prepared statements.

```php
// VULNERABLE a inyección SQL
$esteInsert = "INSERT INTO `rubros` (`did`, `nombre`, `habilitado`, `quien`) 
               VALUES ({$did}, '{$nombre}', {$habilitado}, {$quien})";
$mysqli->query($esteInsert);
```

**Impacto:**
- Un atacante puede ejecutar consultas SQL arbitrarias
- Posible extracción completa de la base de datos
- Modificación o eliminación de datos

**Recomendación:**
- Usar **prepared statements** con parámetros vinculados:
```php
$stmt = $mysqli->prepare("INSERT INTO rubros (did, nombre, habilitado, quien) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isii", $did, $nombre, $habilitado, $quien);
$stmt->execute();
```

---

### 🔴 CRÍTICO: Ausencia de Tokens CSRF

**Archivos Afectados:**
- Todos los formularios del sistema
- `login-register.php`
- `adm/admEncuestas.php`
- `usuarios/admUsuarios.php`

**Problema:**
No hay protección contra Cross-Site Request Forgery. Los formularios no tienen tokens de validación.

**Impacto:**
- Un atacante puede engañar a un usuario autenticado para ejecutar acciones no deseadas
- Creación de usuarios administrativos
- Modificación de datos
- Eliminación de registros

**Recomendación:**
- Implementar tokens CSRF en todos los formularios
- Validar tokens en el servidor antes de procesar

---

### 🟠 ALTO: Autenticación Débil

**Archivo:** `login-register.php` (líneas 3-11)

**Problemas:**
```php
if (isset($_POST['logueando'])){
    $_SESSION['ScapaUsuario'] = $_POST['Usuario'];
    $_SESSION['ScapaPsw'] = $_POST['contrasenia'];
    // No hay validación contra base de datos aquí
    sleep(2);
    header("Location: ../encuestas/");
}
```

1. Las credenciales se aceptan sin validación inmediata
2. No hay límite de intentos de login (fuerza bruta)
3. No hay registro de intentos fallidos
4. El `sleep(2)` no es suficiente protección contra fuerza bruta
5. No hay implementación de CAPTCHA

**Recomendación:**
- Validar credenciales antes de crear sesión
- Implementar límite de intentos (3-5 intentos, luego bloqueo temporal)
- Agregar CAPTCHA después de múltiples fallos
- Registrar todos los intentos de autenticación

---

### 🟠 ALTO: Sesiones Inseguras

**Archivo:** `conector.php` (línea 2)

**Problemas:**
```php
session_start();
```

No hay configuración de seguridad de sesiones:
- Sin cookies HttpOnly
- Sin cookies Secure
- Sin regeneración de ID de sesión
- Sin timeout de sesión
- Sin validación de IP o User-Agent

**Recomendación:**
```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_start();
session_regenerate_id(true);
```

---

### 🟠 ALTO: Hash Predictible para Autenticación

**Archivo:** `usuarios/ADM.php` (líneas 178-183)

**Problema:**
```php
$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$hash = '';
for ($i = 0; $i < 35; $i++) {
    $indice = rand(0, strlen($caracteres) - 1);
    $hash .= $caracteres[$indice];
}
```

Se usa `rand()` en lugar de `random_bytes()` para generar tokens de autenticación. `rand()` no es criptográficamente seguro.

**Recomendación:**
```php
$hash = bin2hex(random_bytes(35));
```

---

### 🟠 ALTO: Exposición de Información Sensible

**Archivos:**
- `adm/ADM.php` (línea 2)
- `cuenta/ADM.php` (línea 2)
- Múltiples archivos

**Problema:**
```php
ini_set("display_errors", 1);
error_reporting(E_ALL);
```

Los errores se muestran directamente al usuario, exponiendo:
- Rutas del servidor
- Estructura de la base de datos
- Detalles de implementación

**Recomendación:**
- Desactivar `display_errors` en producción
- Registrar errores en archivos de log
- Mostrar mensajes genéricos al usuario

---

### 🟡 MEDIO: Falta de Validación de Tipos de Archivo

**Archivo:** `index.php` y otros

**Problema:**
El sistema usa includes dinámicos basados en parámetros GET:

```php
if ($qm == 'ver'){
    $qhInc = "{$qh}{$_SESSION['ScapaUsuarioTipo']}";
} else {
    $qhInc = $qh;
}
include $qm.'/'.$qhInc.'.php';
```

Aunque hay validación con el array `$Gaccesos`, podría ser vulnerable a path traversal.

**Recomendación:**
- Usar whitelist estricta de archivos permitidos
- Validar que el archivo existe antes de incluir
- No permitir caracteres especiales en parámetros

---

### 🟡 MEDIO: Falta de HTTPS Enforcement

**Problema:**
No hay evidencia de redirección forzada a HTTPS en el código.

**Impacto:**
- Credenciales pueden transmitirse en texto plano
- Sesiones pueden ser interceptadas (session hijacking)
- Datos sensibles sin cifrado en tránsito

**Recomendación:**
- Forzar HTTPS en `.htaccess` o configuración del servidor
- Usar HSTS (HTTP Strict Transport Security)

---

### 🟡 MEDIO: Falta de Headers de Seguridad

**Recomendaciones:**
Agregar headers de seguridad en todas las páginas:

```php
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'");
```

---

### 🟡 MEDIO: Logs de Email con Credenciales

**Archivo:** `cuenta/ADM.php` y `usuarios/ADM.php`

**Problema:**
```php
$GlobalMailSMTPDebug = 3;
```

El nivel de debug 3 puede registrar credenciales SMTP en logs.

**Recomendación:**
- Usar nivel 0 o 1 en producción
- Nunca usar nivel 3 (muestra autenticación)

---

## 3. ARCHIVOS SOSPECHOSOS O INNECESARIOS

### ⚠️ Archivos que deberían eliminarse:

1. **`conector_viejo.php`** - Archivo obsoleto con credenciales
2. **`cuenta/z$ ADM.php`** - Backup innecesario
3. **`cuenta/z$ cambioPas.php`** - Backup innecesario
4. **`ver/z$ ultimoAdm.php`** - Backup innecesario
5. **`ver/z$ ultimoSocioAdmExcel.php 2024-01-30`** - Backup con fecha
6. **`ver/z$ ultimoSocioAdmExcel.php 2024-02-09`** - Backup con fecha
7. **Carpetas `/test/` y `/examples/` de PHPMailer** - No necesarias en producción

---

## 4. BUENAS PRÁCTICAS ENCONTRADAS

✅ **Aspectos Positivos:**

1. Uso de MySQLi en lugar de mysql_* deprecado
2. Intento de sanitización con función `Flimpiar()`
3. Sistema de permisos por tipo de usuario (`$Gaccesos`)
4. Validación de tipo de datos con multiplicación por 1 (`*1`)
5. Uso de UTF-8 en base de datos
6. Sistema de versionado con campo `superado` (soft deletes)

---

## 5. PLAN DE ACCIÓN PRIORITARIO

### Urgente (24-48 horas):

1. ✅ **Mover credenciales a archivo `.env`**
2. ✅ **Cambiar TODAS las contraseñas expuestas**
   - Base de datos
   - Email SMTP
   - Contraseñas de usuarios administrativos
3. ✅ **Revisar logs de acceso** para detectar compromisos
4. ✅ **Desactivar `display_errors` en producción**

### Corto Plazo (1-2 semanas):

5. ✅ **Implementar password hashing** (bcrypt/argon2)
6. ✅ **Migrar a prepared statements** en todas las consultas
7. ✅ **Implementar tokens CSRF**
8. ✅ **Agregar límite de intentos de login**
9. ✅ **Configurar sesiones seguras**
10. ✅ **Forzar HTTPS**

### Mediano Plazo (1 mes):

11. ✅ Implementar sistema de logs de auditoría
12. ✅ Agregar autenticación de dos factores (2FA)
13. ✅ Realizar pentesting profesional
14. ✅ Implementar WAF (Web Application Firewall)
15. ✅ Capacitación de seguridad al equipo

---

## 6. CÓDIGO DE EJEMPLO PARA CORRECCIONES

### Configuración Segura de Sesiones:

```php
// Al inicio de conector.php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Regenerar ID después de login
if ($login_exitoso) {
    session_regenerate_id(true);
}
```

### Hashing de Contraseñas:

```php
// Al crear/modificar contraseña
$psw_hash = password_hash($psw, PASSWORD_ARGON2ID);

// Al verificar
if (password_verify($psw_ingresado, $psw_hash_bd)) {
    // Login exitoso
}
```

### Prepared Statements:

```php
// Reemplazar todas las consultas concatenadas
$stmt = $mysqli->prepare("INSERT INTO usuarios (usuario, psw, mail, tipo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $usuario, $psw_hash, $mail, $tipo);
if ($stmt->execute()) {
    // Éxito
}
$stmt->close();
```

### Tokens CSRF:

```php
// Generar token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// En el formulario
echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';

// Validar
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('Token CSRF inválido');
}
```

---

## 7. CONCLUSIONES

El sistema **NO contiene código malicioso** pero presenta **vulnerabilidades críticas** que lo hacen susceptible a diversos ataques. Las principales preocupaciones son:

1. **Credenciales hardcodeadas** - Riesgo inmediato de compromiso
2. **Contraseñas en texto plano** - Violación de mejores prácticas de seguridad
3. **Inyección SQL** - Posible compromiso total del sistema
4. **Ausencia de CSRF** - Vulnerable a ataques de falsificación

**Es imperativo implementar las correcciones sugeridas antes de mantener el sistema en producción.**

---

## 8. RECURSOS ADICIONALES

- OWASP Top 10: https://owasp.org/www-project-top-ten/
- PHP Security Guide: https://www.php.net/manual/es/security.php
- Password Hashing PHP: https://www.php.net/manual/es/function.password-hash.php
- CSRF Prevention: https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html

---

**Fin del Informe**

*Este informe es confidencial y debe ser tratado con la máxima seguridad.*
