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
    const EVENT_BUFFER_FILLED = 'client.write.buffer:filled';

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

        $this->event->bind('client.write.buffer:send', $this);
        $this->event->bind('client.write.buffer:fill_send', $this);
    }

    public function content()
    {
        return $this->buffer;
    }

    public function empty()
    {
        return '' === $this->buffer;
    }

    public function length()
    {
        return strlen($this->buffer);
    }

    public function size()
    {
        return $this->size;
    }

    public function status()
    {
        return $this->status;
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
            $this->event->listen('client.write.buffer:send', new Listener($this->socket, \Event::WRITE));
        }
    }

    public function write($content)
    {
        if ( ! $this->status) {
            $this->buffer = $content;
            $this->event->listen('client.write.buffer:send', new Listener($this->socket, \Event::WRITE));
        } else {
            if (strlen($content) + strlen($this->buffer) <= $this->size) {
                $this->buffer .= $content;
            } else { // If buffer filled
                do {
                    $freeSize = $this->size - strlen($this->buffer);
                    $this->buffer .= substr($content, 0, $freeSize);
                    $content = substr($content, $freeSize);
                    $this->event->listen('client.write.buffer:fill_send', new Listener($this->socket, \Event::WRITE | \Event::PERSIST, $this->out()));
                    if ('' !== $this->buffer) { // If event client.write.buffer:filled no handle
                        $this->buffer = '';
                    }
                } while (false !== $content);
            }
        }
    }

    public function out($length = self::OUT_LENGTH)
    {
        while (strlen($this->buffer)) {
            $content = substr($this->buffer, 0, $length);
            $this->buffer = substr($this->buffer, $length);
            $this->size = strlen($this->buffer);

            yield $content;
        }
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function onSend($socket, $what, Listener $listener)
    {
        socket_write($socket, $this->buffer);
        $this->buffer = '';
        $listener->delete();
        $this->event->free($listener->name(), $listener, true);
    }

    public function OnFillSend($socket, $what, Listener $listener)
    {
        /** @var \Generator $gen */
        $gen = $listener->argument();
        socket_write($socket, $gen->current()); echo $gen->current();
        $gen->next();
        if ( ! $gen->valid()) {
            $listener->delete();
        }
    }

}