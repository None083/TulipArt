<?php

function obtener_etiquetas_obra($idObra)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        $consulta = "SELECT e.* FROM etiquetas e
                    INNER JOIN etiquetasobras eo ON e.idEtiqueta = eo.idEtiqueta
                    WHERE eo.idObra = ?
                    ORDER BY e.nombre";
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute([$idObra]);
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

// buscar obras que comparten etiquetas con otra obra
function obtener_obras_por_etiquetas($etiquetas, $idObraExcluir = null)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    try {
        if (!is_array($etiquetas)) {
            $etiquetas = explode(',', $etiquetas);
        }

        if (empty($etiquetas)) {
            $respuesta["obras"] = [];
            return $respuesta;
        }

        $placeholders = implode(',', array_fill(0, count($etiquetas), '?'));

        $consulta = "SELECT DISTINCT o.* FROM obras o
                    INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra
                    WHERE eo.idEtiqueta IN ($placeholders)";

        $params = $etiquetas;
        
        // excluir obra actual
        if ($idObraExcluir !== null) {
            $consulta .= " AND o.idObra != ?";
            $params[] = $idObraExcluir;
        }

        $consulta .= " ORDER BY o.fecPubli DESC LIMIT 10";
        
        $sentencia = $conexion->prepare($consulta);
        $sentencia->execute($params);
    } catch (PDOException $e) {
        $sentencia = null;
        $conexion = null;
        $respuesta["error"] = "No se ha podido realizar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["obras"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}
?>
