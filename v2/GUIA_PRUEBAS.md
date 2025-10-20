# 🧪 GUÍA DE PRUEBAS - CAPA Encuestas v2.0

## 🚀 ACCESO AL SISTEMA

**URL Principal:**
```
http://localhost:8888/capa/encuestas/v2/
```

---

## 🔐 CREDENCIALES DE PRUEBA

### Usuario Administrador:
- **Usuario:** `Coordinación`
- **Contraseña:** `para1857`

### Usuario Socio:
- **Usuario:** `CAPA`
- **Contraseña:** (la del sistema viejo - si no la recuerdas, usa el admin)

---

## ✅ FUNCIONALIDADES LISTAS PARA PROBAR

### 1. LOGIN (100% Funcional)
- ✅ Diseño moderno Bootstrap 5
- ✅ Gradiente azul → púrpura de fondo
- ✅ Validación de credenciales con bcrypt
- ✅ Mensajes de error/éxito
- ✅ Responsive mobile

**Prueba:**
1. Ir a `http://localhost:8888/capa/encuestas/v2/`
2. Ingresar usuario y contraseña
3. Verificar redirección al dashboard

---

### 2. DASHBOARD (100% Funcional)

**Admin ve:**
- ✅ Estadísticas (usuarios, socios, artículos, mercados)
- ✅ Última encuesta activa
- ✅ Accesos rápidos
- ✅ Días restantes para encuesta

**Socio ve:**
- ✅ Última encuesta activa
- ✅ Accesos rápidos
- ✅ Días restantes

**Prueba:**
1. Después del login, verificar que cargue el dashboard
2. Ver las cards con estadísticas (si eres admin)
3. Hacer click en "Ver Encuesta"

---

### 3. MÓDULO DE ENCUESTAS (100% Funcional) ⭐

#### 3.1. Última Encuesta - VISTA ADMIN
**URL:** Click en "Encuestas" → "Última Encuesta"

**Funcionalidad:**
- ✅ Ver información de la encuesta actual
- ✅ Ver artículos NO incluidos por socios
- ✅ Tabla responsive (desktop/mobile)

**Prueba:**
1. Login como admin (`Coordinación`)
2. Click en menú "Encuestas" → "Última Encuesta"
3. Ver listado de artículos deshabilitados por socios
4. Verificar responsive (F12 → mobile view)

---

#### 3.2. Última Encuesta - VISTA SOCIO ⭐⭐⭐
**URL:** Click en "Encuestas" → "Última Encuesta"

**Funcionalidad:**
- ✅ **Tab 1: Configuración de Artículos**
  - Ver rubros en acordeones
  - Marcar/desmarcar artículos con los que trabaja
  - Guardar automáticamente vía AJAX
  
- ✅ **Tab 2: Carga de Datos**
  - Seleccionar Rubro → Familia → Artículo
  - Ingresar precios por mercado
  - Guardar automáticamente al salir del campo
  - Solo artículos habilitados aparecen

**Prueba como SOCIO:**
1. Login con usuario socio (si no tenés, creá uno en el sistema viejo)
2. Ir a "Encuestas" → "Última Encuesta"
3. **Tab "Configuración de Artículos":**
   - Expandir un rubro (acordeón)
   - Desmarcar un artículo
   - Verificar que guarda (debería mostrar mensaje)
   - Volver a marcar el artículo
4. **Tab "Carga de Datos":**
   - Seleccionar un rubro del dropdown
   - Seleccionar una familia
   - Seleccionar un artículo
   - Ingresar un precio en algún mercado
   - Salir del campo (blur)
   - Verificar que guarda (input se pone verde brevemente)

---

#### 3.3. Encuestas Anteriores
**URL:** Click en "Encuestas" → "Anteriores"

**Funcionalidad:**
- ✅ Listado de todas las encuestas
- ✅ Estado (Activa/Finalizada)
- ✅ Fechas
- ✅ Responsive (tabla en desktop, cards en mobile)

**Prueba:**
1. Ir a "Encuestas" → "Anteriores"
2. Ver listado de encuestas
3. Verificar badges de estado
4. Probar en mobile (F12)

---

### 4. CONFIGURACIÓN - RUBROS (100% Funcional)

**URL:** Click en "Configuración" → "Rubros"

