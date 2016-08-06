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
use Aurora\Event\EventManageable;
use Aurora\Event\EventManager;
use Aurora\Timer\TimestampManageable;
use Aurora\Timer\TimestampManager;
use Aurora\Timer\TimestampMarker;
use Aurora\Worker\WorkerManager;

class Server implements EventManageable, TimestampManageable
{
    const MASTER = 1;
    const WORKER = 2;

    use EventManager, TimestampManager;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var \Aurora\Pipeline
     */
    protected $pipeline;

    /**
     * @var int
     */
    protected $type = self::MASTER;

    /**
     * @var \Aurora\Timer\TimestampMarker
     */
    protected $timestamp;

    /**
     * @var \Aurora\Worker\WorkerManager
     */
    protected $workerManager;

    public function __construct(EventDispatcher $event = null, $socket = null, TimestampMarker $timestamp = null)
    {
        $this->event = $event ?? $this->createEvent();
        $this->socket = $socket ?? $this->createSocket();
        $this->timestamp = $timestamp ?? $this->createTimestampMarker();

        $this->eventAcceptor = $this->createEventAcceptor();
        $this->workerManager = $this->createWorkerManager();
    }

    public function __destruct()
    {
        unset($this->event);
        socket_close($this->socket);
    }

    public static function createMatchPipeline()
    {
        return new Pipeline();
    }

    public function getPipeline()
    {
        return $this->pipeline;
    }

    public function getSocket()
    {
        return $this->socket;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getWorkManage()
    {
        return $this->workerManager;
    }

    public function bind($address, $port)
    {
        if ( ! @socket_bind($this->socket, $address, $port)) {
            throw new Exception(error_get_last()['message']);
        }
    }

    public function listen($backlog = 0)
    {
        return socket_listen($this->socket, $backlog);
    }

    public function start()
    {
        if ($this->started) {
            throw new Exception("Server has been marked as the start state");
        }

        $this->started = true;
        $this->timestamp->mark(ServerTimestampType::ServerStart);

        if ( ! $this->pipeline) {
            $this->pipeline = new Pipeline();
        }

        $this->eventAcceptor->register();

        $state = $this->event->dispatch();
        if (static::WORKER === $this->type) {
            exit(0);
        }

        return $state;
    }

    public function stop()
    {
        if ( ! $this->started) {
            throw new Exception("Server is marked as stopped");
        }
        $this->started = false;
        $this->timestamp->mark(ServerTimestampType::ServerStop);

        return $this->event->stop();
    }

    public function createWorker($client)
    {
        return new Worker($this, $this->event, $client);
    }

    public function setPipeline(Pipeline $pipeline)
    {
        $this->pipeline = $pipeline;
        $this->pipeline->bind('server', $pipeline);
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    protected function createEvent()
    {
        return new EventDispatcher();
    }

    protected function createEventAcceptor()
    {
        return new Events($this->event, $this);
    }

    protected function createSocket()
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($socket);

        return $socket;
    }

    protected function createWorkerManager()
    {
        return new WorkerManager();
    }

    protected function createTimestampMarker()
    {
        return new TimestampMarker();
    }

}