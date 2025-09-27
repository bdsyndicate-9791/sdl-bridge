<?php
namespace SDLBridge\Types;

abstract class BaseType implements TypeInterface
{
    abstract public function getName(): string;
    abstract public function validate(mixed $value): bool;
    abstract public function sanitize(mixed $value): string;
    abstract public function getExample(): string;
}