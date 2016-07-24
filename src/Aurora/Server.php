<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\IPC\PipeMessage;
use Event;
use EventBase;

class Server implements SignalAcceptable
{
    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @var array
     */
    protected $signalEvents = [];

    /**
     * @var array
     */
    protected $clientEvents = [];

    /**
     * @var EventBase
     */
    protected $eventBase;

    /**
     * @var int
     */
    protected $socketReadBufferSize = 512;

    /**
     * @var \Aurora\Pipeline
     */
    protected $pipeline;

    /**
     * @var bool
     */
    protected $started = false;

    protected $beginTimeMark = '';

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($this->socket);

        $this->eventBase = new EventBase();
        $this->event = new Event($this->eventBase, $this->socket, Event::READ |
            Event::PERSIST, [$this, 'onAccept']);
        $this->event->add();
        $this->addSignalEvents([SIGTERM, SIGUSR1, SIGUSR2]);

        $this->pipeline = new Pipeline();
    }

    protected function addSignalEvents(array $signals)
    {
        foreach ($signals as $signal) {
            $this->signalEvents[$signal] = Event::signal($this->eventBase, $signal, [$this, SignalAcceptable::EVENT_CALLBACK]);
            $this->signalEvents[$signal]->add();
        }
    }

    public function __destruct()
    {
        $this->event->free();
        $this->eventBase->free();
        socket_close($this->socket);
    }

    public function acceptSignal($signal, $fc)
    {
        switch ($signal) {
            case SIGTERM:
                $this->eventBase->exit();
                break;
            case SIGUSR1: // Workers Shard Memory Message
                break;
            case SIGUSR2: // Daemon Pipeline Message
                $pipeMessage = new PipeMessage('/tmp/' . posix_getpid());
                $request = $pipeMessage->receive();

                if ($request['msg'] == 'status') {
                    $pipeMessage->send(['status' => true]);
                }

                break;
            default:
                return;
        }
    }

    public function bind($address, $port)
    {
        return socket_bind($this->socket, $address, $port);
    }

    public function listen($backlog = 0)
    {
        return socket_listen($this->socket, $backlog);
    }

    public function pipe($callback)
    {
        return $this->pipeline->pipe($callback);
    }

    public function pipeline()
    {
        return $this->pipeline;
    }

    public function start()
    {
        if ($this->started) {
            throw new Exception("Server has been marked as the start state");
        }
        $this->started = true;

        return $this->eventBase->dispatch();
    }

    public function stop()
    {
        if ( ! $this->started) {
            throw new Exception("Server is marked as stopped");
        }
        $this->started = false;

        return $this->eventBase->stop();
    }

    public function onAccept($socket, $events, $arg)
    {
        $client = socket_accept($socket);
        socket_set_nonblock($client);

        $event = new Event($this->eventBase, $client, Event::READ | Event::PERSIST,
            [$this, 'onRead']);

        $clientEvent = $this->addClientEvent($client, $event);
        $event->set($this->eventBase, $client, Event::READ | Event::PERSIST,
            [$this, 'onRead'], $clientEvent);
        $event->add();
    }

    protected function addClientEvent($client, $event)
    {
        $clientEvent = (object)['client' => $client, 'event' => $event];
        array_push($this->clientEvents, $clientEvent);
        end($this->clientEvents);
        $clientEvent->ref = key($this->clientEvents);

        return $clientEvent;
    }

    public function onRead($socket, $events, $clientEvent)
    {
        $clientEvent->event->del();
        unset($this->clientEvents[$clientEvent->ref]);

        switch (pcntl_fork()) {
            case 0:
                $this->pipeline->open();

                do {
                    $segment = socket_read($socket, $this->socketReadBufferSize);
                    $this->pipeline->append($segment);
                } while ($this->socketReadBufferSize == strlen($segment));

                $client = new Client($socket);

                $this->pipeline->bind('client', $client);
                $this->pipeline->run();
                $this->pipeline->close();

                socket_close($socket);
                exit(0);
            case -1:
                socket_close($socket);
                throw new Exception('Failed to create a work process');
            default:
                socket_close($socket);
        }
    }

    public function setSocketReadBufferSize($size)
    {
        $this->socketReadBufferSize = $size;
    }

}