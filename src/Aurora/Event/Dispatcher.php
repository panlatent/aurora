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
        if ( ! is_callable($callback) && ( ! is_object($callback) || ! $callback instanceof EventAcceptable)) {
            throw new Exception('Aurora\\Event\\Dispatcher::bind(): expects parameter 1 to be a valid callback
                                or implements Aurora\\Event\\EventAcceptable, give a ' . gettype($callback));
        }

        if ( ! is_object($callback)) {
            $callback = (object)['callback' => $callback];
        }
        $this->binds->add($name, $callback);
    }

    public function forward()
    {
        try {
            switch ($num = func_num_args()) {
                case 1:
                    $listener = func_get_arg(0);
                    $this->fire($listener->name(), [$listener]);
                    break;
                case 2:
                    $signal = func_get_arg(0);
                    $listener = func_get_arg(1);
                    $this->fire($listener->name(), [$signal, $listener]);
                    break;
                case 3:
                    $fd = func_get_arg(0);
                    $what = func_get_arg(1);
                    $listener = func_get_arg(2);
                    $this->fire($listener->name(), [$fd, $what, $listener]);
                    break;
                default:
                    throw new Exception("Aurora\\Event\\Dispatcher::forward(): unable to forward the Libevent event,
                                    it is required to accept 1 to 3 arguments, give $num arguments");
            }
        } catch (\Throwable $ex) {
            var_dump($ex);
            echo $ex->getMessage();
            die();
        }
    }

    public function listen($name, Listener $listener, $auto = true)
    {
        $this->listeners->add($name, $listener);
        $listener->setEventBase($this->base);
        $listener->setName($name);
        $listener->setCallback([$this, 'forward']);
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

    public function reset()
    {
        $this->base->reInit();
        $this->binds = new Container();
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