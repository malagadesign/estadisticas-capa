<?php
/**
 * Script para probar el hashing de contraseñas
 */

$password = 'malaga77';

echo "<h1>🔐 Prueba de Hashing de Contraseñas</h1>";
echo "<p><strong>Contraseña:</strong> {$password}</p>";

// Generar hash
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "<p><strong>Hash generado:</strong> {$hash}</p>";
echo "<p><strong>Longitud del hash:</strong> " . strlen($hash) . " caracteres</p>";

// Verificar el hash
$isValid = password_verify($password, $hash);
echo "<p><strong>¿Hash verifica correctamente?</strong> " . ($isValid ? '✅ SÍ' : '❌ NO') . "</p>";

// Simular hash truncado (como está en la BD)
$truncatedHash = substr($hash, 0, 20);
echo "<hr>";
echo "<h2>Problema detectado:</h2>";
echo "<p><strong>Hash truncado (primeros 20 caracteres):</strong> {$truncatedHash}...</p>";
echo "<p><strong>¿Hash truncado verifica correctamente?</strong> ";
$isValidTruncated = password_verify($password, $truncatedHash);
echo ($isValidTruncated ? '✅ SÍ' : '❌ NO') . "</p>";

// Probar con el hash que viste
$hashFromDB = '$2y$10$waraxnnmTGttQYRuzi';
echo "<hr>";
echo "<h2>Hash desde la BD:</h2>";
echo "<p><strong>Hash en BD:</strong> {$hashFromDB}</p>";
echo "<p><strong>Longitud:</strong> " . strlen($hashFromDB) . " caracteres (debería ser 60)</p>";
echo "<p><strong>¿Este hash verifica con 'malaga77'?</strong> ";
$isValidDB = password_verify($password, $hashFromDB);
echo ($isValidDB ? '✅ SÍ' : '❌ NO') . "</p>";

if (!$isValidDB) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3>❌ Problema identificado:</h3>";
    echo "<p>El hash en la BD está truncado. Esto indica que:</p>";
    echo "<ul>";
    echo "<li>La columna <code>psw</code> probablemente tiene un límite de caracteres muy pequeño (ej: VARCHAR(20))</li>";
    echo "<li>O el hash se está cortando al insertarlo en la BD</li>";
    echo "</ul>";
    echo "<p><strong>Solución:</strong> Verificar la estructura de la tabla y aumentar el tamaño de la columna <code>psw</code> a <code>VARCHAR(255)</code></p>";
    echo "</div>";
}

