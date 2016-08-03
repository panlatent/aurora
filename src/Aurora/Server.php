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
use Aurora\Event\Listener;
use Aurora\Event\SignalAcceptable;

class Server implements EventAcceptable, SignalAcceptable
{
    const MASTER = 1;
    const WORKER = 2;
    const EVENT_SOCKET_ACCEPT = 'socket:accept';
    const EVENT_SIGNAL_ACCEPT = 'signal:accept';

    use EventAccept;

    /**
     * @var \Aurora\Event\Dispatcher
     */
    protected $event;

    /**
     * @var \Aurora\Pipeline
     */
    protected $pipeline;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var int
     */
    protected $type = self::MASTER;

    /**
     * @var array
     */
    protected $workerPidStore = [];

    public function __construct()
    {
        $this->createEvent();
        $this->createSocket();
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

    public function pipeline()
    {
        return $this->pipeline;
    }

    public function type()
    {
        return $this->type;
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
        } elseif ( ! $this->pipeline) {
            $this->pipeline = new Pipeline();
        }

        $this->started = true;
        $this->event->listen(static::EVENT_SOCKET_ACCEPT,
            new Listener($this->socket, \Event::READ | \Event::PERSIST));

        $state = $this->event->base()->dispatch();

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

        return $this->event->base()->stop();
    }

    public function onAccept($socket, $what, Listener $listener)
    {
        if ( ! $client = socket_accept($socket)) {
            throw new Exception("Accept a socket connect failed");
        }
        socket_set_nonblock($client);

        $worker = $this->createWorker($client);
        if (static::MASTER === $this->type) {
            $this->workerPidStore[] = $worker->pid();
            socket_close($client);
        }
    }

    public function acceptSignal($signal, $arg)
    {
        switch ($signal) {
            case SIGTERM:
                foreach ($this->workerPidStore as $pid) {
                    posix_kill($pid, SIGKILL);
                }
                $this->event->base()->exit();
                break;
            case SIGUSR1: // @todo Workers Shard Memory Message
                break;
            case SIGUSR2: // @todo Daemon and Worker Pipeline Message
                break;
            case SIGCHLD:
                while (($pid = pcntl_waitpid(-1, $status, WUNTRACED | WNOHANG)) > 0) {
                    if (false !== ($key = array_search($pid, $this->workerPidStore))) {
                        unset($this->workerPidStore[$key]);
                    }
                }
                break;
            default:
                return;
        }
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

    protected function addSignalEvents(array $signals)
    {
        foreach ($signals as $signal) {
            $this->event->listen('signal:accept', new Listener($signal, \Event::SIGNAL));
        }
    }

    protected function createWorker($client)
    {
        return new Worker($this, $this->event, $client);
    }

    protected function createEvent()
    {
        $this->event = new EventDispatcher();
        $this->event->bind(static::EVENT_SOCKET_ACCEPT, $this);
        $this->event->bind(static::EVENT_SIGNAL_ACCEPT, [$this, SignalAcceptable::EVENT_SIGNAL_CALLBACK]);
        $this->addSignalEvents([SIGTERM, SIGUSR1, SIGUSR2, SIGCHLD]);
    }

    protected function createSocket()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($this->socket);
    }
}