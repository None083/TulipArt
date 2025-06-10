<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'Firebase/autoload.php';


// Configuración para Railway y local
define("SERVIDOR_BD", getenv('MYSQLHOST') ?: 'localhost');
define("USUARIO_BD", getenv('MYSQLUSER') ?: 'root');
define("CLAVE_BD", getenv('MYSQLPASSWORD') ?: '');
define("NOMBRE_BD", getenv('MYSQLDATABASE') ?: 'tulipart');
define("PUERTO_BD", getenv('MYSQLPORT') ?: 3306);
define("PASSWORD_API", getenv('PASSWORD_API') ?: "PASSWORD_DE_MI_APLICACION");



function validateToken()
{

    $headers = apache_request_headers();
    if (!isset($headers["Authorization"]))
        return false;
    else {
        $authorization = $headers["Authorization"];
        $authorizationArray = explode(" ", $authorization);
        $token = $authorizationArray[1];
        try {
            $info = JWT::decode($token, new Key(PASSWORD_API, 'HS256'));
        } catch (\Throwable $th) {
            return false;
        }

        try {
            $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        } catch (PDOException $e) {

            $respuesta["error"] = "Imposible conectar:" . $e->getMessage();
            return $respuesta;
        }

        try {
            $consulta = "select * from usuarios where idUsu=?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$info->data]);
        } catch (PDOException $e) {
            $respuesta["error"] = "Imposible realizar la consulta:" . $e->getMessage();
            $sentencia = null;
            $conexion = null;
            return $respuesta;
        }
        if ($sentencia->rowCount() > 0) {
            $respuesta["usuario"] = $sentencia->fetch(PDO::FETCH_ASSOC);

            $payload['exp'] = time() + 3600;
            $payload['data'] = $respuesta["usuario"]["idUsu"];
            $jwt = JWT::encode($payload, PASSWORD_API, 'HS256');
            $respuesta["token"] = $jwt;
        } else
            $respuesta["mensaje_baneo"] = "El usuario no se encuentra registrado en la BD";

        $sentencia = null;
        $conexion = null;
        return $respuesta;
    }
}

