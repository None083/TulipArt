<?php
// Obtener variables de entorno de Railway
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Conectar a la base de datos
try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    echo "Conexión exitosa a la base de datos.\n";
    
    // Leer el archivo SQL
    $sql = file_get_contents(__DIR__ . '/tulipart.sql');
    
    // Ejecutar el SQL
    if ($conn->multi_query($sql)) {
        echo "Base de datos importada con éxito.\n";
        
        // Vaciar resultados para liberar memoria
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    } else {
        echo "Error al importar: " . $conn->error . "\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}