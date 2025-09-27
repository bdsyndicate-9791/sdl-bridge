<?php
namespace SDLBridge\Types;

class DateType extends BaseType
{
    public function getName(): string { return 'date'; }

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) return false;
        $d = \DateTime::createFromFormat('Y-m-d', $value);
        return $d && $d->format('Y-m-d') === $value;
    }

    public function sanitize(mixed $value): string
    {
        if (!$this->validate($value)) {
            throw new \InvalidArgumentException("Fecha inválida. Formato esperado: YYYY-MM-DD.");
        }
        global $wpdb;
        return "'" . $wpdb->_real_escape($value) . "'";
    }

    public function getExample(): string { return "'2024-05-01'"; }
}