function login($usuario, $clave)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "Imposible conectar:" . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "select * from usuarios where nombreUsuario=? and clave=?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$usuario, $clave]);
    } catch (PDOException $e) {
        $respuesta["error"] = "Imposible realizar la consulta:" . $e->getMessage();
        $sentencia = null;
        $conexion = null;
        return $respuesta;
    }

    if ($sentencia->rowCount() > 0) {
        $respuesta["usuario"] = $sentencia->fetch(PDO::FETCH_ASSOC);


        $payload = ['exp' => time() + 3600, 'data' => $respuesta["usuario"]["idUsu"]];
        $jwt = JWT::encode($payload, PASSWORD_API, 'HS256');
        $respuesta["token"] = $jwt;
    } else
        $respuesta["mensaje"] = "El usuario no se encuentra registrado en la BD";


    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_obras()
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de batos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "select * from obras order by idObra desc";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No he podido realizarse la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["obras"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_fotos_obra($idObra)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM fotos WHERE idObra=?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["fotos"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_usuario($idUsu)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM usuarios WHERE idUsu = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["usuario"] = $sentencia->fetch(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_seguidores($idUsu)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM siguen WHERE idSeguido = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["seguidores"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_obras_usuario($idUsu)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM obras WHERE idUsu = ? ORDER BY fecPubli DESC";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["obras_usuario"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_likes_obra($idObra)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM likes WHERE idObra = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["likes_obra"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function dar_like_obra($idUsu, $idObra)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "INSERT IGNORE INTO likes (idUsuLike, idObra) VALUES (?, ?)";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu, $idObra]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["mensaje"] = "Like añadido correctamente";
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function quitar_like_obra($idUsu, $idObra)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "DELETE FROM likes WHERE idUsuLike = ? AND idObra = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu, $idObra]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["mensaje"] = "Like eliminado correctamente";
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_comentarios_obra($idObra)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM comentan WHERE idObra = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["comentarios_obra"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_seguidores_usuario($idUsu)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM siguen WHERE idSeguido = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["seguidores_usuario"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function seguir_usuario($idUsuSeguidor, $idUsuSeguido)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "INSERT IGNORE INTO siguen (idSeguidor, idSeguido) VALUES (?, ?)";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsuSeguidor, $idUsuSeguido]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["mensaje"] = "Usuario seguido correctamente";
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function dejar_seguir_usuario($idUsuSeguidor, $idUsuSeguido)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "DELETE FROM siguen WHERE idSeguidor = ? AND idSeguido = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsuSeguidor, $idUsuSeguido]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["mensaje"] = "Dejado de seguir correctamente";
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_obra($idObra)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM obras WHERE idObra = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    if ($sentencia->rowCount() > 0) {
        $respuesta["obra"] = $sentencia->fetch(PDO::FETCH_ASSOC);
    } else {
        $respuesta["mensaje"] = "Obra no encontrada";
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_etiquetas()
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM etiquetas";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute();
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["etiquetas"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function crear_etiqueta($datos)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "INSERT INTO etiquetas (nombre) VALUES (?)";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute($datos);
        $idEtiqueta = $conexion->lastInsertId(); // Obtener el ID de la etiqueta recién insertada
        $respuesta["mensaje"] = "Etiqueta creada correctamente";
        $respuesta["idEtiqueta"] = $idEtiqueta; // Devolver el ID
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

// relacion etiqueta con obra
function crear_etiqueta_obra($datos)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "INSERT INTO etiquetasobras (idObra, idEtiqueta) VALUES (?, ?)";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute($datos);
        $respuesta["mensaje"] = "Relación creada correctamente";
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function buscar_etiqueta($nombre)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT * FROM etiquetas WHERE nombre = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$nombre]);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["etiquetas"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_imagen_por_id($idFoto) {
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Error de conexión a la base de datos";
        exit;
    }

    try {
        $consulta = "SELECT foto FROM fotos WHERE idFoto = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idFoto]);
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Error al consultar la base de datos";
        exit;
    }

    if (!$resultado) {
        header("HTTP/1.1 404 Not Found");
        echo "Imagen no encontrada";
        exit;
    }

    $rutaArchivo = 'images/obras/' . $resultado['foto'];
    
    if (!file_exists($rutaArchivo)) {
        header("HTTP/1.1 404 Not Found");
        echo "Archivo de imagen no encontrado";
        exit;
    }

    $extension = pathinfo($rutaArchivo, PATHINFO_EXTENSION);
    switch (strtolower($extension)) {
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        default:
            header('Content-Type: application/octet-stream');
    }

    readfile($rutaArchivo);
    exit;
}

function crear_obra_con_imagenes($idUsu, $title, $description, $downloadable, $matureContent, $aiGenerated, $imagenes_temporal) {
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    // iniciar transacción
    $conexion->beginTransaction();

    try {
        // crear obra
        $consulta = "INSERT INTO obras (idUsu, nombreObra, descObra, fecPubli, downloadable, matureContent, aiGenerated) VALUES (?, ?, ?, NOW(), ?, ?, ?)";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu, $title, $description, $downloadable, $matureContent, $aiGenerated]);
        $idObra = $conexion->lastInsertId();
        
        // nombre obra seguro
        $nombreSeguro = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $title));
        $nombreSeguro = preg_replace('/_+/', '_', $nombreSeguro);
        $nombreSeguro = trim($nombreSeguro, '_');
        
        if (empty($nombreSeguro)) {
            $nombreSeguro = "obra"; // si después de limpiar queda vacío
        }
        
        // procesar imágenes temporales
        $contador = 0;
        foreach ($imagenes_temporal as $imagen) {
            $nombreTemporal = $imagen['nombreTemporal'];
            $nombreOriginal = $imagen['nombreOriginal'];
            
            // verificar que el archivo existe
            $rutaTemporal = 'images/temporales/' . $nombreTemporal;
            if (!file_exists($rutaTemporal)) {
                // si una imagen no existe hacer rollback
                $conexion->rollBack();
                $respuesta["error"] = "Una de las imágenes temporales no existe: " . $nombreTemporal;
                return $respuesta;
            }
            
            $extension = pathinfo($nombreTemporal, PATHINFO_EXTENSION);
            
            // crear nuevo nombre imagen: nombreObra_idObra_numero(de imagen en un mismo post).extension
            $nuevoNombre = $nombreSeguro . '_' . $idObra . '_' . $contador . '.' . $extension;

            $rutaDestino = 'images/obras/' . $nuevoNombre;
            
            // crear carpeta si no existe
            if (!file_exists('images/obras/')) {
                mkdir('images/obras/', 0777, true);
            }
            
            // copiar el archivo (no mover aún por evitar problemas si hay error)
            if (!copy($rutaTemporal, $rutaDestino)) {
                $conexion->rollBack();
                $respuesta["error"] = "No se pudo copiar la imagen: " . $nombreTemporal;
                return $respuesta;
            }
            
            // insertar en la bd
            $consulta = "INSERT INTO fotos (idObra, foto) VALUES (?, ?)";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$idObra, $nuevoNombre]);
            
            // +1 al contador para la siguiente imagen
            $contador++;
        }
        
        // si todo ha ido bien confirmar transacción
        $conexion->commit();
        
        // eliminar archivos temporales
        foreach ($imagenes_temporal as $imagen) {
            $rutaTemporal = 'images/temporales/' . $imagen['nombreTemporal'];
            if (file_exists($rutaTemporal)) {
                unlink($rutaTemporal);
            }
        }
        
        $respuesta["mensaje"] = "Obra creada correctamente";
        $respuesta["idObra"] = $idObra;
    } catch (PDOException $e) {
        $conexion->rollBack();
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_comentarios_obra_user($idObra) {
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT c.*, u.nombreUsuario, u.fotoPerfil FROM comentan c
                    JOIN usuarios u ON c.idUsu = u.idUsu
                    WHERE c.idObra = ? order by c.numComentario desc";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);
    } catch (PDOException $e) {
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
    }

    if ($sentencia->rowCount() > 0) {
        $respuesta["comentarios_info"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $respuesta["mensaje"] = "No hay comentarios para esta obra.";
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function crear_comentario_obra($idUsu, $idObra, $comentario)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "INSERT INTO comentan (idUsu, idObra, textoComentario, fecCom, horaCom) VALUES (?, ?, ?, NOW(), CURTIME())";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu, $idObra, $comentario]);
        $respuesta["mensaje"] = "Comentario creado correctamente";
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function eliminar_comentario_obra($idComentario) {
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "DELETE FROM comentan WHERE numComentario = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idComentario]);
        $respuesta["mensaje"] = "Comentario eliminado correctamente";
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function editar_comentario_obra($idComentario, $nuevoComentario) {
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "UPDATE comentan SET textoComentario = ? WHERE numComentario = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$nuevoComentario, $idComentario]);
        $respuesta["mensaje"] = "Comentario editado correctamente";
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

// borrar obra, fotos asociadas y relación etiquetasobras
function borrar_obra($idObra) {
    $respuesta = array();

    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "DELETE FROM fotos WHERE idObra = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);

        $consulta = "DELETE FROM etiquetasobras WHERE idObra = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);

        $consulta = "DELETE FROM obras WHERE idObra = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);

        $respuesta["mensaje"] = "Obra borrada correctamente";
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_alertas_seguidores($idUsu) {
    $respuesta = array();

    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT usuarios.nombreUsuario, usuarios.idUsu FROM usuarios join siguen on usuarios.idUsu = siguen.idSeguidor WHERE siguen.idSeguido = ? AND siguen.visto = 0";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu]);
        $respuesta["alertas_seguidores"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_alertas_likes($idUsu) {
    $respuesta = array();

    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT u.nombreUsuario AS usuario_like, o.nombreObra AS nombre_obra, l.idObra, l.idUsuLike
             FROM likes l
             JOIN usuarios u ON l.idUsuLike = u.idUsu
             JOIN obras o ON l.idObra = o.idObra
             WHERE o.idUsu = ? AND l.visto = 0";
        
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu]);
        $respuesta["alertas_likes"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_alertas_comentarios($idUsu) {
    $respuesta = array();

    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT u.nombreUsuario AS usuario_comentario, o.nombreObra AS nombre_obra, c.idObra, c.numComentario, u.idUsu
             FROM comentan c
             JOIN usuarios u ON c.idUsu = u.idUsu
             JOIN obras o ON c.idObra = o.idObra
             WHERE o.idUsu = ? AND c.visto = 0";
        
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idUsu]);
        $respuesta["alertas_comentarios"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function marcar_alerta_follow_visto($idSeguido, $idSeguidor) {
    $respuesta = array();

    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "UPDATE siguen SET visto = 1 WHERE idSeguido = ? AND idSeguidor = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idSeguido, $idSeguidor]);
        $respuesta["mensaje"] = "Alerta de seguimiento marcada como vista";
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function marcar_alerta_like_visto($idObra, $idUsu) {
    $respuesta = array();

    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "UPDATE likes SET visto = 1 WHERE idObra = ? AND idUsuLike = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra, $idUsu]);
        $respuesta["mensaje"] = "Alerta de like marcada como vista";
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function marcar_alerta_comentario_visto($idComentario) {
    $respuesta = array();

    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "UPDATE comentan SET visto = 1 WHERE numComentario = ?";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idComentario]);
        $respuesta["mensaje"] = "Alerta de comentario marcada como vista";
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $sentencia = null;
    $conexion = null;
    return $respuesta;
}
