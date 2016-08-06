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

class InternalServerErrorException extends Exception
{
    protected $statusCode = 500;

    protected $statusMessage = 'Internal Server Error';
} 