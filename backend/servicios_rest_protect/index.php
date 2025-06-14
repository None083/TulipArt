<?php
// cors
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ocultar avisos deprecados y warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

// crear estructura carpetas si no existe
$directorios = [
    'images',
    'images/temporales',
    'images/obras'
];

foreach ($directorios as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

require __DIR__ . '/Slim/autoload.php';
require "src/funciones_CTES_servicios.php";
require "src/funciones_filtros.php";
require "src/funciones_etiquetas.php";


$app = new \Slim\App;

$app->get('/', function () {
    echo "Bienvenido a TulipArt!";
});

$app->get('/logueado', function () {
    $test = validateToken();
    if (is_array($test))
        echo json_encode($test);
    else
        echo json_encode(array("no_auth" => "No tienes permisos para usar este servicio"));
});

$app->post('/login', function ($request) {

    $usuario = $request->getParam("username");
    $clave = $request->getParam("password");
    echo json_encode(login($usuario, $clave));
});

$app->get('/obras', function ($request) {
    $filtro = $request->getQueryParam("filtro", "");
    $valor = $request->getQueryParam("valor", "");
    $ordenar = $request->getQueryParam("ordenar", "");
    $pagina = (int)$request->getQueryParam("pagina", 1);
    $limite = (int)$request->getQueryParam("limite", 20);

    echo json_encode(obtener_obras_filtradas($filtro, $valor, $ordenar, $pagina, $limite));
});

$app->get('/obras/{idUsu}', function ($request) {
    $idUsu = $request->getAttribute("idUsu");
    $ordenar = $request->getQueryParam("ordenar", "");
    echo json_encode(obtener_obras_usuario_filtradas($idUsu, $ordenar));
});

$app->get('/obra/{idObra}', function ($request) {
    $idObra = $request->getAttribute("idObra");
    echo json_encode(obtener_obra($idObra));
});

$app->get('/fotos_obra/{idObra}', function ($request) {
    $idObra = $request->getAttribute("idObra");
    echo json_encode(obtener_fotos_obra($idObra));
});

$app->get('/obtener_imagen/{idFoto}', function ($request) {
    $idFoto = $request->getAttribute('idFoto');
    obtener_imagen_por_id($idFoto);
});

$app->post('/crear_obra_con_imagenes', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $idUsu = $request->getParam("idUsu");
            $title = $request->getParam("title");
            $description = $request->getParam("description");
            $downloadable = $request->getParam("downloadable");
            $matureContent = $request->getParam("matureContent");
            $aiGenerated = $request->getParam("aiGenerated");
            $imagenes_temporal = json_decode($request->getParam("imagenes_temporal"), true);

            // verificar datos obligatorios
            if (!$idUsu || !$title || !$description || !$imagenes_temporal) {
                echo json_encode(["error" => "Faltan datos obligatorios"]);
                return;
            }

            $resultado = crear_obra_con_imagenes($idUsu, $title, $description, $downloadable, $matureContent, $aiGenerated, $imagenes_temporal);

            echo json_encode($resultado);
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->post('/subir_imagen_temporal', function ($request) {

    if (!isset($_FILES['file'])) {
        echo json_encode(["error" => "No se ha enviado ningún archivo"]);
        return;
    }

    $archivo = $_FILES['file'];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $respuesta["error"] = "Error al subir el archivo";
        echo json_encode($respuesta);
        return;
    }

    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($archivo['type'], $tiposPermitidos)) {
        echo json_encode(["error" => "El archivo debe ser una imagen (JPEG, PNG o GIF)"]);
        return;
    }

    // nombre único foto temporal
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid() . '.' . $extension;

    $rutaDestino = 'images/temporales/' . $nombreArchivo;

    // crear carpeta si no existe
    if (!file_exists('images/temporales/')) {
        mkdir('images/temporales/', 0777, true);
    }

    // mover el archivo
    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        echo json_encode(["error" => "No se pudo guardar el archivo"]);
        return;
    }

    $urlBase = "https://tulipart-production.up.railway.app/images/temporales/";
    $urlTemporal = $urlBase . $nombreArchivo;

    $respuesta = [
        "mensaje" => "Imagen subida temporalmente",
        "nombreTemporal" => $nombreArchivo,
        "nombreOriginal" => $archivo['name'],
        "urlTemporal" => $urlTemporal
    ];

    echo json_encode($respuesta);
});

