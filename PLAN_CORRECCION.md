# PLAN DE CORRECCIÓN - Sistema de Encuestas CAPA

## ACCIONES INMEDIATAS (Hoy)

### 1. Crear archivo .gitignore
Si no existe, crear `.gitignore` en la raíz del proyecto:

```
.env
.env.local
*.log
conector_viejo.php
z$*.php
```

### 2. Crear archivo de configuración seguro

Crear archivo `.env` (fuera del directorio web si es posible):

```env
# Base de datos
DB_HOST=localhost
DB_USER=mlgcapa_enc
DB_PASSWORD=NUEVA_CONTRASEÑA_SEGURA_AQUI
DB_NAME=mlgcapa_enc

# Email
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=estadisticas@capa.org.ar
MAIL_PASSWORD=NUEVA_CONTRASEÑA_EMAIL_AQUI
MAIL_FROM_NAME=CAPA
MAIL_REPLY_TO=capa@capa.org.ar
```

### 3. Cambiar todas las contraseñas

**Prioridad CRÍTICA:**

1. ✅ Cambiar contraseña de base de datos `mlgcapa_enc`
2. ✅ Cambiar contraseña del email `estadisticas@capa.org.ar`
3. ✅ Cambiar contraseñas de todos los usuarios administrativos
4. ✅ Cambiar contraseñas de usuarios tipo "socio"

**Comando para generar contraseñas seguras:**
```bash
# En terminal (Linux/Mac)
openssl rand -base64 32

# O usar generador online confiable
```

### 4. Revisar logs de acceso

```bash
# Buscar accesos sospechosos
grep -i "conector.php" /var/log/apache2/access.log
grep -i ".env" /var/log/apache2/access.log
grep -i "ADM.php" /var/log/apache2/access.log

# Buscar intentos de SQL injection
grep -i "union" /var/log/apache2/access.log
grep -i "select.*from" /var/log/apache2/access.log
```

---

## CORRECCIONES DE CÓDIGO (Esta semana)

### 1. Modificar conector.php

**Crear nuevo archivo:** `config.php`

```php
<?php
// Cargar variables de entorno
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? '');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');

define('MAIL_HOST', $_ENV['MAIL_HOST'] ?? '');
define('MAIL_PORT', $_ENV['MAIL_PORT'] ?? '587');
define('MAIL_USER', $_ENV['MAIL_USER'] ?? '');
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD'] ?? '');
```

**Modificar conector.php:**

Reemplazar líneas 39-42:
```php
// ANTES (INSEGURO):
$DB_HOST = "localhost";
$DB_USER = "mlgcapa_enc";
$DB_PASSWORD = "7CAmlgPA7";
$DB_NAME = "mlgcapa_enc";

// DESPUÉS (SEGURO):
require_once __DIR__ . '/config.php';
$DB_HOST = DB_HOST;
$DB_USER = DB_USER;
$DB_PASSWORD = DB_PASSWORD;
$DB_NAME = DB_NAME;
```

Reemplazar líneas 160-166:
```php
// ANTES (INSEGURO):
$GlobalMailHost = 'smtp.office365.com';
$GlobalMailUser = 'estadisticas@capa.org.ar';
$GlobalMailPasw = 'Capa1932$';

// DESPUÉS (SEGURO):
$GlobalMailHost = MAIL_HOST;
$GlobalMailUser = MAIL_USER;
$GlobalMailPasw = MAIL_PASSWORD;
```

### 2. Configurar sesiones seguras

En `conector.php` línea 2, reemplazar:

```php
// ANTES:
session_start();

// DESPUÉS:
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Solo si usas HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Regenerar ID en login exitoso
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    session_regenerate_id(true);
    unset($_SESSION['login_success']);
}
```

### 3. Desactivar errores en producción

Al inicio de todos los archivos PHP principales, cambiar:

```php
// ANTES:
ini_set("display_errors", 1);
error_reporting(E_ALL);

// DESPUÉS:
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/logs/php-errors.log");
error_reporting(E_ALL);
```

Crear directorio de logs:
```bash
mkdir -p logs
chmod 755 logs
echo "deny from all" > logs/.htaccess
```

### 4. Migrar contraseñas a hash

**PASO 1:** Agregar nueva columna en la base de datos:

```sql
ALTER TABLE `usuarios` ADD COLUMN `psw_hash` VARCHAR(255) NULL AFTER `psw`;
```

**PASO 2:** Script de migración `migrar_passwords.php`:

