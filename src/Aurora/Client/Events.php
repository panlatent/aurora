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
    /**
     * @var \Aurora\Client
     */
    protected $bind;

    /**
     * @var \Aurora\Timer\Dispatcher
     */
    protected $timer;

    public function __construct($bind)
    {
        parent::__construct($bind);
        $this->timer = new TimerDispatcher();
    }

    public function register()
    {
        $this->timer->insert(function() { // Socket init wait timeout
            $timestamp = $this->bind->timestamp();
            if ( ! ($socketFirstReadUT = $timestamp->get(ServerTimestampType::SocketFirstRead))) { // HTTP Connection first request timeout
                $interval = TimestampMarker::interval($timestamp->get(ServerTimestampType::ClientStart));
                if ($interval >= $this->bind->config()->socket_first_wait_timeout) {
                    $this->bind->close();
                }
            }
        });
        $this->timer->insert(function() { // Socket complete read wait timeout
            $timestamp = $this->bind->timestamp();
            if (($socketLastReadUT = $timestamp->get(ServerTimestampType::SocketLastRead))) {
                $interval = TimestampMarker::interval($socketLastReadUT);
                if ($interval >= $this->bind->config()->socket_last_wait_timeout) {
                    $this->bind->close();
                }
            }
        });

        $this->event->bind('client:timer', $this);
        $this->event->listen('client:timer', $timer = Listener::timer(\Event::TIMEOUT | \Event::PERSIST), false);
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
}