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
namespace SDLBridge\Types;

class StringType extends BaseType
{
    public function getName(): string { return 'string'; }

    public function validate(mixed $value): bool
    {
        return is_string($value) || is_numeric($value) || is_bool($value);
    }

    public function sanitize(mixed $value): string
    {
        if (!$this->validate($value)) {
            throw new \InvalidArgumentException("Valor no válido para tipo 'string'.");
        }
        // Convertir a cadena segura
        $str = (string) $value;
        global $wpdb;
        return "'" . $wpdb->_real_escape($str) . "'";
    }

    public function getExample(): string { return "'ejemplo'"; }
}