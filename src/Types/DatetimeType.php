<?php
namespace SDLBridge\Types;

class DatetimeType extends BaseType
{
    public function getName(): string { return 'datetime'; }

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) return false;
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        return $d && $d->format('Y-m-d H:i:s') === $value;
    }

    public function sanitize(mixed $value): string
    {
        if (!$this->validate($value)) {
            throw new \InvalidArgumentException("Datetime inválido. Formato esperado: YYYY-MM-DD HH:MM:SS.");
        }
        global $wpdb;
        return "'" . $wpdb->_real_escape($value) . "'";
    }

    public function getExample(): string { return "'2024-05-01 12:00:00'"; }
}