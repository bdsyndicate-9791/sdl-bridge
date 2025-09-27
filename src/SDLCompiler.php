<?php
namespace SDLBridge;

use SDLBridge\exceptions\SDLException;

class SDLCompiler
{
    private TypeRegistry $typeRegistry;

    public function __construct(TypeRegistry $typeRegistry)
    {
        $this->typeRegistry = $typeRegistry;
    }

    public function compile(string $sqlTemplate, array $variables, array $params): string
    {
        $finalSql = $sqlTemplate;
        $placeholders = [];

        foreach ($variables as $varName => $typeName) {
            if (!isset($params[$varName])) {
                throw new SDLException("Parámetro requerido '$varName' no proporcionado.");
            }

            $type = $this->typeRegistry->get($typeName);
            $value = $params[$varName];

            if (!$type->validate($value)) {
                throw new SDLException("Valor inválido para '$varName' (tipo: $typeName).");
            }

            $sanitized = $type->sanitize($value);
            $token = '{{' . $varName . ':' . $typeName . '}}';

            // Usar placeholder único para evitar colisiones
            $placeholder = '___SDL_' . bin2hex(random_bytes(8)) . '___';
            $placeholders[$placeholder] = $sanitized;
            $finalSql = str_replace($token, $placeholder, $finalSql);
        }

        // Reemplazar placeholders
        foreach ($placeholders as $ph => $val) {
            $finalSql = str_replace($ph, $val, $finalSql);
        }

        return $finalSql;
    }
}