```php
<?php
require_once 'conector.php';

// Obtener todos los usuarios
$stmt = $mysqli->query("SELECT `id`, `did`, `psw` FROM `usuarios` WHERE `psw_hash` IS NULL");

while ($row = $stmt->fetch_assoc()) {
    $id = $row['id'];
    $psw_plain = $row['psw'];
    
    // Generar hash
    $psw_hash = password_hash($psw_plain, PASSWORD_ARGON2ID);
    
    // Actualizar
    $update = $mysqli->prepare("UPDATE `usuarios` SET `psw_hash` = ? WHERE `id` = ?");
    $update->bind_param("si", $psw_hash, $id);
    $update->execute();
    
    echo "Usuario ID {$id} migrado\n";
}

echo "Migración completada. IMPORTANTE: Ahora actualizar el código de login.\n";
```

**PASO 3:** Modificar `login-register.php`:

```php
<?php
if (isset($_POST['logueando'])){
    $usuario = $_POST['Usuario'];
    $contrasenia = $_POST['contrasenia'];
    
    // Buscar usuario en BD
    $stmt = $mysqli->prepare("SELECT `did`, `usuario`, `psw_hash`, `tipo`, `mail`, `habilitado` 
                              FROM `usuarios` 
                              WHERE `usuario` = ? AND `habilitado` = 1 AND `elim` = 0 AND `superado` = 0 
                              LIMIT 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Verificar contraseña
        if (password_verify($contrasenia, $row['psw_hash'])) {
            // Login exitoso
            $_SESSION['ScapaUsuario'] = $row['usuario'];
            $_SESSION['ScapaPsw'] = $row['psw_hash']; // Ahora es hash
            $_SESSION['ScapaUsuarioTipo'] = $row['tipo'];
            $_SESSION['ScapaUsuarioDid'] = $row['did'];
            $_SESSION['ScapaUsuarioMail'] = $row['mail'];
            $_SESSION['ScapaUsuarioLog'] = false;
            $_SESSION['login_success'] = true;
            
            header("Location: ../encuestas/");
            exit();
        } else {
            // Contraseña incorrecta
            $error = "Credenciales incorrectas";
        }
    } else {
        // Usuario no encontrado
        $error = "Credenciales incorrectas";
    }
    
    // Esperar para prevenir fuerza bruta
    sleep(2);
}
```

### 5. Implementar Prepared Statements

**Ejemplo en `adm/ADM.php` línea 46:**

```php
// ANTES (INSEGURO):
$esteInsert = "INSERT INTO `rubros` (`did`, `nombre`, `habilitado`, `quien`) 
               VALUES ({$did}, '{$nombre}', {$habilitado}, {$quien})";
$mysqli->query($esteInsert);

// DESPUÉS (SEGURO):
$stmt = $mysqli->prepare("INSERT INTO `rubros` (`did`, `nombre`, `habilitado`, `quien`) 
                          VALUES (?, ?, ?, ?)");
$stmt->bind_param("isii", $did, $nombre, $habilitado, $quien);
if ($stmt->execute()) {
    $idIsertado = $stmt->insert_id;
    // ... resto del código
}
$stmt->close();
```

**Aplicar este patrón en:**
- `adm/ADM.php` (todas las inserciones)
- `cuenta/ADM.php` (línea 94)
- `usuarios/ADM.php` (línea 196)
- `ver/ultimoADMmontos.php` (línea 79)
- `ver/ultimoADMarticulos.php` (línea 34)

### 6. Implementar protección CSRF

**Crear archivo:** `csrf.php`

```php
<?php
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
```

**En `conector.php`:**
```php
require_once __DIR__ . '/csrf.php';
```

**En todos los formularios, agregar:**
```php
<?php echo csrf_field(); ?>
```

**En todos los procesadores de formularios:**
```php
if (!csrf_verify()) {
    die('Token CSRF inválido');
}
```

### 7. Limitar intentos de login

**Crear tabla:**
```sql
CREATE TABLE `login_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ip` VARCHAR(45) NOT NULL,
    `username` VARCHAR(100),
    `attempts` INT DEFAULT 1,
    `last_attempt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ip` (`ip`)
) ENGINE=InnoDB;
```

**Modificar `login-register.php`:**

