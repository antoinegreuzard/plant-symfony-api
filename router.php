<?php

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$query = $_SERVER['QUERY_STRING'] ?? '';

// Gérer les fichiers existants
if ($path !== '/' && file_exists(__DIR__.'/public'.$path)) {
    return false;
}

// Supprimer les trailing slashes (sauf racine)
if ($path !== '/' && str_ends_with($path, '/')) {
    $location = rtrim($path, '/');
    if ($query !== '') {
        $location .= '?'.$query;
    }
    header("Location: ".$location, true, 301);
    exit;
}

// Tout rediriger vers index.php (front-controller Symfony)
require __DIR__.'/public/index.php';
