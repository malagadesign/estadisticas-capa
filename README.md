# Sistema de Encuestas CAPA

Sistema web para gestión y administración de encuestas estadísticas de CAPA (Cámara Argentina de Productores Avícolas).

## 🌐 URL del Sistema

**Producción:** https://estadistica-capa.org.ar/encuestas

## 📋 Descripción

Sistema desarrollado en PHP para la administración y visualización de encuestas estadísticas. Permite a socios y administradores cargar, consultar y exportar datos estadísticos.

## 🔧 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- SSL/HTTPS configurado
- Extensiones PHP necesarias:
  - mysqli
  - mbstring
  - session

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone [URL_DEL_REPOSITORIO]
cd encuestas
```

### 2. Configurar variables de entorno

```bash
# Copiar el archivo de ejemplo
cp env.example.txt .env

# Editar .env con tus credenciales
nano .env
```

### 3. Configurar la base de datos

```bash
# Importar la estructura de la base de datos
# NOTA: El archivo .sql está en .gitignore por seguridad
# Solicitar al administrador el dump de la base de datos
mysql -u usuario -p nombre_bd < estructura.sql
```

### 4. Configurar permisos

```bash
# Crear directorio de logs
mkdir -p logs
chmod 755 logs

# Verificar permisos de archivos
chmod 644 *.php
chmod 755 adm/ usuarios/ ver/
```

### 5. Verificar instalación

Acceder a: `https://tu-dominio.com/encuestas/`

## 🔐 Seguridad

### Variables de Entorno

El archivo `.env` contiene información sensible y **NUNCA** debe subirse al repositorio.

Variables requeridas:
- `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` - Configuración de base de datos
- `MAIL_HOST`, `MAIL_USER`, `MAIL_PASSWORD` - Configuración de email
- `ENVIRONMENT` - production/development
- `SESSION_COOKIE_SECURE` - 1 para HTTPS (recomendado)

### Configuración de Seguridad

- ✅ Protección CSRF implementada
- ✅ Sesiones seguras con cookies HttpOnly
- ✅ Prepared statements para prevenir SQL injection
- ✅ Sanitización de entradas
- ✅ Headers de seguridad configurados
- ✅ Logs de errores protegidos

## 📁 Estructura del Proyecto

```
/
├── adm/              # Módulos de administración
├── cuenta/           # Gestión de cuentas y passwords
├── usuarios/         # Administración de usuarios
├── ver/              # Visualización de datos
├── css/              # Estilos
├── js/               # JavaScript
├── logs/             # Logs del sistema (no incluido en Git)
├── config.php        # Configuración principal
├── conector.php      # Conexión a BD y autenticación
├── csrf.php          # Protección CSRF
└── .env              # Variables de entorno (no incluido en Git)
```

## 👥 Tipos de Usuario

1. **Administradores** - Acceso completo al sistema
2. **Socios** - Visualización de estadísticas

## 📧 Configuración de Email

El sistema utiliza PHPMailer 6 para el envío de correos:
- Recuperación de contraseñas
- Notificaciones administrativas
- Envío de links de acceso

Configuración en `.env`:
```
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=tu-email@dominio.com
MAIL_PASSWORD=tu-password
```

## 📊 Base de Datos

Tablas principales:
- `usuarios` - Usuarios del sistema
- `articulos` - Artículos de encuestas
- `rubros` - Rubros/categorías
- `familias` - Familias de productos
- `mercados` - Mercados
- `encuestas` - Datos de encuestas

## 🔄 Actualización

```bash
# Actualizar desde el repositorio
git pull origin main

# Si hay cambios en la estructura de BD
# Aplicar migraciones correspondientes
```

## 📝 Logs

Los logs se guardan en `/logs/php-errors.log` y están protegidos mediante `.htaccess`.

Ver logs en tiempo real:
```bash
tail -f logs/php-errors.log
```

## 🆘 Soporte

Para problemas o consultas:
- Email: capa@capa.org.ar
- Revisar documentación en `/INSTRUCCIONES_CONFIGURACION.md`
- Revisar informe de seguridad en `/INFORME_SEGURIDAD.md`

## 📄 Documentación Adicional

- `INSTRUCCIONES_CONFIGURACION.md` - Guía de configuración detallada
- `INFORME_SEGURIDAD.md` - Auditoría de seguridad
- `PLAN_CORRECCION.md` - Plan de correcciones implementadas
- `RESUMEN_CAMBIOS_IMPLEMENTADOS.txt` - Resumen de cambios

## ⚠️ Importante

- **NUNCA** subir el archivo `.env` al repositorio
- **NUNCA** subir dumps de base de datos con datos reales
- Cambiar todas las contraseñas por defecto
- Mantener PHP y MySQL actualizados
- Revisar logs regularmente
- Realizar backups periódicos

## 📜 Licencia

Uso privado - CAPA (Cámara Argentina de Productores Avícolas)

---

Desarrollado para CAPA - Sistema de Encuestas Estadísticas

