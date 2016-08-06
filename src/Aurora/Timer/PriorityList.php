<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Timer;

class PriorityList implements \Countable, \Iterator
{
    protected $storage;

    public function __construct()
    {
        $this->storage = new MaxHeap();
    }

    public function count()
    {
        return $this->storage->count();
    }

    public function isEmpty()
    {
        return $this->storage->isEmpty();
    }

    public function insert($value , $priority)
    {
        $this->storage->insert($this->storage->wrap($value, $priority));
    }

    public function rewind()
    {
        $this->storage->rewind();
    }

    public function current()
    {
        return $this->storage->unwrap($this->storage->current());
    }

    public function key()
    {
        return $this->storage->key();
    }

    public function next()
    {
        $this->storage->next();
    }

    public function valid()
    {
        return $this->storage->valid();
    }

}