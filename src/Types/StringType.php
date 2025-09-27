<?php
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