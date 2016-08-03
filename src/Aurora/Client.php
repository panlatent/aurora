<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Client\Events;
use Aurora\Event\Dispatcher as EventDispatcher;
use Aurora\Event\EventManageable;
use Aurora\Event\EventManager;

class Client implements EventManageable
{
    use EventManager;

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

        $this->createEventAcceptor();
        $this->eventAcceptor->setEvent($this->event);
        $this->eventAcceptor->register();

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
        if ( ! $this->event->base()->gotStop()) {
            $this->event->base()->stop();
        }
    }

    public function send($content)
    {
        socket_write($this->socket, $content);
    }

    protected function createEventAcceptor()
    {
        $this->eventAcceptor = new Events($this);
    }

}