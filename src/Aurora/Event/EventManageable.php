<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Event;

interface EventManageable
{
    public function getEvent();

    public function getEventAcceptor();

    public function setEvent(Dispatcher $dispatcher);

    public function setEventAcceptor(EventAcceptor $eventAcceptor);
}