$app->post('/limpiar_imagenes_temporales', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $imagenesJson = $request->getParam('imagenes');
            if (!$imagenesJson) {
                echo json_encode(["error" => "No se recibieron datos de imágenes"]);
                return;
            }

            $imagenes = json_decode($imagenesJson, true);
            if (!is_array($imagenes)) {
                echo json_encode(["error" => "Formato de datos incorrecto"]);
                return;
            }

            $eliminadas = 0;

            foreach ($imagenes as $imagen) {
                if (isset($imagen['nombreTemporal'])) {
                    $rutaArchivo = 'images/temporales/' . $imagen['nombreTemporal'];
                    if (file_exists($rutaArchivo)) {
                        if (unlink($rutaArchivo)) {
                            $eliminadas++;
                        }
                    }
                }
            }

            echo json_encode([
                "mensaje" => "Proceso completado",
                "eliminadas" => $eliminadas
            ]);
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->get('/usuario/{idUsu}', function ($request) {
    $idUsu = $request->getAttribute("idUsu");
    echo json_encode(obtener_usuario($idUsu));
});

$app->get('/seguidores/{idUsu}', function ($request) {
    $idUsu = $request->getAttribute("idUsu");
    echo json_encode(obtener_seguidores($idUsu));
});

//obtener likes obra
$app->get('/likes_obra/{idObra}', function ($request) {
    $idObra = $request->getAttribute("idObra");
    echo json_encode(obtener_likes_obra($idObra));
});

$app->post('/dar_like_obra', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $idUsu = $request->getParam("idUsu");
            $idObra = $request->getParam("idObra");
            echo json_encode(dar_like_obra($idUsu, $idObra));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->delete('/quitar_like_obra/{idUsu}/{idObra}', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $idUsu = $request->getAttribute("idUsu");
            $idObra = $request->getAttribute("idObra");
            echo json_encode(quitar_like_obra($idUsu, $idObra));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->get('/seguidores_usuario/{idUsu}', function ($request) {
    $idUsu = $request->getAttribute("idUsu");
    echo json_encode(obtener_seguidores_usuario($idUsu));
});

$app->post('/seguir_usuario', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $idUsuSeguidor = $request->getParam("idUsuSeguidor");
            $idUsuSeguido = $request->getParam("idUsuSeguido");
            echo json_encode(seguir_usuario($idUsuSeguidor, $idUsuSeguido));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->delete('/dejar_seguir_usuario/{idUsuSeguidor}/{idUsuSeguido}', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $idUsuSeguidor = $request->getAttribute("idUsuSeguidor");
            $idUsuSeguido = $request->getAttribute("idUsuSeguido");
            echo json_encode(dejar_seguir_usuario($idUsuSeguidor, $idUsuSeguido));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->get('/comentarios_obra/{idObra}', function ($request) {
    $idObra = $request->getAttribute("idObra");
    echo json_encode(obtener_comentarios_obra($idObra));
});

$app->get('/etiquetas', function () {
    echo json_encode(obtener_etiquetas());
});

$app->get('/etiquetas_obra/{idObra}', function ($request) {
    $idObra = $request->getAttribute('idObra');
    echo json_encode(obtener_etiquetas_obra($idObra));
});

$app->get('/obras_por_etiquetas', function ($request) {
    $etiquetas = $request->getQueryParam('etiquetas');
    $excluir = $request->getQueryParam('excluir');
    echo json_encode(obtener_obras_por_etiquetas($etiquetas, $excluir));
});

$app->post('/crear_etiqueta', function ($request) {
    $test = validateToken();
    //echo json_encode($test);
    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $datos = array();
            $datos[] = $request->getParam("nombre");
            echo json_encode(crear_etiqueta($datos));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

// crear relacion etiqueta obra
$app->post('/crear_etiqueta_obra', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $datos = array();
            $datos[] = $request->getParam("idObra");
            $datos[] = $request->getParam("idEtiqueta");
            echo json_encode(crear_etiqueta_obra($datos));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->get('/buscar_etiqueta/{nombre}', function ($request) {
    $nombre = $request->getAttribute('nombre');
    echo json_encode(buscar_etiqueta($nombre));
});

$app->get('/comentarios_obra_user/{idObra}', function ($request) {
    $idObra = $request->getAttribute('idObra');
    echo json_encode(obtener_comentarios_obra_user($idObra));
});

$app->post('/crear_comentario_obra', function ($request) {
    $test = validateToken();
    //echo json_encode($test);
    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $datos = array();
            $datos[] = $request->getParam("idUsu");
            $datos[] = $request->getParam("idObra");
            $datos[] = $request->getParam("textoComentario");
            echo json_encode(crear_comentario_obra($datos[0], $datos[1], $datos[2]));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->delete('/eliminar_comentario_obra/{idComentario}', function ($request) {
    $test = validateToken();
    //echo json_encode($test);
    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $idComentario = $request->getAttribute('idComentario');
            echo json_encode(eliminar_comentario_obra($idComentario));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->put('/editar_comentario_obra/{idComentario}', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $idComentario = $request->getAttribute('idComentario');
            $nuevoTexto = $request->getParam('new-comment');
            if (!$nuevoTexto) {
                echo json_encode(array("error" => "El texto del comentario no puede estar vacío"));
                return;
            }
            echo json_encode(editar_comentario_obra($idComentario, $nuevoTexto));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

// borrar obra, fotos y ralación etiquetasobras
$app->delete('/borrar_obra/{idObra}', function ($request) {
    $test = validateToken();

    if (is_array($test)) {
        if (isset($test["usuario"])) {
            $idObra = $request->getAttribute('idObra');
            echo json_encode(borrar_obra($idObra));
        } else
            echo json_encode($test);
    } else
        echo json_encode(array("no_auth" => "No tienes permiso para usar el servicio"));
});

$app->get('/alertas_seguidores/{idUsu}', function ($request) {
    $idUsu = $request->getAttribute("idUsu");
    echo json_encode(obtener_alertas_seguidores($idUsu));
});

$app->get('/alertas_likes/{idUsu}', function ($request) {
    $idUsu = $request->getAttribute("idUsu");
    echo json_encode(obtener_alertas_likes($idUsu));
});

$app->get('/alertas_comentarios/{idUsu}', function ($request) {
    $idUsu = $request->getAttribute("idUsu");
    echo json_encode(obtener_alertas_comentarios($idUsu));
});

// editar alerta follow vista
$app->put('/alerta_follow_visto/{idSeguido}/{idSeguidor}', function ($request) {
    $idSeguido = $request->getAttribute("idSeguido");
    $idSeguidor = $request->getAttribute("idSeguidor");
    echo json_encode(marcar_alerta_follow_visto($idSeguido, $idSeguidor));
});

// editar alerta like vista
$app->put('/alerta_like_visto/{idObra}/{idUsuLike}', function ($request) {
    $idObra = $request->getAttribute("idObra");
    $idUsuLike = $request->getAttribute("idUsuLike");
    echo json_encode(marcar_alerta_like_visto($idObra, $idUsuLike));
});

// editar alerta comentario vista
$app->put('/alerta_comentario_visto/{idComentario}', function ($request) {
    $idComentario = $request->getAttribute("idComentario");
    echo json_encode(marcar_alerta_comentario_visto($idComentario));
});

$app->run();
