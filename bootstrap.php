<?php
if (!defined('ABSPATH')) exit;

$libBase = __DIR__;
$debugAutoload = true; // ← deja esto en true por ahora

spl_autoload_register(function ($class) use ($libBase, $debugAutoload) {
    $prefix = 'SDLBridge\\';
    
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $filePath = $libBase . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

    if ($debugAutoload) {
        error_log("Autoloader: buscando clase '$class'");
        error_log("Ruta construida: " . $filePath);
        error_log("¡Archivo existe? " . (file_exists($filePath) ? 'SI' : 'NO'));
    }

    if (file_exists($filePath)) {
        require $filePath;
        if ($debugAutoload) {
            error_log(" Cargado exitosamente.");
        }
    }
});

// Alias global
if (!class_exists('SDLBridge')) {
    class SDLBridge extends \SDLBridge\ReportEngine
    {
        public static function report(string $name): \SDLBridge\ReportEngine
        {
            return parent::report($name);
        }
    }
}