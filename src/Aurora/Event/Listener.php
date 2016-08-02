<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Event;

class Listener
{
    /**
     * @var \EventBase
     */
    protected $base;

    /**
     * @var \Event
     */
    protected $event;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var resource|int
     */
    protected $fd;

    /**
     * @var int
     */
    protected $what;

    /**
     * @var mixed
     */
    protected $data;

    public function __construct($fd, $what, $arg = null)
    {
        $this->fd = $fd;
        $this->what = $what;
        $this->arg = $arg;
    }

    public function listen($timeout = null)
    {
        $this->event = new \Event($this->base, $this->fd, $this->what, $this->callback, $this);
        if (null === $timeout) {
            $this->event->add();
        } else {
            $this->event->add($timeout);
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function data()
    {
        return $this->data;
    }

    public function delete()
    {
        if (is_int($this->fd)) {
            $this->event->delSignal();
        } else {
            $this->event->del();
        }
    }

    public function setEventBase(\EventBase $base)
    {
        $this->base = $base;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setListenName($name)
    {
        $this->name = $name;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

}