<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Event;

trait EventManager
{
    /**
     * @var \Aurora\Event\Dispatcher
     */
    protected $event;

    public function setEventManager(Dispatcher $dispatcher)
    {
        $this->event = $dispatcher;
    }
}