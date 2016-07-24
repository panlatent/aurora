<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Pipeline\Exception;

class Pipeline
{
    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var \Aurora\Pipeline
     */
    protected $next;

    /**
     * @var bool
     */
    protected $closed = true;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var array
     */
    protected $data = [];

    public function __construct()
    {
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
            while ($n = $next->next()) {
                $next = $n;
            }

            $next->join($pipe);
        }

        return $this;
    }

    public function next()
    {
        return $this->next;
    }

    public function run()
    {
        $content = $this->content;

        foreach ($this->sections as $section) {
            if ($this->closed) {
                break;
            }
            if (null !== ($temp = call_user_func_array($section, [$content, $this]))) {
                $content = $temp;
            }
        }

        if ( ! $this->closed && $this->next) {
            foreach ($this->data as $item => $value) {
                $this->next->bind($item, $value);
            }
            $this->next->write($content);
            $this->next->open();
            $this->next->run();
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
        $this->content = $content;
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
            $this->content = '';
        }

        return $this;
    }

    public function append($content)
    {
        $this->content .= $content;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

}