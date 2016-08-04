<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

use Aurora\Event\EventAcceptable;
use Aurora\Event\EventManageable;
use Aurora\Http\Pipeline\Events;

class Pipeline extends \Aurora\Pipeline implements EventAcceptable, EventManageable
{
    public function dispatch($content)
    {
        parent::dispatch($content);

        if ($this->event) {
            $this->event->fire('http.pipeline:dispatch');
        }

        return $this;
    }

    protected function createEventAcceptor()
    {
        return new Events($this);
    }
}