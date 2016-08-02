<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Event;

interface SignalAcceptable
{
    const EVENT_SIGNAL_CALLBACK = 'acceptSignal';

    /**
     * @param int   $signal
     * @param mixed $arg
     *
     * @return void
     */
    public function acceptSignal($signal, $arg);
}