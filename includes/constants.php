<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

use Aurora\Aurora;

defined('AURORA_DAEMON') or define('AURORA_DAEMON', true);
defined('AURORA_OS') or define('AURORA_OS', Aurora::os());
defined('AURORA_ROOT') or define('AURORA_ROOT', realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR);
defined('AURORA_PWD') or define('AURORA_PWD', getcwd() . DIRECTORY_SEPARATOR);
defined('AURORA_INI') or define('AURORA_INI', Aurora::INI);
defined('AURORA_VERSION') or define('AURORA_VERSION', Aurora::VERSION);