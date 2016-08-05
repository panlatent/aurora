<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

class ClientConfig extends Config
{
    public $socket_first_wait_timeout = 10;

    public $socket_last_wait_timeout = 10;

    public $socket_read_buffer_size = 512;
}