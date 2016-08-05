<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Event;

class Container implements \Countable
{
    /**
     * @var int
     */
    protected $count;

    /**
     * @var array
     */
    protected $storage = [];

    public function add($name, $value)
    {
        if ( ! isset($this->storage[$name])) {
            $this->storage[$name] = new \SplObjectStorage();
        }

        $this->storage[$name]->attach($value);
        ++ $this->count;
    }

    public function count()
    {
        return $this->count;
    }

    public function countSub($name)
    {
        return count($this->storage[$name]);
    }

    public function get($name)
    {
        return $this->storage[$name];
    }

    public function isset($name)
    {
        return isset($this->storage[$name]);
    }

    public function issetSub($name, $object)
    {
        if ( ! isset($this->storage[$name])) {
            return false;
        }

        return $this->storage[$name]->contains($object);
    }

    public function unset($name)
    {
        $this->count -= count($this->storage[$name]);
        unset($this->storage[$name]);
    }

    public function unsetSub($name, $object)
    {
        /** @var \SplObjectStorage $objectStorage */
        $objectStorage = $this->storage[$name];
        if ($objectStorage->contains($object)) {
            $objectStorage->detach($object);
            --$this->count;
        }
    }

}