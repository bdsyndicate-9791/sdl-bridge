<?php
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