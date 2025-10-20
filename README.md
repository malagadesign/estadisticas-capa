# 🚀 CAPA Encuestas v2.0

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
├── public/              # Carpeta pública (document root)
│   ├── index.php       # Entry point
│   ├── .htaccess       # Routing y seguridad
│   └── assets/         # CSS, JS, imágenes
│
├── app/                # Aplicación
│   ├── controllers/    # Controladores
│   ├── models/         # Modelos (acceso a datos)
│   ├── views/          # Vistas (HTML+PHP)
│   ├── middleware/     # Middlewares
│   └── helpers/        # Funciones auxiliares
│
├── core/               # Core del framework
│   ├── Router.php
│   ├── Database.php
│   ├── View.php
│   ├── Request.php
│   └── Session.php
│
├── config/             # Configuración
│   ├── app.php         # Config general
│   └── routes.php      # Definición de rutas
│
└── storage/            # Almacenamiento
    ├── logs/
    ├── uploads/
    └── cache/
```

---

## 🚀 INSTALACIÓN Y ACCESO

### 1. **Acceder al sistema nuevo**

**URL:**
```
http://localhost:8888/capa/encuestas/v2/
```

**O también:**
```
http://localhost:8888/capa/encuestas/v2/index.php
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

- ✅ Usa la misma BD: `mlgcapa_enc`
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

### Por implementar:
- ⏳ Login attempts limit
- ⏳ Rate limiting
- ⏳ Logs de auditoría

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

## 🧪 PRUEBAS

### 1. **Login**
```
1. Ir a: http://localhost:8888/capa/encuestas/v2/
2. Ingresar credenciales
3. Verificar redirección al dashboard
```

### 2. **Dashboard**
```
1. Verificar estadísticas (si es admin)
2. Ver última encuesta activa
3. Verificar accesos rápidos
```

### 3. **Responsive**
```
1. Abrir DevTools (F12)
2. Cambiar a vista mobile (375px)
3. Verificar que todo sea visible y usable
```

---

## 🎯 ESTADO ACTUAL

### ✅ COMPLETADO (Fase 1):
1. ✅ Estructura del proyecto
2. ✅ Core del framework (Router, Database, View, Session, Request)
3. ✅ Sistema de autenticación
4. ✅ Layouts con Bootstrap 5
5. ✅ Diseño responsive mobile-first
6. ✅ Paleta de colores CAPA
7. ✅ Dashboard funcional

### ⏳ PENDIENTE (Próximas fases):
- ⏳ Módulo de Encuestas (última, anteriores, carga de datos)
- ⏳ Módulo de Configuración (CRUD rubros, familias, artículos, mercados, encuestas)
- ⏳ Módulo de Usuarios (CRUD administrativos y socios)
- ⏳ Middlewares de autorización
- ⏳ Modelos para acceso a datos
- ⏳ Validaciones de formularios
- ⏳ Upload de Excel
- ⏳ Exportación de datos

---

## 🔄 MIGRACIÓN

### Convivencia con sistema viejo:
- ✅ Ambos sistemas funcionan en paralelo
- ✅ Misma base de datos
- ✅ Usuarios pueden usar ambos
- ✅ Cuando v2.0 esté completo, se reemplaza v1.0

### Plan de migración gradual:
1. **Fase 1:** Login y Dashboard (✅ COMPLETADO)
2. **Fase 2:** Módulo de Encuestas
3. **Fase 3:** Módulo de Configuración
4. **Fase 4:** Módulo de Usuarios
5. **Fase 5:** Testing y ajustes finales
6. **Fase 6:** Reemplazo completo de v1.0

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

**¡Sistema listo para probar!** 🎉

Navega a `http://localhost:8888/capa/encuestas/v2/` para comenzar.

