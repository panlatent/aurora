<?php
/**
 * Server
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/seven-server
 * @license https://opensource.org/licenses/MIT
 */

namespace Seven\Server;

class SocketServer {

    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var \Event
     */
    protected $event;

    /**
     * @var array
     */
    protected $clientEvents = [];

    /**
     * @var \EventBase
     */
    protected $eventBase;

    /**
     * @var int
     */
    protected $socketReadBufferSize = 512;

    /**
     * @var callable
     */
    protected $onMessage;

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($this->socket);

        $this->eventBase = new \EventBase();
        $this->event = new \Event($this->eventBase, $this->socket, \Event::READ |
            \Event::PERSIST, [$this, 'onAccept']);
        $this->event->add();
    }

    public function __destruct()
    {
        $this->event->free();
        $this->eventBase->free();
        socket_close($this->socket);
    }

    public function bind($address, $port)
    {
        return socket_bind($this->socket, $address, $port);
    }

    public function listen($backlog = 0)
    {
        return socket_listen($this->socket, $backlog);
    }

    public function start()
    {
        if (null === $this->onMessage) {
            throw new Exception('Did not set the onMessage event');
        }

        $this->eventBase->dispatch();
    }

    public function onAccept($socket, $events, $arg)
    {
        $client = socket_accept($socket);
        socket_set_nonblock($client);

        $event = new \Event($this->eventBase, $client, \Event::READ | \Event::PERSIST,
            [$this, 'onRead']);

        $clientEvent = $this->addClientEvent($client, $event);
        $event->set($this->eventBase, $client, \Event::READ | \Event::PERSIST,
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

    public function onRead($client, $events, $clientEvent)
    {
        $clientEvent->event->del();
        unset($this->clientEvents[$clientEvent->ref]);

        switch (pcntl_fork()) {
            case 0:
                $message = '';
                do {
                    $segment = socket_read($client, $this->socketReadBufferSize);
                    $message .= $segment;
                } while ($this->socketReadBufferSize == strlen($segment));

                call_user_func_array($this->onMessage, [$client, $message]);

                socket_close($client);
                exit(0);
            case -1:
                socket_close($client);
                throw new Exception('Failed to create a work process');
            default:
                socket_close($client);
        }
    }

    public function setOnMessage(\Closure $callback)
    {
        $this->onMessage = $callback;
    }

    public function setSocketReadBufferSize($size)
    {
        $this->socketReadBufferSize = $size;
    }
}