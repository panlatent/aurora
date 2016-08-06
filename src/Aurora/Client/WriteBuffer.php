<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Client;

use Aurora\Event\Dispatcher as EventDispatcher;
use Aurora\Event\EventAccept;
use Aurora\Event\EventAcceptable;
use Aurora\Event\Listener;

class WriteBuffer implements EventAcceptable
{
    const OUT_LENGTH = 1024;
    const EVENT_BUFFER_SEND = 'client.write.buffer:send';

    use EventAccept;

    protected $buffer = '';

    protected $event;

    protected $socket;

    protected $size = 0;

    protected $status = false;

    public function __construct(EventDispatcher $event, $socket, $size = 4096)
    {
        $this->event = $event;
        $this->socket = $socket;
        $this->size = $size;

        $this->event->bind(static::EVENT_BUFFER_SEND, $this);
    }

    public function getContent()
    {
        return $this->buffer;
    }

    public function getLength()
    {
        return strlen($this->buffer);
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isEmpty()
    {
        return '' === $this->buffer;
    }

    public function open()
    {
        if ($this->status) {
            throw new Exception("client write buffer already opened");
        }

        $this->status = true;
        $this->clear();
    }

    public function close()
    {
        if ( ! $this->status) {
            throw new Exception("client write buffer already closed");
        }

        $this->status = false;
        $this->clear();
    }

    public function clear()
    {
        $this->buffer = '';
    }

    public function flush()
    {
        if (null !== $this->buffer) {
            $listener = new Listener($this->event, $this->socket, \Event::WRITE);
            $listener->register(static::EVENT_BUFFER_SEND);
            $listener->listen();
        }
    }

    public function write($content)
    {
        if ( ! $this->status) {
            $this->buffer = $content;
            $listener = new Listener($this->event, $this->socket, \Event::WRITE);
            $listener->register(static::EVENT_BUFFER_SEND);
            $listener->listen();
        } else {
            if (strlen($content) + strlen($this->buffer) <= $this->size) {
                $this->buffer .= $content;
            } else { // If buffer filled
                $time = (int)(strlen($content)/$this->size);
                socket_write($this->socket, $this->buffer);
                for ($i = 0; $i < $time; ++$i) {
                    socket_write($this->socket, substr($content, $i*$this->size, $this->size));
                }
                $this->buffer = substr($content, $this->size * $time);
            }
        }
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function onSend($socket, Listener $listener)
    {
        socket_write($socket, $this->buffer);
        $this->clear();
        $listener->delete();
        $this->event->free($listener->getName(), $listener, true);
    }

}