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