**Funcionalidad:**
- ✅ Listar todos los rubros
- ✅ Crear nuevo rubro (modal)
- ✅ Editar rubro existente (modal)
- ✅ Habilitar/deshabilitar
- ✅ Guardar vía AJAX

**Prueba como ADMIN:**
1. Ir a "Configuración" → "Rubros"
2. Click en "Nuevo Rubro"
3. Completar formulario
4. Guardar
5. Verificar que aparece en la tabla
6. Click en el botón "Editar" (icono lápiz)
7. Modificar nombre
8. Guardar
9. Verificar cambios

---

### 5. NAVEGACIÓN Y UI (100% Funcional)

**Funcionalidad:**
- ✅ Menú superior azul oscuro CAPA
- ✅ Dropdowns funcionales
- ✅ Hamburger menu en mobile
- ✅ Footer con "Powered by malagadesign"
- ✅ Colores CAPA en toda la interfaz
- ✅ Responsive en todos los tamaños

**Prueba:**
1. Navegar por todos los menús
2. Abrir DevTools (F12)
3. Cambiar a vista mobile (375px)
4. Verificar que el menú se convierte en hamburger
5. Click en hamburger
6. Verificar que abre el menú
7. Navegar entre secciones

---

## ❌ FUNCIONALIDADES PENDIENTES (No probar aún)

- ⏳ Configuración → Familias (backend listo, falta vista)
- ⏳ Configuración → Artículos (backend listo, falta vista)
- ⏳ Configuración → Mercados (backend listo, falta vista)
- ⏳ Configuración → Encuestas (backend listo, falta vista)
- ⏳ Usuarios → Administrativos (backend listo, falta vista)
- ⏳ Usuarios → Socios (backend listo, falta vista)
- ⏳ Cuenta → Cambiar Contraseña (pendiente)

---

## 🎨 ASPECTOS DE DISEÑO A EVALUAR

1. **Colores:**
   - ¿Te gustan los colores CAPA (#001A4D, #9D4EDD)?
   - ¿El gradiente del login se ve bien?
   - ¿Los botones tienen buen contraste?

2. **UX/UI:**
   - ¿La navegación es intuitiva?
   - ¿Los formularios son claros?
   - ¿Los mensajes de éxito/error son visibles?

3. **Responsive:**
   - ¿Se ve bien en mobile?
   - ¿Las tablas son legibles?
   - ¿Los formularios son usables en pantallas pequeñas?

4. **Performance:**
   - ¿Carga rápido?
   - ¿Los AJAX funcionan bien?
   - ¿Hay algún retraso notable?

---

## 🐛 REPORTE DE PROBLEMAS

Si encontrás algún problema, anotá:
1. ¿Qué estabas haciendo?
2. ¿Qué esperabas que pasara?
3. ¿Qué pasó en realidad?
4. Screenshot si es posible

---

## 📝 CHECKLIST DE PRUEBAS

### Login
- [ ] Login exitoso con credenciales correctas
- [ ] Mensaje de error con credenciales incorrectas
- [ ] Redirección al dashboard
- [ ] Se ve bien en mobile

### Dashboard
- [ ] Estadísticas visibles (admin)
- [ ] Última encuesta se muestra
- [ ] Links funcionan
- [ ] Responsive en mobile

### Encuestas - Admin
- [ ] Ver artículos no incluidos
- [ ] Tabla responsive
- [ ] Navegación funciona

### Encuestas - Socio (⭐ MÁS IMPORTANTE)
- [ ] Configurar artículos funciona
- [ ] Marcar/desmarcar guarda correctamente
- [ ] Carga de datos: seleccionar rubro/familia/artículo
- [ ] Ingresar precio y guardar automáticamente
- [ ] Los datos persisten al recargar
- [ ] Responsive en mobile

### Configuración - Rubros
- [ ] Listar rubros
- [ ] Crear nuevo rubro
- [ ] Editar rubro existente
- [ ] Modal funciona
- [ ] Guardar funciona

### Navegación
- [ ] Menú superior funciona
- [ ] Dropdowns se abren
- [ ] Hamburger en mobile funciona
- [ ] Footer visible
- [ ] Logout funciona

---

## ✅ PRÓXIMOS PASOS DESPUÉS DE PROBAR

1. Reportar problemas/sugerencias
2. Confirmar qué funciona bien
3. Decidir si continuar con las vistas restantes
4. Ajustar diseño si es necesario

---

**¡A probar!** 🚀🎉

