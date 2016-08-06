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

    /**
     * @var \Aurora\Event\EventAcceptor
     */
    protected $eventAcceptor;

    /**
     * @return \Aurora\Event\Dispatcher
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return \Aurora\Event\EventAcceptor
     */
    public function getEventAcceptor()
    {
        return $this->eventAcceptor;
    }

    /**
     * @param \Aurora\Event\Dispatcher $dispatcher
     */
    public function setEvent(Dispatcher $dispatcher)
    {
        $this->event = $dispatcher;
    }

    /**
     * @param \Aurora\Event\EventAcceptor $eventAcceptor
     */
    public function setEventAcceptor(EventAcceptor $eventAcceptor)
    {
        $this->eventAcceptor = $eventAcceptor;
    }
}