<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Event;

class Dispatcher
{
    /**
     * @var \EventBase
     */
    protected $base;

    /**
     * @var \Aurora\Event\Container
     */
    protected $binds;

    /**
     * @var \Aurora\Event\Container
     */
    protected $listeners;

    public function __construct()
    {
        $this->base = new \EventBase();
        $this->binds = new Container();
        $this->listeners = new Container();
    }

    public function __destruct()
    {
        $this->base->free();
    }

    public function base()
    {
        return $this->base;
    }

    public function fire($name, $arg = [])
    {
        if ($this->binds->isset($name)) {
            foreach ($this->binds->get($name) as $callback) {
                if (is_object($callback)) {
                    if ($callback instanceof EventAcceptable) {
                        $callback->acceptEvent($name, $arg);
                        continue;
                    } elseif ( ! $callback instanceof \Closure) {
                        $callback = $callback->callback;
                    }
                }

                call_user_func_array($callback, $arg);
            }
        }
    }

    public function bind($name, $callback)
    {
        if ( ! is_object($callback)) {
            $callback = (object)['callback' => $callback];
        }
        $this->binds->add($name, $callback);
    }

    public function forward($fd, $what, Listener $listener)
    {
        $this->fire($listener->name(), [$fd, $what, $listener]);
    }

    public function listen($name, Listener $listener, $auto = true)
    {
        $this->listeners->add($name, $listener);
        $listener->setEventBase($this->base);
        $listener->setCallback([$this, 'forward']);
        $listener->setListenName($name);
        if ($auto) {
            $listener->listen();
        }
    }

    public function free($name, Listener $listener = null, $onlyClear = false)
    {
        if (null === $listener) {
            /** @var \Aurora\Event\Listener $listener */
            foreach ($this->listeners->get($name) as $listener) {
                if ( ! $onlyClear) {
                    $listener->delete();
                }
            }
            $this->listeners->unset($name);
        } else {
            if ( ! $onlyClear) {
                $listener->delete();
            }
            $this->listeners->remove($name, $listener);
        }
    }

    public function reInit()
    {
        $this->base->reInit();
        $this->listeners = new Container();
    }

    protected function getNameInfo($name)
    {
        if (false === ($pos = strpos($name, ':'))) {
            return ['name' => $name, 'action' => '*'];
        } else {
            return ['name' => substr($name, 0, $pos), 'action' => substr($name, $pos + 1)];
        }
    }
}