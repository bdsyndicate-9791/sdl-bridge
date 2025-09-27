<?php
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