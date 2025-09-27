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
        if ($reportsDir === '') {
            $reportsDir = WP_CONTENT_DIR . '/SDLBridge/reports';
        }
        $this->reportsDir = rtrim($reportsDir, '/');
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
        $parser = new SDLParser();
        $parsed = $parser->parse($content);

        $compiler = new SDLCompiler($this->typeRegistry);
        $finalSql = $compiler->compile(
            $parsed['sql_template'],
            $parsed['variables'],
            $this->params
        );

        global $wpdb;
        $results = $wpdb->get_results($finalSql, ARRAY_A);

        return (object)[
            'rows' => $results,
            'sql' => $finalSql,
            'metadata' => $parsed['metadata']
        ];
    }

    // Método para registrar nuevos tipos (extensibilidad)
    public function registerType(string $name, $type): void
    {
        $this->typeRegistry->register($name, $type);
    }
}