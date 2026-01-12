<?php
// Configuración de la base de datos
$host = '127.0.0.1';
$dbname = 'taguay_BdSistema';
$username = 'taguay_Usuario';
$password = 'Taguay2552.';

try {
    // Crear una nueva conexión PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Configurar el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mensaje de confirmación
    echo "Conexión a la base de datos exitosa!";
    
} catch(PDOException $e) {
    // En caso de error, mostrar el mensaje de error
    echo "Error de conexión: " . $e->getMessage();
}
?>