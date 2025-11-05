# 📊 ANÁLISIS DEL SISTEMA ACTUAL - CAPA Encuestas

## 🎯 RESUMEN EJECUTIVO

**Sistema:** Plataforma de encuestas para relevamiento de precios  
**Framework Actual:** Bootstrap 3 (2013)  
**Base de Datos:** MySQL - 8 tablas  
**Usuarios Totales:** 296 (mix de admin y socios)

---

## 👥 TIPOS DE USUARIO Y PERMISOS

### 1. **Administradores (`tipo = 'adm'`)**
**Ejemplos:** `Coordinación`, `liit`

**Permisos:**
```php
usuarios:
  ├─ admUsuarios (gestión usuarios administrativos)
  └─ admSocios (gestión socios)

adm (configuración):
  ├─ admRubros
  ├─ admFamilias
  ├─ admArticulos
  ├─ admMercados
  └─ admEncuestas

ver (encuestas):
  ├─ ultimo (última encuesta + estadísticas)
  └─ anteriores (historial)

cuenta:
  ├─ cambioPas
  └─ cerrar
```

### 2. **Socios (`tipo = 'socio'`)**
**Ejemplos:** `CAPA`, otros socios

**Permisos:**
```php
ver (encuestas):
  ├─ ultimo (última encuesta - solo carga)
  └─ anteriores (historial)
```

---

## 🗄️ ESTRUCTURA DE BASE DE DATOS

### Tablas:

1. **`usuarios`** - Usuarios del sistema (admin y socios)
2. **`rubros`** - Categorías principales (ej: "Alimentos")
3. **`familias`** - Subcategorías de rubros (ej: "Lácteos")
4. **`articulos`** - Productos específicos (ej: "Leche entera")
5. **`mercados`** - Lugares de venta (ej: "Supermercado A")
6. **`encuestas`** - Períodos de relevamiento
7. **`articulosUsuarios`** - Relación: qué artículos maneja cada socio
8. **`articulosMontos`** - Datos: precios cargados por socio/encuesta

### Relaciones:
```
rubros (1) → (N) familias
familias (1) → (N) articulos
articulos (N) ← (N) usuarios (via articulosUsuarios)
articulos + mercados + encuestas + usuarios → articulosMontos (precios)
```

---

## 🔧 FUNCIONALIDADES POR MÓDULO

### A. **USUARIOS** (solo admin)

#### `admUsuarios` - Usuarios Administrativos
- ✅ Listar usuarios admin
- ✅ Crear nuevo admin
- ✅ Modificar admin existente
- ✅ Habilitar/deshabilitar
- ✅ Envío de emails con credenciales

#### `admSocios` - Socios
- ✅ Listar socios
- ✅ Crear nuevo socio
- ✅ Modificar socio existente
- ✅ Habilitar/deshabilitar
- ✅ Configuración de artículos por socio
- ✅ Envío de emails

---

### B. **CONFIGURACIÓN (ADM)** (solo admin)

#### `admRubros` - Rubros
- ✅ Listar rubros
- ✅ Crear/modificar rubro
- ✅ Habilitar/deshabilitar
- **UI:** Modal simple

#### `admFamilias` - Familias
- ✅ Listar familias por rubro
- ✅ Crear/modificar familia
- ✅ Asignar a rubro
- ✅ Habilitar/deshabilitar
- **UI:** Modal simple

#### `admArticulos` - Artículos
- ✅ Listar artículos por familia
- ✅ Crear/modificar artículo
- ✅ Asignar a familia
- ✅ Habilitar/deshabilitar
- **UI:** Modal con select múltiple (Chosen)

#### `admMercados` - Mercados
- ✅ Listar mercados
- ✅ Crear/modificar mercado
- ✅ Habilitar/deshabilitar
- **UI:** Modal simple

#### `admEncuestas` - Encuestas
- ✅ Listar encuestas
- ✅ Crear nueva encuesta
- ✅ Definir período (desde/hasta)
- ✅ Habilitar/deshabilitar
- **UI:** DataTables + Modal con Datepicker

---

### C. **ENCUESTAS (VER)**

#### Admin - `ultimo` → `ultimoadm.php`
- ✅ Ver última encuesta activa
- ✅ Ver artículos NO incluidos por socios
- ✅ Ver datos cargados por todos los socios
- ✅ Visualización en tabla
- **UI:** Tabs (Artículos / Ver datos)

