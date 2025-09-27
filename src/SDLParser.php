<?php
namespace SDLBridge;

use SDLBridge\exceptions\SDLException;

class SDLParser
{
    private const METADATA_PATTERN = '/\/\*\s*SDL-METADATA(.*?)SDL-METADATA\s*\*\//s';
    private const VARIABLE_PATTERN = '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*:\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/';

    public function parse(string $sdlContent): array
    {
        $metadata = $this->extractMetadata($sdlContent);
        $variables = $this->extractVariables($sdlContent);

        return [
            'metadata' => $metadata,
            'variables' => $variables,
            'sql_template' => $sdlContent
        ];
    }

    private function extractMetadata(string $content): array
    {
        if (!preg_match(self::METADATA_PATTERN, $content, $matches)) {
            return [];
        }

        // Eliminar el bloque del contenido (para no interferir)
        // Pero por ahora, solo devolvemos metadatos como array vacío (YAML opcional en v2)
        return []; // Extensible: en v2, parsear YAML
    }

    private function extractVariables(string $content): array
    {
        if (!preg_match_all(self::VARIABLE_PATTERN, $content, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $vars = [];
        foreach ($matches as $match) {
            $name = $match[1];
            $type = $match[2];
            if (isset($vars[$name])) {
                throw new SDLException("Variable '$name' duplicada.");
            }
            $vars[$name] = $type;
        }
        return $vars;
    }
}