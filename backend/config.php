<?php
// Archivo de configuración global para URLs y entorno

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    // Si estamos en localhost, añade la ruta del proyecto
    if ($host === 'localhost') {
        return $protocol . $host . '/TulipArt';
    }
    // En producción, simplemente usa la raíz del dominio
    return $protocol . $host;
}

define('BASE_URL', getBaseUrl());
define('API_URL', BASE_URL . '/backend/servicios_rest_protect');
