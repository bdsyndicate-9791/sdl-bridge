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
namespace SDLBridge;

class ReportEngine
{
    private string $reportsDir;
    private TypeRegistry $typeRegistry;
    private ?array $params = null;
    private ?string $reportName = null;

    
    
    public function __construct(string $reportsDir = '')
    {
        // Resolución de ruta
        if ($reportsDir !== '') {
            $this->reportsDir = rtrim($reportsDir, '/');
        } elseif (defined('SDL_REPORTS_PATH') && is_string(SDL_REPORTS_PATH)) {
            $this->reportsDir = rtrim(SDL_REPORTS_PATH, '/');
        } else {
            if (!defined('WP_CONTENT_DIR')) {
                throw new \RuntimeException('WP_CONTENT_DIR no está definido. Asegúrate de que WordPress esté cargado.');
            }
            $this->reportsDir = rtrim(WP_CONTENT_DIR . '/SDLBridge/reports', '/');
        }

        // Validación y alerta (solo en admin)
        if (!is_dir($this->reportsDir)) {
            static $noticeShown = false;
            if (!$noticeShown) {
                $dir = function_exists('esc_html') 
                    ? esc_html($this->reportsDir) 
                    : htmlspecialchars($this->reportsDir, ENT_QUOTES, 'UTF-8');

                echo '<div class="notice notice-error is-dismissible">
                    <p><strong>SDLBridge:</strong> No se encontró el directorio de reportes: <code>' . $dir . '</code></p>
                    <p>Verifica que los archivos de reporte estén instalados correctamente.</p>
                </div>';
                $noticeShown = true;
            }
            die;
        }

        $this->typeRegistry = new TypeRegistry();
    }

    public static function report(string $name): self
    {
        $instance = new self();
        $instance->reportName = $name;
        return $instance;
    }

    public function withParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function execute()
    {
        if (!$this->reportName) {
            throw new \LogicException("Nombre de reporte no especificado.");
        }
        if ($this->params === null) {
            $this->params = [];
        }

        $filePath = $this->reportsDir . '/' . $this->reportName . '.sdl';
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Reporte no encontrado: $filePath");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("No se pudo leer el archivo del reporte: {$filePath}");
        }

        $parser = new SDLParser();
        $parsed = $parser->parse($content);

        $compiler = new SDLCompiler($this->typeRegistry);
        $finalSql = $compiler->compile(
            $parsed['sql_template'] ?? '',
            $parsed['variables'] ?? [],
            $this->params
        );
        

        global $wpdb;
        $results = $wpdb->get_results($finalSql, ARRAY_A);

        return (object)[
            'rows' => $results,
            'sql' => $finalSql,
            'metadata' => $parsed['metadata'] ?? []
        ];
    }

    // Método para registrar nuevos tipos (extensibilidad)
    public function registerType(string $name, $type): void
    {
        $this->typeRegistry->register($name, $type);
    }
}