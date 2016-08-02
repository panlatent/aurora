<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Event\Dispatcher as EventDispatcher;

class Client
{
    protected $socket;

    protected $event;

    protected $keep = false;

    protected $streamline = true;

    public function __construct($socket)
    {
        $this->socket = $socket;
    }

    public function __destruct()
    {

    }

    public function close()
    {
        if ($this->socket) {
            socket_close($this->socket);
        }
    }

    public function socket()
    {
        return $this->socket();
    }

    public function isKeep()
    {
        return $this->keep;
    }

    public function isStreamline()
    {
        return $this->streamline;
    }

    public function send($content)
    {
        socket_write($this->socket, $content);
    }
}