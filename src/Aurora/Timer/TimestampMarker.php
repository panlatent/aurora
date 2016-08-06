<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Timer;

class TimestampMarker
{
    protected $marks = [];

    public function __construct()
    {

    }

    public static function interval($timestamp, $now = 0)
    {
        if ( ! $now) $now = microtime(true);

        return ($now - $timestamp);
    }

    public static function intervalEqual($expected, $actual, $mistake = 0)
    {
        return ($actual >= $expected - $mistake && $actual <= $expected + $mistake);
    }

    public static function inIntervalRange($interval, $min, $max, $mistake = 0)
    {
        return ($interval >= $min - $mistake && $interval <= $max + $mistake);
    }

    public function has($name)
    {
        return isset($this->marks[$name]);
    }

    public function get($name, $default = 0)
    {
        if ( ! $this->has($name)) return $default;

        return $this->marks[$name];
    }

    public function mark($name, $microsecond = 0)
    {
        if ( ! $microsecond) $microsecond = microtime(true);

        $this->marks[$name] = $microsecond;
    }

    public function update($name)
    {
        $this->marks[$name] = microtime(true);
    }

    public function remove($name)
    {
        unset($this->marks[$name]);
    }

    public function clear()
    {
        $this->marks = [];
    }


}