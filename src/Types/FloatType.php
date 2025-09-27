<?php
namespace SDLBridge\Types;

class FloatType extends BaseType
{
    public function getName(): string { return 'float'; }

    public function validate(mixed $value): bool
    {
        if (is_bool($value)) return false;
        return is_numeric($value);
    }

    public function sanitize(mixed $value): string
    {
        if (!$this->validate($value)) {
            throw new \InvalidArgumentException("Valor no válido para tipo 'float'.");
        }
        // Normalizar a formato sin comas, con punto
        return sprintf('%.10F', (float)$value);
    }

    public function getExample(): string { return "3.1415"; }
}