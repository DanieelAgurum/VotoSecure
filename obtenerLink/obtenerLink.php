<?php 
function getBaseUrl($folder = 'votosecure') {

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];

    // Buscar la carpeta del proyecto sin importar mayúsculas
    $pos = stripos($scriptName, '/' . $folder);

    if ($pos !== false) {
        $basePath = substr($scriptName, 0, $pos + strlen($folder) + 1);
        return $protocol . '://' . $host . $basePath;
    }

    return $protocol . '://' . $host . '/';
}
?>