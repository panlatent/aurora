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

class ExpectationFailedException extends Exception
{
    protected $statusCode = 417;

    protected $statusMessage = 'Expectation Failed';
}