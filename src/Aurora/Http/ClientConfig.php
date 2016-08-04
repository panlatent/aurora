<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

class ClientConfig extends \Aurora\ClientConfig
{
    public $permanent_connection_wait_timeout = 5;
}