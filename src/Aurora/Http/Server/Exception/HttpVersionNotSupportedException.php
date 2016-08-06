<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http\Server\Exception;

use Aurora\Http\Server\Exception;

class HttpVersionNotSupportedException extends Exception
{
    protected $statusCode = 505;

    protected $statusMessage = 'HTTP Version Not Supported';
}