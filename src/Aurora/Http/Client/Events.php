<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http\Client;

use Aurora\Event\Dispatcher;
use Aurora\Http\ServerTimestampType;
use Aurora\Timer\TimestampMarker;

class Events extends \Aurora\Client\Events
{
    /**
     * @var \Aurora\Http\Client
     */
    protected $bind;

    public function __construct(Dispatcher $dispatcher, $bind)
    {
        parent::__construct($dispatcher, $bind);
        /**
         * @todo Client connection execute timeout
         * @todo Client request execute timeout
         */
        $this->timer->insert([$this, 'onKeepAliveTimeoutTimer']);
    }

    public function onKeepAliveTimeoutTimer()
    {
        if ( ! $this->bind->keepAlive())
            return;
        $timestamp = $this->bind->timestamp();
        if ($requestLastUT = $timestamp->get(ServerTimestampType::RequestLast)) { // HTTP Connection keep-alive timeout
            $interval = TimestampMarker::interval($requestLastUT);
            $timeout = $this->bind->config()->keep_alive_timeout;
            if ($interval >= $timeout || TimestampMarker::intervalEqual($timeout, $interval, 0.25)) {
                $this->bind->declareClose();
            }
        }
    }

    public function onSocketReadWaitTimeoutTimer()
    {
        if (null === $this->bind->keepAlive() || true === $this->bind->keepAlive()) return; // Ignore this timer at keep-alive

        $timestamp = $this->bind->timestamp();
        if (($socketLastReadUT = $timestamp->get(ServerTimestampType::SocketLastRead))) {
            $interval = TimestampMarker::interval($socketLastReadUT);
            if ($interval >= $this->bind->config()->socket_last_wait_timeout) {
                $this->bind->declareClose();
            }
        }
    }
}