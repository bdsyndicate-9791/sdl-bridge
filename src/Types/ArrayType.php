<?php
namespace SDLBridge\Types;

class ArrayType extends BaseType
{
    public function getName(): string { return 'array'; }

    public function validate(mixed $value): bool
    {
        if (!is_array($value)) return false;
        if (empty($value)) return false; // Evitar IN ()
        foreach ($value as $item) {
            if (!is_scalar($item) && $item !== null) return false;
        }
        return true;
    }

    public function sanitize(mixed $value): string
    {
        if (!$this->validate($value)) {
            throw new \InvalidArgumentException("Array inválido para tipo 'array'. Debe ser un array de valores escalares.");
        }

        global $wpdb;
        $sanitized = [];
        foreach ($value as $item) {
            if ($item === null) {
                $sanitized[] = 'NULL';
            } elseif (is_int($item)) {
                $sanitized[] = (string)(int)$item;
            } elseif (is_float($item)) {
                $sanitized[] = sprintf('%.10F', $item);
            } else {
                // Tratar como string
                $sanitized[] = "'" . $wpdb->_real_escape((string)$item) . "'";
            }
        }

        return '(' . implode(',', $sanitized) . ')';
    }

    public function getExample(): string { return "(1,2,3)"; }
}