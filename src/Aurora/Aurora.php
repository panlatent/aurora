<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

class Aurora
{
    const NAME = 'Aurora';
    const INI = 'aurora.ini';
    const VERSION = '0.1.0';
    const EXTENSIONS = ['posix', 'pcntl', 'event'];

    public static function getOperatingSystemType()
    {
        switch (PHP_OS) {
            case 'WINNT':
            case 'WIN32':
            case 'Windows':
                return 'win';
            case 'NetBSD':
            case 'OpenBSD':
            case 'FreeBSD':
                return 'bsd';
            case 'Darwin':
                return 'osx';
            case 'Unix':
            case 'Linux':
            case 'CYGWIN_NT-5.1':
            default:
                return 'unix';
        }
    }
}