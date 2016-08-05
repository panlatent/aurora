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
use Aurora\Config\ConfigManager;
use Aurora\Event\Dispatcher as EventDispatcher;
use Aurora\Event\EventAccept;
use Aurora\Event\EventAcceptable;
use Aurora\Event\EventManageable;
use Aurora\Event\Listener;
use Aurora\Support\Posix;
use Aurora\Timer\TimestampManageable;
use Aurora\Timer\TimestampManager;

class Worker implements EventAcceptable, ConfigManageable, TimestampManageable
{
    use EventAccept, ConfigManager, TimestampManager;

    protected $server;

    protected $event;

    protected $pid;

    protected $socket;

    public function __construct(Server $server, EventDispatcher $event, $socket, WorkerConfig $config = null)
    {
        if (-1 === ($this->pid = pcntl_fork())) {
            throw new Exception('Failed to create a work process');
        } elseif ($this->pid) {
            return;
        }

        try {
            $server->setType(Server::WORKER);
            $event->reset();
            $this->server = $server;
            $this->event = $event;
            $this->socket = $socket;
            $this->timestamp = $server->timestamp();
            $this->timestamp->mark(ServerTimestampType::WorkerStart);
            $this->config = $config ?? new WorkerConfig();

            Posix::setUser($this->config->worker_user, $this->config->worker_user_group);

            $pipeline = $this->server->pipeline();
            $pipeline->bind('worker', $this);
            if ($pipeline instanceof EventManageable && ! $pipeline->event()) {
                $pipeline->setEvent($event);
            }

            $this->createClient();
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

    public function server()
    {
        return $this->server;
    }

    protected function createClient()
    {
        $client = new Client($this, $this->event, $this->socket, $this->server->pipeline());

        return $client;
    }
}