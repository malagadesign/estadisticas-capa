<?php
/**
 * Script para limpiar encuestas con fechas inválidas
 */

// Cargar configuración
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';

echo "<h1>Limpieza de Encuestas con Fechas Inválidas</h1>";

try {
    $db = Database::getInstance();
    
    // Buscar encuestas con fechas inválidas
    $encuestas_problematicas = $db->fetchAll(
        "SELECT did, nombre, desde, hasta, desdeText, hastaText 
         FROM encuestas 
         WHERE (desde = '0000-00-00' OR hasta = '0000-00-00' OR desde IS NULL OR hasta IS NULL)
         AND superado = 0 AND elim = 0"
    );
    
    echo "<h2>Encuestas encontradas con fechas inválidas:</h2>";
    
    if (empty($encuestas_problematicas)) {
        echo "<p>✅ No se encontraron encuestas con fechas inválidas.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Desde</th><th>Hasta</th><th>DesdeText</th><th>HastaText</th><th>Acción</th></tr>";
        
        foreach ($encuestas_problematicas as $enc) {
            echo "<tr>";
            echo "<td>" . $enc['did'] . "</td>";
            echo "<td>" . htmlspecialchars($enc['nombre']) . "</td>";
            echo "<td>" . $enc['desde'] . "</td>";
            echo "<td>" . $enc['hasta'] . "</td>";
            echo "<td>" . htmlspecialchars($enc['desdeText']) . "</td>";
            echo "<td>" . htmlspecialchars($enc['hastaText']) . "</td>";
            
            // Intentar reparar si tiene desdeText y hastaText válidos
            if (!empty($enc['desdeText']) && !empty($enc['hastaText']) && 
                $enc['desdeText'] !== '30/11/-0001' && $enc['hastaText'] !== '30/11/-0001') {
                
                $desdeFormato = DateTime::createFromFormat('d/m/Y', $enc['desdeText']);
                $hastaFormato = DateTime::createFromFormat('d/m/Y', $enc['hastaText']);
                
                if ($desdeFormato && $hastaFormato) {
                    $desdeDB = $desdeFormato->format('Y-m-d');
                    $hastaDB = $hastaFormato->format('Y-m-d');
                    
                    $db->query(
                        "UPDATE encuestas SET desde = ?, hasta = ? WHERE did = ?",
                        ['ssi', $desdeDB, $hastaDB, $enc['did']]
                    );
                    
                    echo "<td style='color: green;'>✅ Reparada</td>";
                } else {
                    echo "<td style='color: red;'>❌ No se puede reparar</td>";
                }
            } else {
                echo "<td style='color: red;'>❌ Sin fechas válidas</td>";
            }
            
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    // Mostrar todas las encuestas para verificar
    echo "<h2>Todas las encuestas (después de la limpieza):</h2>";
    
    $todas_encuestas = $db->fetchAll(
        "SELECT did, nombre, desde, hasta, desdeText, hastaText, habilitado 
         FROM encuestas 
         WHERE superado = 0 AND elim = 0 
         ORDER BY did DESC"
    );
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Desde</th><th>Hasta</th><th>DesdeText</th><th>HastaText</th><th>Habilitado</th></tr>";
    
    foreach ($todas_encuestas as $enc) {
        $color = ($enc['desde'] === '0000-00-00' || $enc['hasta'] === '0000-00-00') ? 'background-color: #ffcccc;' : '';
        echo "<tr style='$color'>";
        echo "<td>" . $enc['did'] . "</td>";
        echo "<td>" . htmlspecialchars($enc['nombre']) . "</td>";
        echo "<td>" . $enc['desde'] . "</td>";
        echo "<td>" . $enc['hasta'] . "</td>";
        echo "<td>" . htmlspecialchars($enc['desdeText']) . "</td>";
        echo "<td>" . htmlspecialchars($enc['hastaText']) . "</td>";
        echo "<td>" . ($enc['habilitado'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h2>✅ Limpieza completada</h2>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
