<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Pipeline;

class Buffer
{
    protected $storage = '';

    public function size()
    {
        return strlen($this->storage);
    }

    public function find($needle, $start = 0)
    {
        return strpos($this->storage, $needle, $start);
    }

    public function append($content)
    {
        $this->storage .= $content;
    }

    public function insert($point, $content)
    {
        $before = substr($this->storage, 0, $point);
        $after = substr($this->storage, $point);

        $this->storage = $before . $content . $after;
    }

    public function write($content)
    {
        $this->storage = $content;
    }

    public function pop($length, $abandonSize = 0)
    {
        if ($length < 0) {
            throw new Exception("The second argument only is a non-negative");
        }

        $content = substr($this->storage, 0, $length);
        $this->storage = substr($this->storage, $length + $abandonSize);

        return $content;
    }
}