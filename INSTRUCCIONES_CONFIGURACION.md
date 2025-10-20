# INSTRUCCIONES DE CONFIGURACIÓN
## Sistema de Encuestas CAPA - Configuración Post-Migración

---

## ✅ CAMBIOS IMPLEMENTADOS

Se han implementado las siguientes mejoras de seguridad en el sistema:

### 1. Archivos Creados:
- ✅ `.gitignore` - Protección de archivos sensibles en Git
- ✅ `env.example.txt` - Plantilla de configuración
- ✅ `config.php` - Gestor seguro de configuración
- ✅ `csrf.php` - Protección contra ataques CSRF
- ✅ `.htaccess` - Reglas de seguridad del servidor

### 2. Archivos Modificados:
- ✅ `conector.php` - Ahora usa configuración segura sin credenciales hardcodeadas
- ✅ `adm/ADM.php` - Desactivado display_errors
- ✅ `cuenta/ADM.php` - Desactivado display_errors
- ✅ `usuarios/ADM.php` - Desactivado display_errors
- ✅ `ver/ultimoADMmontos.php` - Desactivado display_errors
- ✅ `ver/ultimoADMarticulos.php` - Desactivado display_errors
- ✅ `log/index.php` - Desactivado display_errors

### 3. Archivos Eliminados:
- ✅ `conector_viejo.php` (contenía credenciales expuestas)
- ✅ `cuenta/z$ ADM.php` (backup innecesario)
- ✅ `cuenta/z$ cambioPas.php` (backup innecesario)
- ✅ `ver/z$ ultimoAdm.php` (backup innecesario)

---

## ⚠️ PASOS CRÍTICOS A REALIZAR AHORA

### PASO 1: Crear archivo .env

Debes crear un archivo llamado `.env` (sin extensión, solo ".env") en la raíz del proyecto `/Users/mica/htdocs/capa/encuestas/`

**Opción A - Copiar desde plantilla (recomendado):**
```bash
cd /Users/mica/htdocs/capa/encuestas/
cp env.example.txt .env
```

**Opción B - Crear manualmente:**
Crea un archivo `.env` con el siguiente contenido:

```env
# BASE DE DATOS
DB_HOST=localhost
DB_USER=mlgcapa_enc
DB_PASSWORD=CAMBIAR_CONTRASEÑA_AQUI
DB_NAME=mlgcapa_enc

# CONFIGURACIÓN DE EMAIL SMTP
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=estadisticas@capa.org.ar
MAIL_PASSWORD=CAMBIAR_CONTRASEÑA_EMAIL_AQUI
MAIL_FROM_NAME=CAPA
MAIL_REPLY_TO=capa@capa.org.ar

# CONFIGURACIÓN DE SEGURIDAD
DISPLAY_ERRORS=0
SESSION_COOKIE_SECURE=1

# CONFIGURACIÓN DE EMAIL ADMINISTRATIVO
ADMIN_EMAIL=capa@capa.org.ar

# ENTORNO (development, production)
ENVIRONMENT=production
```

### PASO 2: Cambiar TODAS las contraseñas

**IMPORTANTE:** Las contraseñas anteriores estaban expuestas en el código. Debes cambiarlas inmediatamente:

#### 2.1. Contraseña de Base de Datos

```bash
# 1. Conectarse a MySQL
mysql -u root -p

# 2. Cambiar contraseña (reemplaza NUEVA_CONTRASEÑA_SEGURA por una contraseña fuerte)
ALTER USER 'mlgcapa_enc'@'localhost' IDENTIFIED BY 'NUEVA_CONTRASEÑA_SEGURA';
FLUSH PRIVILEGES;
EXIT;

# 3. Actualizar el archivo .env con la nueva contraseña
# Editar .env y reemplazar DB_PASSWORD=CAMBIAR_CONTRASEÑA_AQUI por la nueva
```

**Generar contraseña segura:**
```bash
# En terminal Mac/Linux
openssl rand -base64 32

# O usar generador online: https://passwordsgenerator.net/
# Configuración sugerida: 20 caracteres, letras, números y símbolos
```

#### 2.2. Contraseña de Email

Accede a la configuración de la cuenta de email `estadisticas@capa.org.ar` y cambia la contraseña.

Luego actualiza el archivo `.env`:
```env
MAIL_PASSWORD=NUEVA_CONTRASEÑA_EMAIL_AQUI
```

#### 2.3. Contraseñas de Usuarios del Sistema

Todas las contraseñas de usuarios deben ser cambiadas porque están en texto plano en la base de datos.

**NOTA IMPORTANTE:** Actualmente el sistema almacena contraseñas en texto plano. Esto se solucionará en la siguiente fase (implementación de password hashing).

### PASO 3: Configurar permisos de archivos

```bash
cd /Users/mica/htdocs/capa/encuestas/

# Proteger archivo .env
chmod 600 .env

# Crear y proteger directorio de logs
mkdir -p logs
chmod 755 logs
echo "Order deny,allow\nDeny from all" > logs/.htaccess

# Verificar que config.php y csrf.php tengan permisos correctos
chmod 644 config.php
chmod 644 csrf.php
```

