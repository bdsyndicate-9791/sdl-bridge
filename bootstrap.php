<?php
/*
                                 Apache License  
                           Version 2.0, January 2004  
                      http://www.apache.org/licenses/

TERMS AND CONDITIONS FOR USE, REPRODUCTION, AND DISTRIBUTION

Copyright 2025 Benjamin Sánchez Cárdenas <bdsyndicate@zohomail.com>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at:

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
 */
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