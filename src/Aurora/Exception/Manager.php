<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Exception;

class Manager
{
    public static function throwErrorExceptionHandler($severity, $message, $filename, $line, $context)
    {
        if ( ! (error_reporting() & $severity)) {
            return;
        }

        throw new \Error($message, 0, $severity, $filename, $line);
    }

    public static function defaultExceptionHandler(\Throwable $exception) // @todo
    {
        exit(0);
    }
}