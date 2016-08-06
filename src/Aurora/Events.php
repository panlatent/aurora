<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Event\Dispatcher;
use Aurora\Event\EventAcceptor;
use Aurora\Event\Listener;
use Aurora\Event\SignalAcceptable;

class Events extends EventAcceptor implements SignalAcceptable
{
    const EVENT_SIGNAL = 'server:signal';
    const EVENT_SOCKET_ACCEPT = 'server.socket:accept';
    const EVENT_TIMER = 'server.timer';

    /**
     * @var \Aurora\Server
     */
    protected $bind;

    protected $timer;

    public function __construct(Dispatcher $event, $bind)
    {
        parent::__construct($event, $bind);
        $event->bind(static::EVENT_SIGNAL, $this);
        $event->bind(static::EVENT_SOCKET_ACCEPT, $this);
        $this->addSignalEvents([SIGTERM, SIGUSR1, SIGUSR2, SIGCHLD]);
    }

    public function register()
    {
        $listener = new Listener($this->event, $this->bind->socket(), \Event::READ | \Event::PERSIST);
        $listener->register(static::EVENT_SOCKET_ACCEPT);
        $listener->listen();

        $listener = Listener::timer($this->event, true);
        $listener->register(static::EVENT_TIMER);
        $listener->listen(1);
    }

    public function onSignal($signal, $arg)
    {
        switch ($signal) {
            case SIGTERM:
                $this->bind->workManage()->killAll();
                $this->event->base()->exit();
                break;
            case SIGUSR1: // @todo Workers Shard Memory Message
                break;
            case SIGUSR2: // @todo Daemon and Worker Pipeline Message
                break;
            case SIGCHLD:
                while (($pid = pcntl_waitpid(-1, $status, WUNTRACED | WNOHANG)) > 0) {
                    $this->bind->workManage()->remove($pid);
                }
                break;
            default:
                return;
        }
    }

    public function onAccept($socket)
    {
        if ( ! $client = socket_accept($socket)) {
            throw new Exception("Accept a socket connect failed");
        }
        socket_set_nonblock($client);

        $worker = $this->bind->createWorker($client);
        if (Server::MASTER === $this->bind->type()) {
            $this->bind->workManage()->add($worker);
            socket_close($client);
        }
    }

    public function onTimer()
    {

    }

    protected function addSignalEvents(array $signals)
    {
        foreach ($signals as $signal) {
            $listener = Listener::signal($this->event, $signal);
            $listener->register(static::EVENT_SIGNAL);
            $listener->listen();
        }
    }
}