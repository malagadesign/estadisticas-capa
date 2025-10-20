# 🎨 GUÍA DE ESTILOS - SISTEMA DE ENCUESTAS CAPA
**Fecha: 8 de Octubre, 2025**

---

## 📋 PALETA DE COLORES OFICIAL

### **Colores Principales:**

| Color | Hex | RGB | Uso |
|-------|-----|-----|-----|
| 🔵 **Azul Marino** | `#001A4D` | rgb(0, 26, 77) | Header, Footer, Botones principales |
| 💜 **Violeta Púrpura** | `#9D4EDD` | rgb(157, 78, 221) | Acentos, Links activos, Tabs |
| 💟 **Lila Claro** | `#C084FC` | rgb(192, 132, 252) | Hover, Estados activos |
| ⚪ **Blanco** | `#FFFFFF` | rgb(255, 255, 255) | Texto sobre fondos oscuros |

---

## 🔘 BOTONES

### **Botones Principales (Azul Marino)**

**Clases afectadas:**
- `.btn-success` - Botón "Ingresar", "Guardar"
- `.btn-info` - Botón "Crear nueva familia", "Agregar"
- `.btn-primary` - Botones primarios
- `.notika-btn-success` / `.notika-btn-info` / `.notika-btn-primary`

**Estilo:**
```css
background: #001A4D;
color: #FFFFFF;
border-color: #001A4D;
```

**Hover:**
```css
background: #002B6B; /* Azul marino más claro */
```

**Ejemplo HTML:**
```html
<button class="btn btn-success">Ingresar</button>
<button class="btn btn-info">Crear nueva familia</button>
<a href="#" class="btn btn-primary">Guardar</a>
```

---

## 📑 TABS Y NAVEGACIÓN

### **Tabs Activos (Violeta)**

**Clases:**
- `.nav-tabs > li.active > a`

**Estilo:**
```css
color: #9D4EDD;
border-bottom: 2px solid #9D4EDD;
font-weight: 600;
```

**Ejemplo:**
```html
<ul class="nav nav-tabs">
    <li class="active"><a href="#rubros">Rubros</a></li>
    <li><a href="#familias">Familias</a></li>
    <li><a href="#articulos">Artículos</a></li>
</ul>
```

---

## 🎨 ELEMENTOS ESPECÍFICOS

### **Header y Footer**
```css
.header-top-area,
.footer-copyright-area {
    background: #001A4D;
    color: #FFFFFF;
}
```

### **Enlaces en Footer**
```css
.footer-copyright-area a {
    color: #FFFFFF;
}

.footer-copyright-area a:hover {
    color: #C084FC; /* Lila claro */
}
```

### **Formularios - Estados Focus**
```css
.form-control:focus {
    border-color: #9D4EDD;
    box-shadow: 0 0 0 0.2rem rgba(157, 78, 221, 0.25);
}
```

### **Badges y Labels**
```css
.badge-success,
.label-success {
    background: #9D4EDD;
    color: #FFFFFF;
}
```

### **Progress Bars**
```css
.progress-bar-success {
    background: #9D4EDD;
}
```

### **Alerts**
```css
.alert-success {
    background: rgba(157, 78, 221, 0.1);
    border-color: #9D4EDD;
    color: #001A4D;
}
```

---

## 📦 COMPONENTES INTERACTIVOS

### **Paginación**
```css
.pagination > .active > a {
    background: #001A4D;
    border-color: #001A4D;
}

.pagination > li > a:hover {
    color: #9D4EDD;
    border-color: #9D4EDD;
}
```

### **Dropdowns**
```css
.dropdown-menu > .active > a {
    background: #001A4D;
    color: #FFFFFF;
}

.dropdown-menu > li > a:hover {
    background: rgba(157, 78, 221, 0.1);
    color: #001A4D;
}
```

### **Switches y Checkboxes**
```css
.nk-toggle-switch input:checked + .ts-helper {
    background: #9D4EDD;
}

input[type="checkbox"]:checked {
    background: #9D4EDD;
    border-color: #9D4EDD;
}
```

---

## 📁 ARCHIVOS DE ESTILOS

### **Orden de carga:**
1. `css/bootstrap.min.css` - Framework base
2. `css/main.css` - Estilos principales del template
3. `style.css` - Estilos personalizados generales
4. `css/responsive.css` - Responsive design
5. **`css/capa-custom.css`** - **Estilos CAPA (ÚLTIMO - Sobrescribe todo)**

### **Cache Busting:**
Todos los archivos CSS incluyen `?v=<?php echo time(); ?>` para evitar problemas de caché.

---

## 🛠️ CÓMO AGREGAR NUEVOS ESTILOS

### **Para botones nuevos:**
```css
/* Agregar al archivo css/capa-custom.css */
.tu-nuevo-boton {
    background-color: #001A4D !important;
    border-color: #001A4D !important;
    color: #ffffff !important;
}

.tu-nuevo-boton:hover {
    background-color: #002B6B !important;
}
```

### **Para elementos interactivos:**
```css
/* Usar violeta para estados activos */
.elemento.active {
    color: #9D4EDD !important;
    border-bottom: 2px solid #9D4EDD;
}
```

---

## ⚠️ NOTAS IMPORTANTES

1. **`!important`:** Se usa en `capa-custom.css` para sobrescribir estilos del template
2. **Caché:** Los cambios en CSS pueden tardar en verse. Usar Cmd+Shift+R (hard refresh)
3. **Consistencia:** Siempre usar los colores de la paleta oficial
4. **Accesibilidad:** El contraste azul marino/blanco cumple con WCAG AAA (7:1)

---

## 🎯 EJEMPLOS DE USO

### **Página de Login:**
```html
<button class="btn btn-success notika-btn-success waves-effect">
    Ingresar
</button>
```
**Resultado:** Botón azul marino con texto blanco

### **Botón de Acción:**
```html
<button class="btn btn-info">Crear nueva familia</button>
```
**Resultado:** Botón azul marino con texto blanco

### **Tab Activo:**
```html
<li class="active"><a href="#familias">Familias</a></li>
```
**Resultado:** Texto violeta con borde inferior violeta

---

## 📞 SOPORTE

Para cambios de diseño o nuevos componentes:
- **Archivo de estilos:** `css/capa-custom.css`
- **Paleta de referencia:** `PALETA_COLORES_CAPA.md`
- **Diseñado por:** malagadesign (hola@malaga-design.com)

---

**Última actualización:** 8 de Octubre, 2025  
**Versión:** 2.0 - Paleta CAPA completa

