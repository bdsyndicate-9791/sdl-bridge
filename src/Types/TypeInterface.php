<?php
namespace SDLBridge\Types;

interface TypeInterface
{
    public function getName(): string;
    public function validate(mixed $value): bool;
    public function sanitize(mixed $value): string;
    public function getExample(): string;
}