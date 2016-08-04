<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

use Aurora\Aurora;

if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    die("PHP version can not be lower than 7.0\n");
} elseif ('cli' != php_sapi_name()) {
    die("Only run in PHP CLI environment\n");
}

$extensions = get_loaded_extensions();
foreach (Aurora::EXTENSIONS as $extension) {
    if ( ! in_array($extension, $extensions)) {
        die("This package require $extension extension\n");
    }
}

error_reporting(E_ALL);
//set_error_handler('Aurora\Exception\Manager::throwErrorExceptionHandler', E_ALL);
//set_exception_handler('Aurora\Exception\Manager::throwErrorExceptionHandler');