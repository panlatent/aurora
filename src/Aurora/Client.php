<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Config\ConfigManageable;
use Aurora\Client\Events;
use Aurora\Config\ConfigManager;
use Aurora\Event\Dispatcher as EventDispatcher;
use Aurora\Event\EventManageable;
use Aurora\Event\EventManager;
use Aurora\Timer\TimestampManageable;
use Aurora\Timer\TimestampManager;

class Client implements EventManageable, ConfigManageable, TimestampManageable
{
    use EventManager, ConfigManager, TimestampManager;

    protected $config;

    protected $worker;

    protected $event;

    protected $socket;

    protected $pipeline;

    public function __construct(Worker $worker, EventDispatcher $event, $socket, Pipeline $pipeline, Config $config = null)
    {
        $this->worker = $worker;
        $this->event = $event;
        $this->socket = $socket;
        $this->pipeline = $pipeline;
        $this->config = $config ?? $this->createConfig();
        $this->timestamp = $worker->timestamp();
        $this->timestamp->mark(ServerTimestampType::ClientStart);

        $this->eventAcceptor = $this->createEventAcceptor();
        $this->eventAcceptor->setEvent($this->event);
        $this->eventAcceptor->register();

        $this->pipeline->bind('client', $this);
        $this->pipeline->open();
    }

    public function worker()
    {
        $this->worker;
    }

    public function socket()
    {
        return $this->socket;
    }

    public function pipeline()
    {
        return $this->pipeline;
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

    protected function createConfig()
    {
        return new ClientConfig();
    }

    protected function createEventAcceptor()
    {
        return new Events($this);
    }

}