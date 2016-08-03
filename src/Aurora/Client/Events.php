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

class Events extends EventAcceptor
{
    /**
     * @var \Aurora\Client
     */
    protected $bind;

    protected $timers = [];

    public function register()
    {
        $this->event->bind('client:timer', $this);
        $this->event->listen('client:timer', $timer = Listener::timer(\Event::TIMEOUT | \Event::PERSIST), false);
        $timer->listen(1);
    }

    public function onTimer()
    {
        foreach ($this->timers as $timer) {
            if (false === call_user_func($timer, $this)) {
                break;
            }
        }
    }
}