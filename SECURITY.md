# 🔐 Política de Seguridad

## Versiones Soportadas

| Versión | Soportada          |
| ------- | ------------------ |
| main    | ✅ Sí              |

## 🔒 Configuración Segura

### Variables de Entorno

Este proyecto utiliza variables de entorno para manejar información sensible. **NUNCA** se deben incluir credenciales en el código.

#### Archivo `.env` (NO incluido en el repositorio)

```env
# Base de datos
DB_HOST=localhost
DB_USER=tu_usuario
DB_PASSWORD=tu_contraseña_segura
DB_NAME=nombre_base_datos

# Email
MAIL_HOST=smtp.servidor.com
MAIL_PORT=587
MAIL_USER=email@dominio.com
MAIL_PASSWORD=contraseña_email
ADMIN_EMAIL=admin@dominio.com

# Seguridad
ENVIRONMENT=production
SESSION_COOKIE_SECURE=1
DISPLAY_ERRORS=0
```

### Archivos Protegidos por .gitignore

Los siguientes archivos **NUNCA** deben subirse al repositorio:

- ✅ `.env` - Variables de entorno
- ✅ `*.log` - Logs del sistema
- ✅ `*.sql` - Dumps de base de datos
- ✅ `logs/` - Directorio de logs
- ✅ Archivos de backup

## 🚨 Reportar Vulnerabilidades

Si encontrás una vulnerabilidad de seguridad:

1. **NO** la publiques en issues públicos
2. Contactá directamente a: capa@capa.org.ar
3. Incluí:
   - Descripción detallada de la vulnerabilidad
   - Pasos para reproducirla
   - Impacto potencial
   - Si es posible, una solución sugerida

**Tiempo de respuesta esperado:** 48-72 horas

## ✅ Medidas de Seguridad Implementadas

### 1. Protección de Credenciales
- ✅ Variables de entorno en archivo `.env`
- ✅ Sin credenciales hardcodeadas
- ✅ `.env` en `.gitignore`

### 2. Protección contra SQL Injection
- ✅ Prepared statements (mysqli)
- ✅ Sanitización de entradas
- ✅ Validación de tipos de datos

### 3. Protección CSRF
- ✅ Tokens CSRF en formularios
- ✅ Validación de tokens en servidor
- ✅ Regeneración de tokens

### 4. Sesiones Seguras
- ✅ Cookies HttpOnly
- ✅ Cookies Secure (HTTPS)
- ✅ SameSite=Strict
- ✅ Regeneración periódica de session ID
- ✅ Tiempo de expiración configurado

### 5. Manejo de Errores
- ✅ Logs protegidos con .htaccess
- ✅ Errores no mostrados al usuario en producción
- ✅ Mensajes genéricos para usuarios

### 6. Control de Acceso
- ✅ Validación de sesiones
- ✅ Verificación de permisos por tipo de usuario
- ✅ Protección de directorios administrativos

## 📋 Checklist de Despliegue Seguro

Antes de desplegar en producción, verificar:

- [ ] Archivo `.env` configurado con credenciales únicas
- [ ] `ENVIRONMENT=production` en `.env`
- [ ] `DISPLAY_ERRORS=0` en `.env`
- [ ] `SESSION_COOKIE_SECURE=1` (si se usa HTTPS)
- [ ] SSL/HTTPS habilitado
- [ ] Permisos de archivos correctos:
  - [ ] `chmod 644` para archivos PHP
  - [ ] `chmod 755` para directorios
  - [ ] `chmod 600` para `.env`
- [ ] Contraseñas de BD cambiadas
- [ ] Contraseñas de email cambiadas
- [ ] Logs protegidos (`.htaccess` en `/logs/`)
- [ ] Backup de base de datos configurado
- [ ] Monitoreo de logs activo

## 🔄 Rotación de Credenciales

Se recomienda cambiar las credenciales cada:

- **Contraseñas de BD:** Cada 90 días
- **Contraseñas de email:** Cada 90 días
- **Tokens de sesión:** Automático (cada 30 minutos)
- **Después de:**
  - Salida de un miembro del equipo
  - Sospecha de compromiso
  - Incidente de seguridad

## 📚 Recursos de Seguridad

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://www.php.net/manual/es/security.php)
- [CSRF Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)

## 🔍 Auditorías de Seguridad

Última auditoría: Octubre 2025

Ver detalles en:
- `/INFORME_SEGURIDAD.md`
- `/PLAN_CORRECCION.md`
- `/RESUMEN_CAMBIOS_IMPLEMENTADOS.txt`

## ⚠️ Notas Importantes

1. **Nunca** commitear archivos con credenciales
2. **Siempre** revisar `git diff` antes de hacer commit
3. **Usar** branches para desarrollo
4. **Proteger** la rama `main` de pushes directos
5. **Revisar** logs regularmente

---

Para más información sobre configuración segura, ver `/INSTRUCCIONES_CONFIGURACION.md`

