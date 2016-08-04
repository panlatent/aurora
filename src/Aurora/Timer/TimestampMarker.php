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

    public function isset($name)
    {
        return isset($this->marks[$name]);
    }

    public function get($name, $default = 0)
    {
        if ( ! $this->isset($name)) return $default;

        return $this->marks[$name];
    }

    public function mark($name, $microsecond = 0)
    {
        if ( ! $microsecond) $microsecond = microtime(true);

        $this->marks[$name] = $microsecond;
    }

    public function update($name, $microsecond = 0)
    {
        if ( ! $this->isset($name)) {
            throw new Exception("");
        }

        $this->marks[$name] = $microsecond ?: microtime(true);
    }

    public function unset($name)
    {
        unset($this->marks[$name]);
    }

    public function clear()
    {
        $this->marks = [];
    }

    public function isInRange($min, $max)
    {

    }
}