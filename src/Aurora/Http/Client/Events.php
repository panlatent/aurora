<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http\Client;

use Aurora\Http\ServerTimestampType;
use Aurora\Timer\TimestampMarker;

class Events extends \Aurora\Client\Events
{
    /**
     * @var \Aurora\Http\Client
     */
    protected $bind;

    public function register()
    {
        parent::register();
        /**
         * @todo Client connection execute timeout
         * @todo Client request execute timeout
         */
        $this->timer->insert(function() {
            $timestamp = $this->bind->timestamp();
            if ($requestLastUT = $timestamp->get(ServerTimestampType::RequestLast)) { // HTTP Connection keep-alive timeout
                $interval = TimestampMarker::interval($requestLastUT);
                $timeout = $this->bind->config()->permanent_connection_wait_timeout;
                if ($interval >= $timeout || TimestampMarker::intervalEqual($timeout, $interval, 0.25)) {
                    $this->bind->declareClose();
                }
            }
        });
    }
}