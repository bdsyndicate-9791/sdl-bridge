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

class IntType extends BaseType
{
    public function getName(): string { return 'int'; }

    public function validate(mixed $value): bool
    {
        if (is_bool($value)) return false; // Evitar que true → 1, false → 0 sin intención
        return is_numeric($value) && (string)(int)$value === (string)$value;
    }

    public function sanitize(mixed $value): string
    {
        if (!$this->validate($value)) {
            throw new \InvalidArgumentException("Valor no válido para tipo 'int'.");
        }
        return (string)(int)$value;
    }

    public function getExample(): string { return "42"; }
}