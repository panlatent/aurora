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
    protected $worker;

    protected $event;

    protected $socket;

    protected $pipeline;

    public function __construct(Worker $worker, EventDispatcher $event, $socket, Pipeline $pipeline)
    {
        $this->worker = $worker;
        $this->event = $event;
        $this->socket = $socket;
        $this->pipeline = $pipeline;

        $this->pipeline->bind('client', $this);
        $this->pipeline->open();
    }

    public function pipeline()
    {
        return $this->pipeline;
    }

    public function socket()
    {
        return $this->socket;
    }

    public function close()
    {
        socket_close($this->socket);
        $this->event->base()->stop();
    }

    public function send($content)
    {
        socket_write($this->socket, $content);
    }

}