<?php
// Script de verificación para el sistema de wallet
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->connect();
    
    echo "<h2>Verificación del Sistema Wallet</h2>";
    
    // Verificar conexión
    echo "<p>✅ Conexión a base de datos: OK</p>";
    
    // Verificar estructura de tabla cash_wallet
    $stmt = $conn->prepare("DESCRIBE cash_wallet");
    $stmt->execute();
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Estructura de tabla cash_wallet:</h3>";
    echo "<ul>";
    foreach ($columnas as $columna) {
        echo "<li>{$columna['Field']} - {$columna['Type']}</li>";
    }
    echo "</ul>";
    
    // Verificar si existe la tabla saldo_usuarios
    $stmt = $conn->prepare("SHOW TABLES LIKE 'saldo_usuarios'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabla saldo_usuarios: Existe</p>";
    } else {
        echo "<p>❌ Tabla saldo_usuarios: No existe</p>";
    }
    
    // Contar transacciones
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM cash_wallet");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>📊 Total transacciones en cash_wallet: {$result['total']}</p>";
    
    // Mostrar algunas transacciones de ejemplo
    $stmt = $conn->prepare("SELECT * FROM cash_wallet ORDER BY fecha_log DESC LIMIT 5");
    $stmt->execute();
    $transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Últimas 5 transacciones:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Monto</th><th>Tipo</th><th>Fecha</th></tr>";
    foreach ($transacciones as $trans) {
        echo "<tr>";
        echo "<td>{$trans['id']}</td>";
        echo "<td>{$trans['id_user']}</td>";
        echo "<td>{$trans['monto']}</td>";
        echo "<td>{$trans['tipo']}</td>";
        echo "<td>{$trans['fecha_log']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?> 