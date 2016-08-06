<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http\Client\Exception;

use Aurora\Http\Client\Exception;

class RequestedRangNotSatisfiableException extends Exception
{
    protected $statusCode = 416;

    protected $statusMessage = 'Requested Rang Not Satisfiable';
}