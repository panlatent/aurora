<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Timer;

interface TimestampManageable
{
    /**
     * @return \Aurora\Timer\TimestampMarker
     */
    public function getTimestamp();

    /**
     * @param \Aurora\Timer\TimestampMarker $timestamp
     * @return void
     */
    public function setTimestamp(TimestampMarker $timestamp);
}