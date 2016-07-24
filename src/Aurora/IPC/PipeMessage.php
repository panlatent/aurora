<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\IPC;


class PipeMessage
{
    protected $pipe;

    public function __construct($pathname, $mod = 0660)
    {
        $this->pipe = new Pipe($pathname, $mod);
    }

    public function clear()
    {
        $this->pipe->write('');
    }

    public function receive()
    {
        $data = $this->pipe->read();

        return unserialize($data);
    }

    public function send($message)
    {
        $data = serialize($message);

        return $this->pipe->write($data);
    }
}