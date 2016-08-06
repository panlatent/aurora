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
    protected $dispatcher;

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

    /**
     * @var int
     */
    protected $priority;

    public function __construct(Dispatcher $dispatcher, $fd, $what, $arg = null)
    {
        $this->dispatcher = $dispatcher;
        $this->base = $dispatcher->getBase();
        $this->fd = $fd;
        $this->what = $what;
        $this->argument = $arg;
        $this->callback = [$this->dispatcher, Dispatcher::FORWARD_METHOD_NAME];
    }

    public static function signal(Dispatcher $dispatcher, $signal, $arg = null)
    {
        return new static($dispatcher, $signal, \Event::SIGNAL, $arg);
    }

    public static function timer(Dispatcher $dispatcher, $isPersist = false, $arg = null)
    {
        $what = $isPersist ? \Event::TIMEOUT | \Event::PERSIST : \Event::TIMEOUT;

        return new static($dispatcher, -1, $what, $arg);
    }

    public function getArgument()
    {
        return $this->argument;
    }

    public function getBase()
    {
        return $this->base;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isPersist()
    {
        return ($this->what & \Event::PERSIST) === \Event::PERSIST;
    }

    public function isTimer()
    {
        return ($this->what & \Event::TIMEOUT) === \Event::TIMEOUT;
    }

    public function isRead()
    {
        return ($this->what & \Event::READ) === \Event::READ;
    }

    public function isWrite()
    {
        return ($this->what & \Event::WRITE) === \Event::WRITE;
    }

    public function register($name)
    {
        if ( ! $this->base) {
            throw new Exception("need to set an event base");
        } elseif ( !is_callable($this->callback)) {
            throw new Exception("need to set a callback");
        }

        $this->name = $name;
        $this->dispatcher->getListeners()->add($name, $this);
        $this->event = new \Event($this->base, $this->fd, $this->what, $this->callback, $this);
        $this->event->setPriority($this->priority);

        return $this;
    }

    public function listen($timeout = -1)
    {
        if ( ! $this->event) {
            throw new Exception("need register a event");
        }
        if ( ! $this->event->add($timeout)) {
            throw new Exception("event listen failed");
        }

        return $this;
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

    public function free()
    {
        $this->dispatcher->getListeners()->removeSub($this->name, $this);
    }

    public function setEventBase(\EventBase $base)
    {
        $this->base = $base;
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

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

}