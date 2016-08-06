<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

class ParameterStorage implements \ArrayAccess, \Countable, \Iterator
{
    protected $storage;

    public function __construct($storage = [])
    {
        $this->storage = $storage;
    }

    public function count()
    {
        return count($this->storage);
    }

    public function all()
    {
        return $this->storage;
    }

    public function get($name, $default = false)
    {
        return $this->storage[$name] ?? $default;
    }

    public function has($name)
    {
        return isset($this->storage[$name]);
    }

    public function set($name, $value)
    {
        $this->storage[$name] = $value;
    }

    public function remove($name)
    {
        unset($this->storage[$name]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function rewind()
    {
        reset($this->storage);
    }

    public function current()
    {
        return current($this->storage);
    }

    public function next()
    {
        next($this->storage);
    }

    public function key()
    {
        return key($this->storage);
    }

    public function valid()
    {
        return false !== $this->current();
    }
}