<?php
namespace SDLBridge\exceptions;

/**
 * Excepción base para todos los errores relacionados con el procesamiento de SDL.
 * Se lanza cuando hay problemas de sintaxis, variables faltantes, validación, etc.
 */
class SDLException extends \RuntimeException
{
}