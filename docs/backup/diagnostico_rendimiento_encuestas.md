# 📊 Diagnóstico de Rendimiento - Encuestas

## Problema Identificado
Los resultados de la encuesta tardan mucho en cargar porque se están cargando **TODOS** los artículos, familias, rubros y mercados de una vez.

## Análisis Actual

### Código actual (v2/app/controllers/EncuestasController.php:49-52)
```php
$rubros = $rubroModel->getAll();      // Carga TODOS los rubros
$familias = $familiaModel->getAll();  // Carga TODAS las familias
$articulos = $articuloModel->getAll(); // Carga TODOS los artículos ⚠️
$mercados = $mercadoModel->getAll();   // Carga TODOS los mercados
```

### Problemas:
1. **Muchos registros**: Si hay 500+ artículos, se cargan todos
2. **JavaScript pesado**: Todo se pasa como JSON al frontend
3. **Sin paginación**: Todo se muestra en una sola lista larga
4. **Sin filtros**: No se puede filtrar por rubro/familia

## Soluciones Propuestas

### Solución 1: Carga Diferida (Lazy Loading) ⭐ RECOMENDADA
- Cargar artículos **solo cuando el usuario seleccione una familia**
- Cargar por demanda vía AJAX
- Reducir carga inicial del 90%

**Implementación:**
```php
// Backend: Nuevo endpoint para cargar artículos
public function getArticulosPorFamilia() {
    $familiaDid = Request::get('familiaDid');
    $articulos = $articuloModel->getByFamilia($familiaDid);
    View::json($articulos);
}

// Frontend: Cargar por demanda
async function cargarArticulos(familiaDid) {
    const response = await fetch(`/v2/encuestas/articulos?familiaDid=${familiaDid}`);
    const articulos = await response.json();
    // Renderizar solo estos artículos
}
```

### Solución 2: Paginación
- Mostrar 50 artículos por página
- Navegación prev/next
- Mejor UX

### Solución 3: Virtual Scrolling
- Renderizar solo los artículos visibles en viewport
- Cargar más al hacer scroll
- Usar librerías como `react-window` o `vue-virtual-scroller`

### Solución 4: Gráficos Visuales
En lugar de mostrar una lista interminable, mostrar:
- **Gráfico de barras** por rubro
- **Gráfico circular** por familia
- **Heatmap** de completitud por socio
- **Tabla resumen** con agregaciones

## Plan de Implementación

### Fase 1: Optimización Inmediata (2-3 horas)
1. Implementar lazy loading de artículos
2. Agregar loading indicators
3. Reducir payload inicial

### Fase 2: Visualizaciones (4-6 horas)
1. Agregar gráfico de completitud
2. Tabla de resumen por rubro
3. Filtros interactivos

### Fase 3: Paginación (2-3 horas)
1. Implementar paginación en frontend
2. Optimizar queries con LIMIT/OFFSET
3. Cachear resultados

## Impacto Esperado
- **Carga inicial**: De 5-10s a <1s
- **Payload**: De 2MB a <100KB
- **UX**: Lista infinita → Lista navegable/filtrada
- **Rendimiento**: 90% mejora

## ¿Cuál preferís implementar primero?

