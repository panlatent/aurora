<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Timer;

class Dispatcher
{
    /**
     * @var \SplPriorityQueue
     */
    protected $timers;

    public function __construct()
    {
        $this->timers = new \SplPriorityQueue();
    }

    public function getTimers()
    {
        return $this->timers;
    }

    public function insert($callback, $priority = 0)
    {
        $this->timers->insert($callback, $priority);
    }

    public function dispatch()
    {
        $timers = clone $this->timers;
        foreach ($timers as $timer) {
            if (false === call_user_func($timer, $this)) {
                break;
            }
        }
    }
}