<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Pipeline\Buffer;
use Aurora\Pipeline\Exception;
use Generator;

class Pipeline
{
    /**
     * @var bool
     */
    protected $closed = true;

    /**
     * @var \Aurora\Pipeline\Buffer
     */
    protected $buffer;

    /**
     * @var \Aurora\Pipeline
     */
    protected $next;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $sections = [];

    public function __construct()
    {
        $this->buffer = new Buffer();
    }

    public function pipe($callback)
    {
        if ( ! is_callable($callback)) {
            throw new Exception("Parameter must be a callable type");
        }
        $this->sections[] = $callback;

        return $this;
    }

    public function join(Pipeline $pipe)
    {
        if ( ! $this->next) {
            $this->next = $pipe;
        } else {
            $next = $this->next;
            while ($afterNext = $next->next()) {
                $next = $afterNext;
            }

            $next->join($pipe);
        }

        return $this;
    }

    public function next()
    {
        return $this->next;
    }

    public function open()
    {
        if ( ! $this->closed) {
            throw new Exception("Pipeline has been opened");
        }
        $this->closed = false;

        return $this;
    }

    public function close()
    {
        if ( ! $this->closed) {
            $this->closed = true;
        }

        return $this;
    }


    public function buffer()
    {
        return $this->buffer;
    }

    public function data($name)
    {
        return $this->data[$name];
    }

    public function send($length = null)
    {
        if (null === $length) {
            $length = $this->buffer->size();
        }
        $this->dispatch($this->buffer->pop($length));
    }

    public function dispatch($content)
    {
        foreach ($this->sections as $section) {
            if ($this->closed) {
                break;
            }

            if (is_callable($section)) {
                if (null !== ($generator = call_user_func_array($section, [$content, $this]))) {
                    if (is_object($generator) && $generator instanceof Generator) {
                        /** @var \Generator $generator */
                        while ($generator->valid()) {
                            if (null !== ($res = $generator->current())) {
                                $content = $res;
                            }
                            $generator->send($content);
                        }

                        if (null !== ($res = $generator->getReturn())) {
                            $content = $res;
                        }
                    } else {
                        $content = $generator;
                    }
                }
            }
        }

        if ( ! $this->closed && $this->next) {
            foreach ($this->data as $item => $value) {
                $this->next->bind($item, $value);
            }
            $this->next->write($content);
            $this->next->open();
            $this->next->send();
            $this->next->close();
        }

        return $this;
    }

    public function bind($name, $data)
    {
        $this->data[$name] = $data;
    }

    public function write($content)
    {
        $this->buffer->write($content);
    }

    public function append($content)
    {
        $this->buffer->append($content);
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

}