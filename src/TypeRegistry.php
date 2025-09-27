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

use SDLBridge\Types\TypeInterface;
use SDLBridge\exceptions\TypeNotFoundException;

class TypeRegistry
{
    private array $types = [];

    public function __construct()
    {
        // Registrar tipos base
        $this->register('string', new \SDLBridge\Types\StringType());
        $this->register('int', new \SDLBridge\Types\IntType());
        $this->register('float', new \SDLBridge\Types\FloatType());
        $this->register('bool', new \SDLBridge\Types\BoolType());
        $this->register('date', new \SDLBridge\Types\DateType());
        $this->register('datetime', new \SDLBridge\Types\DatetimeType());
        $this->register('array', new \SDLBridge\Types\ArrayType());
    }

    public function register(string $name, TypeInterface $type): void
    {
        $this->types[strtolower($name)] = $type;
    }

    public function get(string $name): TypeInterface
    {
        $name = strtolower($name);
        if (!isset($this->types[$name])) {
            throw new TypeNotFoundException("Tipo '$name' no registrado.");
        }
        return $this->types[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->types[strtolower($name)]);
    }
}