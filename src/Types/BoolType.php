<?php
namespace SDLBridge\Types;

class BoolType extends BaseType
{
    public function getName(): string { return 'bool'; }

    public function validate(mixed $value): bool
    {
        return is_bool($value) || in_array($value, [0, 1, '0', '1'], true);
    }

    public function sanitize(mixed $value): string
    {
        if (!$this->validate($value)) {
            throw new \InvalidArgumentException("Valor no válido para tipo 'bool'.");
        }
        // En SQL, usamos 1/0
        return $value ? '1' : '0';
    }

    public function getExample(): string { return "1"; }
}