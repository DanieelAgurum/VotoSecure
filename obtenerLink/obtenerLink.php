<?php
function getBaseUrl($folder = 'VotoSecure')
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];

    // Buscar insensible a mayúsculas
    $pos = stripos($uri, '/' . $folder);
    if ($pos !== false) {
        // Extraer la ruta hasta la carpeta, sin importar mayúsculas
        $basePath = substr($uri, 0, $pos + strlen($folder) + 1);

        // Asegurar que termine con '/'
        if (substr($basePath, -1) !== '/') {
            $basePath .= '/';
        }

        return $protocol . '://' . $host . $basePath;
    } else {
        return $protocol . '://' . $host . '/';
    }
}
?>
