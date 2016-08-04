<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Timer;

trait TimestampManager
{
    /**
     * @var \Aurora\Timer\TimestampMarker
     */
    protected $timestamp;

    /**
     * @return \Aurora\Timer\TimestampMarker
     */
    public function timestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \Aurora\Timer\TimestampMarker $timestamp
     * @return void
     */
    public function setTimestamp(TimestampMarker $timestamp)
    {
        $this->timestamp = $timestamp;
    }
}