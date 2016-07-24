<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

class Client
{
    protected $socket;

    public function __construct($socket)
    {
        $this->socket = $socket;
    }

    public function socket()
    {
        return $this->socket();
    }

    public function send($content)
    {
        socket_write($this->socket, $content);
    }
}