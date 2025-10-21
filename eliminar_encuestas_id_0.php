<?php
/**
 * Script directo para eliminar encuestas con ID 0
 */

// Cargar configuración
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';

echo "<h1>Eliminación directa de encuestas con ID 0</h1>";

try {
    $db = Database::getInstance();
    
    // Mostrar encuestas actuales
    echo "<h2>Encuestas actuales:</h2>";
    
    $encuestas = $db->fetchAll(
        "SELECT did, nombre, desde, hasta, habilitado 
         FROM encuestas 
         WHERE superado = 0 AND elim = 0 
         ORDER BY did ASC"
    );
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Desde</th><th>Hasta</th><th>Habilitado</th></tr>";
    
    foreach ($encuestas as $enc) {
        $color = ($enc['did'] == 0) ? 'background-color: #ffcccc;' : '';
        echo "<tr style='$color'>";
        echo "<td>" . $enc['did'] . "</td>";
        echo "<td>" . htmlspecialchars($enc['nombre']) . "</td>";
        echo "<td>" . $enc['desde'] . "</td>";
        echo "<td>" . $enc['hasta'] . "</td>";
        echo "<td>" . ($enc['habilitado'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Contar encuestas con ID 0
    $count_id_0 = $db->fetchOne("SELECT COUNT(*) as total FROM encuestas WHERE did = 0 AND superado = 0 AND elim = 0");
    
    echo "<h2>Encuestas con ID 0 encontradas: " . $count_id_0['total'] . "</h2>";
    
    if ($count_id_0['total'] > 0) {
        echo "<p style='color: orange;'>⚠️ Se encontraron " . $count_id_0['total'] . " encuestas con ID 0.</p>";
        
        // Intentar diferentes métodos de eliminación
        echo "<h3>Intentando eliminación...</h3>";
        
        // Método 1: Soft delete (marcar como eliminado)
        echo "<p>Método 1: Soft delete (marcar como eliminado)...</p>";
        $resultado1 = $db->query("UPDATE encuestas SET elim = 1 WHERE did = 0 AND superado = 0 AND elim = 0");
        
        if ($resultado1) {
            echo "<p style='color: green;'>✅ Soft delete exitoso.</p>";
        } else {
            echo "<p style='color: red;'>❌ Soft delete falló: " . $db->mysqli->error . "</p>";
        }
        
        // Método 2: Hard delete (eliminar físicamente)
        echo "<p>Método 2: Hard delete (eliminar físicamente)...</p>";
        $resultado2 = $db->query("DELETE FROM encuestas WHERE did = 0");
        
        if ($resultado2) {
            echo "<p style='color: green;'>✅ Hard delete exitoso.</p>";
        } else {
            echo "<p style='color: red;'>❌ Hard delete falló: " . $db->mysqli->error . "</p>";
        }
        
        // Verificar resultado
        $count_after = $db->fetchOne("SELECT COUNT(*) as total FROM encuestas WHERE did = 0 AND superado = 0 AND elim = 0");
        echo "<p>Encuestas con ID 0 después de la eliminación: " . $count_after['total'] . "</p>";
        
        if ($count_after['total'] == 0) {
            echo "<p style='color: green;'>✅ ¡Éxito! Todas las encuestas con ID 0 han sido eliminadas.</p>";
        } else {
            echo "<p style='color: red;'>❌ Aún quedan " . $count_after['total'] . " encuestas con ID 0.</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ No hay encuestas con ID 0.</p>";
    }
    
    // Mostrar encuestas finales
    echo "<h2>Encuestas finales:</h2>";
    
    $encuestas_finales = $db->fetchAll(
        "SELECT did, nombre, desde, hasta, habilitado 
         FROM encuestas 
         WHERE superado = 0 AND elim = 0 
         ORDER BY did ASC"
    );
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Desde</th><th>Hasta</th><th>Habilitado</th></tr>";
    
    foreach ($encuestas_finales as $enc) {
        echo "<tr>";
        echo "<td>" . $enc['did'] . "</td>";
        echo "<td>" . htmlspecialchars($enc['nombre']) . "</td>";
        echo "<td>" . $enc['desde'] . "</td>";
        echo "<td>" . $enc['hasta'] . "</td>";
        echo "<td>" . ($enc['habilitado'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h2>✅ Proceso completado</h2>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
