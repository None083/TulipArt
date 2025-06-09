<?php
// Función auxiliar para contar el total de registros para paginación
function contar_total_obras($conexion, $filtro = "", $valor = "") {
    // Construir la consulta SQL base
    $sql = "SELECT COUNT(DISTINCT o.idObra) FROM obras o";
    $parametros = [];
    
    // Aplicar filtros según los parámetros recibidos
    if ($filtro && $valor) {
        switch ($filtro) {
            case "buscar":
                // Búsqueda por título o por etiquetas
                $sql .= " LEFT JOIN etiquetasobras eo ON o.idObra = eo.idObra 
                           LEFT JOIN etiquetas e ON eo.idEtiqueta = e.idEtiqueta 
                           WHERE o.nombreObra LIKE ? OR e.nombre LIKE ?";
                $parametros[] = "%$valor%";
                $parametros[] = "%$valor%";
                break;
            case "etiqueta":
                // Filtrar por etiqueta específica
                $sql .= " INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra 
                           INNER JOIN etiquetas e ON eo.idEtiqueta = e.idEtiqueta 
                           WHERE e.nombre = ?";
                $parametros[] = $valor;
                break;
            case "usuario":
                // Filtrar por usuario específico
                $sql .= " WHERE o.idUsu = ?";
                $parametros[] = $valor;
                break;
            case "siguiendo":
                // Obras de usuarios que sigue el usuario logueado
                $sql .= " INNER JOIN siguen s ON o.idUsu = s.idSeguido 
                           WHERE s.idSeguidor = ?";
                $parametros[] = $valor;
                break;
            case "for_you":
                // Obras con etiquetas similares a las obras que ha dado like el usuario
                $sql .= " INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra
                          WHERE eo.idEtiqueta IN (
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

// Reemplaza la función obtener_obras() existente
function obtener_obras_filtradas($filtro = "", $valor = "", $ordenar = "", $pagina = 1, $limite = 20)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No se ha podido conectar a la base de datos: " . $e->getMessage();
        return $respuesta;
    }
    
    // Cálculo del offset para paginación
    $offset = ($pagina - 1) * $limite;
    
    // Construir la consulta SQL base
    $sql = "SELECT DISTINCT o.* FROM obras o";
    $parametros = [];
    
    // Aplicar filtros según los parámetros recibidos
    if ($filtro && $valor) {
        switch ($filtro) {
            case "buscar":
                // Búsqueda por título o por etiquetas
                $sql .= " LEFT JOIN etiquetasobras eo ON o.idObra = eo.idObra 
                           LEFT JOIN etiquetas e ON eo.idEtiqueta = e.idEtiqueta 
                           WHERE o.nombreObra LIKE ? OR e.nombre LIKE ?";
                $parametros[] = "%$valor%";
                $parametros[] = "%$valor%";
                break;
            case "etiqueta":
                // Filtrar por etiqueta específica
                $sql .= " INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra 
                           INNER JOIN etiquetas e ON eo.idEtiqueta = e.idEtiqueta 
                           WHERE e.nombre = ?";
                $parametros[] = $valor;
                break;
            case "usuario":
                // Filtrar por usuario específico
                $sql .= " WHERE o.idUsu = ?";
                $parametros[] = $valor;
                break;
            case "siguiendo":
                // Obras de usuarios que sigue el usuario logueado
                $sql .= " INNER JOIN siguen s ON o.idUsu = s.idSeguido 
                           WHERE s.idSeguidor = ?";
                $parametros[] = $valor;
                break;
            case "for_you":
                // Obras con etiquetas similares a las obras que ha dado like el usuario
                $sql .= " INNER JOIN etiquetasobras eo ON o.idObra = eo.idObra
                          WHERE eo.idEtiqueta IN (
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
    
    // Aplicar ordenación
    if ($ordenar) {
        switch ($ordenar) {
            case "recientes":
                $sql .= " ORDER BY o.fecPubli DESC";
                break;
            case "trending":
                $sql .= " LEFT JOIN (SELECT idObra, COUNT(*) as num_likes 
                           FROM likes GROUP BY idObra) l ON o.idObra = l.idObra 
                           ORDER BY COALESCE(l.num_likes, 0) DESC, o.fecPubli DESC";
                break;
            default:
                $sql .= " ORDER BY o.fecPubli DESC"; // Por defecto, ordenar por fecha descendente
        }
    } else {
        $sql .= " ORDER BY o.fecPubli DESC"; // Por defecto, ordenar por fecha descendente
    }    // Aplicar límite para paginación - usar valores directamente en el SQL en lugar de parámetros
    $limite = (int)$limite;  // Convertir explícitamente a entero
    $offset = (int)$offset;  // Convertir explícitamente a entero
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

// Actualización de obtener_obras_usuario para soportar ordenamiento
function obtener_obras_usuario_filtradas($idUsu, $ordenar = "")
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    } catch (PDOException $e) {
        $respuesta["error"] = "No ha podido conectarse a la base de datos: " . $e->getMessage();
        return $respuesta;
    }

    $sql = "SELECT * FROM obras WHERE idUsu = ?";
    $parametros = [$idUsu];
    
    // Aplicar ordenación
    if ($ordenar) {
        switch ($ordenar) {
            case "recientes":
                $sql .= " ORDER BY fecPubli DESC";
                break;
            case "trending":
                $sql .= " LEFT JOIN (SELECT idObra, COUNT(*) as num_likes 
                           FROM likes GROUP BY idObra) l ON obras.idObra = l.idObra 
                           ORDER BY COALESCE(l.num_likes, 0) DESC, fecPubli DESC";
                break;
            default:
                $sql .= " ORDER BY fecPubli DESC"; // Por defecto, ordenar por fecha descendente
        }
    } else {
        $sql .= " ORDER BY fecPubli DESC"; // Por defecto, ordenar por fecha descendente
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