### PASO 4: Verificar que HTTPS esté configurado

El sistema ahora fuerza HTTPS. Verifica que el certificado SSL esté instalado:

```bash
# Verificar certificado SSL
openssl s_client -connect capa.org.ar:443 -servername capa.org.ar
```

Si no tienes certificado SSL:
1. Obtén uno gratuito en: https://letsencrypt.org/
2. O usa Certbot: https://certbot.eff.org/

**Temporalmente**, si necesitas desactivar el forzado de HTTPS:
Edita `.htaccess` y comenta estas líneas:
```apache
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### PASO 5: Probar el sistema

1. **Probar conexión a base de datos:**
```bash
# Acceder al sistema y verificar que no hay errores de conexión
curl https://capa.org.ar/encuestas/
```

2. **Probar login:**
   - Ir a: https://capa.org.ar/encuestas/
   - Intentar iniciar sesión
   - Verificar que funciona correctamente

3. **Revisar logs de errores:**
```bash
tail -f /Users/mica/htdocs/capa/encuestas/logs/php-errors.log
```

---

## 🔐 VERIFICACIÓN DE SEGURIDAD

### Checklist de Verificación:

- [ ] Archivo `.env` creado y con permisos 600
- [ ] Contraseña de base de datos cambiada
- [ ] Contraseña de email cambiada
- [ ] `.env` actualizado con nuevas contraseñas
- [ ] Directorio `logs/` creado con `.htaccess`
- [ ] HTTPS funcionando correctamente
- [ ] Sistema probado y funcionando
- [ ] Login funcionando correctamente
- [ ] No se muestran errores de PHP en pantalla
- [ ] Archivo `.gitignore` funcionando (si usas Git)

---

## 📋 SIGUIENTE FASE (Próximos pasos recomendados)

Las siguientes mejoras deberían implementarse en una segunda fase:

### 1. Migración a Password Hashing
- Implementar `password_hash()` y `password_verify()`
- Migrar contraseñas existentes

### 2. Implementación de CSRF en formularios
- Agregar `csrf_field()` en todos los formularios
- Validar tokens en procesadores

### 3. Prepared Statements
- Migrar todas las consultas SQL a prepared statements

### 4. Límite de intentos de login
- Crear tabla `login_attempts`
- Implementar bloqueo temporal

### 5. Autenticación de dos factores (2FA)
- Para usuarios administrativos

---

## 🆘 TROUBLESHOOTING

### Problema: "Error de configuración del sistema"
**Causa:** No se encuentra el archivo `.env` o tiene formato incorrecto.

**Solución:**
```bash
# Verificar que existe
ls -la /Users/mica/htdocs/capa/encuestas/.env

# Verificar permisos
chmod 600 /Users/mica/htdocs/capa/encuestas/.env

# Verificar formato (no debe tener espacios extras)
cat /Users/mica/htdocs/capa/encuestas/.env
```

### Problema: "Error de conexión con la base de datos"
**Causa:** Credenciales incorrectas en `.env`.

**Solución:**
1. Verificar que `DB_PASSWORD` en `.env` es correcto
2. Probar conexión manualmente:
```bash
mysql -u mlgcapa_enc -p mlgcapa_enc
```

### Problema: Error 500 - Internal Server Error
**Causa:** Puede ser error de sintaxis en `.htaccess` o permisos.

**Solución:**
```bash
# Ver logs de Apache
tail -f /var/log/apache2/error_log

# Temporalmente renombrar .htaccess para identificar si es el problema
mv .htaccess .htaccess.backup
# Probar el sitio
# Si funciona, revisar el .htaccess línea por línea
```

### Problema: Las sesiones no funcionan
**Causa:** `SESSION_COOKIE_SECURE=1` requiere HTTPS.

**Solución temporal (solo para desarrollo):**
En `.env` cambiar:
```env
SESSION_COOKIE_SECURE=0
```

---

## 📞 CONTACTO Y SOPORTE

Si encuentras problemas durante la configuración:

1. Revisa los logs: `/Users/mica/htdocs/capa/encuestas/logs/php-errors.log`
2. Revisa los logs de Apache: `/var/log/apache2/error_log`
3. Consulta el archivo `PLAN_CORRECCION.md` para detalles adicionales

---

## 🔒 RECORDATORIOS DE SEGURIDAD

- ✅ NUNCA subir el archivo `.env` a repositorio Git
- ✅ NUNCA compartir credenciales por email o mensajes sin cifrar
- ✅ Cambiar contraseñas cada 90 días
- ✅ Usar contraseñas diferentes para cada servicio
- ✅ Mantener backups actualizados antes de cualquier cambio
- ✅ Revisar logs regularmente en busca de actividad sospechosa

---

**Fecha de implementación:** 8 de Octubre, 2025

**¡Éxito con la configuración!** 🚀