```php
function check_login_attempts($ip, $mysqli) {
    $stmt = $mysqli->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $attempts = $row['attempts'];
        $last = strtotime($row['last_attempt']);
        $now = time();
        
        // Resetear después de 15 minutos
        if ($now - $last > 900) {
            $mysqli->query("DELETE FROM login_attempts WHERE ip = '$ip'");
            return true;
        }
        
        // Bloquear después de 5 intentos
        if ($attempts >= 5) {
            return false;
        }
    }
    
    return true;
}

function register_failed_attempt($ip, $username, $mysqli) {
    $stmt = $mysqli->prepare("INSERT INTO login_attempts (ip, username, attempts) 
                              VALUES (?, ?, 1) 
                              ON DUPLICATE KEY UPDATE attempts = attempts + 1, 
                              last_attempt = CURRENT_TIMESTAMP");
    $stmt->bind_param("ss", $ip, $username);
    $stmt->execute();
}

// Al inicio del login:
$ip = $_SERVER['REMOTE_ADDR'];
if (!check_login_attempts($ip, $mysqli)) {
    die("Demasiados intentos fallidos. Intente nuevamente en 15 minutos.");
}

// Si el login falla:
register_failed_attempt($ip, $usuario, $mysqli);
```

---

## CONFIGURACIÓN DE SERVIDOR

### 1. Forzar HTTPS

**En `.htaccess`:**
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Headers de seguridad
Header always set X-Frame-Options "DENY"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';"

# Ocultar información del servidor
ServerSignature Off
```

### 2. Proteger archivos sensibles

**Agregar a `.htaccess`:**
```apache
# Denegar acceso a archivos sensibles
<FilesMatch "^(\.env|config\.php|conector\.php|conector_viejo\.php)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Denegar acceso a backups
<FilesMatch "^z\$.*\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Proteger directorio de logs
<DirectoryMatch "logs">
    Order allow,deny
    Deny from all
</DirectoryMatch>
```

---

## LIMPIEZA DE ARCHIVOS

### Eliminar archivos innecesarios:

```bash
# Backups y archivos viejos
rm conector_viejo.php
rm cuenta/z$*.php
rm ver/z$*.php

# Ejemplos de PHPMailer (no necesarios en producción)
rm -rf PHPMailer/examples/
rm -rf PHPMailer/test/
rm -rf cuenta/PHPMailer/examples/
rm -rf cuenta/PHPMailer/test/
rm -rf usuarios/PHPMailer/examples/
rm -rf usuarios/PHPMailer/test/
```

---

## TESTING

### 1. Probar después de cada cambio:

```bash
# Test de login
curl -X POST https://capa.org.ar/encuestas/login-register.php \
  -d "Usuario=test&contrasenia=test&logueando=si"

# Test de SQL injection (debería fallar)
curl -X POST https://capa.org.ar/encuestas/adm/ADM.php \
  -d "nombre=test' OR '1'='1"
```

### 2. Verificar logs:

```bash
tail -f logs/php-errors.log
```

---

## CHECKLIST DE VERIFICACIÓN

- [ ] Credenciales movidas a `.env`
- [ ] Todas las contraseñas cambiadas
- [ ] `.gitignore` actualizado
- [ ] Sesiones configuradas de forma segura
- [ ] `display_errors` desactivado
- [ ] Passwords migradas a hash
- [ ] Prepared statements implementados
- [ ] Tokens CSRF en todos los formularios
- [ ] Límite de intentos de login implementado
- [ ] HTTPS forzado
- [ ] Headers de seguridad configurados
- [ ] Archivos innecesarios eliminados
- [ ] Logs revisados
- [ ] Testing realizado

---

## MONITOREO POST-IMPLEMENTACIÓN

### Configurar alertas:

1. **Intentos de login fallidos:** > 10 en 1 hora
2. **Errores SQL:** Cualquier error de sintaxis SQL
3. **Errores 500:** Más de 5 en 1 hora
4. **Accesos a archivos protegidos:** `.env`, `config.php`

### Herramientas recomendadas:

- **Fail2ban:** Bloquear IPs con intentos sospechosos
- **ModSecurity:** WAF para Apache
- **OSSEC:** Sistema de detección de intrusos

---

## CONTACTOS DE EMERGENCIA

En caso de detectar una brecha de seguridad:

1. Desactivar el sitio inmediatamente
2. Revisar logs de acceso
3. Contactar al equipo de desarrollo
4. Notificar a los usuarios si hay compromiso de datos
5. Cambiar todas las credenciales nuevamente

---

**IMPORTANTE:** Realizar backup completo antes de aplicar cualquier cambio.

```bash
# Backup de archivos
tar -czf encuestas_backup_$(date +%Y%m%d).tar.gz /path/to/encuestas/

# Backup de base de datos
mysqldump -u mlgcapa_enc -p mlgcapa_enc > backup_$(date +%Y%m%d).sql
```
