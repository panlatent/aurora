<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Event;

class Container
{
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
    }

    public function get($name)
    {
        return $this->storage[$name];
    }

    public function remove($name, $object = null)
    {
        if (null === $object) {
            unset($this->storage[$name]);
        } else {
            /** @var \SplObjectStorage $objects */
            $objects = $this->storage[$name];
            $objects->detach($objects);
        }
    }

}