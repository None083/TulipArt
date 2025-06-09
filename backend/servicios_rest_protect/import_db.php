<?php
// Obtener variables de entorno de Railway
echo "MYSQLHOST: " . getenv('MYSQLHOST') . "\n";
echo "MYSQLUSER: " . getenv('MYSQLUSER') . "\n";
echo "MYSQLPASSWORD: " . getenv('MYSQLPASSWORD') . "\n";
echo "MYSQLDATABASE: " . getenv('MYSQLDATABASE') . "\n";
echo "MYSQLPORT: " . getenv('MYSQLPORT') . "\n";
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Para debugging - Mostrar valores (comentar en producción)
echo "Intentando conectar a la base de datos...\n";
// Verificar si el archivo SQL existe
$sqlFilePath = __DIR__ . '/tulipart.sql';
if (!file_exists($sqlFilePath)) {
    die("Error: El archivo SQL no se encuentra en la ruta: " . $sqlFilePath . "\n");
}
echo "Archivo SQL encontrado en la ruta: " . $sqlFilePath . "\n";
echo "Host: " . $host . "\n";
echo "Usuario: " . $user . "\n";
echo "Base de datos: " . $db . "\n";
echo "Puerto: " . $port . "\n";

// Conectar a la base de datos
try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    echo "Conexión exitosa a la base de datos.\n";
    
    // Leer el archivo SQL - Actualizada la ruta
    $sql = file_get_contents(__DIR__ . '/tulipart.sql');
    
    // Configurar el charset para la conexión
    $conn->set_charset("utf8mb4");
    
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