#### Socio - `ultimo` → `ultimosocio.php`
- ✅ Ver última encuesta activa
- ✅ **Configurar artículos** con los que trabaja
- ✅ **Carga de datos por pantalla**:
  - Seleccionar rubro → familia → artículo
  - Ingresar precios por mercado
  - Tipos: "costo", "venta", etc.
- ✅ **Carga por Excel**:
  - Descargar modelo
  - Upload archivo
- **UI:** Tabs (Configuración / Carga pantalla / Carga Excel)

#### `anteriores`
- ✅ Listar encuestas pasadas
- ✅ Ver datos históricos
- **UI:** DataTables

---

## 🎨 PROBLEMAS DEL DISEÑO ACTUAL

### 1. **Framework Obsoleto**
- ❌ Bootstrap 3 (2013) - desactualizado
- ❌ No responsive real
- ❌ Librerías pesadas (jQuery plugins viejos)

### 2. **Arquitectura**
- ❌ Sin separación de responsabilidades
- ❌ Lógica mezclada con presentación
- ❌ Sin routing formal
- ❌ Código duplicado

### 3. **Performance**
- ❌ Carga TODAS las librerías en todas las páginas
- ❌ Queries no optimizadas (fetchall sin límites)
- ❌ Sin caching

### 4. **UX/UI**
- ❌ Menú desaparece en mobile
- ❌ Tablas no legibles en mobile
- ❌ No hay indicadores de carga
- ❌ Modales no responsive

### 5. **Seguridad** (ya mejorada parcialmente)
- ✅ Passwords hasheados (migrado)
- ✅ CSRF protection implementado
- ✅ Prepared statements (parcialmente)
- ⚠️ Falta implementar en todos los módulos

---

## 🚀 FLUJO DE TRABAJO TÍPICO

### Admin:
1. Login
2. **Configuración inicial:**
   - Crear rubros
   - Crear familias dentro de rubros
   - Crear artículos dentro de familias
   - Crear mercados
3. **Gestión de usuarios:**
   - Dar de alta socios
   - Configurar artículos por socio
4. **Crear encuesta:**
   - Definir período (desde/hasta)
   - Activar
5. **Monitorear datos:**
   - Ver qué socios cargaron
   - Ver artículos faltantes
   - Ver datos cargados

### Socio:
1. Login
2. **Ver última encuesta**
3. **Configurar artículos:**
   - Marcar con qué artículos trabaja
4. **Cargar datos:**
   - Opción A: Por pantalla (select + input)
   - Opción B: Por Excel (upload)
5. **Ver historial** (encuestas anteriores)

---

## 🎯 OBJETIVOS DE LA MIGRACIÓN

### Técnicos:
- ✅ **Bootstrap 5** - Framework moderno
- ✅ **Mobile-first** - Responsive real
- ✅ **Arquitectura MVC ligera** - Separación de responsabilidades
- ✅ **Performance** - Lazy loading, queries optimizadas
- ✅ **Seguridad** - Prepared statements en todo

### UX/UI:
- ✅ **Navegación clara** - Menú visible en mobile
- ✅ **Tablas legibles** - Card view en mobile
- ✅ **Feedback visual** - Loaders, toasts, validaciones
- ✅ **Paleta CAPA** - Azul #001A4D, Púrpura #9D4EDD

### Funcionales:
- ✅ **Mantener TODAS las funcionalidades actuales**
- ✅ **Sin romper la base de datos**
- ✅ **Sin perder datos**
- ✅ **Mismo flujo de trabajo** (no confundir usuarios)

---

## 📋 PRIORIDADES DE MIGRACIÓN

1. **🔐 Autenticación** (crítico)
2. **📊 Visualización de encuestas** (uso principal)
3. **💼 Gestión de configuración** (admin)
4. **👥 Gestión de usuarios** (admin)
5. **🎨 Refinamiento UI/UX**

---

## 🔄 PRÓXIMOS PASOS

1. ✅ Análisis completo → COMPLETADO
2. 🚀 Diseño de nueva arquitectura → EN PROGRESO
3. 📦 Implementación base → PENDIENTE

