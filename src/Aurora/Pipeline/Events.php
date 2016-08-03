<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Pipeline;

use Aurora\Event\EventAcceptor;

class Events extends EventAcceptor
{
    /**
     * @var \Aurora\Pipeline
     */
    protected $bind;

    public function register()
    {
        $this->event->bind('pipeline:append', $this);
    }

    public function onAppend()
    {
        $this->bind->send();
    }
}