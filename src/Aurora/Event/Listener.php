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
     * @var resource|int
     */
    protected $fd;

    /**
     * @var mixed
     */
    protected $argument;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $what;

    public function __construct($fd, $what, $arg = null)
    {
        $this->fd = $fd;
        $this->what = $what;
        $this->argument = $arg;
    }

    public static function signal($signal, $arg = null)
    {
        return new static($signal, \Event::SIGNAL, $arg);
    }

    public static function timer($what, $arg = null)
    {
        return new static(-1, $what, $arg);
    }

    public function argument()
    {
        return $this->argument;
    }

    public function event()
    {
        return $this->event;
    }

    public function name()
    {
        return $this->name;
    }

    public function listen($timeout = -1)
    {
        if ( ! $this->base) {
            throw new Exception("Aurora\\Event\\Listener::listen(): need to set an event base");
        } elseif ( !is_callable($this->callback)) {
            throw new Exception("Aurora\\Event\\Listener::listen(): need to set a callback");
        }

        $this->event = new \Event($this->base, $this->fd, $this->what, $this->callback, $this);
        if ( ! $this->event->add($timeout)) {
            throw new Exception("event listener to listen a event failed");
        }
    }

    public function delete()
    {
        if (is_int($this->fd)) {
            if ($this->fd == -1)
                $this->event->delTimer();
            else
                $this->event->delSignal();
        } else {
            $this->event->del();
        }
    }

    public function setEventBase(\EventBase $base)
    {
        $this->base = $base;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setArgument($arg)
    {
        $this->argument = $arg;
    }

    public function setCallback($callback)
    {
        if ( ! is_callable($callback)) {
            throw new Exception("Aurora\\Event\\Listener::setCallback(): 
                                need a valid callback, give a " . gettype($this->callback));
        }

        $this->callback = $callback;
    }

}