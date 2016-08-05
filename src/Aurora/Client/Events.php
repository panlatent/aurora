<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Client;

use Aurora\Event\EventAcceptor;
use Aurora\Event\Listener;
use Aurora\ServerTimestampType;
use Aurora\Timer\Dispatcher as TimerDispatcher;
use Aurora\Timer\TimestampMarker;

class Events extends EventAcceptor
{
    const EVENT_SOCKET_READ = 'client.socket:read';
    const EVENT_SOCKET_WRITE = 'client.socket:write';
    const EVENT_TIMER = 'client:timer';

    /**
     * @var \Aurora\Client
     */
    protected $bind;

    /**
     * @var \Aurora\Timer\Dispatcher
     */
    protected $timer;

    public function __construct($dispatcher, $bind)
    {
        parent::__construct($dispatcher, $bind);

        $this->event->bind(static::EVENT_SOCKET_READ, $this);
        $this->event->bind(static::EVENT_SOCKET_WRITE, $this);
        $this->event->bind(static::EVENT_TIMER, $this);

        $this->timer = new TimerDispatcher();
        $this->timer->insert([$this, 'onSocketInitWaitTimeoutTimer']);
        $this->timer->insert([$this, 'onSocketReadWaitTimeoutTimer']);
    }

    public function register()
    {
        $listener = new Listener($this->event, $this->bind->socket(), \Event::READ | \Event::PERSIST, $this->bind);
        $listener->register(static::EVENT_SOCKET_READ);
        $listener->listen();

        $timer = Listener::timer($this->event, true);
        $timer->register(static::EVENT_TIMER);
        $timer->listen(0.25);
    }

    public function timer()
    {
        return $this->timer;
    }

    public function onTimer()
    {
        $this->timer->dispatch();
    }

    public function onRead($socket, Listener $listener)
    {
        /** @var \Aurora\Client $client */
        $client = $listener->argument();
        $segment = socket_read($socket, $this->bind->config()->socket_read_buffer_size);
        if (false === $segment) {
            $no = socket_last_error($socket);
            $message = $no != 0 ? socket_strerror($no) : '';
            $listener->delete();
            $client->close();
            throw new Exception($message, $no);
        } elseif ("" === $segment) {
            $listener->delete();
            $client->close();
        } else {
            if ( ! $this->bind->timestamp()->isset(ServerTimestampType::SocketFirstRead)) {
                $this->bind->timestamp()->mark(ServerTimestampType::SocketFirstRead);
            }
            $this->bind->timestamp()->mark(ServerTimestampType::SocketLastRead);
            $client->pipeline()->append($segment);
        }
    }

    public function onWrite($socket, Listener $listener)
    {
        socket_write($socket, $listener->argument());
    }

    public function onSocketInitWaitTimeoutTimer()
    {
        $timestamp = $this->bind->timestamp();
        if ( ! ($socketFirstReadUT = $timestamp->get(ServerTimestampType::SocketFirstRead))) { // HTTP Connection first request timeout
            $interval = TimestampMarker::interval($timestamp->get(ServerTimestampType::ClientStart));
            if ($interval >= $this->bind->config()->socket_first_wait_timeout) {
                $this->bind->declareClose();
            }
        }
    }

    public function onSocketReadWaitTimeoutTimer()
    {
        $timestamp = $this->bind->timestamp();
        if (($socketLastReadUT = $timestamp->get(ServerTimestampType::SocketLastRead))) {
            $interval = TimestampMarker::interval($socketLastReadUT);
            if ($interval >= $this->bind->config()->socket_last_wait_timeout) {
                $this->bind->declareClose();
            }
        }
    }
}