<?php

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$query = $_SERVER['QUERY_STRING'] ?? '';

// Gérer les fichiers existants
$file = __DIR__.'/public'.$path;
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false; // laisse PHP gérer les fichiers statiques
}

// Supprimer les trailing slashes (sauf racine)
if ($path !== '/' && str_ends_with($path, '/')) {
    $location = rtrim($path, '/');
    if (!empty($query)) {
        $location .= '?'.$query;
    }
    header("Location: ".$location, true, 301);
    exit;
}

// ⚠️ Important : définir correctement ces deux variables pour Symfony
$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Redirige tout vers le front controller Symfony
require __DIR__.'/public/index.php';
