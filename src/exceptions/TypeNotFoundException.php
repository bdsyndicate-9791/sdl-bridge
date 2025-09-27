<?php
namespace SDLBridge\exceptions;

/**
 * Excepción específica lanzada cuando se hace referencia a un tipo
 * que no ha sido registrado en el TypeRegistry.
 * Ejemplo: {{fecha:fechota}} cuando 'fechota' no existe.
 */
class TypeNotFoundException extends SDLException
{
}