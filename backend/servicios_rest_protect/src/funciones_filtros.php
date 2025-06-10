<?php
// contar total de obras para paginación
function contar_total_obras($conexion, $filtro = "", $valor = "") {

    $sql = "SELECT COUNT(DISTINCT o.idObra) FROM obras o";
    $parametros = [];

    // joins según el filtro
    if ($filtro && $valor) {
        switch ($filtro) {
            case "buscar":
                $sql .= " LEFT JOIN etiquetasobras eo ON o.idObra = eo.idObra 
                           LEFT JOIN etiquetas e ON eo.idEtiqueta = e.idEtiqueta";
                break;
            case "etiqueta":
                $sql .= " INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra 
                           INNER JOIN etiquetas e ON eo.idEtiqueta = e.idEtiqueta";
                break;
            case "siguiendo":
                $sql .= " INNER JOIN siguen s ON o.idUsu = s.idSeguido";
                break;
            case "for_you":
                $sql .= " INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra";
                break;
        }
    }
    
    // condiciones where según el filtro
    if ($filtro && $valor) {
        switch ($filtro) {
            case "buscar":
                $sql .= " WHERE o.nombreObra LIKE ? OR e.nombre LIKE ?";
                $parametros[] = "%$valor%";
                $parametros[] = "%$valor%";
                break;
            case "etiqueta":
                $sql .= " WHERE e.nombre = ?";
                $parametros[] = $valor;
                break;
            case "usuario":
                $sql .= " WHERE o.idUsu = ?";
                $parametros[] = $valor;
                break;
            case "siguiendo":
                $sql .= " WHERE s.idSeguidor = ?";
                $parametros[] = $valor;
                break;
            case "for_you":
                $sql .= " WHERE eo.idEtiqueta IN (
                              SELECT DISTINCT e.idEtiqueta FROM etiquetas e
                              INNER JOIN etiquetasobras eo ON e.idEtiqueta = eo.idEtiqueta
                              INNER JOIN obras o ON eo.idObra = o.idObra
                              INNER JOIN likes l ON o.idObra = l.idObra
                              WHERE l.idUsuLike = ?
                          ) AND o.idObra NOT IN (
                              SELECT idObra FROM likes WHERE idUsuLike = ?
                          )";
                $parametros[] = $valor;
                $parametros[] = $valor;
                break;
        }
    }
    
    try {
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute($parametros);
        return $sentencia->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function obtener_obras_filtradas($filtro = "", $valor = "", $ordenar = "", $pagina = 1, $limite = 20)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se ha podido conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }
    
    // cálculo offset para paginación
    $offset = ($pagina - 1) * $limite;
    
    // consulta sql base
    // para trending incluye num_likes para poder ordenar por ese campo
    if ($ordenar === "trending") {
        $sql = "SELECT DISTINCT o.*, COALESCE(l.num_likes, 0) as num_likes FROM obras o";
    } else {
        $sql = "SELECT DISTINCT o.* FROM obras o";
    }
    $parametros = [];
    
    // joins según el filtro
    if ($filtro && $valor) {
        switch ($filtro) {
            case "buscar":
                $sql .= " LEFT JOIN etiquetasobras eo ON o.idObra = eo.idObra 
                          LEFT JOIN etiquetas e ON eo.idEtiqueta = e.idEtiqueta";
                break;
            case "etiqueta":
                $sql .= " INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra 
                          INNER JOIN etiquetas e ON eo.idEtiqueta = e.idEtiqueta";
                break;
            case "siguiendo":
                $sql .= " INNER JOIN siguen s ON o.idUsu = s.idSeguido";
                break;
            case "for_you":
                $sql .= " INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra";
                break;
        }
    }

    if ($ordenar === "trending") {
        $sql .= " LEFT JOIN (SELECT idObra, COUNT(*) as num_likes 
                  FROM likes GROUP BY idObra) l ON o.idObra = l.idObra";
    }
    
    // where según el filtro
    if ($filtro && $valor) {
        switch ($filtro) {
            case "buscar":
                $sql .= " WHERE o.nombreObra LIKE ? OR e.nombre LIKE ?";
                $parametros[] = "%$valor%";
                $parametros[] = "%$valor%";
                break;
            case "etiqueta":
                $sql .= " WHERE e.nombre = ?";
                $parametros[] = $valor;
                break;
            case "usuario":
                $sql .= " WHERE o.idUsu = ?";
                $parametros[] = $valor;
                break;
            case "siguiendo":
                $sql .= " WHERE s.idSeguidor = ?";
                $parametros[] = $valor;
                break;
            case "for_you":
                $sql .= " WHERE eo.idEtiqueta IN (
                              SELECT DISTINCT e.idEtiqueta FROM etiquetas e
                              INNER JOIN etiquetasobras eo ON e.idEtiqueta = eo.idEtiqueta
                              INNER JOIN obras o2 ON eo.idObra = o2.idObra
                              INNER JOIN likes l ON o2.idObra = l.idObra
                              WHERE l.idUsuLike = ?
                          ) AND o.idObra NOT IN (
                              SELECT idObra FROM likes WHERE idUsuLike = ?
                          )";
                $parametros[] = $valor;
                $parametros[] = $valor;
                break;
        }
    }
    
    // ordenación
    if ($ordenar) {
        switch ($ordenar) {
            case "recientes":
                $sql .= " ORDER BY o.fecPubli DESC";
                break;
            case "trending":
                $sql .= " ORDER BY num_likes DESC, o.fecPubli DESC";
                break;
            default:
                $sql .= " ORDER BY o.fecPubli DESC";
        }
    } else {
        $sql .= " ORDER BY o.fecPubli DESC";
    }
    
    // límite paginación
    $limite = (int)$limite;
    $offset = (int)$offset;
    $sql .= " LIMIT $limite OFFSET $offset";
    
    try {
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute($parametros);
    } catch (PDOException $e) {
        $respuesta["error"] = "Error al ejecutar la consulta: " . $e->getMessage();
        return $respuesta;
    }

    $respuesta["obras"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    $respuesta["total_registros"] = contar_total_obras($conexion, $filtro, $valor);
    $respuesta["pagina_actual"] = $pagina;
    $respuesta["registros_por_pagina"] = $limite;
    $respuesta["total_paginas"] = ceil($respuesta["total_registros"] / $limite);
    
    $sentencia = null;
    $conexion = null;
    return $respuesta;
}

function obtener_obras_usuario_filtradas($idUsu, $ordenar = "")
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    // Para trending, incluimos num_likes en el SELECT
    if ($ordenar === "trending") {
        $sql = "SELECT o.*, COALESCE(l.num_likes, 0) as num_likes 
                FROM obras o 
                LEFT JOIN (SELECT idObra, COUNT(*) as num_likes FROM likes GROUP BY idObra) l 
                ON o.idObra = l.idObra 
                WHERE o.idUsu = ?";
    } else {
        $sql = "SELECT * FROM obras WHERE idUsu = ?";
    }
    
    $parametros = [$idUsu];

    if ($ordenar) {
        switch ($ordenar) {
            case "recientes":
                $sql .= " ORDER BY fecPubli DESC";
                break;
            case "trending":
                $sql .= " ORDER BY num_likes DESC, fecPubli DESC";
                break;
            default:
                $sql .= " ORDER BY fecPubli DESC";
        }
    } else {
        $sql .= " ORDER BY fecPubli DESC";
    }

    try {
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute($parametros);
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
