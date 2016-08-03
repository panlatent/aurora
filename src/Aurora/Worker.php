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
use Aurora\Event\EventAccept;
use Aurora\Event\EventAcceptable;
use Aurora\Event\EventManageable;
use Aurora\Event\Listener;

class Worker implements EventAcceptable
{
    const EVENT_SOCKET_READ = 'socket:read';

    use EventAccept;

    protected $server;

    protected $event;

    protected $pid;

    protected $socket;

    /**
     * @var int
     */
    protected $socketReadBufferSize = 512;

    public function __construct(Server $server, EventDispatcher $event, $socket)
    {
        if (-1 === ($this->pid = pcntl_fork())) {
            throw new Exception('Failed to create a work process');
        } elseif ($this->pid) {
            return;
        }

        try {
            $server->setType(Server::WORKER);

            $this->server = $server;
            $this->socket = $socket;
            $this->event = $event;

            $this->event->reset();
            $this->event->bind(static::EVENT_SOCKET_READ, $this);

            $pipeline = $this->server->pipeline();
            $pipeline->bind('worker', $this);
            if ($pipeline instanceof EventManageable && ! $pipeline->event()) {
                $pipeline->setEvent($event);
            }

            $client = $this->createClient();
            $this->event->listen(static::EVENT_SOCKET_READ, new Listener($socket, \Event::READ | \Event::PERSIST, $client));
        } catch (\Throwable $ex) {
            echo sprintf('"%s" in "%s:%d"', $ex->getMessage(), $ex->getFile(), $ex->getLine()), "\n";
            echo $ex->getTraceAsString();
            if ( ! $this->event->base()->gotStop()) {
                $this->event->base()->stop();
            }
        }
    }

    public function pid()
    {
        return $this->pid;
    }

    public function onRead($socket, $what, Listener $listener)
    {
        /** @var \Aurora\Client $client */
        $client = $listener->argument();
        $segment = socket_read($socket, $this->socketReadBufferSize);
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
            $client->pipeline()->append($segment);
        }
    }

    public function setSocketReadBufferSize($size)
    {
        $this->socketReadBufferSize = $size;
    }

    protected function createClient()
    {
        $client = new Client($this, $this->event, $this->socket, $this->server->pipeline());

        return $client;
    }
}